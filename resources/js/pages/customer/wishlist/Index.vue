<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { useWishlist } from '@/composables/useWishlist'
import { useCart } from '@/composables/useCart'
import GuestLayout from '@/layouts/GuestLayout.vue'

// UI components
import Button from '@/components/ui/button/Button.vue'
import Badge from '@/components/ui/badge/Badge.vue'
import Card from '@/components/ui/card/Card.vue'
import CardContent from '@/components/ui/card/CardContent.vue'
import CardHeader from '@/components/ui/card/CardHeader.vue'
import CardTitle from '@/components/ui/card/CardTitle.vue'

// Icons
import {
  Heart,
  ShoppingCart,
  Trash2,
  ArrowLeft,
  Package,
  Star
} from 'lucide-vue-next'

defineOptions({
  layout: GuestLayout,
})

// Props from Inertia
const props = defineProps({
  wishlistItems: {
    type: Array,
    default: () => []
  }
})

// Composables
const { removeFromWishlist, isLoading } = useWishlist()
const { addToCart } = useCart()

// Local state
const showNotification = ref(false)
const notificationMessage = ref('')
const notificationType = ref<'success' | 'error'>('success')

// Methods
const handleRemoveFromWishlist = async (productId: number) => {
  const result = await removeFromWishlist(productId)

  if (result.success) {
    notificationMessage.value = result.message
    notificationType.value = 'success'
    showNotification.value = true

    // Refresh the page to update the wishlist
    router.reload()
  } else {
    notificationMessage.value = result.message
    notificationType.value = 'error'
    showNotification.value = true
  }
}

const handleAddToCart = async (product: any) => {
  const result = await addToCart({
    id: product.id,
    name: product.name,
    price: product.price,
    image: product.main_image,
    quantity: 1
  })

  if (result.success) {
    notificationMessage.value = `${product.name} added to cart!`
    notificationType.value = 'success'
    showNotification.value = true
  } else {
    notificationMessage.value = 'Failed to add to cart'
    notificationType.value = 'error'
    showNotification.value = true
  }
}

const handleNotificationClose = () => {
  showNotification.value = false
}

const continueShopping = () => {
  router.visit('/categories')
}

// Initialize wishlist count on mount
onMounted(() => {
  // The wishlist items are passed from the server
})
</script>

