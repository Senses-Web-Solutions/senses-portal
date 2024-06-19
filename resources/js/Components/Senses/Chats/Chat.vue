<template>
    <div class="flex flex-col justify-between flex-grow">
        <div class="p-3 border-b border-zinc-200 flex items-center justify-between">
            <div class="text-black font-semibold text-xl">
                {{ chat.name }}
            </div>

            <div class="flex items-center">
                <TransitionGroup
                    enter-active-class="transition duration-150 ease-out"
                    enter-from-class="scale-75 opacity-0"
                    enter-to-class="scale-100 opacity-100"
                    leave-active-class="transition duration-150 ease-in"
                    leave-from-class="scale-100 opacity-100"
                    leave-to-class="scale-75 opacity-0"
                >
                    <div v-for="agent in chat.agents" :key="agent.id">
                        <UserPopover 
                            panel-class="origin-top-left left-0" 
                            :id="agent?.id" 
                            :title="agent?.full_name"
                        >
                            <Tag class="mr-2" highlight-border :highlight-dot="false" colour="purple">
                                {{ agent?.full_name }}
                            </Tag>
                        </UserPopover>
                    </div>
                </TransitionGroup>
                <ButtonGroup>
                    <ChatActions :chat="chat" :show-history="showHistory" />
                </ButtonGroup>
            </div>
        </div>


        <div id="messages" class="flex-grow overflow-y-auto overflow-x-hidden p-3 relative bg-white">
            <TransitionGroup
                enter-active-class="transition duration-150 ease-out"
                enter-from-class="scale-75 opacity-0"
                enter-to-class="scale-100 opacity-100"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="scale-100 opacity-100"
                leave-to-class="scale-75 opacity-0"
            >
                <Message
                    v-for="(message, index) in chat?.messages"
                    :key="'message:' + message.id"
                    :message="message"
                    :in-chain="isInChain(message, index)"
                    :your-assigned="yourAssigned"
                />
            </TransitionGroup>
        </div>


        <div v-if="chat?.typers?.size > 0" class="transition duration-200 text-black px-3 py-1 border-t border-zinc-200 text-zinc-400" :class="{ 'opacity-0' : !chat?.typers.size }">
            {{ [...chat?.typers].join(', ') }} {{ chat?.typers.size > 1 ? 'are' : 'is' }} typing...
        </div>


        <div class="border-t border-zinc-200 flex relative">
            <div class="flex flex-col w-full p-3 translate">
                <div 
                    id="input"
                    class="text-black bg-transparent border-0 flex-grow focus:ring-0 textarea-resize"
                    :class="{
                        'cursor-not-allowed': !yourAssigned,
                        'mb-3': files.length > 0
                    }"
                    v-html="message.content"
                    :contenteditable="yourAssigned"
                    @keydown="textareaKeydown"
                    @paste="textareaPaste"
                    data-placeholder="Type a message..."
                >
                </div>
                <div id="preview"></div>
                <div class="flex flex-wrap" v-if="files">
                    <File class="mb-2 mr-2" v-for="file in files" :key="file.id" :file="file" :removeable="true" @remove="removeFile"/>
                </div>
                <div id="picker" class="z-50 absolute -translate-y-[105%] translate-x-[100%]"></div>
            </div>
            <div class="pl-3 py-3 pr-3 flex relative">
                <ButtonGroup class="!items-start">
                    <SecondaryButton size="xs" @click="toggleEmojiPicker">
                        <EmojiHappyIcon class="w-4 h-4 text-zinc-500" />
                    </SecondaryButton>

                    <SecondaryButton size="xs" @click="openDropzone">
                        <PhotographIcon class="w-4 h-4 text-zinc-500" />
                    </SecondaryButton>
                </ButtonGroup>

                <div id="dropzone"></div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Dropzone from 'dropzone';
import data from '@emoji-mart/data';
import { Picker } from 'emoji-mart';
import { EmojiHappyIcon, PhotographIcon } from '@heroicons/vue/outline';

import Message from '../Messages/Message.vue';
import SeInput from '../../Ui/Inputs/SeInput.vue';
import UserPopover from '../Users/UserPopover.vue';
import Tag from '../../Ui/Tags/Tag.vue';
import ButtonGroup from '../../Ui/Buttons/ButtonGroup.vue';
import PrimaryButton from '../../Ui/Buttons/PrimaryButton.vue';
import SecondaryButton from '../../Ui/Buttons/SecondaryButton.vue';
import WarningButton from '../../Ui/Buttons/WarningButton.vue';
import SuccessButton from '../../Ui/Buttons/SuccessButton.vue';
import File from '../Files/File.vue';

