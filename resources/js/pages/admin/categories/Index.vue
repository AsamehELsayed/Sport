<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-foreground">Categories</h1>
        <p class="text-muted-foreground">Manage product categories</p>
      </div>
      <Link
        href="/admin/categories/create"
        class="inline-flex items-center px-4 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 transition-colors"
      >
        <Plus class="w-4 h-4 mr-2" />
        Add Category
      </Link>
    </div>

    <!-- Categories Table -->
    <Card>
      <CardContent class="p-0">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="border-b border-border">
                <th class="text-left py-3 px-4 font-medium text-muted-foreground">Name</th>
                <th class="text-left py-3 px-4 font-medium text-muted-foreground">Slug</th>
                <th class="text-left py-3 px-4 font-medium text-muted-foreground">Products</th>
                <th class="text-left py-3 px-4 font-medium text-muted-foreground">Status</th>
                <th class="text-left py-3 px-4 font-medium text-muted-foreground">Sort Order</th>
                <th class="text-left py-3 px-4 font-medium text-muted-foreground">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="category in categories.data" :key="category.id" class="border-b border-border hover:bg-muted/50">
                <td class="py-3 px-4">
                  <div class="flex items-center space-x-3">
                    <div v-if="category.image" class="w-10 h-10 rounded-lg bg-muted flex items-center justify-center">
                      <img :src="category.image" :alt="category.name" class="w-10 h-10 rounded-lg object-cover" />
                    </div>
                    <div v-else class="w-10 h-10 rounded-lg bg-muted flex items-center justify-center">
                      <Tag class="w-5 h-5 text-muted-foreground" />
                    </div>
                    <div>
                      <p class="font-medium">{{ category.name }}</p>
                      <p v-if="category.description" class="text-sm text-muted-foreground truncate max-w-xs">
                        {{ category.description }}
                      </p>
                    </div>
                  </div>
                </td>
                <td class="py-3 px-4">
                  <code class="text-sm bg-muted px-2 py-1 rounded">{{ category.slug }}</code>
                </td>
                <td class="py-3 px-4">
                  <span class="text-sm text-muted-foreground">{{ category.products_count || 0 }} products</span>
                </td>
                <td class="py-3 px-4">
                  <Badge :variant="category.is_active ? 'default' : 'secondary'">
                    {{ category.is_active ? 'Active' : 'Inactive' }}
                  </Badge>
                </td>
                <td class="py-3 px-4">
                  <span class="text-sm text-muted-foreground">{{ category.sort_order }}</span>
                </td>
                <td class="py-3 px-4">
                  <div class="flex items-center space-x-2">
                    <Link
                      :href="`/admin/categories/${category.id}`"
                      class="text-sm text-primary hover:underline"
                    >
                      View
                    </Link>
                    <Link
                      :href="`/admin/categories/${category.id}/edit`"
                      class="text-sm text-primary hover:underline"
                    >
                      Edit
                    </Link>
                    <button
                      @click="deleteCategory(category)"
                      class="text-sm text-destructive hover:underline"
                    >
                      Delete
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </CardContent>
    </Card>

    <!-- Pagination -->
    <div v-if="categories.links && categories.links.length > 3" class="flex items-center justify-center">
      <div class="flex space-x-1">
        <Link
          v-for="(link, index) in categories.links"
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
import { Plus, Tag } from 'lucide-vue-next'
import Card from '@/components/ui/card/Card.vue'
import CardContent from '@/components/ui/card/CardContent.vue'
import Badge from '@/components/ui/badge/Badge.vue'
import AdminLayout from '@/layouts/AdminLayout.vue'

defineOptions({
  layout: AdminLayout,
})

const props = defineProps({
  categories: Object,
})

const deleteCategory = (category) => {
  if (confirm(`Are you sure you want to delete "${category.name}"?`)) {
    router.delete(`/admin/categories/${category.id}`)
  }
}
</script>
