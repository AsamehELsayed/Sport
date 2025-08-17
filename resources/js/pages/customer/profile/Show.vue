<script setup>
import { ref, computed } from "vue"
import { router } from "@inertiajs/vue3"
import GuestLayout from '@/layouts/GuestLayout.vue'

// UI components
import Button from "@/components/ui/button/Button.vue"
import Input from "@/components/ui/Input.vue"
import Card from "@/components/ui/card/Card.vue"
import CardContent from "@/components/ui/card/CardContent.vue"
import CardHeader from "@/components/ui/card/CardHeader.vue"
import CardTitle from "@/components/ui/card/CardTitle.vue"
import Tabs from "@/components/ui/tabs/Tabs.vue"
import TabsList from "@/components/ui/tabs/TabsList.vue"
import TabsTrigger from "@/components/ui/tabs/TabsTrigger.vue"
import TabsContent from "@/components/ui/tabs/TabsContent.vue"
import Badge from "@/components/ui/badge/Badge.vue"

// Icons
import {
  User,
  Mail,
  Phone,
  Calendar,
  MapPin,
  Camera,
  Trash2,
  Save,
  Shield,
  Settings,
  Heart,
  ShoppingBag,
  LogOut,
  Eye,
  EyeOff,
  LoaderCircle
} from "lucide-vue-next"

defineOptions({
  layout: GuestLayout,
})

const props = defineProps({
  user: {
    type: Object,
    required: true
  }
})

// Form data
const profileForm = ref({
  name: props.user.name || '',
  email: props.user.email || '',
  phone: props.user.phone || '',
  date_of_birth: props.user.date_of_birth || '',
  address_line_1: props.user.address_line_1 || '',
  address_line_2: props.user.address_line_2 || '',
  city: props.user.city || '',
  state: props.user.state || '',
  postal_code: props.user.postal_code || '',
  country: props.user.country || 'US',
  gender: props.user.gender || '',
  bio: props.user.bio || '',
  marketing_emails: props.user.marketing_emails || false,
  order_updates: props.user.order_updates || false,
  preferred_sports: props.user.preferred_sports || ''
})

const passwordForm = ref({
  current_password: '',
  password: '',
  password_confirmation: ''
})

const errors = ref({})
const isLoading = ref(false)
const showCurrentPassword = ref(false)
const showNewPassword = ref(false)
const showConfirmPassword = ref(false)
const activeTab = ref('profile')

// Computed
const profilePhotoUrl = computed(() => {
  if (props.user.profile_photo) {
    return `/storage/${props.user.profile_photo}`
  }
  return null
})

const initials = computed(() => {
  return props.user.name
    .split(' ')
    .map(word => word.charAt(0))
    .join('')
    .toUpperCase()
    .slice(0, 2)
})

// Methods
function updateProfile() {
  isLoading.value = true
  errors.value = {}

  router.put('/profile', profileForm.value, {
    onSuccess: () => {
      isLoading.value = false
    },
    onError: (validationErrors) => {
      errors.value = validationErrors
      isLoading.value = false
    }
  })
}

function updatePassword() {
  isLoading.value = true
  errors.value = {}

  router.put('/profile/password', passwordForm.value, {
    onSuccess: () => {
      isLoading.value = false
      passwordForm.value = {
        current_password: '',
        password: '',
        password_confirmation: ''
      }
    },
    onError: (validationErrors) => {
      errors.value = validationErrors
      isLoading.value = false
    }
  })
}

function updatePhoto(event) {
  const file = event.target.files[0]
  if (file) {
    const formData = new FormData()
    formData.append('profile_photo', file)

    router.put('/profile/photo', formData, {
      onSuccess: () => {
        // Refresh the page to show new photo
        window.location.reload()
      },
      onError: (validationErrors) => {
        errors.value = validationErrors
      }
    })
  }
}

