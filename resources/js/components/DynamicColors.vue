<script setup lang="ts">
import { onMounted, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();

const applyColors = () => {
    const appearance = page.props.appearance;
    if (!appearance) return;

    const root = document.documentElement;

    // Apply primary color
    if (appearance.primary_color) {
        root.style.setProperty('--primary-color', appearance.primary_color);
    }

    // Apply secondary color
    if (appearance.secondary_color) {
        root.style.setProperty('--secondary-color', appearance.secondary_color);
    }

    // Apply accent color
    if (appearance.accent_color) {
        root.style.setProperty('--accent-color', appearance.accent_color);
    }
};

onMounted(() => {
    applyColors();
});

// Watch for changes in appearance props
watch(() => page.props.appearance, () => {
    applyColors();
}, { deep: true });
</script>

<template>
    <div style="display: none;"></div>
</template>
