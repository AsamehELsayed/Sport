<script setup lang="ts">
import { ref, onMounted } from "vue";
import { CheckCircle, X } from "lucide-vue-next";
import Button from "@/components/ui/Button.vue";

interface Props {
  message: string;
  type?: 'success' | 'error' | 'info';
  duration?: number;
  show: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  type: 'success',
  duration: 3000
});

const emit = defineEmits<{
  close: [];
}>();

const isVisible = ref(false);

onMounted(() => {
  if (props.show) {
    showNotification();
  }
});

const showNotification = () => {
  isVisible.value = true;

  if (props.duration > 0) {
    setTimeout(() => {
      hideNotification();
    }, props.duration);
  }
};

const hideNotification = () => {
  isVisible.value = false;
  emit('close');
};

const getTypeClasses = () => {
  switch (props.type) {
    case 'success':
      return 'bg-green-50 border-green-200 text-green-800';
    case 'error':
      return 'bg-red-50 border-red-200 text-red-800';
    case 'info':
      return 'bg-blue-50 border-blue-200 text-blue-800';
    default:
      return 'bg-green-50 border-green-200 text-green-800';
  }
};

const getIcon = () => {
  switch (props.type) {
    case 'success':
      return CheckCircle;
    case 'error':
      return X;
    case 'info':
      return CheckCircle;
    default:
      return CheckCircle;
  }
};
</script>

<template>
  <Transition
    enter-active-class="transition ease-out duration-300"
    enter-from-class="transform translate-y-2 opacity-0"
    enter-to-class="transform translate-y-0 opacity-100"
    leave-active-class="transition ease-in duration-200"
    leave-from-class="transform translate-y-0 opacity-100"
    leave-to-class="transform translate-y-2 opacity-0"
  >
    <div
      v-if="isVisible"
      class="fixed top-4 right-4 z-50 max-w-sm w-full"
    >
      <div
        class="rounded-lg border p-4 shadow-lg backdrop-blur-sm"
        :class="getTypeClasses()"
      >
        <div class="flex items-start gap-3">
          <component
            :is="getIcon()"
            class="w-5 h-5 mt-0.5 flex-shrink-0"
            :class="type === 'success' ? 'text-green-600' : type === 'error' ? 'text-red-600' : 'text-blue-600'"
          />
          <div class="flex-1">
            <p class="text-sm font-medium">
              {{ message }}
            </p>
          </div>
          <Button
            variant="ghost"
            size="sm"
            class="flex-shrink-0 -mt-1 -mr-2"
            @click="hideNotification"
          >
            <X class="w-4 h-4" />
          </Button>
        </div>
      </div>
    </div>
  </Transition>
</template>
