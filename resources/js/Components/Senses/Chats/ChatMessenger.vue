<template>
    <div class="flex flex-col justify-between flex-grow">
        <div class="p-3 border-b border-zinc-200 flex items-center justify-between">
            <div class="text-black font-semibold text-xl">
                {{ chat.name }}
            </div>

            <div class="flex items-center">
                <UserPopover 
                    v-if="agent" 
                    panel-class="origin-top-left left-0" 
                    :id="agent?.id" 
                    :title="agent?.full_name"
                >
                    <Tag class="mr-2" highlight-border :highlight-dot="false" colour="purple">
                        {{ agent?.full_name }}
                    </Tag>
                </UserPopover>
                <ButtonGroup>
                    <PrimaryButton v-if="unassigned" @click="acceptChat">Accept Chat</PrimaryButton>
                    <SecondaryButton v-if="yourAssigned">Leave</SecondaryButton>
                    <SecondaryButton v-if="yourAssigned">Invite Agent</SecondaryButton>
                    <SecondaryButton v-if="yourAssigned">Mark As Resolved</SecondaryButton>
                </ButtonGroup>
            </div>
        </div>
        <div id="messages" class="flex-grow overflow-y-auto overflow-x-hidden p-3 relative bg-white">
            <Message
                v-for="(message, index) in chat?.messages"
                :key="'message:' + message.id"
                :message="message"
                :in-chain="isInChain(message, index)"
            />
        </div>
        <div v-if="chat?.typers?.size > 0" class="transition duration-200 text-black px-3 py-1 border-t border-zinc-200 text-zinc-400" :class="{ 'opacity-0' : !chat?.typers.size }">
            {{ [...chat?.typers].join(', ') }} {{ chat?.typers.size > 1 ? 'are' : 'is' }} typing...
        </div>
        <div class="border-t border-zinc-200 flex relative">
            <div 
                id="input"
                class="p-3 text-black bg-transparent border-0 flex-grow focus:ring-0 pr-32 textarea-resize"
                :class="{
                    'cursor-not-allowed': !yourAssigned,
                }"
                v-html="message.content"
                :contenteditable="yourAssigned"
                @keydown="textareaKeydown"
                @paste="textareaPaste"
                data-placeholder="Type a message..."
            >
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

import Message from '../Messages/Message.vue';
import SeInput from '../../Ui/Inputs/SeInput.vue';
import UserPopover from '../Users/UserPopover.vue';
import Tag from '../../Ui/Tags/Tag.vue';
import ButtonGroup from '../../Ui/Buttons/ButtonGroup.vue';
import PrimaryButton from '../../Ui/Buttons/PrimaryButton.vue';
import SecondaryButton from '../../Ui/Buttons/SecondaryButton.vue';
import WarningButton from '../../Ui/Buttons/WarningButton.vue';
import SuccessButton from '../../Ui/Buttons/SuccessButton.vue';

import user from '../../../Support/user';
import sanitiseContent from '../../../Support/sanitiseContent';

