<template>
  <div class="min-h-screen bg-background">
    <div class="container mx-auto px-4 py-8">
      <!-- Header -->
      <div class="flex items-center justify-between mb-8">
        <div>
          <h1 class="text-3xl font-bold">Products</h1>
          <p class="text-muted-foreground">Manage your product catalog</p>
        </div>
        <Link :href="route('admin.products.create')">
          <Button>
            <Plus class="h-4 w-4 mr-2" />
            Add Product
          </Button>
        </Link>
      </div>

      <!-- Products Table -->
      <div class="bg-card border border-border rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-muted/50">
              <tr>
                <th class="text-left p-4 font-medium">Product</th>
                <th class="text-left p-4 font-medium">Category</th>
                <th class="text-left p-4 font-medium">Brand</th>
                <th class="text-left p-4 font-medium">Price</th>
                <th class="text-left p-4 font-medium">Discount</th>
                <th class="text-left p-4 font-medium">Stock</th>
                <th class="text-left p-4 font-medium">Status</th>
                <th class="text-left p-4 font-medium">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="product in products?.data || []" :key="product.id" class="border-t border-border hover:bg-muted/30">
                <td class="p-4">
                  <div class="flex items-center gap-3">
                    <div class="w-16 h-16 rounded-lg overflow-hidden bg-muted">
                      <img
                        v-if="product.main_image && product.main_image !== '/images/placeholder-product.jpg'"
                        :src="product.main_image"
                        :alt="product.name"
                        class="w-full h-full object-cover"
                        @error="$event.target.style.display='none'; $event.target.nextElementSibling.style.display='flex'"
                      />
                      <div v-else class="w-full h-full flex items-center justify-center text-muted-foreground">
                        <Package class="h-6 w-6" />
                      </div>
                      <!-- Fallback placeholder when image fails to load -->
                      <div class="w-full h-full hidden items-center justify-center text-muted-foreground bg-muted">
                        <Package class="h-6 w-6" />
                      </div>
                    </div>
                    <div>
                      <div class="font-medium">{{ product.name }}</div>
                      <div class="text-sm text-muted-foreground">{{ product.sku }}</div>
                    </div>
                  </div>
                </td>
                <td class="p-4">
                  <span v-if="product.category" class="text-sm">{{ product.category.name }}</span>
                  <span v-else class="text-sm text-muted-foreground">No category</span>
                </td>
                <td class="p-4">
                  <span v-if="product.brand" class="text-sm">{{ product.brand.name }}</span>
                  <span v-else class="text-sm text-muted-foreground">No brand</span>
                </td>
                <td class="p-4">
                  <div class="flex items-center gap-2">
                    <span class="font-medium">${{ product.final_price }}</span>
                    <span v-if="product.discount > 0" class="text-sm text-muted-foreground line-through">
                      ${{ product.price }}
                    </span>
                  </div>
                </td>
                <td class="p-4">
                  <span v-if="product.discount > 0" class="text-sm">%{{ product.discount }}</span>
                  <span v-else class="text-sm">No discount
</span>
                </td>
                <td class="p-4">
                  <div class="flex items-center gap-2">
                    <span class="text-sm">{{ product.total_stock || 0 }}</span>
                    <Badge :variant="product.has_stock ? 'default' : 'destructive'">
                      {{ product.has_stock ? 'In Stock' : 'Out of Stock' }}
                    </Badge>
                  </div>
                </td>
                <td class="p-4">
                  <div class="flex items-center gap-2">
                    <Badge :variant="product.is_active ? 'default' : 'secondary'">
                      {{ product.is_active ? 'Active' : 'Inactive' }}
                    </Badge>
                    <Badge v-if="product.is_featured" variant="secondary">Featured</Badge>
                  </div>
                </td>
                <td class="p-4">
                  <div class="flex items-center gap-2">
                    <Link :href="route('admin.products.show', product.id)">
                      <Button variant="ghost" size="icon">
                        <Eye class="h-4 w-4" />
                      </Button>
                    </Link>
                    <Link :href="route('admin.products.edit', product.id)">
                      <Button variant="ghost" size="icon">
                        <Edit class="h-4 w-4" />
                      </Button>
                    </Link>
                    <Button
                      variant="ghost"
                      size="icon"
                      @click="deleteProduct(product.id)"
                      class="text-destructive hover:text-destructive"
                    >
                      <Trash2 class="h-4 w-4" />
                    </Button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="products?.links && products.links.length > 3" class="p-4 border-t border-border">
          <div class="flex items-center justify-center space-x-1">
            <template v-for="(link, index) in products.links" :key="index">
              <Link
                v-if="link.url"
                :href="link.url"
                :class="[
                  'px-3 py-2 text-sm rounded-md transition-colors',
                  link.active
                    ? 'bg-primary text-primary-foreground'
                    : 'text-muted-foreground hover:text-foreground hover:bg-muted'
                ]"
              >
                <span v-html="link.label"></span>
              </Link>
              <span
                v-else
                class="px-3 py-2 text-sm rounded-md opacity-50 cursor-not-allowed"
              >
                <span v-html="link.label"></span>
              </span>
            </template>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import Button from '@/components/ui/button/Button.vue'
import Badge from '@/components/ui/badge/Badge.vue'
import { Plus, Eye, Edit, Trash2, Package } from 'lucide-vue-next'
import AdminLayout from '@/layouts/AdminLayout.vue'

defineOptions({
  layout: AdminLayout,
});

const props = defineProps({
  products: Object
})

const deleteProduct = (productId) => {
  if (confirm('Are you sure you want to delete this product?')) {
    // Handle delete
    console.log('Deleting product:', productId)
  }
}

</script>
