<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div>
      <h1 class="text-3xl font-bold text-foreground">Edit Customer Group</h1>
      <p class="text-muted-foreground">Update the details for this customer group</p>
    </div>

    <!-- Edit Form -->
    <Card class="max-w-2xl">
      <CardHeader>
        <CardTitle>Group Information</CardTitle>
        <CardDescription>Update the details for your customer group</CardDescription>
      </CardHeader>
      <CardContent>
        <form @submit.prevent="submit" class="space-y-6">
          <div class="space-y-2">
            <label for="name" class="text-sm font-medium">Group Name</label>
            <Input
              id="name"
              v-model="form.name"
              type="text"
              placeholder="Enter group name"
              :error="form.errors.name"
              required
            />
          </div>

          <div class="space-y-2">
            <label for="description" class="text-sm font-medium">Description</label>
            <textarea
              id="description"
              v-model="form.description"
              rows="3"
              class="w-full px-3 py-2 border border-input bg-background rounded-md text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
              placeholder="Enter group description (optional)"
            ></textarea>
          </div>

          <div class="space-y-2">
            <label for="color" class="text-sm font-medium">Group Color</label>
            <div class="flex items-center space-x-3">
              <input
                id="color"
                v-model="form.color"
                type="color"
                class="w-12 h-10 border border-input rounded-md cursor-pointer"
              />
              <Input
                v-model="form.color"
                type="text"
                placeholder="#3B82F6"
                class="flex-1"
                :error="form.errors.color"
              />
            </div>
            <p class="text-xs text-muted-foreground">Choose a color to identify this group</p>
          </div>

          <div class="space-y-2">
            <div class="flex items-center space-x-2">
              <input
                id="is_active"
                v-model="form.is_active"
                type="checkbox"
                class="rounded"
              />
              <label for="is_active" class="text-sm font-medium">Active</label>
            </div>
            <p class="text-xs text-muted-foreground">Inactive groups won't be available for customer assignment</p>
          </div>

          <div class="flex items-center justify-end space-x-3 pt-4">
            <Link
              :href="route('admin.customer-groups.show', group.id)"
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
              <span v-else>Update Group</span>
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
  group: Object,
})

const form = useForm({
  name: props.group.name,
  description: props.group.description || '',
  color: props.group.color,
  is_active: props.group.is_active,
})

const submit = () => {
  form.put(route('admin.customer-groups.update', props.group.id))
}
</script>
