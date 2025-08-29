<script setup>
import { cn } from '@/lib/utils';

const props = defineProps({
    variant: {
        type: String,
        default: 'default',
    },
    class: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <main :class="cn('flex-1', props.class)">
        <slot />
    </main>
</template>
