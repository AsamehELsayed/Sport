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
  User, 
  Mail, 
  Lock, 
  Phone, 
  Calendar,
  Eye, 
  EyeOff, 
  LoaderCircle,
  Zap
} from "lucide-vue-next"

defineOptions({
  layout: GuestLayout,
})

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  phone: '',
  date_of_birth: '',
  marketing_emails: true,
  order_updates: true
})

const errors = ref({})
const isLoading = ref(false)
const showPassword = ref(false)
const showConfirmPassword = ref(false)

function handleSubmit() {
  isLoading.value = true
  errors.value = {}

  router.post('/register', form.value, {
    onSuccess: () => {
      isLoading.value = false
    },
    onError: (validationErrors) => {
      errors.value = validationErrors
      isLoading.value = false
    }
  })
}

function togglePassword() {
  showPassword.value = !showPassword.value
}

function toggleConfirmPassword() {
  showConfirmPassword.value = !showConfirmPassword.value
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
        <h2 class="text-3xl font-bold text-foreground">Create your account</h2>
        <p class="mt-2 text-muted-foreground">
          Join thousands of sports enthusiasts
        </p>
      </div>

      <!-- Registration Form -->
      <Card class="border-0 shadow-lg">
        <CardHeader class="pb-4">
          <CardTitle class="text-xl">Sign up for free</CardTitle>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Name -->
            <div>
              <label for="name" class="block text-sm font-medium text-foreground mb-2">
                Full name
              </label>
              <div class="relative">
                <User class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                <Input
                  id="name"
                  v-model="form.name"
                  type="text"
                  placeholder="Enter your full name"
                  class="pl-10"
                  :class="{ 'border-red-500': errors.name }"
                  required
                />
              </div>
              <p v-if="errors.name" class="text-sm text-red-600 mt-1">{{ errors.name }}</p>
            </div>

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

            <!-- Phone -->
            <div>
              <label for="phone" class="block text-sm font-medium text-foreground mb-2">
                Phone number (optional)
              </label>
              <div class="relative">
                <Phone class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                <Input
                  id="phone"
                  v-model="form.phone"
                  type="tel"
                  placeholder="Enter your phone number"
                  class="pl-10"
                  :class="{ 'border-red-500': errors.phone }"
                />
              </div>
              <p v-if="errors.phone" class="text-sm text-red-600 mt-1">{{ errors.phone }}</p>
            </div>

            <!-- Date of Birth -->
            <div>
              <label for="date_of_birth" class="block text-sm font-medium text-foreground mb-2">
                Date of birth (optional)
              </label>
              <div class="relative">
                <Calendar class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                <Input
                  id="date_of_birth"
                  v-model="form.date_of_birth"
                  type="date"
                  class="pl-10"
                  :class="{ 'border-red-500': errors.date_of_birth }"
                />
              </div>
              <p v-if="errors.date_of_birth" class="text-sm text-red-600 mt-1">{{ errors.date_of_birth }}</p>
            </div>

            <!-- Password -->
            <div>
              <label for="password" class="block text-sm font-medium text-foreground mb-2">
                Password
              </label>
              <div class="relative">
                <Lock class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                <Input
                  id="password"
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  placeholder="Create a password"
                  class="pl-10 pr-10"
                  :class="{ 'border-red-500': errors.password }"
                  required
                />
                <button
                  type="button"
                  @click="togglePassword"
                  class="absolute right-3 top-1/2 transform -translate-y-1/2 text-muted-foreground hover:text-foreground"
                >
                  <Eye v-if="!showPassword" class="h-4 w-4" />
                  <EyeOff v-else class="h-4 w-4" />
                </button>
              </div>
              <p v-if="errors.password" class="text-sm text-red-600 mt-1">{{ errors.password }}</p>
            </div>

            <!-- Confirm Password -->
            <div>
              <label for="password_confirmation" class="block text-sm font-medium text-foreground mb-2">
                Confirm password
              </label>
              <div class="relative">
                <Lock class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                <Input
                  id="password_confirmation"
                  v-model="form.password_confirmation"
                  :type="showConfirmPassword ? 'text' : 'password'"
                  placeholder="Confirm your password"
                  class="pl-10 pr-10"
                  :class="{ 'border-red-500': errors.password_confirmation }"
                  required
                />
                <button
                  type="button"
                  @click="toggleConfirmPassword"
                  class="absolute right-3 top-1/2 transform -translate-y-1/2 text-muted-foreground hover:text-foreground"
                >
                  <Eye v-if="!showConfirmPassword" class="h-4 w-4" />
                  <EyeOff v-else class="h-4 w-4" />
                </button>
              </div>
              <p v-if="errors.password_confirmation" class="text-sm text-red-600 mt-1">{{ errors.password_confirmation }}</p>
            </div>

            <!-- Preferences -->
            <div class="space-y-3">
              <label class="flex items-center">
                <input
                  v-model="form.marketing_emails"
                  type="checkbox"
                  class="rounded border-gray-300 text-primary focus:ring-primary"
                />
                <span class="ml-2 text-sm text-muted-foreground">
                  Receive marketing emails about new products and offers
                </span>
              </label>
              <label class="flex items-center">
                <input
                  v-model="form.order_updates"
                  type="checkbox"
                  class="rounded border-gray-300 text-primary focus:ring-primary"
                />
                <span class="ml-2 text-sm text-muted-foreground">
                  Receive order updates and shipping notifications
                </span>
              </label>
            </div>

            <!-- Submit button -->
            <Button
              type="submit"
              class="w-full"
              size="lg"
              :disabled="isLoading"
            >
              <LoaderCircle v-if="isLoading" class="w-4 h-4 mr-2 animate-spin" />
              Create account
            </Button>
          </form>

          <!-- Terms -->
          <div class="text-center mt-4">
            <p class="text-xs text-muted-foreground">
              By creating an account, you agree to our
              <a href="#" class="text-primary hover:underline">Terms of Service</a>
              and
              <a href="#" class="text-primary hover:underline">Privacy Policy</a>
            </p>
          </div>

          <!-- Divider -->
          <div class="relative my-6">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-border"></div>
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="px-2 bg-background text-muted-foreground">Or continue with</span>
            </div>
          </div>

          <!-- Social registration buttons -->
          <div class="space-y-3">
            <Button variant="outline" class="w-full" disabled>
              <svg class="w-4 h-4 mr-2" viewBox="0 0 24 24">
                <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
              </svg>
              Continue with Google
            </Button>
            <Button variant="outline" class="w-full" disabled>
              <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
              </svg>
              Continue with Facebook
            </Button>
          </div>

          <!-- Sign in link -->
          <div class="text-center mt-6">
            <p class="text-sm text-muted-foreground">
              Already have an account?
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