<script setup>
defineProps({
    message: {
        type: String,
        default: '',
    },
    class: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <div v-show="message" class="text-sm text-red-600">
        {{ message }}
    </div>
</template>
