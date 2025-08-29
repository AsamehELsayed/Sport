<script setup>
defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <div>
        <h2 class="text-lg font-medium">{{ title }}</h2>
        <p class="mt-1 text-sm text-muted-foreground">{{ description }}</p>
    </div>
</template>
