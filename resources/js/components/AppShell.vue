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
    <div :class="cn('flex min-h-screen', props.class)">
        <slot />
    </div>
</template>
