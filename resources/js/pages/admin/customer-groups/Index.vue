<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-foreground">Customer Groups</h1>
        <p class="text-muted-foreground">Manage customer groups and organize your customers</p>
      </div>
      <Link
        :href="route('admin.customer-groups.create')"
        class="inline-flex items-center px-4 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 transition-colors"
      >
        <Plus class="w-4 h-4 mr-2" />
        Create Group
      </Link>
    </div>

    <!-- Groups Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <Card v-for="group in groups.data" :key="group.id" class="hover:shadow-lg transition-shadow">
        <CardHeader>
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div
                class="w-4 h-4 rounded-full"
                :style="{ backgroundColor: group.color }"
              ></div>
              <CardTitle class="text-lg">{{ group.name }}</CardTitle>
            </div>
            <Badge v-if="!group.is_active" variant="secondary">Inactive</Badge>
          </div>
          <CardDescription v-if="group.description">
            {{ group.description }}
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div class="space-y-4">
            <div class="flex items-center justify-between text-sm">
              <span class="text-muted-foreground">Customers</span>
              <span class="font-medium">{{ group.customers_count }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
              <span class="text-muted-foreground">Created</span>
              <span>{{ formatDate(group.created_at) }}</span>
            </div>
          </div>
        </CardContent>
        <CardFooter class="flex items-center justify-between">
          <Link
            :href="route('admin.customer-groups.show', group.id)"
            class="text-sm text-primary hover:underline"
          >
            View Details
          </Link>
          <div class="flex items-center space-x-2">
            <Link
              :href="route('admin.customer-groups.edit', group.id)"
              class="text-sm text-primary hover:underline"
            >
              Edit
            </Link>
            <button
              @click="deleteGroup(group)"
              class="text-sm text-destructive hover:underline"
            >
              Delete
            </button>
          </div>
        </CardFooter>
      </Card>
    </div>

    <!-- Empty State -->
    <div v-if="groups.data.length === 0" class="text-center py-12">
              <div class="w-16 h-16 mx-auto mb-4 bg-muted rounded-full flex items-center justify-center">
          <Users class="w-8 h-8 text-muted-foreground" />
        </div>
      <h3 class="text-lg font-medium text-foreground mb-2">No customer groups yet</h3>
      <p class="text-muted-foreground mb-4">Create your first customer group to start organizing your customers.</p>
      <Link
        :href="route('admin.customer-groups.create')"
        class="inline-flex items-center px-4 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 transition-colors"
      >
        <Plus class="w-4 h-4 mr-2" />
        Create First Group
      </Link>
    </div>

    <!-- Pagination -->
    <div v-if="groups.links && groups.links.length > 3" class="flex items-center justify-center">
      <div class="flex space-x-1">
        <Link
          v-for="(link, index) in groups.links"
          :key="index"
          :href="link.url"
          :class="[
            'px-3 py-2 text-sm rounded-md transition-colors',
            link.active
              ? 'bg-primary text-primary-foreground'
              : 'text-muted-foreground hover:text-foreground hover:bg-muted',
            !link.url ? 'opacity-50 cursor-not-allowed' : ''
          ]"
        >
          <span v-html="link.label"></span>
        </Link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import Card from '@/components/ui/card/Card.vue'
import CardHeader from '@/components/ui/card/CardHeader.vue'
import CardTitle from '@/components/ui/card/CardTitle.vue'
import CardDescription from '@/components/ui/card/CardDescription.vue'
import CardContent from '@/components/ui/card/CardContent.vue'
import CardFooter from '@/components/ui/card/CardFooter.vue'
import Badge from '@/components/ui/badge/Badge.vue'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { Plus, Users } from 'lucide-vue-next'

defineOptions({
  layout: AdminLayout,
})

const props = defineProps({
  groups: Object,
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}

const deleteGroup = (group) => {
  if (confirm(`Are you sure you want to delete "${group.name}"?`)) {
    router.delete(route('admin.customer-groups.destroy', group.id))
  }
}
</script>
