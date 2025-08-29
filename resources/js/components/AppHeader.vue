<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    breadcrumbs: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <header class="border-b bg-background px-4 py-3">
        <div class="flex items-center justify-between">
            <nav class="flex items-center space-x-2 text-sm">
                <template v-for="(breadcrumb, index) in breadcrumbs" :key="index">
                    <Link
                        v-if="index < breadcrumbs.length - 1"
                        :href="breadcrumb.href"
                        class="text-muted-foreground hover:text-foreground"
                    >
                        {{ breadcrumb.title }}
                    </Link>
                    <span v-else class="text-foreground font-medium">
                        {{ breadcrumb.title }}
                    </span>
                    <span v-if="index < breadcrumbs.length - 1" class="text-muted-foreground">/</span>
                </template>
            </nav>
            <div class="flex items-center space-x-4">
                <!-- User menu or other header items can go here -->
            </div>
        </div>
    </header>
</template>
