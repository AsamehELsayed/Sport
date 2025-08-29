<script setup lang="ts">
import { useAppearance } from '@/composables/useAppearance';
import Nav from "@/components/main/Nav.vue";
import Footer from "@/components/main/Footer.vue";

// Apply appearance settings
const { applyAppearance } = useAppearance();
</script>

<template>
    <Nav />
    <slot />
    <Footer />
</template>
