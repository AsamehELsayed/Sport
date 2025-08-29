<template>
  <div class="space-y-4">
    <!-- Main Image -->
    <div class="aspect-square rounded-lg overflow-hidden bg-card border shadow-sm">
      <img
        :src="currentImage"
        :alt="productName"
        class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
        @error="$emit('image-error', $event)"
      />
    </div>

    <!-- Thumbnail Images -->
    <div v-if="availableImages.length > 1" class="grid grid-cols-4 gap-2">
      <div
        v-for="(img, i) in availableImages"
        :key="i"
        class="aspect-square rounded-lg overflow-hidden bg-card cursor-pointer border-2 transition-all duration-200"
        :class="currentImageIndex === i ? 'border-primary' : 'border-transparent hover:border-muted-foreground/20'"
        @click="$emit('select-image', i)"
      >
        <img
          :src="img"
          :alt="`${productName} view ${i + 1}`"
          class="w-full h-full object-cover"
          @error="$emit('image-error', $event)"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  currentImage: {
    type: String,
    required: true
  },
  availableImages: {
    type: Array,
    default: () => []
  },
  currentImageIndex: {
    type: Number,
    default: 0
  },
  productName: {
    type: String,
    required: true
  }
})

defineEmits(['select-image', 'image-error'])
</script>
