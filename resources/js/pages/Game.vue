<script setup lang="ts">
import { Form, Head, usePage } from "@inertiajs/vue3";
import sendIconUrl from "@/../icons/send.png";
import { Ref, ref } from "vue";
import attachIconUrl from "@/../icons/image.png";
import { store } from "@/routes/chatMessages";
import MessageBubble from "@/components/MessageBubble.vue";
import { useEcho } from "@laravel/echo-vue";
import orcBaseImg from "@/../images/orc_base.webp";
import orcThinkingImg from "@/../images/orc_thinking.webp";
import orcLaughingImg from "@/../images/orc_laugh.webp";
import orcAngryImg from "@/../images/orc_angry.webp";
import { AIResponseDTO } from "@/types";

const reactionSpriteMap: Record<string, string> = {
    angry: orcAngryImg,
    laughing: orcLaughingImg,
};

const currentOrcImg = ref(orcBaseImg);

const page = usePage();

const props = defineProps<{
    aiConversationId?: string;
    gameId: number;
}>();

const aiConversationId: Ref<string | null> = ref(
    props.aiConversationId ?? null,
);

type Segment = { text: string; highlight: boolean };

function parseSegments(raw: string): Segment[] {
    const segments: Segment[] = [];
    const regex = /\*\*(.*?)\*\*/g;
    let last = 0;
    let m: RegExpExecArray | null;
    while ((m = regex.exec(raw)) !== null) {
        if (m.index > last)
            segments.push({ text: raw.slice(last, m.index), highlight: false });
        segments.push({ text: m[1], highlight: true });
        last = regex.lastIndex;
    }
    if (last < raw.length)
        segments.push({ text: raw.slice(last), highlight: false });
    return segments;
}

function buildHtml(segments: Segment[], charCount: number): string {
    let remaining = charCount;
    let html = "";
    for (const seg of segments) {
        if (remaining <= 0) break;
        const chunk = seg.text.slice(0, remaining);
        remaining -= chunk.length;
        html += seg.highlight
            ? `<span class='text-accent'">${chunk}</span>`
            : chunk;
    }
    return html;
}

const currentHtml = ref("");
let messageQueue: string[] = [];
let isProcessingQueue = false;
let advanceTimer: ReturnType<typeof setTimeout> | null = null;
let typewriterTimer: ReturnType<typeof setTimeout> | null = null;

function startTyping(text: string, onDone: () => void) {
    const segments = parseSegments(text);
    const totalChars = segments.reduce((n, s) => n + s.text.length, 0);
    let charIndex = 0;

    function tick() {
        charIndex++;
        currentHtml.value = buildHtml(segments, charIndex);
        if (charIndex < totalChars) {
            typewriterTimer = setTimeout(tick, 15);
        } else {
            onDone();
        }
    }

    currentHtml.value = "";
    if (typewriterTimer) clearTimeout(typewriterTimer);
    tick();
}

function processNextMessage() {
    if (messageQueue.length === 0) {
        isProcessingQueue = false;
        currentOrcImg.value = orcBaseImg;
        return;
    }

    isProcessingQueue = true;
    const next = messageQueue.shift()!;

    startTyping(next, () => {
        // 2.5s pause after typing finishes, then show next
        advanceTimer = setTimeout(processNextMessage, 2500);
    });
}

function displayAiMessages(messages: string[], reaction?: string | null) {
    if (advanceTimer) clearTimeout(advanceTimer);
    if (typewriterTimer) clearTimeout(typewriterTimer);
    currentHtml.value = "";
    messageQueue = [...messages];
    isProcessingQueue = false;

    if (reaction && reactionSpriteMap[reaction]) {
        currentOrcImg.value = reactionSpriteMap[reaction];
    }

    processNextMessage();
}

function applyFloatingAnimation(el: Element) {
    el.classList.add("floating");
}

const messageContentInput = ref("");
const lastUserMessage = ref("");
const lastImagePreviewUrl = ref<string | null>(null);
const isUserMessageShown = ref(false);
const isWaitingForLLM = ref(false);

const selectedImage = ref<File | null>(null);
const imagePreviewUrl = ref<string | null>(null);
const fileInputRef = ref<HTMLInputElement | null>(null);

function onImageSelected(event: Event) {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0] ?? null;
    selectedImage.value = file;
    if (imagePreviewUrl.value) {
        URL.revokeObjectURL(imagePreviewUrl.value);
    }
    imagePreviewUrl.value = file ? URL.createObjectURL(file) : null;
}

function triggerFileInput() {
    fileInputRef.value?.click();
}

function clearImage() {
    selectedImage.value = null;
    if (imagePreviewUrl.value) {
        URL.revokeObjectURL(imagePreviewUrl.value);
        imagePreviewUrl.value = null;
    }
    if (fileInputRef.value) {
        fileInputRef.value.value = "";
    }
}

function handleSuccess() {
    lastUserMessage.value = messageContentInput.value;
    messageContentInput.value = "";

    // Preserve the preview URL for the bubble before clearing the input state
    lastImagePreviewUrl.value = imagePreviewUrl.value;
    selectedImage.value = null;
    imagePreviewUrl.value = null;
    if (fileInputRef.value) {
        fileInputRef.value.value = "";
    }

    isWaitingForLLM.value = true;
    currentOrcImg.value = orcThinkingImg;

    displayUserMessage();

    setTimeout(hideUserMessage, 7000);
}

function displayUserMessage() {
    isUserMessageShown.value = true;
}

