<script setup>
import { cn } from '@/lib/utils';

const props = defineProps({
    class: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <aside :class="cn('w-64 border-r bg-background', props.class)">
        <div class="p-4">
            <h1 class="text-xl font-bold">SportApp</h1>
        </div>
        <nav class="p-4">
            <!-- Navigation items can be added here -->
        </nav>
    </aside>
</template>
