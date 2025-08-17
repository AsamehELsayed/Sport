<template>
  <div class="space-y-4">
    <!-- Image Upload Area -->
    <div class="border-2 border-dashed border-border rounded-lg p-6 text-center hover:border-primary/50 transition-colors">
      <input
        ref="fileInput"
        type="file"
        :accept="accept"
        :multiple="multiple"
        @change="handleFileChange"
        class="hidden"
      />
      
      <div v-if="!hasImages" @click="triggerFileInput" class="cursor-pointer">
        <div class="mx-auto h-12 w-12 text-muted-foreground">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
          </svg>
        </div>
        <div class="mt-4 flex text-sm text-muted-foreground">
          <span class="relative cursor-pointer rounded-md font-medium text-primary hover:text-primary/80">
            {{ multiple ? 'Upload images' : 'Upload image' }}
          </span>
          <p class="pl-1">or drag and drop</p>
        </div>
        <p class="text-xs text-muted-foreground">
          {{ acceptText }}
        </p>
      </div>
    </div>

    <!-- Preview Images -->
    <div v-if="hasImages" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
      <div 
        v-for="(image, index) in displayImages" 
        :key="index"
        class="relative group aspect-square rounded-lg overflow-hidden border border-border"
      >
        <img 
          :src="getImageUrl(image)" 
          :alt="`Image ${index + 1}`"
          class="w-full h-full object-cover"
        />
        
        <!-- Remove Button -->
        <button
          @click="removeImage(index)"
          class="absolute top-2 right-2 bg-destructive text-destructive-foreground rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity hover:bg-destructive/90"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Error Message -->
    <p v-if="error" class="text-sm text-destructive">{{ error }}</p>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  modelValue: {
    type: [Array, String],
    default: () => []
  },
  multiple: {
    type: Boolean,
    default: false
  },
  accept: {
    type: String,
    default: 'image/*'
  },
  maxFiles: {
    type: Number,
    default: 5
  },
  maxSize: {
    type: Number,
    default: 2048 // 2MB
  }
})

const emit = defineEmits(['update:modelValue', 'error'])

const fileInput = ref(null)
const error = ref('')

const acceptText = computed(() => {
  if (props.accept === 'image/*') {
    return 'PNG, JPG, GIF, WEBP up to 2MB'
  }
  return props.accept
})

const displayImages = computed(() => {
  if (Array.isArray(props.modelValue)) {
    return props.modelValue
  }
  return props.modelValue ? [props.modelValue] : []
})

const hasImages = computed(() => {
  return displayImages.value.length > 0
})

const getImageUrl = (image) => {
  if (typeof image === 'string') {
    if (image.startsWith('http')) {
      return image
    }
    return `/storage/${image}`
  }
  return URL.createObjectURL(image)
}

const triggerFileInput = () => {
  fileInput.value?.click()
}

const handleFileChange = (event) => {
  const files = Array.from(event.target.files || [])
  error.value = ''

  // Validate files
  for (const file of files) {
    if (!file.type.startsWith('image/')) {
      error.value = 'Please select only image files'
      emit('error', error.value)
      return
    }

    if (file.size > props.maxSize * 1024) {
      error.value = `File ${file.name} is too large. Maximum size is ${props.maxSize}KB`
      emit('error', error.value)
      return
    }
  }

  if (files.length > props.maxFiles) {
    error.value = `Maximum ${props.maxFiles} files allowed`
    emit('error', error.value)
    return
  }

  // Update model value
  if (props.multiple) {
    const newImages = [...displayImages.value, ...files]
    emit('update:modelValue', newImages)
  } else {
    emit('update:modelValue', files[0])
  }

  // Clear input
  event.target.value = ''
}

const removeImage = (index) => {
  if (props.multiple) {
    const newImages = displayImages.value.filter((_, i) => i !== index)
    emit('update:modelValue', newImages)
  } else {
    emit('update:modelValue', null)
  }
}

// Watch for external changes to modelValue
watch(() => props.modelValue, () => {
  error.value = ''
}, { deep: true })
</script>
