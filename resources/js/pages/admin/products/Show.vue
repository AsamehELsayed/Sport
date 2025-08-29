<script setup>
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import Button from '@/components/ui/button/Button.vue'
import Badge from '@/components/ui/badge/Badge.vue'
import Card from '@/components/ui/card/Card.vue'
import CardContent from '@/components/ui/card/CardContent.vue'
import CardHeader from '@/components/ui/card/CardHeader.vue'
import CardTitle from '@/components/ui/card/CardTitle.vue'
import { ArrowLeft, Edit, Package, Tag, BarChart3 } from 'lucide-vue-next'
import AdminLayout from '@/layouts/AdminLayout.vue'

defineOptions({
  layout: AdminLayout,
});

const props = defineProps({
  product: Object
})

const currentImageIndex = ref(0)

const currentImage = computed(() => {
  return props.product?.main_image || '/images/placeholder-product.jpg'
})

const totalStock = computed(() => {
  return props.product?.variants?.reduce((sum, variant) => sum + (variant.stock || 0), 0) || 0
})

const hasStock = computed(() => totalStock.value > 0)

const selectImage = (index) => {
  currentImageIndex.value = index
}
</script>

<template>
  <div class="min-h-screen bg-background">
    <div class="container mx-auto px-4 py-8">
      <!-- Header -->
      <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-4">
          <Link
            :href="route('admin.products.index')"
            class="flex items-center gap-2 text-muted-foreground hover:text-primary transition-colors"
          >
            <ArrowLeft class="h-4 w-4" />
            Back to Products
          </Link>
          <div class="h-6 w-px bg-border"></div>
          <h1 class="text-3xl font-bold">{{ product?.name }}</h1>
        </div>
        <div class="flex items-center gap-3">
          <Link :href="route('admin.products.edit', product?.id)">
            <Button variant="outline">
              <Edit class="h-4 w-4 mr-2" />
              Edit Product
            </Button>
          </Link>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Product Images -->
        <div class="lg:col-span-1 space-y-4">
          <Card>
            <CardContent class="p-6">
              <div class="aspect-square rounded-lg overflow-hidden bg-card border shadow-sm mb-4">
                <img
                  :src="currentImage"
                  :alt="product?.name"
                  class="w-full h-full object-cover"
                />
              </div>

              <!-- Note: Images are now managed through variants -->
              <div class="p-4 bg-muted/50 rounded-lg">
                <p class="text-sm text-muted-foreground text-center">
                  Product images are now managed through variants.
                  Please view the variants section below to see available images.
                </p>
              </div>
            </CardContent>
          </Card>
        </div>

        <!-- Product Details -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Basic Info -->
          <Card>
            <CardHeader>
              <CardTitle class="flex items-center gap-2">
                <Package class="h-5 w-5" />
                Product Information
              </CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="text-sm font-medium text-muted-foreground">Name</label>
                  <p class="text-lg font-semibold">{{ product?.name }}</p>
                </div>
                <div>
                  <label class="text-sm font-medium text-muted-foreground">SKU</label>
                  <p class="text-lg">{{ product?.sku || 'N/A' }}</p>
                </div>
                <div>
                  <label class="text-sm font-medium text-muted-foreground">Price</label>
                  <p class="text-2xl font-bold text-primary">${{ product?.price }}</p>
                </div>
                <div>
                  <label class="text-sm font-medium text-muted-foreground">Discount</label>
                  <p class="text-lg">{{ product?.discount ? `$${product.discount}` : 'None' }}</p>
                </div>
                <div>
                  <label class="text-sm font-medium text-muted-foreground">Category</label>
                  <p class="text-lg">{{ product?.category?.name || 'Uncategorized' }}</p>
                </div>
                <div>
                  <label class="text-sm font-medium text-muted-foreground">Brand</label>
                  <p class="text-lg">{{ product?.brand?.name || 'No Brand' }}</p>
                </div>
                <div>
                  <label class="text-sm font-medium text-muted-foreground">Status</label>
                  <div class="flex items-center gap-2">
                    <Badge :variant="product?.is_active ? 'default' : 'secondary'">
                      {{ product?.is_active ? 'Active' : 'Inactive' }}
                    </Badge>
                    <Badge v-if="product?.is_featured" variant="secondary">Featured</Badge>
                  </div>
                </div>
                <div>
                  <label class="text-sm font-medium text-muted-foreground">Stock Status</label>
                  <div class="flex items-center gap-2">
                    <Badge :variant="hasStock ? 'default' : 'destructive'">
                      {{ hasStock ? `${totalStock} in stock` : 'Out of stock' }}
                    </Badge>
                  </div>
                </div>
              </div>

              <div>
                <label class="text-sm font-medium text-muted-foreground">Description</label>
                <p class="text-muted-foreground mt-1">{{ product?.description || 'No description available' }}</p>
              </div>
            </CardContent>
          </Card>

          <!-- Variants -->
          <Card>
            <CardHeader>
              <CardTitle class="flex items-center gap-2">
                <Tag class="h-5 w-5" />
                Product Variants
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div class="overflow-x-auto">
                <table class="w-full">
                  <thead>
                    <tr class="border-b">
                      <th class="text-left p-2 font-medium">Image</th>
                      <th class="text-left p-2 font-medium">Size</th>
                      <th class="text-left p-2 font-medium">Color</th>
                      <th class="text-left p-2 font-medium">Stock</th>
                      <th class="text-left p-2 font-medium">SKU</th>
                      <th class="text-left p-2 font-medium">Price Adjustment</th>
                      <th class="text-left p-2 font-medium">Default</th>
                      <th class="text-left p-2 font-medium">Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="variant in product?.variants" :key="variant.id" class="border-b">
                      <td class="p-2">
                        <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-100">
                          <img
                            :src="variant.main_image"
                            :alt="`${variant.size} ${variant.color}`"
                            class="w-full h-full object-cover"
                          />
                        </div>
                      </td>
                      <td class="p-2">{{ variant.size }}</td>
                      <td class="p-2">{{ variant.color || 'N/A' }}</td>
                      <td class="p-2">
                        <Badge :variant="variant.stock > 0 ? 'default' : 'destructive'">
                          {{ variant.stock }}
                        </Badge>
                      </td>
                      <td class="p-2">{{ variant.sku || 'N/A' }}</td>
                      <td class="p-2">
                        {{ variant.price_adjustment ? `$${variant.price_adjustment}` : 'None' }}
                      </td>
                      <td class="p-2">
                        <Badge v-if="variant.is_default" variant="secondary">Default</Badge>
                        <span v-else class="text-muted-foreground">-</span>
                      </td>
                      <td class="p-2">
                        <Badge :variant="variant.is_active ? 'default' : 'secondary'">
                          {{ variant.is_active ? 'Active' : 'Inactive' }}
                        </Badge>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </CardContent>
          </Card>

          <!-- Statistics -->
          <Card>
            <CardHeader>
              <CardTitle class="flex items-center gap-2">
                <BarChart3 class="h-5 w-5" />
                Product Statistics
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center p-4 bg-primary/10 rounded-lg">
                  <div class="text-2xl font-bold text-primary">{{ totalStock }}</div>
                  <div class="text-sm text-muted-foreground">Total Stock</div>
                </div>
                <div class="text-center p-4 bg-secondary/10 rounded-lg">
                  <div class="text-2xl font-bold text-secondary">{{ product?.variants?.length || 0 }}</div>
                  <div class="text-sm text-muted-foreground">Variants</div>
                </div>
                <div class="text-center p-4 bg-accent/10 rounded-lg">
                  <div class="text-2xl font-bold text-accent">{{ product?.orderItems?.length || 0 }}</div>
                  <div class="text-sm text-muted-foreground">Orders</div>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  </div>
</template>
