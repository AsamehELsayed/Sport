<template>
  <div class="space-y-4">
    <!-- Colors (Always shown first) -->
    <div v-if="availableColors.length > 0">
      <h3 class="font-semibold mb-3">Color</h3>
      <div class="flex flex-wrap gap-2">
        <Button
          v-for="color in availableColors"
          :key="color"
          :variant="selectedColor === color ? 'default' : 'outline'"
          size="sm"
          class="min-w-16"
          @click="$emit('select-color', color)"
        >
          <span class="w-4 h-4 rounded-full mr-2" :style="{ backgroundColor: color }"></span>
          {{ color }}
        </Button>
      </div>
      <p v-if="!selectedColor" class="text-sm text-muted-foreground mt-2">
        Please select a color first
      </p>
    </div>

    <!-- Sizes (Only shown when color is selected) -->
    <div v-if="selectedColor && availableSizesForColor.length > 0">
      <h3 class="font-semibold mb-3">Size</h3>
      <div class="flex flex-wrap gap-2">
        <Button
          v-for="size in availableSizesForColor"
          :key="size"
          :variant="selectedSize === size ? 'default' : 'outline'"
          size="sm"
          class="min-w-16"
          @click="$emit('select-size', size)"
        >
          {{ size }}
        </Button>
      </div>
      <p v-if="!selectedSize" class="text-sm text-muted-foreground mt-2">
        Please select a size for {{ selectedColor }}
      </p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import Button from "@/components/ui/button/Button.vue"

const props = defineProps({
  availableSizes: {
    type: Array,
    default: () => []
  },
  availableColors: {
    type: Array,
    default: () => []
  },
  availableVariants: {
    type: Array,
    default: () => []
  },
  selectedSize: {
    type: String,
    default: ""
  },
  selectedColor: {
    type: String,
    default: ""
  },
  selectedVariant: {
    type: Object,
    default: null
  }
})

// Computed property to get sizes available for the selected color
const availableSizesForColor = computed(() => {
  if (!props.selectedColor) return []

  return props.availableVariants
    .filter(variant => variant.color === props.selectedColor && variant.stock > 0)
    .map(variant => variant.size)
    .filter((size, index, arr) => arr.indexOf(size) === index) // Remove duplicates
})

defineEmits(['select-size', 'select-color', 'select-variant'])
</script>