<template>
  <div class="min-h-screen bg-background">
    <!-- Header -->
    <div class="bg-gradient-to-r from-red-500/10 to-pink-500/10 border-b border-border">
      <div class="container mx-auto px-4 py-8">
        <div class="flex items-center gap-3 mb-4">
          <Link href="/" class="text-muted-foreground hover:text-primary">
            Home
          </Link>
          <span class="text-muted-foreground">/</span>
          <span class="text-foreground font-medium">Wishlist</span>
        </div>

        <div class="flex items-center gap-4 mb-6">
          <div class="flex items-center gap-3">
            <div class="p-3 bg-red-500/10 rounded-full">
              <Heart class="w-8 h-8 text-red-500" />
            </div>
            <div>
              <h1 class="text-4xl font-bold mb-2 text-foreground">
                My Wishlist
              </h1>
              <p class="text-muted-foreground text-lg">
                Your saved items for later
              </p>
            </div>
          </div>
        </div>

        <!-- Wishlist Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
          <div class="bg-card border border-border rounded-lg p-4">
            <div class="flex items-center gap-3">
              <Package class="w-6 h-6 text-primary" />
              <div>
                <p class="text-sm text-muted-foreground">Total Items</p>
                <p class="text-2xl font-bold text-foreground">{{ wishlistItems.length }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="container mx-auto px-4 py-8">
      <!-- Empty State -->
      <div v-if="wishlistItems.length === 0" class="text-center py-16">
        <div class="mb-6">
          <Heart class="w-16 h-16 text-muted-foreground mx-auto mb-4" />
          <h2 class="text-2xl font-bold text-foreground mb-2">Your wishlist is empty</h2>
          <p class="text-muted-foreground mb-6">
            Start adding products to your wishlist to save them for later
          </p>
        </div>
        <Button @click="continueShopping" class="bg-primary text-primary-foreground hover:bg-primary/90">
          <ArrowLeft class="w-4 h-4 mr-2" />
          Continue Shopping
        </Button>
      </div>

      <!-- Wishlist Items -->
      <div v-else class="space-y-6">
        <div class="flex items-center justify-between">
          <h2 class="text-2xl font-bold text-foreground">
            Wishlist Items ({{ wishlistItems.length }})
          </h2>
          <Button variant="outline" @click="continueShopping">
            <ArrowLeft class="w-4 h-4 mr-2" />
            Continue Shopping
          </Button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <Card
            v-for="item in wishlistItems"
            :key="item.id"
            class="group cursor-pointer transition-all duration-300 hover:scale-[1.02] bg-card border border-border rounded-xl overflow-hidden shadow-sm hover:shadow-lg"
          >
            <!-- Image Container -->
            <div class="relative overflow-hidden">
              <img
                :src="item.product.images[0] || '/images/placeholder-product.svg'"
                :alt="item.product.name"
                class="w-full h-64 object-cover transition-transform duration-500 group-hover:scale-110"
                @error="$event.target.src = '/images/placeholder-product.svg'"
              />

              <!-- Overlay for better text readability -->
              <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

              <!-- Remove from Wishlist Button -->
              <Button
                variant="ghost"
                size="icon"
                class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm text-red-600 hover:bg-red-500 hover:text-white shadow-sm transition-all duration-300 z-10"
                :disabled="isLoading"
                @click.stop="handleRemoveFromWishlist(item.product.id)"
              >
                <Trash2 class="w-4 h-4" />
              </Button>
            </div>

            <!-- Content -->
            <CardContent class="p-4 space-y-3">
              <!-- Category and Brand -->
              <div class="flex items-center gap-2 text-xs text-muted-foreground">
                <span v-if="item.product.category">{{ item.product.category.name }}</span>
                <span v-if="item.product.category && item.product.brand">•</span>
                <span v-if="item.product.brand">{{ item.product.brand.name }}</span>
              </div>

              <!-- Product Name -->
              <Link :href="`/products/${item.product.id}`" class="block">
                <h3 class="font-semibold text-foreground line-clamp-2 group-hover:text-primary transition-colors">
                  {{ item.product.name }}
                </h3>
              </Link>

              <!-- Rating -->
              <div class="flex items-center gap-1">
                <div class="flex items-center">
                  <Star class="w-4 h-4 fill-yellow-400 text-yellow-400" />
                  <Star class="w-4 h-4 fill-yellow-400 text-yellow-400" />
                  <Star class="w-4 h-4 fill-yellow-400 text-yellow-400" />
                  <Star class="w-4 h-4 fill-yellow-400 text-yellow-400" />
                  <Star class="w-4 h-4 fill-yellow-400 text-yellow-400" />
                </div>
                <span class="text-xs text-muted-foreground">(4.5)</span>
              </div>

              <!-- Price -->
              <div class="flex items-center gap-2">
                <span class="text-lg font-bold text-foreground">
                  ${{ item.product.price  }}
                </span>
              </div>

              <!-- Actions -->
              <div class="flex gap-2 pt-2">
                <Button
                  class="flex-1 bg-primary text-primary-foreground hover:bg-primary/90"
                  :disabled="isLoading"
                  @click.stop="handleAddToCart(item.product)"
                >
                  <ShoppingCart class="w-4 h-4 mr-2" />
                  Add to Cart
                </Button>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>

    <!-- Notification -->
    <div
      v-if="showNotification"
      class="fixed top-4 right-4 z-50 max-w-sm w-full"
    >
      <div
        class="bg-card border border-border rounded-lg shadow-lg p-4 flex items-center gap-3"
        :class="notificationType === 'success' ? 'border-green-500/20 bg-green-500/10' : 'border-red-500/20 bg-red-500/10'"
      >
        <div
          class="w-2 h-2 rounded-full"
          :class="notificationType === 'success' ? 'bg-green-500' : 'bg-red-500'"
        ></div>
        <p
          class="text-sm font-medium"
          :class="notificationType === 'success' ? 'text-green-700' : 'text-red-700'"
        >
          {{ notificationMessage }}
        </p>
        <button
          @click="handleNotificationClose"
          class="ml-auto text-muted-foreground hover:text-foreground"
        >
          ×
        </button>
      </div>
    </div>
  </div>
</template>
