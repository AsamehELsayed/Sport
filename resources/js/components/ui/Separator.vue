<script setup>
import { computed } from "vue"

const props = defineProps({
  orientation: {
    type: String,
    default: "horizontal", // horizontal | vertical
  },
  decorative: {
    type: Boolean,
    default: true,
  },
  class: {
    type: String,
    default: "",
  },
})

const separatorClasses = computed(() => {
  return [
    "shrink-0 bg-border",
    props.orientation === "horizontal" ? "h-[1px] w-full" : "h-full w-[1px]",
    props.class,
  ]
})
</script>

<template>
  <div
    role="separator"
    :aria-orientation="orientation"
    :aria-hidden="decorative ? 'true' : undefined"
    :class="separatorClasses"
  />
</template>