function hideUserMessage() {
    isUserMessageShown.value = false;
    lastUserMessage.value = "";
    if (lastImagePreviewUrl.value) {
        URL.revokeObjectURL(lastImagePreviewUrl.value);
        lastImagePreviewUrl.value = null;
    }
}

//

useEcho("user.1", ".chat.new_ai_message", (res: { data: AIResponseDTO }) => {
    const { data } = res;
    const messages = Array.isArray(data.messages)
        ? data.messages
        : Object.values(data.messages as Record<string, string>);
    displayAiMessages(messages, data.reaction);

    if (data.conversationId) {
        aiConversationId.value = data.conversationId;
    }

    isWaitingForLLM.value = false;
});
</script>

<template>
    <Head title="Jaskinia Orka"></Head>

    <main class="flex flex-col flex-1 items-center">
        <MessageBubble
            v-if="currentHtml"
            class="z-100 relative mt-12 w-135"
            :animated="false"
        >
            <span v-html="currentHtml" />
        </MessageBubble>

        <img
            :src="currentOrcImg"
            alt=""
            class="top-46 left-1/2 absolute w-70 h-full object-contain aspect-video -translate-x-1/2 -translate-y-23"
        />

        <div class="relative flex-1 mb-16 w-135">
            <Transition name="message" @after-enter="applyFloatingAnimation">
                <MessageBubble
                    v-if="isUserMessageShown"
                    :animated="false"
                    class="bottom-0 left-0 absolute"
                >
                    <img
                        v-if="lastImagePreviewUrl"
                        :src="lastImagePreviewUrl"
                        alt="Załączony obraz"
                        class="block mb-2 max-w-full max-h-40 object-contain pixel-box"
                    />
                    <span v-if="lastUserMessage">{{ lastUserMessage }}</span>
                </MessageBubble>
            </Transition>
        </div>

        <Form
            :action="store()"
            enctype="multipart/form-data"
            :transform="
                (data) => ({
                    ...data,
                    ai_conversation_id: aiConversationId,
                    game_id: props.gameId,
                    image: selectedImage,
                })
            "
            @success="handleSuccess"
            class="relative mt-auto w-135"
        >
            <input type="hidden" name="chat_conversation_id" :value="1" />

            <!-- Image preview badge -->
            <Transition name="preview">
                <div
                    v-if="selectedImage"
                    class="flex items-center gap-2 bg-slate-800 mb-2 px-3 py-1.5 border-goblin pixel-box"
                >
                    <img
                        v-if="imagePreviewUrl"
                        :src="imagePreviewUrl"
                        alt="Podgląd"
                        class="w-8 h-8 object-cover pixel-box"
                    />
                    <span
                        class="flex-1 font-sans text-platinum text-xs truncate"
                        >{{ selectedImage.name }}</span
                    >
                    <button
                        type="button"
                        @click="clearImage"
                        class="font-sans text-red-400 hover:text-red-300 text-xs cursor-pointer"
                        aria-label="Usuń obraz"
                    >
                        ✕
                    </button>
                </div>
            </Transition>

            <div class="relative">
                <input
                    v-model.trim="messageContentInput"
                    placeholder="Wprowadź wiadomość do orka..."
                    type="text"
                    name="content"
                    id="user_message"
                    :disabled="isWaitingForLLM"
                    class="bg-platinum px-4 py-3 pr-22 focus:outline-none w-full font-sans text-onyx transition-opacity duration-300 pixel-box"
                    :class="{
                        'opacity-50 cursor-not-allowed': isWaitingForLLM,
                    }"
                />

                <!-- Hidden file input -->
                <input
                    ref="fileInputRef"
                    type="file"
                    name="image"
                    accept="image/*"
                    class="hidden"
                    @change="onImageSelected"
                />

                <!-- Attach button -->
                <button
                    type="button"
                    @click="triggerFileInput"
                    :disabled="isWaitingForLLM"
                    class="top-1/2 right-11 absolute w-6 h-6 transition-opacity -translate-y-1/2 duration-300 attach-btn"
                    :class="
                        isWaitingForLLM
                            ? 'opacity-30 cursor-not-allowed'
                            : 'cursor-pointer'
                    "
                    title="Dołącz obraz"
                >
                    <img
                        :src="attachIconUrl"
                        alt="Attach icon"
                        class="w-full h-full"
                    />
                </button>

                <button
                    type="submit"
                    :disabled="isWaitingForLLM"
                    class="top-1/2 right-4 absolute w-6 h-6 transition-opacity -translate-y-1/2 duration-300"
                    :class="
                        isWaitingForLLM
                            ? 'opacity-30 cursor-not-allowed'
                            : 'cursor-pointer'
                    "
                >
                    <img
                        :src="sendIconUrl"
                        alt="Send icon"
                        class="w-full h-full"
                    />
                </button>
            </div>
        </Form>
    </main>
</template>

<style scoped>
@reference "../../css/app.css";

.message-enter-active,
.message-leave-active {
    @apply origin-center transition-all;
}

.message-enter-from {
    transform: translateY(36px) scale(0.5);
    opacity: 0;
}

.message-enter-to {
    transform: translateY(0) scale(1);
    opacity: 1;
}

.message-leave-to {
    opacity: 0;
}

.preview-enter-active,
.preview-leave-active {
    transition: all 0.2s ease;
}

.preview-enter-from,
.preview-leave-to {
    opacity: 0;
    transform: translateY(8px);
}

.attach-btn {
    filter: brightness(0.7);
    transition: filter 0.15s ease;
}

.attach-btn:hover {
    filter: brightness(1.2);
}
</style>
