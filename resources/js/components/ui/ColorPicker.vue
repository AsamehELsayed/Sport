<template>
  <div class="space-y-4">
    <!-- Modern Color Palette Grid -->
    <div class="grid grid-cols-8 gap-3">
      <div
        v-for="color in predefinedColors"
        :key="color.name"
        @click="selectColor(color)"
        class="group relative aspect-square rounded-2xl border-3 transition-all duration-300 hover:scale-110 hover:shadow-xl cursor-pointer overflow-hidden"
        :class="[
          selectedColor?.name === color.name
            ? 'border-primary shadow-2xl ring-4 ring-primary/20 scale-105'
            : 'border-gray-200 hover:border-gray-300 hover:shadow-lg'
        ]"
        :style="{ backgroundColor: color.value }"
        :title="color.name"
      >
        <!-- Selection Indicator -->
        <div
          v-if="selectedColor?.name === color.name"
          class="absolute inset-0 flex items-center justify-center"
        >
          <div class="w-6 h-6 bg-white rounded-full shadow-lg flex items-center justify-center">
            <svg class="w-4 h-4 text-primary" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
          </div>
        </div>

        <!-- Hover Effect -->
        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors duration-200"></div>

        <!-- Color Name Tooltip -->
        <div class="absolute -bottom-8 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
          {{ color.name }}
        </div>
      </div>
    </div>

    <!-- Custom Color Section -->
    <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-2xl p-4 border border-gray-200">
      <div class="flex items-center gap-4">
        <div class="relative">
          <input
            type="color"
            :value="customColor"
            @input="updateCustomColor"
            class="w-12 h-12 rounded-xl border-3 border-gray-300 cursor-pointer hover:border-primary transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-primary/20 focus:border-primary shadow-lg"
            title="Choose custom color"
          />
          <div class="absolute inset-0 rounded-xl bg-gradient-to-br from-white/20 to-transparent pointer-events-none"></div>
        </div>
        <div class="flex-1">
          <label class="block text-sm font-semibold text-gray-700 mb-1">Custom Color</label>
          <p class="text-xs text-gray-500">Click to choose your own color</p>
        </div>
        <Button
          v-if="customColor && customColor !== '#000000'"
          @click="selectCustomColor"
          variant="outline"
          size="sm"
          class="bg-white hover:bg-gray-50 border-gray-300"
        >
          Use Custom
        </Button>
      </div>
    </div>

    <!-- Selected Color Display -->
    <div v-if="selectedColor" class="bg-gradient-to-r from-primary/5 to-primary/10 rounded-2xl p-4 border-2 border-primary/20">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
          <div
            class="w-12 h-12 rounded-xl border-3 border-primary/30 shadow-lg"
            :style="{ backgroundColor: selectedColor.value }"
          ></div>
          <div>
            <h4 class="font-semibold text-gray-900">{{ selectedColor.name }}</h4>
            <p class="text-sm text-gray-600">{{ selectedColor.value.toUpperCase() }}</p>
          </div>
        </div>
        <Button
          @click="clearSelection"
          variant="ghost"
          size="sm"
          class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full w-8 h-8 p-0"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </Button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import Button from '@/components/ui/Button.vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['update:modelValue'])

const selectedColor = ref(null)
const customColor = ref('#000000')

// Modern color palette with better colors
const predefinedColors = [
  { name: 'Classic Black', value: '#000000' },
  { name: 'Pure White', value: '#FFFFFF' },
  { name: 'Navy Blue', value: '#1e3a8a' },
  { name: 'Royal Blue', value: '#3b82f6' },
  { name: 'Sky Blue', value: '#0ea5e9' },
  { name: 'Forest Green', value: '#166534' },
  { name: 'Emerald', value: '#10b981' },
  { name: 'Lime Green', value: '#84cc16' },
  { name: 'Deep Red', value: '#991b1b' },
  { name: 'Crimson', value: '#dc2626' },
  { name: 'Coral', value: '#f97316' },
  { name: 'Amber', value: '#f59e0b' },
  { name: 'Golden Yellow', value: '#eab308' },
  { name: 'Purple', value: '#7c3aed' },
  { name: 'Pink', value: '#ec4899' },
  { name: 'Rose', value: '#f43f5e' },
  { name: 'Slate Gray', value: '#475569' },
  { name: 'Cool Gray', value: '#6b7280' },
  { name: 'Warm Gray', value: '#78716c' },
  { name: 'Brown', value: '#92400e' },
  { name: 'Tan', value: '#d97706' },
  { name: 'Beige', value: '#f5f5dc' },
  { name: 'Cream', value: '#fefce8' },
  { name: 'Mint', value: '#a7f3d0' },
  { name: 'Lavender', value: '#ddd6fe' }
]

function selectColor(color) {
  selectedColor.value = color
  emit('update:modelValue', color.value)
}

function updateCustomColor(event) {
  customColor.value = event.target.value
}

function selectCustomColor() {
  const customColorObj = {
    name: 'Custom Color',
    value: customColor.value
  }
  selectedColor.value = customColorObj
  emit('update:modelValue', customColor.value)
}

function clearSelection() {
  selectedColor.value = null
  emit('update:modelValue', '')
}

// Watch for external changes
watch(() => props.modelValue, (newValue) => {
  if (!newValue) {
    selectedColor.value = null
    return
  }

  // Find if it matches a predefined color
  const foundColor = predefinedColors.find(color => color.value.toLowerCase() === newValue.toLowerCase())
  if (foundColor) {
    selectedColor.value = foundColor
  } else {
    // It's a custom color
    selectedColor.value = {
      name: 'Custom Color',
      value: newValue
    }
    customColor.value = newValue
  }
}, { immediate: true })
</script>

<style scoped>
.border-3 {
  border-width: 3px;
}
</style>
