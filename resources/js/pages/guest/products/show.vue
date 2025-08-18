<script setup lang="ts">
import { ref, computed, onMounted } from "vue"
import { useCart } from "@/composables/useCart"
import { Link } from "@inertiajs/vue3"

// UI components
import Button from "@/components/ui/button/Button.vue"
import Badge from "@/components/ui/badge/Badge.vue"
import Card from "@/components/ui/card/Card.vue"
import CardContent from "@/components/ui/card/CardContent.vue"
import Tabs from "@/components/ui/tabs/Tabs.vue"
import TabsList from "@/components/ui/tabs/TabsList.vue"
import TabsTrigger from "@/components/ui/tabs/TabsTrigger.vue"
import TabsContent from "@/components/ui/tabs/TabsContent.vue"

// Icons
import { Star, Heart, ShoppingCart, Minus, Plus, Truck, Shield, RotateCcw, AlertCircle, CheckCircle } from "lucide-vue-next"

import GuestLayout from '@/layouts/GuestLayout.vue';

defineOptions({
    layout: GuestLayout,
});

// Props from Inertia
const props = defineProps({
  product: Object,
  relatedProducts: Array
});

const { addToCart } = useCart()

const quantity = ref(1)
const selectedSize = ref("")
const isFavorite = ref(false)
const currentImageIndex = ref(0)
const isLoading = ref(false)
const showAddedToCart = ref(false)

// Computed properties
const currentImage = computed(() => {
  if (props.product?.images && props.product.images.length > 0) {
    return `${props.product.images[currentImageIndex.value]}`
  }
  return '/images/placeholder-product.svg'
})

const canIncrement = computed(() => quantity.value < props.product?.total_stock)
const canAddToCart = computed(() =>
  props.product?.has_stock &&
  selectedSize.value &&
  quantity.value > 0 &&
  quantity.value <= props.product?.total_stock
)

const availableSizes = computed(() => {
  return props.product?.available_sizes || []
})

// Handle image load errors
const handleImageError = (event) => {
  event.target.src = '/images/placeholder-product.svg'
}

// Methods
function incrementQuantity() {
  if (canIncrement.value) {
    quantity.value++
  }
}

function decrementQuantity() {
  quantity.value = Math.max(1, quantity.value - 1)
}

function selectImage(index) {
  currentImageIndex.value = index
}

function handleAddToCart() {
  if (!canAddToCart.value) return

  isLoading.value = true

  // Add item to cart
  const cartItem = {
    id: props.product.id,
    name: props.product.name,
    price: props.product.final_price || props.product.price,
    originalPrice: props.product.discount > 0 ? props.product.price : null,
    image: currentImage.value,
    size: selectedSize.value,
    inStock: props.product.has_stock,
    stockQuantity: props.product.total_stock,
    rating: props.product.rating,
    reviews: props.product.reviews_count,
    category: props.product.category?.name,
    brand: props.product.brand?.name
  }

  addToCart(cartItem, quantity.value)

  // Simulate API call
  setTimeout(() => {
    showAddedToCart.value = true
    isLoading.value = false

    // Hide success message after 3 seconds
    setTimeout(() => {
      showAddedToCart.value = false
    }, 3000)
  }, 1000)
}

function toggleFavorite() {
  isFavorite.value = !isFavorite.value
}

// Lifecycle
onMounted(() => {
  // Auto-select first size if only one available
  if (availableSizes.value.length === 1) {
    selectedSize.value = availableSizes.value[0]
  }
})
</script>