import user from '../../../Support/user';
import sanitiseContent from '../../../Support/sanitiseContent';
import ChatActions from './ChatActions.vue';

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
        SuccessButton,
        EmojiHappyIcon,
        PhotographIcon,
        File,
        ChatActions,
    },
    props: {
        chat: {
            type: [Object, null],
            required: true
        },
        showHistory: {
            type: Boolean,
            default: false
        },
    },
    data() {
        return {
            message: {
                chat_id: this.chat.id,
                content: '',
                author: user().full_name,
                from_agent: true,
                sent_at: null,
                file_ids: [],
            },
            isTyping: false,
            typingTimeout: null,
            user: user(),

            showEmojiPicker: false,

            dropzone: null,
            acceptedFiles: null,

            files: [],
        }
    },
    computed: {
        unassigned() {
            return this.chat?.agents?.length === 0 || !this.chat?.agents;
        },
        yourAssigned() {
            return this.chat?.agents?.some(agent => agent.id === this.user.id) ?? false;
        },
        defaultMessage() {
            if (this.yourAssigned) {
                return `Hi, my name is ${this.user.full_name}. How can I help you today?`;
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
        
        this.setupKeyboardShortcuts();
        this.setupDropzone();
    },

    methods: {
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
            const div = event.target;

            if (event.key === 'Backspace' && div.textContent.length === 1) {
                div.innerHTML = '';
                return;
            }

            if (event.key === 'Enter' && event.shiftKey) {
                // Scroll to bottom of element
                div.scrollTop = div.scrollHeight;
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
                }
            });

            document.getElementById('input').innerHTML = '';
            this.message.content = '';
            this.message.file_ids = [];
            this.files = [];
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
            }, 5000);
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
                    let inputElement = document.getElementById('input');
                    inputElement.appendChild(resizedImage);
                    
                    const br = document.createElement("br");
                    inputElement.appendChild(br);
                    const range = document.createRange();
                    const sel = window.getSelection();
                    range.setStart(br, 0);
                    range.collapse(true);
                    sel.removeAllRanges();
                    sel.addRange(range);
                };

                resizedImage.src = base64data;
            };

            reader.readAsDataURL(blob);
        },

        toggleEmojiPicker() {
            this.showEmojiPicker = !this.showEmojiPicker;

            if (this.showEmojiPicker) {
                this.setupEmojiPicker();
            } else {
                this.destroyEmojiPicker();
            }
        },

        setupEmojiPicker() {
            new Picker({
                parent: document.getElementById('picker'),
                data: data,
                onEmojiSelect: (emoji) => {
                    document.getElementById('input').innerHTML += emoji.native;
                },
            })
        },

        destroyEmojiPicker() {
            document.getElementById('picker').innerHTML = '';
        },

        setupKeyboardShortcuts() {
            document.addEventListener('keydown', (event) => {
                if (event.ctrlKey && event.key === 'e') {
                    this.toggleEmojiPicker();
                }
            });
        },

        setupDropzone() {
            this.dropzone = new Dropzone(document.getElementById('dropzone'), {
                url: '/api/v2/files',
                clickable: document.getElementById('dropzone'),
                previewsContainer: document.getElementById('preview'),
                previewTemplate:
            `<div class="relative grid grid-cols-12 last:border-transparent border-b border-zinc-200">
                <div class="py-2 px-1 col-span-12">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                            <img data-dz-thumbnail dclass="w-auto h-10" />
                        </div>
                        <div class="space-y-2 truncate ml-4 w-full">
                            <div class="flex justify-between items-end space-x-2">
                                <div class="text-base text-zinc-700 truncate">
                                    <span data-dz-name></span>
                                </div>
                                <div class="text-sm text-zinc-500">
                                    <span data-dz-size></span>
                                </div>
                            </div>
                            <div class="dz-progress-bar opacity-75 w-full">
                                <div class="flex flex-grow bg-zinc-200 rounded" role="progressbar">
                                    <div data-dz-uploadprogress class="w-0 bg-primary-700 rounded h-1 text-center transition" style="transition: width 0.5s">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="dz-error-message hidden text-red-600"><span data-dz-errormessage></span></div>
            </div>`,
                acceptedFiles: this.acceptedFiles,
                parallelUploads: 5,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                success: (file, response) => {
                    this.dropzone.removeFile(file);
                    this.addFile(response);
                },
                error: (file, error, response) => {
                    this.dropzone.removeFile(file);
                    alert(error.message);
                }
            });
        },

        openDropzone() {
            this.dropzone.hiddenFileInput.click();
        },

        addFile(file) {
            this.files.push(file);
            this.message.file_ids.push(file.id);
        },

        removeFile(removedFile) {
            this.files = this.files.filter(f => f.id !== removedFile.id);
            this.message.file_ids = this.message.file_ids.filter(id => id !== removedFile.id);
        }
    }
}
</script>
<style>
    em-emoji-picker {
        --rgb-accent: 136, 108, 179;
        --rgb-background: 39, 39, 42;
    }
</style>