<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-foreground">Brands</h1>
        <p class="text-muted-foreground">Manage product brands</p>
      </div>
      <Link
        href="/admin/brands/create"
        class="inline-flex items-center px-4 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 transition-colors"
      >
        <Plus class="w-4 h-4 mr-2" />
        Add Brand
      </Link>
    </div>

    <!-- Brands Table -->
    <Card>
      <CardContent class="p-0">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="border-b border-border">
                <th class="text-left py-3 px-4 font-medium text-muted-foreground">Brand</th>
                <th class="text-left py-3 px-4 font-medium text-muted-foreground">Slug</th>
                <th class="text-left py-3 px-4 font-medium text-muted-foreground">Products</th>
                <th class="text-left py-3 px-4 font-medium text-muted-foreground">Website</th>
                <th class="text-left py-3 px-4 font-medium text-muted-foreground">Status</th>
                <th class="text-left py-3 px-4 font-medium text-muted-foreground">Sort Order</th>
                <th class="text-left py-3 px-4 font-medium text-muted-foreground">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="brand in brands.data" :key="brand.id" class="border-b border-border hover:bg-muted/50">
                <td class="py-3 px-4">
                  <div class="flex items-center space-x-3">
                    <div v-if="brand.logo" class="w-10 h-10 rounded-lg bg-muted flex items-center justify-center">
                      <img :src="brand.logo" :alt="brand.name" class="w-10 h-10 rounded-lg object-cover" />
                    </div>
                    <div v-else class="w-10 h-10 rounded-lg bg-muted flex items-center justify-center">
                      <Award class="w-5 h-5 text-muted-foreground" />
                    </div>
                    <div>
                      <p class="font-medium">{{ brand.name }}</p>
                      <p v-if="brand.description" class="text-sm text-muted-foreground truncate max-w-xs">
                        {{ brand.description }}
                      </p>
                    </div>
                  </div>
                </td>
                <td class="py-3 px-4">
                  <code class="text-sm bg-muted px-2 py-1 rounded">{{ brand.slug }}</code>
                </td>
                <td class="py-3 px-4">
                  <span class="text-sm text-muted-foreground">{{ brand.products_count || 0 }} products</span>
                </td>
                <td class="py-3 px-4">
                  <a
                    v-if="brand.website"
                    :href="brand.website"
                    target="_blank"
                    class="text-sm text-primary hover:underline"
                  >
                    {{ brand.website }}
                  </a>
                  <span v-else class="text-sm text-muted-foreground">-</span>
                </td>
                <td class="py-3 px-4">
                  <Badge :variant="brand.is_active ? 'default' : 'secondary'">
                    {{ brand.is_active ? 'Active' : 'Inactive' }}
                  </Badge>
                </td>
                <td class="py-3 px-4">
                  <span class="text-sm text-muted-foreground">{{ brand.sort_order }}</span>
                </td>
                <td class="py-3 px-4">
                  <div class="flex items-center space-x-2">
                    <Link
                      :href="`/admin/brands/${brand.id}`"
                      class="text-sm text-primary hover:underline"
                    >
                      View
                    </Link>
                    <Link
                      :href="`/admin/brands/${brand.id}/edit`"
                      class="text-sm text-primary hover:underline"
                    >
                      Edit
                    </Link>
                    <button
                      @click="deleteBrand(brand)"
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
    <div v-if="brands.links && brands.links.length > 3" class="flex items-center justify-center">
      <div class="flex space-x-1">
        <Link
          v-for="(link, index) in brands.links"
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
import { Plus, Award } from 'lucide-vue-next'
import Card from '@/components/ui/card/Card.vue'
import CardContent from '@/components/ui/card/CardContent.vue'
import Badge from '@/components/ui/badge/Badge.vue'
import AdminLayout from '@/layouts/AdminLayout.vue'

defineOptions({
  layout: AdminLayout,
})

const props = defineProps({
  brands: Object,
})

const deleteBrand = (brand) => {
  if (confirm(`Are you sure you want to delete "${brand.name}"?`)) {
    router.delete(`/admin/brands/${brand.id}`)
  }
}
</script>
