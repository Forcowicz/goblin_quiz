<script setup lang="ts">
const props = withDefaults(defineProps<{
    content?: string;
    animated?: boolean;
}>(), {
    animated: true
});
</script>

<template>
    <div class="p-4 pixel-box bg-platinum text-onyx" :class="{'floating': props.animated}">
        <slot/>
    </div>
</template>

<style scoped>

</style>
