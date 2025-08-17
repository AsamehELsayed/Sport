<script setup>
import { ref } from "vue"
import { router } from "@inertiajs/vue3"
import GuestLayout from '@/layouts/GuestLayout.vue'

// UI components
import Button from "@/components/ui/button/Button.vue"
import Input from "@/components/ui/Input.vue"
import Card from "@/components/ui/card/Card.vue"
import CardContent from "@/components/ui/card/CardContent.vue"
import CardHeader from "@/components/ui/card/CardHeader.vue"
import CardTitle from "@/components/ui/card/CardTitle.vue"

// Icons
import {
  Mail,
  LoaderCircle,
  Zap,
  ArrowLeft
} from "lucide-vue-next"

defineOptions({
  layout: GuestLayout,
})

const form = ref({
  email: ''
})

const errors = ref({})
const isLoading = ref(false)
const success = ref(false)

function handleSubmit() {
  isLoading.value = true
  errors.value = {}

  router.post('/forgot-password', form.value, {
    onSuccess: () => {
      isLoading.value = false
      success.value = true
    },
    onError: (validationErrors) => {
      errors.value = validationErrors
      isLoading.value = false
    }
  })
}
</script>

<template>
  <div class="min-h-screen bg-background flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <!-- Header -->
      <div class="text-center">
        <div class="flex justify-center mb-6">
          <div class="w-12 h-12 bg-gradient-to-r from-red-500 to-yellow-500 rounded-lg flex items-center justify-center">
            <Zap class="w-8 h-8 text-white" />
          </div>
        </div>
        <h2 class="text-3xl font-bold text-foreground">Reset your password</h2>
        <p class="mt-2 text-muted-foreground">
          Enter your email address and we'll send you a link to reset your password
        </p>
      </div>

      <!-- Success Message -->
      <div v-if="success" class="text-center">
        <div class="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
          <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
          </svg>
        </div>
        <h3 class="text-lg font-semibold mb-2">Check your email</h3>
        <p class="text-muted-foreground mb-6">
          We've sent a password reset link to <strong>{{ form.email }}</strong>
        </p>
        <Button @click="$router.visit('/login')" variant="outline">
          <ArrowLeft class="w-4 h-4 mr-2" />
          Back to login
        </Button>
      </div>

      <!-- Reset Form -->
      <Card v-else class="border-0 shadow-lg">
        <CardHeader class="pb-4">
          <CardTitle class="text-xl">Forgot your password?</CardTitle>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Email -->
            <div>
              <label for="email" class="block text-sm font-medium text-foreground mb-2">
                Email address
              </label>
              <div class="relative">
                <Mail class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                <Input
                  id="email"
                  v-model="form.email"
                  type="email"
                  placeholder="Enter your email"
                  class="pl-10"
                  :class="{ 'border-red-500': errors.email }"
                  required
                />
              </div>
              <p v-if="errors.email" class="text-sm text-red-600 mt-1">{{ errors.email }}</p>
            </div>

            <!-- Submit button -->
            <Button
              type="submit"
              class="w-full"
              size="lg"
              :disabled="isLoading"
            >
              <LoaderCircle v-if="isLoading" class="w-4 h-4 mr-2 animate-spin" />
              Send reset link
            </Button>
          </form>

          <!-- Back to login -->
          <div class="text-center mt-6">
            <p class="text-sm text-muted-foreground">
              Remember your password?
              <a href="/login" class="text-primary hover:underline font-medium">
                Sign in
              </a>
            </p>
          </div>
        </CardContent>
      </Card>
    </div>
  </div>
</template>
