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
import ProductImages from '@/components/guest/ProductImages.vue';
import ProductInfo from '@/components/guest/ProductInfo.vue';

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
const selectedColor = ref("")
const selectedVariant = ref(null)
const isFavorite = ref(false)
const currentImageIndex = ref(0)
const isLoading = ref(false)
const showAddedToCart = ref(false)

// Computed properties
const currentImage = computed(() => {
  // If a variant is selected and has images, use variant images
  if (selectedVariant.value && selectedVariant.value.has_images) {
    return selectedVariant.value.main_image
  }

  // If no variant selected, try to get default variant images
  const defaultVariant = availableVariants.value.find(v => v.is_default && v.has_images)
  if (defaultVariant) {
    return defaultVariant.main_image
  }

  // Fallback to placeholder
  return '/images/placeholder-product.jpg'
})

const availableImages = computed(() => {
  // If a variant is selected and has images, use variant images
  if (selectedVariant.value && selectedVariant.value.has_images) {
    return selectedVariant.value.image_urls
  }

  // If no variant selected, try to get default variant images
  const defaultVariant = availableVariants.value.find(v => v.is_default && v.has_images)
  if (defaultVariant) {
    return defaultVariant.image_urls
  }

  // Fallback to placeholder
  return ['/images/placeholder-product.jpg']
})

const canIncrement = computed(() => {
  if (selectedVariant.value) {
    return quantity.value < selectedVariant.value.stock
  }
  return quantity.value < props.product?.total_stock
})

const canAddToCart = computed(() => {
  if (!props.product?.has_stock) return false

  if (selectedVariant.value) {
    return selectedVariant.value.stock > 0 && quantity.value > 0 && quantity.value <= selectedVariant.value.stock
  }

  return selectedSize.value && selectedColor.value && quantity.value > 0 && quantity.value <= props.product?.total_stock
})

const availableSizes = computed(() => {
  return props.product?.available_sizes || []
})

const availableColors = computed(() => {
  return props.product?.available_colors || []
})

const availableVariants = computed(() => {
  return props.product?.variants?.filter(v => v.is_active && v.stock > 0) || []
})

const selectedPrice = computed(() => {
  if (selectedVariant.value) {
    const basePrice = parseFloat(props.product.price) || 0
    const priceAdjustment = parseFloat(selectedVariant.value.price_adjustment) || 0
    return (basePrice + priceAdjustment).toFixed(2)
  }
  return props.product?.final_price || props.product?.price
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

function selectVariant(variant) {
  selectedVariant.value = variant
  selectedSize.value = variant.size
  selectedColor.value = variant.color
  currentImageIndex.value = 0 // Reset to first image
}

function selectColor(color) {
  selectedColor.value = color
  selectedSize.value = "" // Reset size when color changes
  selectedVariant.value = null // Reset variant when color changes

  // Find the first available variant with this color to auto-select if only one size
  const variantsWithColor = availableVariants.value.filter(v => v.color === color && v.stock > 0)
  if (variantsWithColor.length === 1) {
    selectVariant(variantsWithColor[0])
  }
}

function selectSize(size) {
  selectedSize.value = size
  // Find variant with this size and current color
  const variant = availableVariants.value.find(v =>
    v.size === size &&
    v.color === selectedColor.value
  )
  if (variant) {
    selectVariant(variant)
  } else {
    selectedVariant.value = null
  }
}

function handleAddToCart() {
  if (!canAddToCart.value) return

  isLoading.value = true

  // Add item to cart
  const cartItem = {
    id: props.product.id,
    name: props.product.name,
    price: parseFloat(selectedPrice.value),
    originalPrice: props.product.discount > 0 ? props.product.price : null,
    image: currentImage.value,
    size: selectedVariant.value?.size || selectedSize.value,
    color: selectedVariant.value?.color || selectedColor.value,
    inStock: props.product.has_stock,
    stockQuantity: selectedVariant.value?.stock || props.product.total_stock,
    rating: props.product.rating,
    reviews: props.product.reviews_count,
    category: props.product.category?.name,
    brand: props.product.brand?.name,
    variantId: selectedVariant.value?.id
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
  // Auto-select first available color to show photos
  if (availableColors.value.length > 0 && !selectedColor.value) {
    selectColor(availableColors.value[0])
  }

  // Auto-select default variant if available
  const defaultVariant = props.product?.variants?.find(v => v.is_default && v.is_active && v.stock > 0)
  if (defaultVariant) {
    selectVariant(defaultVariant)
  } else if (availableSizes.value.length === 1 && availableColors.value.length === 1) {
    // Auto-select if only one size and color available
    const variant = availableVariants.value.find(v =>
      v.size === availableSizes.value[0] && v.color === availableColors.value[0]
    )
    if (variant) {
      selectVariant(variant)
    }
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
          <!-- Product Images Section -->
          <ProductImages
            :current-image="currentImage"
            :available-images="availableImages"
            :current-image-index="currentImageIndex"
            :product-name="product.name"
            @select-image="selectImage"
            @image-error="handleImageError"
          />

          <!-- Product Info Section -->
          <ProductInfo
            :product="product"
            :selected-price="selectedPrice"
            :selected-variant="selectedVariant"
            :available-sizes="availableSizes"
            :available-colors="availableColors"
            :available-variants="availableVariants"
            :selected-size="selectedSize"
            :selected-color="selectedColor"
            :quantity="quantity"
            :can-increment="canIncrement"
            :can-add-to-cart="canAddToCart"
            :is-favorite="isFavorite"
            @select-size="selectSize"
            @select-color="selectColor"
            @select-variant="selectVariant"
            @increment-quantity="incrementQuantity"
            @decrement-quantity="decrementQuantity"
            @add-to-cart="handleAddToCart"
            @toggle-favorite="toggleFavorite"
          />
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
                    :src="relatedProduct.main_image || '/images/placeholder-product.jpg'"
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
