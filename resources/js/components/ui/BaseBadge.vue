<script setup lang="ts">
import { computed } from "vue"
import { twMerge } from "tailwind-merge" // npm install tailwind-merge

interface Props {
  variant?: "default" | "secondary" | "destructive" | "outline"
}

const props = withDefaults(defineProps<Props>(), {
  variant: "default"
})

const baseClasses =
  "inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"

const variantClasses: Record<string, string> = {
  default: "border-transparent bg-primary text-primary-foreground hover:bg-primary/80",
  secondary: "border-transparent bg-secondary text-secondary-foreground hover:bg-secondary/80",
  destructive: "border-transparent bg-destructive text-destructive-foreground hover:bg-destructive/80",
  outline: "text-foreground"
}

const classes = computed(() =>
  twMerge(baseClasses, variantClasses[props.variant])
)
</script>

<template>
  <div :class="classes">
    <slot />
  </div>
</template>
