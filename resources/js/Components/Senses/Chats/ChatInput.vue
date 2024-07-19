<template>
    <div class="border-t border-zinc-200 flex relative bg-white">
        <div
            v-if="
                searchedCannedMessages.length > 0 &&
                capturingCannedMessageShortcut
            "
            class="absolute -top-44 h-40 w-96 left-3 bg-white shadow-lg p-3 rounded-lg divide-y divide-zinc-200 text-black overflow-y-scroll"
        >
            <div
                v-for="(message, index) in searchedCannedMessages"
                :key="index"
                class="p-2 rounded cursor-pointer hover:bg-zinc-100 transition-all"
                :class="{'bg-zinc-100': activeCannedIndex === index}"
                @click="() => addCannedMessage(index)"
            >
                <strong>{{ message.shortcut }}</strong> {{ message.content }}
            </div>
        </div>
        <div class="flex flex-col w-full p-3 translate relative">
            <div
                id="input"
                class="text-black bg-transparent border-0 flex-grow focus:ring-0 textarea-resize"
                :class="{
                    'cursor-not-allowed': !yourAssigned,
                    'mb-3': files.length > 0,
                }"
                v-html="message.content"
                :contenteditable="yourAssigned"
                @keydown="textareaKeydown"
                @paste="textareaPaste"
                data-placeholder="Type a message..."
            ></div>
            <div id="preview"></div>
            <div class="flex flex-wrap" v-if="files">
                <File
                    class="mb-2 mr-2"
                    v-for="file in files"
                    :key="file.id"
                    :file="file"
                    :removeable="true"
                    @remove="removeFile"
                />
            </div>
            <div
                id="picker"
                class="z-50 absolute -translate-y-[105%] translate-x-[100%]"
            ></div>
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
</template>
<script>
import axios from "axios";
import Dropzone from "dropzone";
import data from "@emoji-mart/data";
import { Picker } from "emoji-mart";
import File from "../Files/File.vue";
import { EmojiHappyIcon, PhotographIcon } from "@heroicons/vue/outline";

import ButtonGroup from "../../Ui/Buttons/ButtonGroup.vue";
import PrimaryButton from "../../Ui/Buttons/PrimaryButton.vue";
import SecondaryButton from "../../Ui/Buttons/SecondaryButton.vue";

import user from "../../../Support/user";
import sanitiseContent from "../../../Support/sanitiseContent";

