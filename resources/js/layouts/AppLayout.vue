<script setup lang="ts">
import caveImg from "@/../images/cave.jpg";
import tempCave from "@/../images/temp_orc.webp";
</script>

<template>
    <div
        class="relative flex flex-col bg-black p-8 min-h-screen overflow-hidden"
    >
        <img
            :src="caveImg"
            alt=""
            class="bottom-0 left-1/2 absolute w-full h-full object-contain aspect-video -translate-x-1/2 -translate-y-23"
        />
        <slot />
    </div>
</template>
