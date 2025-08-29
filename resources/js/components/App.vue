<script setup>
import { useAppearance } from '@/composables/useAppearance';
import DynamicColors from '@/components/DynamicColors.vue';

// Apply appearance settings globally
const { applyAppearance } = useAppearance();
</script>

<template>
    <div id="app">
        <DynamicColors />
        <slot />
    </div>
</template>
