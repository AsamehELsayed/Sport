<template>
  <div class="flex items-center space-x-2">
    <input
      :id="id"
      :name="name"
      :type="type"
      :value="value"
      :checked="checked"
      :disabled="disabled"
      :required="required"
      :tabindex="tabindex"
      class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary focus:ring-offset-2 disabled:opacity-50"
      @change="$emit('update:modelValue', $event.target.checked)"
    />
  </div>
</template>

<script setup>
defineProps({
  id: String,
  name: String,
  type: {
    type: String,
    default: 'checkbox'
  },
  value: [String, Number, Boolean],
  checked: Boolean,
  disabled: Boolean,
  required: Boolean,
  tabindex: [String, Number],
  modelValue: Boolean
})

defineEmits(['update:modelValue'])
</script>
