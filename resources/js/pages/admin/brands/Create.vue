<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div>
      <h1 class="text-3xl font-bold text-foreground">Create Brand</h1>
      <p class="text-muted-foreground">Add a new product brand</p>
    </div>

    <!-- Create Form -->
    <Card>
      <CardHeader>
        <CardTitle>Brand Information</CardTitle>
        <CardDescription>Fill in the details for the new brand</CardDescription>
      </CardHeader>
      <CardContent>
        <form @submit.prevent="submit" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Name -->
            <div class="space-y-2">
              <label for="name" class="text-sm font-medium">Name *</label>
              <Input
                id="name"
                v-model="form.name"
                type="text"
                placeholder="Enter brand name"
                required
              />
              <p v-if="form.errors.name" class="text-sm text-destructive">{{ form.errors.name }}</p>
            </div>

            <!-- Sort Order -->
            <div class="space-y-2">
              <label for="sort_order" class="text-sm font-medium">Sort Order</label>
              <Input
                id="sort_order"
                v-model="form.sort_order"
                type="number"
                placeholder="0"
                min="0"
              />
              <p v-if="form.errors.sort_order" class="text-sm text-destructive">{{ form.errors.sort_order }}</p>
            </div>
          </div>

          <!-- Description -->
          <div class="space-y-2">
            <label for="description" class="text-sm font-medium">Description</label>
            <textarea
              id="description"
              v-model="form.description"
              rows="4"
              class="w-full px-3 py-2 border border-input bg-background rounded-md text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
              placeholder="Enter brand description"
            ></textarea>
            <p v-if="form.errors.description" class="text-sm text-destructive">{{ form.errors.description }}</p>
          </div>

          <!-- Website -->
          <div class="space-y-2">
            <label for="website" class="text-sm font-medium">Website</label>
            <Input
              id="website"
              v-model="form.website"
              type="url"
              placeholder="https://example.com"
            />
            <p v-if="form.errors.website" class="text-sm text-destructive">{{ form.errors.website }}</p>
          </div>

          <!-- Logo Upload -->
          <div class="space-y-2">
            <label class="text-sm font-medium">Brand Logo</label>
            <ImageUpload
              v-model="form.logo"
              :multiple="false"
              :max-files="1"
            />
            <p v-if="form.errors.logo" class="text-sm text-destructive">{{ form.errors.logo }}</p>
          </div>

          <!-- Status -->
          <div class="flex items-center space-x-2">
            <input
              id="is_active"
              v-model="form.is_active"
              type="checkbox"
              class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary"
            />
            <label for="is_active" class="text-sm font-medium">Active</label>
          </div>

          <!-- Form Actions -->
          <div class="flex items-center justify-end space-x-3 pt-6 border-t border-border">
            <Link
              href="/admin/brands"
              class="px-4 py-2 text-sm font-medium text-muted-foreground hover:text-foreground transition-colors"
            >
              Cancel
            </Link>
            <button
              type="submit"
              :disabled="form.processing"
              class="px-4 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 transition-colors disabled:opacity-50"
            >
              {{ form.processing ? 'Creating...' : 'Create Brand' }}
            </button>
          </div>
        </form>
      </CardContent>
    </Card>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import Card from '@/components/ui/card/Card.vue'
import CardContent from '@/components/ui/card/CardContent.vue'
import CardHeader from '@/components/ui/card/CardHeader.vue'
import CardTitle from '@/components/ui/card/CardTitle.vue'
import CardDescription from '@/components/ui/card/CardDescription.vue'
import Input from '@/components/ui/Input.vue'
import ImageUpload from '@/components/ui/ImageUpload.vue'
import AdminLayout from '@/layouts/AdminLayout.vue'

defineOptions({
  layout: AdminLayout,
})

const form = useForm({
  name: '',
  description: '',
  logo: null,
  website: '',
  is_active: true,
  sort_order: 0,
})

const submit = () => {
  form.post(route('admin.brands.store'))
}
</script>