function deletePhoto() {
  if (confirm('Are you sure you want to remove your profile photo?')) {
    router.delete('/profile/photo')
  }
}

function deleteAccount() {
  const password = prompt('Please enter your password to confirm account deletion:')
  if (password) {
    router.delete('/profile', {
      data: { password }
    })
  }
}

function logout() {
  router.post('/logout')
}

function togglePassword(field) {
  if (field === 'current') {
    showCurrentPassword.value = !showCurrentPassword.value
  } else if (field === 'new') {
    showNewPassword.value = !showNewPassword.value
  } else if (field === 'confirm') {
    showConfirmPassword.value = !showConfirmPassword.value
  }
}
</script>

<template>
  <div class="min-h-screen bg-background">
    <main class="container mx-auto px-4 py-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold">My Profile</h1>
        <p class="text-muted-foreground">Manage your account settings and preferences</p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar -->
        <div class="lg:col-span-1">
          <Card class="border-0 shadow-sm">
            <CardContent class="p-6">
              <!-- Profile Photo -->
              <div class="text-center mb-6">
                <div class="relative inline-block">
                  <div class="w-24 h-24 rounded-full bg-gradient-to-r from-red-500 to-yellow-500 flex items-center justify-center text-white text-2xl font-bold">
                    <img
                      v-if="profilePhotoUrl"
                      :src="profilePhotoUrl"
                      :alt="user.name"
                      class="w-24 h-24 rounded-full object-cover"
                    />
                    <span v-else>{{ initials }}</span>
                  </div>
                  <label
                    for="photo-upload"
                    class="absolute bottom-0 right-0 w-8 h-8 bg-primary text-white rounded-full flex items-center justify-center cursor-pointer hover:bg-primary/90"
                  >
                    <Camera class="w-4 h-4" />
                  </label>
                  <input
                    id="photo-upload"
                    type="file"
                    accept="image/*"
                    class="hidden"
                    @change="updatePhoto"
                  />
                </div>
                <div class="mt-4">
                  <h3 class="font-semibold text-lg">{{ user.name }}</h3>
                  <p class="text-muted-foreground">{{ user.email }}</p>
                  <div class="mt-2">
                    <Badge v-if="user.email_verified_at" variant="secondary" class="text-xs">
                      Verified
                    </Badge>
                    <Badge v-else variant="outline" class="text-xs">
                      Unverified
                    </Badge>
                  </div>
                </div>
                <Button
                  v-if="profilePhotoUrl"
                  variant="outline"
                  size="sm"
                  class="mt-2"
                  @click="deletePhoto"
                >
                  <Trash2 class="w-4 h-4 mr-2" />
                  Remove Photo
                </Button>
              </div>

              <!-- Quick Stats -->
              <div class="space-y-4 border-t pt-6">
                <div class="flex items-center justify-between">
                  <span class="text-sm text-muted-foreground">Member since</span>
                  <span class="text-sm font-medium">
                    {{ new Date(user.created_at).toLocaleDateString() }}
                  </span>
                </div>
                <div class="flex items-center justify-between">
                  <span class="text-sm text-muted-foreground">Last login</span>
                  <span class="text-sm font-medium">
                    {{ user.last_login_at ? new Date(user.last_login_at).toLocaleDateString() : 'Never' }}
                  </span>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-3">
          <Tabs v-model="activeTab" class="w-full">
            <TabsList class="grid w-full grid-cols-5">
              <TabsTrigger value="profile">
                <User class="w-4 h-4 mr-2" />
                Profile
              </TabsTrigger>
              <TabsTrigger value="orders">
                <ShoppingBag class="w-4 h-4 mr-2" />
                Orders
              </TabsTrigger>
              <TabsTrigger value="security">
                <Shield class="w-4 h-4 mr-2" />
                Security
              </TabsTrigger>
              <TabsTrigger value="preferences">
                <Settings class="w-4 h-4 mr-2" />
                Preferences
              </TabsTrigger>
              <TabsTrigger value="account">
                <Heart class="w-4 h-4 mr-2" />
                Account
              </TabsTrigger>
            </TabsList>

            <!-- Profile Tab -->
            <TabsContent value="profile" class="mt-6">
              <Card class="border-0 shadow-sm">
                <CardHeader>
                  <CardTitle>Personal Information</CardTitle>
                </CardHeader>
                <CardContent>
                  <form @submit.prevent="updateProfile" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                      <!-- Name -->
                      <div>
                        <label class="block text-sm font-medium text-foreground mb-2">
                          Full Name
                        </label>
                        <div class="relative">
                          <User class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                          <Input
                            v-model="profileForm.name"
                            type="text"
                            class="pl-10"
                            :class="{ 'border-red-500': errors.name }"
                          />
                        </div>
                        <p v-if="errors.name" class="text-sm text-red-600 mt-1">{{ errors.name }}</p>
                      </div>

                      <!-- Email -->
                      <div>
                        <label class="block text-sm font-medium text-foreground mb-2">
                          Email Address
                        </label>
                        <div class="relative">
                          <Mail class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                          <Input
                            v-model="profileForm.email"
                            type="email"
                            class="pl-10"
                            :class="{ 'border-red-500': errors.email }"
                          />
                        </div>
                        <p v-if="errors.email" class="text-sm text-red-600 mt-1">{{ errors.email }}</p>
                      </div>

                      <!-- Phone -->
                      <div>
                        <label class="block text-sm font-medium text-foreground mb-2">
                          Phone Number
                        </label>
                        <div class="relative">
                          <Phone class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                          <Input
                            v-model="profileForm.phone"
                            type="tel"
                            class="pl-10"
                            :class="{ 'border-red-500': errors.phone }"
                          />
                        </div>
                        <p v-if="errors.phone" class="text-sm text-red-600 mt-1">{{ errors.phone }}</p>
                      </div>

                      <!-- Date of Birth -->
                      <div>
                        <label class="block text-sm font-medium text-foreground mb-2">
                          Date of Birth
                        </label>
                        <div class="relative">
                          <Calendar class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                          <Input
                            v-model="profileForm.date_of_birth"
                            type="date"
                            class="pl-10"
                            :class="{ 'border-red-500': errors.date_of_birth }"
                          />
                        </div>
                        <p v-if="errors.date_of_birth" class="text-sm text-red-600 mt-1">{{ errors.date_of_birth }}</p>
                      </div>

                      <!-- Gender -->
                      <div>
                        <label class="block text-sm font-medium text-foreground mb-2">
                          Gender
                        </label>
                        <select
                          v-model="profileForm.gender"
                          class="w-full px-3 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                        >
                          <option value="">Select gender</option>
                          <option value="male">Male</option>
                          <option value="female">Female</option>
                          <option value="other">Other</option>
                          <option value="prefer_not_to_say">Prefer not to say</option>
                        </select>
                        <p v-if="errors.gender" class="text-sm text-red-600 mt-1">{{ errors.gender }}</p>
                      </div>

                      <!-- Country -->
                      <div>
                        <label class="block text-sm font-medium text-foreground mb-2">
                          Country
                        </label>
                        <select
                          v-model="profileForm.country"
                          class="w-full px-3 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                        >
                          <option value="US">United States</option>
                          <option value="CA">Canada</option>
                          <option value="UK">United Kingdom</option>
                          <option value="AU">Australia</option>
                        </select>
                        <p v-if="errors.country" class="text-sm text-red-600 mt-1">{{ errors.country }}</p>
                      </div>
                    </div>

                    <!-- Address -->
                    <div class="space-y-4">
                      <h4 class="font-medium">Address Information</h4>
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                          <label class="block text-sm font-medium text-foreground mb-2">
                            Address Line 1
                          </label>
                          <div class="relative">
                            <MapPin class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                            <Input
                              v-model="profileForm.address_line_1"
                              type="text"
                              class="pl-10"
                              :class="{ 'border-red-500': errors.address_line_1 }"
                            />
                          </div>
                          <p v-if="errors.address_line_1" class="text-sm text-red-600 mt-1">{{ errors.address_line_1 }}</p>
                        </div>

                        <div class="md:col-span-2">
                          <label class="block text-sm font-medium text-foreground mb-2">
                            Address Line 2 (Optional)
                          </label>
                          <Input
                            v-model="profileForm.address_line_2"
                            type="text"
                            :class="{ 'border-red-500': errors.address_line_2 }"
                          />
                          <p v-if="errors.address_line_2" class="text-sm text-red-600 mt-1">{{ errors.address_line_2 }}</p>
                        </div>

                        <div>
                          <label class="block text-sm font-medium text-foreground mb-2">
                            City
                          </label>
                          <Input
                            v-model="profileForm.city"
                            type="text"
                            :class="{ 'border-red-500': errors.city }"
                          />
                          <p v-if="errors.city" class="text-sm text-red-600 mt-1">{{ errors.city }}</p>
                        </div>

                        <div>
                          <label class="block text-sm font-medium text-foreground mb-2">
                            State/Province
                          </label>
                          <Input
                            v-model="profileForm.state"
                            type="text"
                            :class="{ 'border-red-500': errors.state }"
                          />
                          <p v-if="errors.state" class="text-sm text-red-600 mt-1">{{ errors.state }}</p>
                        </div>

                        <div>
                          <label class="block text-sm font-medium text-foreground mb-2">
                            Postal Code
                          </label>
                          <Input
                            v-model="profileForm.postal_code"
                            type="text"
                            :class="{ 'border-red-500': errors.postal_code }"
                          />
                          <p v-if="errors.postal_code" class="text-sm text-red-600 mt-1">{{ errors.postal_code }}</p>
                        </div>
                      </div>
                    </div>

                    <!-- Bio -->
                    <div>
                      <label class="block text-sm font-medium text-foreground mb-2">
                        Bio
                      </label>
                      <textarea
                        v-model="profileForm.bio"
                        rows="4"
                        class="w-full px-3 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                        placeholder="Tell us about yourself..."
                        :class="{ 'border-red-500': errors.bio }"
                      ></textarea>
                      <p v-if="errors.bio" class="text-sm text-red-600 mt-1">{{ errors.bio }}</p>
                    </div>

                    <Button type="submit" :disabled="isLoading">
                      <LoaderCircle v-if="isLoading" class="w-4 h-4 mr-2 animate-spin" />
                      <Save v-else class="w-4 h-4 mr-2" />
                      Save Changes
                    </Button>
                  </form>
                </CardContent>
              </Card>
            </TabsContent>

            <!-- Orders Tab -->
            <TabsContent value="orders" class="mt-6">
              <Card class="border-0 shadow-sm">
                <CardHeader>
                  <CardTitle>My Orders</CardTitle>
                </CardHeader>
                <CardContent>
                  <div class="text-center py-8">
                    <div class="w-16 h-16 bg-muted rounded-full flex items-center justify-center mx-auto mb-4">
                      <ShoppingBag class="w-8 h-8 text-muted-foreground" />
                    </div>
                    <h3 class="text-lg font-semibold text-foreground mb-2">View Your Orders</h3>
                    <p class="text-muted-foreground mb-4">Track your order history and status</p>
                    <a href="/orders" class="text-primary hover:underline">
                      Go to Orders
                    </a>
                  </div>
                </CardContent>
              </Card>
            </TabsContent>

            <!-- Security Tab -->
            <TabsContent value="security" class="mt-6">
              <Card class="border-0 shadow-sm">
                <CardHeader>
                  <CardTitle>Security Settings</CardTitle>
                </CardHeader>
                <CardContent>
                  <form @submit.prevent="updatePassword" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                      <!-- Current Password -->
                      <div>
                        <label class="block text-sm font-medium text-foreground mb-2">
                          Current Password
                        </label>
                        <div class="relative">
                          <Shield class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                          <Input
                            v-model="passwordForm.current_password"
                            :type="showCurrentPassword ? 'text' : 'password'"
                            class="pl-10 pr-10"
                            :class="{ 'border-red-500': errors.current_password }"
                          />
                          <button
                            type="button"
                            @click="togglePassword('current')"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-muted-foreground hover:text-foreground"
                          >
                            <Eye v-if="!showCurrentPassword" class="h-4 w-4" />
                            <EyeOff v-else class="h-4 w-4" />
                          </button>
                        </div>
                        <p v-if="errors.current_password" class="text-sm text-red-600 mt-1">{{ errors.current_password }}</p>
                      </div>

                      <!-- New Password -->
                      <div>
                        <label class="block text-sm font-medium text-foreground mb-2">
                          New Password
                        </label>
                        <div class="relative">
                          <Shield class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                          <Input
                            v-model="passwordForm.password"
                            :type="showNewPassword ? 'text' : 'password'"
                            class="pl-10 pr-10"
                            :class="{ 'border-red-500': errors.password }"
                          />
                          <button
                            type="button"
                            @click="togglePassword('new')"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-muted-foreground hover:text-foreground"
                          >
                            <Eye v-if="!showNewPassword" class="h-4 w-4" />
                            <EyeOff v-else class="h-4 w-4" />
                          </button>
                        </div>
                        <p v-if="errors.password" class="text-sm text-red-600 mt-1">{{ errors.password }}</p>
                      </div>

                      <!-- Confirm Password -->
                      <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-foreground mb-2">
                          Confirm New Password
                        </label>
                        <div class="relative">
                          <Shield class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                          <Input
                            v-model="passwordForm.password_confirmation"
                            :type="showConfirmPassword ? 'text' : 'password'"
                            class="pl-10 pr-10"
                            :class="{ 'border-red-500': errors.password_confirmation }"
                          />
                          <button
                            type="button"
                            @click="togglePassword('confirm')"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-muted-foreground hover:text-foreground"
                          >
                            <Eye v-if="!showConfirmPassword" class="h-4 w-4" />
                            <EyeOff v-else class="h-4 w-4" />
                          </button>
                        </div>
                        <p v-if="errors.password_confirmation" class="text-sm text-red-600 mt-1">{{ errors.password_confirmation }}</p>
                      </div>
                    </div>

                    <Button type="submit" :disabled="isLoading">
                      <LoaderCircle v-if="isLoading" class="w-4 h-4 mr-2 animate-spin" />
                      <Save v-else class="w-4 h-4 mr-2" />
                      Update Password
                    </Button>
                  </form>
                </CardContent>
              </Card>
            </TabsContent>

            <!-- Preferences Tab -->
            <TabsContent value="preferences" class="mt-6">
              <Card class="border-0 shadow-sm">
                <CardHeader>
                  <CardTitle>Preferences</CardTitle>
                </CardHeader>
                <CardContent>
                  <form @submit.prevent="updateProfile" class="space-y-6">
                    <!-- Email Preferences -->
                    <div class="space-y-4">
                      <h4 class="font-medium">Email Preferences</h4>
                      <div class="space-y-3">
                        <label class="flex items-center">
                          <input
                            v-model="profileForm.marketing_emails"
                            type="checkbox"
                            class="rounded border-gray-300 text-primary focus:ring-primary"
                          />
                          <span class="ml-2 text-sm text-muted-foreground">
                            Receive marketing emails about new products and offers
                          </span>
                        </label>
                        <label class="flex items-center">
                          <input
                            v-model="profileForm.order_updates"
                            type="checkbox"
                            class="rounded border-gray-300 text-primary focus:ring-primary"
                          />
                          <span class="ml-2 text-sm text-muted-foreground">
                            Receive order updates and shipping notifications
                          </span>
                        </label>
                      </div>
                    </div>

                    <!-- Sports Preferences -->
                    <div>
                      <label class="block text-sm font-medium text-foreground mb-2">
                        Preferred Sports
                      </label>
                      <Input
                        v-model="profileForm.preferred_sports"
                        type="text"
                        placeholder="e.g., Basketball, Soccer, Running"
                        :class="{ 'border-red-500': errors.preferred_sports }"
                      />
                      <p class="text-sm text-muted-foreground mt-1">
                        Tell us about your favorite sports to get personalized recommendations
                      </p>
                      <p v-if="errors.preferred_sports" class="text-sm text-red-600 mt-1">{{ errors.preferred_sports }}</p>
                    </div>

                    <Button type="submit" :disabled="isLoading">
                      <LoaderCircle v-if="isLoading" class="w-4 h-4 mr-2 animate-spin" />
                      <Save v-else class="w-4 h-4 mr-2" />
                      Save Preferences
                    </Button>
                  </form>
                </CardContent>
              </Card>
            </TabsContent>

            <!-- Account Tab -->
            <TabsContent value="account" class="mt-6">
              <div class="space-y-6">
                <!-- Account Actions -->
                <Card class="border-0 shadow-sm">
                  <CardHeader>
                    <CardTitle>Account Actions</CardTitle>
                  </CardHeader>
                  <CardContent class="space-y-4">
                    <div class="flex items-center justify-between p-4 border rounded-lg">
                      <div>
                        <h4 class="font-medium">Logout</h4>
                        <p class="text-sm text-muted-foreground">Sign out of your account</p>
                      </div>
                      <Button variant="outline" @click="logout">
                        <LogOut class="w-4 h-4 mr-2" />
                        Logout
                      </Button>
                    </div>

                    <div class="flex items-center justify-between p-4 border rounded-lg">
                      <div>
                        <h4 class="font-medium text-red-600">Delete Account</h4>
                        <p class="text-sm text-muted-foreground">Permanently delete your account and all data</p>
                      </div>
                      <Button variant="destructive" @click="deleteAccount">
                        <Trash2 class="w-4 h-4 mr-2" />
                        Delete Account
                      </Button>
                    </div>
                  </CardContent>
                </Card>

                <!-- Account Information -->
                <Card class="border-0 shadow-sm">
                  <CardHeader>
                    <CardTitle>Account Information</CardTitle>
                  </CardHeader>
                  <CardContent>
                    <div class="space-y-4">
                      <div class="flex items-center justify-between">
                        <span class="text-sm text-muted-foreground">Account ID</span>
                        <span class="text-sm font-medium">{{ user.id }}</span>
                      </div>
                      <div class="flex items-center justify-between">
                        <span class="text-sm text-muted-foreground">Member since</span>
                        <span class="text-sm font-medium">
                          {{ new Date(user.created_at).toLocaleDateString() }}
                        </span>
                      </div>
                      <div class="flex items-center justify-between">
                        <span class="text-sm text-muted-foreground">Last login</span>
                        <span class="text-sm font-medium">
                          {{ user.last_login_at ? new Date(user.last_login_at).toLocaleDateString() : 'Never' }}
                        </span>
                      </div>
                      <div class="flex items-center justify-between">
                        <span class="text-sm text-muted-foreground">Email verification</span>
                        <Badge v-if="user.email_verified_at" variant="secondary">
                          Verified
                        </Badge>
                        <Badge v-else variant="outline">
                          Unverified
                        </Badge>
                      </div>
                    </div>
                  </CardContent>
                </Card>
              </div>
            </TabsContent>
          </Tabs>
        </div>
      </div>
    </main>
  </div>
</template>