export default {
    components: {
        File,
        ButtonGroup,
        SecondaryButton,
        PrimaryButton,
        EmojiHappyIcon,
        PhotographIcon,
    },
    props: {
        chat: {
            type: Object,
            required: true,
        },
        yourAssigned: {
            type: Boolean,
            required: true,
        },
    },
    data() {
        return {
            isTyping: false,

            message: {
                chat_id: this.chat.id,
                content: "",
                author: user().full_name,
                from_agent: true,
                sent_at: null,
                file_ids: [],
            },

            showEmojiPicker: false,

            dropzone: null,
            acceptedFiles: null,

            files: [],

            typingTimeout: null,

            user: user(),

            capturingCannedMessageShortcut: false,
            cannedShortcut: "",
            cannedMessages: [],
            activeCannedIndex: 0,
            send: true,
        };
    },
    computed: {
        searchedCannedMessages() {
            // If canned short cut contains '//' then replace it with '/'
            if (this.cannedShortcut.includes("//")) {
                this.cannedShortcut = this.cannedShortcut.replace("//", "/");
            }
            if (!this.capturingCannedMessageShortcut) {
                return [];
            }

            if (this.cannedShortcut === "" || this.cannedShortcut === "/") {
                return this.cannedMessages;
            }

            const array = this.cannedMessages.filter((message) =>
                message.shortcut.includes(this.cannedShortcut)
            );

            return array;
        },
    },
    mounted() {
        axios
            .get(`/api/v2/users/${user().id}/canned-messages`)
            .then((response) => {
                this.cannedMessages = response.data.data;
            })
            .catch((error) => {
                console.log(error);
            });

        this.setupKeyboardShortcuts();
        this.setupDropzone();
    },
    methods: {
        textareaKeydown(event) {
            const div = event.target;

            if (event.key === "Backspace") {
                if (div.textContent.length === 1) {
                    div.innerHTML = "";
                }

                // Check if the '/' character is removed
                // If second to last character is '/' then reset the capturingCannedMessageShortcut
                if (
                    div.textContent.length > 1 &&
                    div.textContent[div.textContent.length - 2] === "/"
                ) {
                    this.cannedShortcut = "/";
                }

                if (div.textContent.endsWith("/")) {
                    this.capturingCannedMessageShortcut = false;
                }
                return;
            }

            if (event.key === "Enter" && event.shiftKey) {
                // Scroll to bottom of element
                div.scrollTop = div.scrollHeight;
                return;
            }

            if (event.key === "/") {
                this.capturingCannedMessageShortcut = true;
                this.send = false;
            }

            if (this.capturingCannedMessageShortcut) {
                if (event.key === "Tab" || event.key === 'Enter') {
                    event.preventDefault();
                    this.addCannedMessage(this.activeCannedIndex);
                } else {
                    this.cannedShortcut += event.key;
                }
            }

            if (event.key === "Enter" && this.send) {
                event.preventDefault();
                this.sendMessage();
            } else {
                // If '/' key then detect shortcut and if press tab after, then load canned_message content

                this.typing();
            }
        },
        addCannedMessage(index){
            this.addCannedMessageToMessage(this.searchedCannedMessages[index].content);
        },
        addCannedMessageToMessage(content) {
            const div = document.getElementById("input");
            // Remove the shortcut from the message
            div.innerText = div.innerText.replace(this.cannedShortcut, "");

            const cannedAbbreviations = {
                user: this.chat.chat_user.full_name,
            };

            let cannedMessage = content;

            const re = /\${(.*?)}/g;

            const matches = cannedMessage.match(re);

            if (matches) {
                matches.forEach((match) => {
                    const key = match.replace("${", "").replace("}", "");
                    cannedMessage = cannedMessage.replace(
                        match,
                        cannedAbbreviations[key]
                    );
                });
            }

            div.innerText = div.innerText + " " + cannedMessage;

            // Put the cursor at the end of the id input
            this.$nextTick(() => {
                const range = document.createRange();
                const sel = window.getSelection();
                if (div.childNodes.length > 0) {
                    const offset = div.childNodes.length > 1 ? 1 : 0;
                    range.setStart(div, offset);
                } else {
                    range.setStart(div, 0);
                }
                range.collapse(true);
                sel.removeAllRanges();
                sel.addRange(range);
                this.capturingCannedMessageShortcut = false;
            });

            this.cannedShortcut = "";
            this.send = true;
        },
        sendMessage() {
            this.message.content = document.getElementById("input").innerHTML;

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
            this.message.chat_id = this.chat.id;

            axios.post(`/api/v2/messages`, this.message).then((response) => {
                if (response.data) {
                    const element = document.getElementById("messages");
                    element.scrollTo({
                        top: element.scrollHeight,
                        behavior: "smooth",
                    });
                }
            });

            document.getElementById("input").innerHTML = "";
            this.message.content = "";
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
            }, 3000);
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
                    let inputElement = document.getElementById("input");
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
                parent: document.getElementById("picker"),
                data: data,
                onEmojiSelect: (emoji) => {
                    document.getElementById("input").innerHTML += emoji.native;
                },
            });
        },

        destroyEmojiPicker() {
            document.getElementById("picker").innerHTML = "";
        },

        setupKeyboardShortcuts() {
            document.addEventListener("keydown", (event) => {
                if (event.ctrlKey && event.key === "e") {
                    this.toggleEmojiPicker();
                }
            });
        },

        setupDropzone() {
            this.dropzone = new Dropzone(document.getElementById("dropzone"), {
                url: "/api/v2/files",
                clickable: document.getElementById("dropzone"),
                previewsContainer: document.getElementById("preview"),
                previewTemplate: `<div class="relative grid grid-cols-12 last:border-transparent border-b border-zinc-200">
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
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
                success: (file, response) => {
                    this.dropzone.removeFile(file);
                    this.addFile(response);
                },
                error: (file, error, response) => {
                    this.dropzone.removeFile(file);
                    alert(error.message);
                },
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
            this.files = this.files.filter((f) => f.id !== removedFile.id);
            this.message.file_ids = this.message.file_ids.filter(
                (id) => id !== removedFile.id
            );
        },
    },
};
</script>
<style>
em-emoji-picker {
    --rgb-accent: 136, 108, 179;
    --rgb-background: 24, 24, 28;
}
</style>