<template>
  <div class="min-h-screen bg-background">

    <main class="container mx-auto px-4 py-8">
      <!-- Loading state -->
      <div v-if="isLoading" class="fixed inset-0 bg-background/80 backdrop-blur-sm z-50 flex items-center justify-center">
        <div class="text-center">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto mb-4"></div>
          <p class="text-muted-foreground">Adding to cart...</p>
        </div>
      </div>

      <!-- Success message -->
      <div
        v-if="showAddedToCart"
        class="fixed top-4 right-4 z-50 bg-green-50 border border-green-200 rounded-lg p-4 shadow-lg flex items-center gap-3"
      >
        <CheckCircle class="h-5 w-5 text-green-600" />
        <div>
          <p class="font-medium text-green-800">Added to cart!</p>
          <p class="text-sm text-green-600">{{ quantity }}x {{ product.name }}</p>
        </div>
      </div>

      <div>
        <!-- Breadcrumb -->
        <nav class="flex items-center space-x-2 text-sm text-muted-foreground mb-8">
          <Link href="/" class="hover:text-primary transition-colors">Home</Link>
          <span>/</span>
          <Link href="/categories" class="hover:text-primary transition-colors">Categories</Link>
          <span>/</span>
          <span v-if="product.category" class="hover:text-primary transition-colors">
            <Link :href="`/categories/${product.category.slug}`">{{ product.category.name }}</Link>
          </span>
          <span v-if="product.category">/</span>
          <span class="text-foreground">{{ product.name }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 xl:gap-12">
          <!-- Product Images -->
          <div class="space-y-4">
            <div class="aspect-square rounded-lg overflow-hidden bg-card border shadow-sm">
              <img
                :src="currentImage"
                :alt="product.name"
                class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                @error="handleImageError"
              />
            </div>
            <div v-if="product.images && product.images.length > 1" class="grid grid-cols-4 gap-2">
              <div
                v-for="(img, i) in product.images"
                :key="i"
                class="aspect-square rounded-lg overflow-hidden bg-card cursor-pointer border-2 transition-all duration-200"
                :class="currentImageIndex === i ? 'border-primary' : 'border-transparent hover:border-muted-foreground/20'"
                @click="selectImage(i)"
              >
                <img
                  :src="`${img}`"
                  :alt="`${product.name} view ${i + 1}`"
                  class="w-full h-full object-cover"
                  @error="handleImageError"
                />
              </div>
            </div>
          </div>

          <!-- Product Info -->
          <div class="space-y-6">
            <div>
              <div class="flex items-center gap-2 mb-3">
                <Badge v-if="product.is_featured" variant="secondary">Featured</Badge>
                <Badge v-if="product.discount > 0" class="bg-destructive text-destructive-foreground">
                  -{{ product.discount_percentage }}%
                </Badge>
                <Badge v-if="!product.has_stock" variant="destructive">Out of Stock</Badge>
              </div>
              <h1 class="text-3xl font-bold mb-3">{{ product.name }}</h1>
              <div class="flex items-center gap-2 mb-4">
                <div class="flex items-center">
                  <Star
                    v-for="i in 5"
                    :key="i"
                    class="h-4 w-4"
                    :class="i <= Math.floor(product.rating) ? 'fill-yellow-400 text-yellow-400' : 'text-muted-foreground'"
                  />
                </div>
                <span class="text-sm text-muted-foreground">
                  {{ product.rating }} ({{ product.reviews_count }} reviews)
                </span>
              </div>
            </div>

            <!-- Price -->
            <div class="flex items-center gap-2">
              <span class="text-3xl font-bold text-primary">${{ (product.final_price || product.price) }}</span>
              <span v-if="product.discount > 0" class="text-lg text-muted-foreground line-through">
                ${{ product.price }}
              </span>
            </div>

            <!-- Stock status -->
            <div class="flex items-center gap-2 text-sm">
              <div class="flex items-center gap-1">
                <div class="w-2 h-2 rounded-full" :class="product.has_stock ? 'bg-green-500' : 'bg-red-500'"></div>
                <span :class="product.has_stock ? 'text-green-600' : 'text-red-600'">
                  {{ product.has_stock ? `${product.total_stock} in stock` : 'Out of stock' }}
                </span>
              </div>
            </div>

            <!-- Description -->
            <p class="text-muted-foreground leading-relaxed">{{ product.description }}</p>

            <!-- Sizes -->
            <div v-if="availableSizes.length > 0">
              <h3 class="font-semibold mb-3">Size</h3>
              <div class="flex flex-wrap gap-2">
                <Button
                  v-for="size in availableSizes"
                  :key="size"
                  :variant="selectedSize === size ? 'default' : 'outline'"
                  size="sm"
                  class="min-w-16"
                  @click="selectedSize = size"
                >
                  {{ size }}
                </Button>
              </div>
              <p v-if="!selectedSize" class="text-sm text-muted-foreground mt-2">
                Please select a size
              </p>
            </div>

            <!-- Quantity -->
            <div>
              <h3 class="font-semibold mb-3">Quantity</h3>
              <div class="flex items-center gap-3">
                <div class="flex items-center border rounded-md">
                  <Button
                    variant="ghost"
                    size="icon"
                    class="h-10 w-10"
                    @click="decrementQuantity"
                    :disabled="quantity <= 1"
                  >
                    <Minus class="h-4 w-4" />
                  </Button>
                  <span class="px-4 py-2 min-w-12 text-center font-medium">{{ quantity }}</span>
                  <Button
                    variant="ghost"
                    size="icon"
                    class="h-10 w-10"
                    @click="incrementQuantity"
                    :disabled="!canIncrement"
                  >
                    <Plus class="h-4 w-4" />
                  </Button>
                </div>
                <span class="text-sm text-muted-foreground">
                  Max: {{ product.total_stock }}
                </span>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-3">
              <Button
                class="flex-1"
                size="lg"
                :disabled="!canAddToCart"
                @click="handleAddToCart"
              >
                <ShoppingCart class="h-5 w-5 mr-2" />
                {{ product.has_stock ? 'Add to Cart' : 'Out of Stock' }}
              </Button>
              <Button
                variant="outline"
                size="lg"
                @click="toggleFavorite"
                :class="isFavorite ? 'text-destructive border-destructive hover:bg-destructive/10' : ''"
              >
                <Heart class="h-5 w-5" :class="isFavorite ? 'fill-current' : ''" />
              </Button>
            </div>

            <!-- Validation messages -->
            <div v-if="!selectedSize && availableSizes.length > 0" class="flex items-center gap-2 text-sm text-amber-600">
              <AlertCircle class="h-4 w-4" />
              <span>Please select a size to add to cart</span>
            </div>

            <!-- Features -->
            <Card class="border-0 shadow-sm">
              <CardContent class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                  <div class="flex flex-col items-center gap-3">
                    <div class="p-3 bg-primary/10 rounded-full">
                      <Truck class="h-6 w-6 text-primary" />
                    </div>
                    <div>
                      <div class="font-semibold">Free Shipping</div>
                      <div class="text-sm text-muted-foreground">On orders over $100</div>
                    </div>
                  </div>
                  <div class="flex flex-col items-center gap-3">
                    <div class="p-3 bg-primary/10 rounded-full">
                      <Shield class="h-6 w-6 text-primary" />
                    </div>
                    <div>
                      <div class="font-semibold">2 Year Warranty</div>
                      <div class="text-sm text-muted-foreground">Official warranty</div>
                    </div>
                  </div>
                  <div class="flex flex-col items-center gap-3">
                    <div class="p-3 bg-primary/10 rounded-full">
                      <RotateCcw class="h-6 w-6 text-primary" />
                    </div>
                    <div>
                      <div class="font-semibold">30-Day Returns</div>
                      <div class="text-sm text-muted-foreground">Easy returns</div>
                    </div>
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>
        </div>

        <!-- Related Products -->
        <div v-if="relatedProducts && relatedProducts.length > 0" class="mt-16">
          <h2 class="text-2xl font-bold mb-8">Related Products</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div
              v-for="relatedProduct in relatedProducts"
              :key="relatedProduct.id"
              class="bg-card border border-border rounded-lg overflow-hidden hover:shadow-md transition-shadow"
            >
              <Link :href="`/products/${relatedProduct.id}`">
                <div class="aspect-square overflow-hidden">
                  <img
                    :src="relatedProduct.images && relatedProduct.images.length > 0 ? `${relatedProduct.images[0]}` : '/images/placeholder-product.svg'"
                    :alt="relatedProduct.name"
                    class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                    @error="handleImageError"
                  />
                </div>
                <div class="p-4">
                  <h3 class="font-semibold text-foreground mb-2 line-clamp-2">{{ relatedProduct.name }}</h3>
                  <p class="text-primary font-bold">${{ relatedProduct.final_price || relatedProduct.price }}</p>
                </div>
              </Link>
            </div>
          </div>
        </div>

        <!-- Tabs -->
        <div class="mt-16">
          <Tabs default-value="details" class="w-full">
            <TabsList class="grid w-full grid-cols-3">
              <TabsTrigger value="details">Details</TabsTrigger>
              <TabsTrigger value="reviews">Reviews ({{ product.reviews_count }})</TabsTrigger>
              <TabsTrigger value="shipping">Shipping</TabsTrigger>
            </TabsList>

            <TabsContent value="details" class="mt-8">
              <Card class="border-0 shadow-sm">
                <CardContent class="p-6">
                  <h3 class="font-semibold mb-6 text-lg">Product Information</h3>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div><span class="font-semibold">SKU:</span> {{ product.sku || 'N/A' }}</div>
                    <div><span class="font-semibold">Category:</span> {{ product.category?.name || 'Uncategorized' }}</div>
                    <div><span class="font-semibold">Brand:</span> {{ product.brand?.name || 'No Brand' }}</div>
                    <div><span class="font-semibold">Stock:</span> {{ product.total_stock }} units</div>
                  </div>
                </CardContent>
              </Card>
            </TabsContent>

            <TabsContent value="reviews" class="mt-8">
              <Card class="border-0 shadow-sm">
                <CardContent class="p-6 text-center py-12">
                  <div class="flex items-center justify-center gap-2 mb-4">
                    <Star class="h-6 w-6 text-yellow-400 fill-current" />
                    <span class="text-2xl font-bold">{{ product.rating }}</span>
                  </div>
                  <h3 class="font-semibold mb-2 text-lg">Customer Reviews</h3>
                  <p class="text-muted-foreground mb-4">{{ product.reviews_count }} verified reviews</p>
                  <Button variant="outline">Write a Review</Button>
                </CardContent>
              </Card>
            </TabsContent>

            <TabsContent value="shipping" class="mt-8">
              <Card class="border-0 shadow-sm">
                <CardContent class="p-6 space-y-6">
                  <h3 class="font-semibold mb-6 text-lg">Shipping Information</h3>
                  <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 border rounded-lg">
                      <div>
                        <div class="font-semibold">Standard Shipping</div>
                        <div class="text-muted-foreground">5-7 business days</div>
                      </div>
                      <div class="text-right">
                        <div class="font-semibold text-green-600">Free</div>
                        <div class="text-sm text-muted-foreground">Orders over $100</div>
                      </div>
                    </div>
                    <div class="flex items-center justify-between p-4 border rounded-lg">
                      <div>
                        <div class="font-semibold">Express Shipping</div>
                        <div class="text-muted-foreground">2-3 business days</div>
                      </div>
                      <div class="text-right">
                        <div class="font-semibold">$15.00</div>
                      </div>
                    </div>
                    <div class="flex items-center justify-between p-4 border rounded-lg">
                      <div>
                        <div class="font-semibold">Next Day Delivery</div>
                        <div class="text-muted-foreground">1 business day</div>
                      </div>
                      <div class="text-right">
                        <div class="font-semibold">$25.00</div>
                      </div>
                    </div>
                  </div>
                </CardContent>
              </Card>
            </TabsContent>
          </Tabs>
        </div>
      </div>
    </main>

  </div>
</template>
