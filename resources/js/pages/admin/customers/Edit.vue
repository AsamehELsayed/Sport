<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div>
      <h1 class="text-3xl font-bold text-foreground">Edit Customer</h1>
      <p class="text-muted-foreground">Update customer information and preferences</p>
    </div>

    <!-- Edit Form -->
    <Card class="max-w-4xl">
      <CardHeader>
        <CardTitle>Customer Information</CardTitle>
        <CardDescription>Update the customer's details and preferences</CardDescription>
      </CardHeader>
      <CardContent>
        <form @submit.prevent="submit" class="space-y-6">
          <!-- Basic Information -->
          <div class="space-y-4">
            <h3 class="text-lg font-medium">Basic Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="space-y-2">
                <label for="name" class="text-sm font-medium">Full Name</label>
                <Input
                  id="name"
                  v-model="form.name"
                  type="text"
                  placeholder="Enter full name"
                  :error="form.errors.name"
                  required
                />
              </div>
              <div class="space-y-2">
                <label for="email" class="text-sm font-medium">Email Address</label>
                <Input
                  id="email"
                  v-model="form.email"
                  type="email"
                  placeholder="Enter email address"
                  :error="form.errors.email"
                  required
                />
              </div>
              <div class="space-y-2">
                <label for="phone" class="text-sm font-medium">Phone Number</label>
                <Input
                  id="phone"
                  v-model="form.phone"
                  type="tel"
                  placeholder="Enter phone number"
                  :error="form.errors.phone"
                />
              </div>
              <div class="space-y-2">
                <label for="date_of_birth" class="text-sm font-medium">Date of Birth</label>
                <Input
                  id="date_of_birth"
                  v-model="form.date_of_birth"
                  type="date"
                  :error="form.errors.date_of_birth"
                />
              </div>
              <div class="space-y-2">
                <label for="gender" class="text-sm font-medium">Gender</label>
                <select
                  id="gender"
                  v-model="form.gender"
                  class="w-full px-3 py-2 border border-input bg-background rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                >
                  <option value="">Select gender</option>
                  <option value="male">Male</option>
                  <option value="female">Female</option>
                  <option value="other">Other</option>
                  <option value="prefer_not_to_say">Prefer not to say</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Address Information -->
          <div class="space-y-4">
            <h3 class="text-lg font-medium">Address Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="space-y-2">
                <label for="address_line_1" class="text-sm font-medium">Address Line 1</label>
                <Input
                  id="address_line_1"
                  v-model="form.address_line_1"
                  type="text"
                  placeholder="Enter address"
                  :error="form.errors.address_line_1"
                />
              </div>
              <div class="space-y-2">
                <label for="address_line_2" class="text-sm font-medium">Address Line 2</label>
                <Input
                  id="address_line_2"
                  v-model="form.address_line_2"
                  type="text"
                  placeholder="Apartment, suite, etc."
                  :error="form.errors.address_line_2"
                />
              </div>
              <div class="space-y-2">
                <label for="city" class="text-sm font-medium">City</label>
                <Input
                  id="city"
                  v-model="form.city"
                  type="text"
                  placeholder="Enter city"
                  :error="form.errors.city"
                />
              </div>
              <div class="space-y-2">
                <label for="state" class="text-sm font-medium">State/Province</label>
                <Input
                  id="state"
                  v-model="form.state"
                  type="text"
                  placeholder="Enter state"
                  :error="form.errors.state"
                />
              </div>
              <div class="space-y-2">
                <label for="postal_code" class="text-sm font-medium">Postal Code</label>
                <Input
                  id="postal_code"
                  v-model="form.postal_code"
                  type="text"
                  placeholder="Enter postal code"
                  :error="form.errors.postal_code"
                />
              </div>
              <div class="space-y-2">
                <label for="country" class="text-sm font-medium">Country</label>
                <Input
                  id="country"
                  v-model="form.country"
                  type="text"
                  placeholder="Enter country"
                  :error="form.errors.country"
                />
              </div>
            </div>
          </div>

          <!-- Additional Information -->
          <div class="space-y-4">
            <h3 class="text-lg font-medium">Additional Information</h3>
            <div class="space-y-4">
              <div class="space-y-2">
                <label for="bio" class="text-sm font-medium">Bio</label>
                <textarea
                  id="bio"
                  v-model="form.bio"
                  rows="3"
                  class="w-full px-3 py-2 border border-input bg-background rounded-md text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                  placeholder="Tell us about yourself..."
                  :error="form.errors.bio"
                ></textarea>
              </div>
              <div class="space-y-2">
                <label for="preferred_sports" class="text-sm font-medium">Preferred Sports</label>
                <Input
                  id="preferred_sports"
                  v-model="form.preferred_sports"
                  type="text"
                  placeholder="e.g., Basketball, Soccer, Running"
                  :error="form.errors.preferred_sports"
                />
                <p class="text-xs text-muted-foreground">Comma-separated list of preferred sports</p>
              </div>
            </div>
          </div>

          <!-- Preferences -->
          <div class="space-y-4">
            <h3 class="text-lg font-medium">Preferences</h3>
            <div class="space-y-4">
              <div class="flex items-center space-x-2">
                <input
                  id="marketing_emails"
                  v-model="form.marketing_emails"
                  type="checkbox"
                  class="rounded"
                />
                <label for="marketing_emails" class="text-sm font-medium">Receive marketing emails</label>
              </div>
              <div class="flex items-center space-x-2">
                <input
                  id="order_updates"
                  v-model="form.order_updates"
                  type="checkbox"
                  class="rounded"
                />
                <label for="order_updates" class="text-sm font-medium">Receive order updates</label>
              </div>
            </div>
          </div>

          <div class="flex items-center justify-end space-x-3 pt-4">
            <Link
              :href="route('admin.customers.show', customer.id)"
              class="px-4 py-2 text-sm border border-input rounded-md hover:bg-muted transition-colors"
            >
              Cancel
            </Link>
            <button
              type="submit"
              :disabled="form.processing"
              class="px-4 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 transition-colors disabled:opacity-50"
            >
              <span v-if="form.processing">Updating...</span>
              <span v-else>Update Customer</span>
            </button>
          </div>
        </form>
      </CardContent>
    </Card>
  </div>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import Card from '@/components/ui/card/Card.vue'
import CardHeader from '@/components/ui/card/CardHeader.vue'
import CardTitle from '@/components/ui/card/CardTitle.vue'
import CardDescription from '@/components/ui/card/CardDescription.vue'
import CardContent from '@/components/ui/card/CardContent.vue'
import Input from '@/components/ui/input/Input.vue'
import AdminLayout from '@/layouts/AdminLayout.vue'

defineOptions({
  layout: AdminLayout,
})

const props = defineProps({
  customer: Object,
})

const form = useForm({
  name: props.customer.name,
  email: props.customer.email,
  phone: props.customer.phone || '',
  date_of_birth: props.customer.date_of_birth || '',
  address_line_1: props.customer.address_line_1 || '',
  address_line_2: props.customer.address_line_2 || '',
  city: props.customer.city || '',
  state: props.customer.state || '',
  postal_code: props.customer.postal_code || '',
  country: props.customer.country || '',
  gender: props.customer.gender || '',
  bio: props.customer.bio || '',
  preferred_sports: props.customer.preferred_sports || '',
  marketing_emails: props.customer.marketing_emails,
  order_updates: props.customer.order_updates,
})

const submit = () => {
  form.put(route('admin.customers.update', props.customer.id))
}
</script>
