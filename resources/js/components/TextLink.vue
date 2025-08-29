<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    class: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <Link
        :href="href"
        class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current"
    >
        <slot />
    </Link>
</template>