export default {
    components: {
        Message,
        SeInput,
        UserPopover,
        Tag,
        ButtonGroup,
        PrimaryButton,
        SecondaryButton,
        WarningButton,
        SuccessButton
    },
    props: {
        chat: {
            type: [Object, null],
            required: true
        },
    },
    data() {
        return {
            message: {
                chat_id: this.chat.id,
                content: '',
                author: user().full_name,
                from_agent: true,
                sent_at: null
            },
            isTyping: false,
            typingTimeout: null,
            user: user()
        }
    },
    computed: {
        unassigned() {
            return this.chat?.user_id === null;
        },
        yourAssigned() {
            return this.chat?.user_id === this.user.id;
        },
        agent() {
            return this.chat?.user;
        },
        defaultMessage() {
            if (this.yourAssigned) {
                return 'Hi, my name is Josh. How can I help you today?';
                } else {
                return 'You are not assigned to this chat';
            }
        },
        defaultMessageSent() {
            // Any message sent by the current user
            return Object.values(this.chat?.messages)?.filter(message => message.author === this.user.full_name);
        },
    },

    watch: {
        typers() {
            this.chat.typers = this.typers;
        }
    },

    mounted() {
        if (!this.defaultMessageSent) {
            this.message.content = this.defaultMessage;
        }
        console.log(this.chat);
    },

    methods: {
        acceptChat() {
            axios.get(`/api/v2/accept/chats/${this.chat.id}`)
                .then(response => {
                    if (response.data?.user_id) {
                        this.chat.user_id = response.data.user_id;
                    }
                    
                    if (response.data?.user) {
                        this.chat.user = response.data.user;
                    }

                    if (response.data?.status) {
                        this.chat.status = response.data.status;
                    }

                    if (response.data?.status_id) {
                        this.chat.status_id = response.data.status_id;
                    }
                });
        },
        isInChain(message) {
            let messages = Object.values(this.chat.messages);
            // Find index of message in array of messages
            const index = messages.findIndex(m => m.id === message.id);

            if (messages.length <= 1 || index === 0) {
                return false;
            }

            let lastMessage = messages[index - 1];
            return message.author === lastMessage.author;
        },
        textareaKeydown(event) {
            if (event.key === 'Enter' && event.shiftKey) {
                // Scroll to bottom of element
                event.target.scrollTop = event.target.scrollHeight;
                return;
            }

            if (event.key === 'Enter') {
                event.preventDefault();
                this.sendMessage();
            } else {
                this.typing();
            }
        },
        sendMessage() {
            this.message.content = document.getElementById('input').innerHTML;

            this.message.content = sanitiseContent(this.message.content);

            if (!this.message.content) {
                return;
            }

            const date = new Date();
            const formattedDate =
                date.getFullYear() +
                "-" +
                String(date.getMonth() + 1).padStart(2, "0") +
                "-" +
                String(date.getDate()).padStart(2, "0") +
                " " +
                String(date.getHours()).padStart(2, "0") +
                ":" +
                String(date.getMinutes()).padStart(2, "0") +
                ":" +
                String(date.getSeconds()).padStart(2, "0");

            this.message.sent_at = formattedDate;
            
            axios.post(`/api/v2/messages`, this.message)
            .then(response => {
                if (response.data) {
                    const element = document.getElementById("messages");
                    element.scrollTo({ top: element.scrollHeight, behavior: 'smooth' });
                    this.message.content = '';
                }
            });
        },

        typing() {
            if (this.isTyping) {
                clearTimeout(this.typingTimeout);
            } else {
                this.isTyping = true;
                axios.post(`/api/v2/typing`, {
                    chat_id: this.chat.id,
                    name: this.user.full_name,
                    from_agent: true,
                });
            }

            this.typingTimeout = setTimeout(() => {
                this.isTyping = false;
                axios.post(`/api/v2/stop/typing`, {
                    chat_id: this.chat.id,
                    name: this.user.full_name,
                    from_agent: true,
                });
            }, 5000); // 5 seconds timeout
        },

        textareaPaste(e) {
            if (e.clipboardData) {
                const items = e.clipboardData.items;
                if (items) {
                Array.from(items).forEach((item) => {
                    if (item.type.indexOf("image") !== -1) {
                        e.preventDefault();
                        this.imagePaste(item);
                    }
                });
                }
            }
        },

        imagePaste(item) {
            const blob = item.getAsFile();
            const reader = new FileReader();

            reader.onloadend = () => {
                const base64data = reader.result;
                const resizedImage = new Image();

                resizedImage.onload = () => {
                this.modalTextArea.appendChild(resizedImage);

                addNewLine(this.modalTextArea);
                };

                resizedImage.src = base64data;
            };

            reader.readAsDataURL(blob);
        }
    }
}
</script>