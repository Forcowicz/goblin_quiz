<script setup lang="ts">
import { onMounted, onUnmounted, ref } from "vue";
import caveImg from "@/../images/cave.jpg";

const viewportHeight = ref<string>("100dvh");

function updateViewport() {
    if (window.visualViewport) {
        viewportHeight.value = `${window.visualViewport.height}px`;
        if (window.scrollY !== 0) {
            window.scrollTo(0, 0);
        }
    }
}

onMounted(() => {
    if (window.visualViewport) {
        updateViewport();
        window.visualViewport.addEventListener("resize", updateViewport);
        window.visualViewport.addEventListener("scroll", updateViewport);
    }
});

onUnmounted(() => {
    if (window.visualViewport) {
        window.visualViewport.removeEventListener("resize", updateViewport);
        window.visualViewport.removeEventListener("scroll", updateViewport);
    }
});
</script>

<template>
    <div
        class="fixed inset-0 flex flex-col bg-black p-3 sm:p-6 md:p-8 overflow-hidden w-full"
        :style="{ height: viewportHeight }"
    >
        <img
            :src="caveImg"
            alt=""
            class="bottom-0 left-1/2 absolute w-full h-full object-cover aspect-video -translate-x-1/2 -translate-y-23 pointer-events-none select-none"
        />
        <slot />
    </div>
</template>
