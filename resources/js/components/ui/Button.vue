<script setup lang="ts">
import { computed } from "vue"
import { twMerge } from "tailwind-merge" // npm install tailwind-merge

interface Props {
  variant?: "default" | "destructive" | "outline" | "secondary" | "ghost" | "link"
  size?: "default" | "sm" | "lg" | "icon"
  fullWidth?: boolean
  loading?: boolean
  type?: "button" | "submit" | "reset"
  disabled?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  variant: "default",
  size: "default",
  fullWidth: false,
  loading: false,
  type: "button",
  disabled: false
})

const baseClasses =
  "inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50"

const variantClasses: Record<string, string> = {
  default: "bg-primary text-primary-foreground hover:bg-primary/90",
  destructive: "bg-destructive text-destructive-foreground hover:bg-destructive/90",
  outline: "border border-input bg-background hover:bg-accent hover:text-accent-foreground",
  secondary: "bg-secondary text-secondary-foreground hover:bg-secondary/80",
  ghost: "hover:bg-accent hover:text-accent-foreground",
  link: "text-primary underline-offset-4 hover:underline"
}

const sizeClasses: Record<string, string> = {
  default: "h-10 px-4 py-2 [&_svg]:h-4 [&_svg]:w-4",
  sm: "h-9 rounded-md px-3 [&_svg]:h-3.5 [&_svg]:w-3.5",
  lg: "h-11 rounded-md px-8 [&_svg]:h-5 [&_svg]:w-5",
  icon: "h-10 w-10 [&_svg]:h-4 [&_svg]:w-4"
}

const classes = computed(() =>
  twMerge(
    baseClasses,
    variantClasses[props.variant],
    sizeClasses[props.size],
    props.fullWidth ? "w-full" : ""
  )
)
</script>

<template>
  <button
    :type="type"
    :disabled="disabled || loading"
    :class="classes"
  >
    <span
      v-if="loading"
      class="animate-spin rounded-full border-2 border-t-transparent border-white h-4 w-4"
    />
    <slot />
  </button>
</template>
