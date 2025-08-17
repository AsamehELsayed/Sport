<script setup>
import { ref, onMounted } from "vue"
import { router } from "@inertiajs/vue3"
import { useCart } from "@/composables/useCart"
import GuestLayout from '@/layouts/GuestLayout.vue'

// UI components
import Button from "@/components/ui/button/Button.vue"
import Badge from "@/components/ui/badge/Badge.vue"
import Card from "@/components/ui/card/Card.vue"
import CardContent from "@/components/ui/card/CardContent.vue"
import CardHeader from "@/components/ui/card/CardHeader.vue"
import CardTitle from "@/components/ui/card/CardTitle.vue"
import Input from "@/components/ui/Input.vue"

// Icons
import {
  ShoppingCart,
  Minus,
  Plus,
  Trash2,
  ArrowLeft,
  CreditCard,
  Truck,
  Shield,
  RotateCcw,
  Heart,
  Star
} from "lucide-vue-next"

// Assets
import runningShoes from "@/assets/running-shoes.jpg"
import basketball from "@/assets/basketball.jpg"
import soccerBall from "@/assets/soccer-ball.jpg"

defineOptions({
  layout: GuestLayout,
})

// Cart composable
const {
  cartItems,
  savedItems,
  itemCount,
  subtotal,
  totalSavings,
  shipping,
  tax,
  total,
  canCheckout,
  updateQuantity,
  removeFromCart,
  moveToSaved,
  moveToCart,
  removeFromSaved,
  clearCart
} = useCart()

const isLoading = ref(false)
const showEmptyCart = ref(false)

// Methods
function handleUpdateQuantity(itemId, size, newQuantity) {
  updateQuantity(itemId, size, newQuantity)
  if (cartItems.value.length === 0) {
    showEmptyCart.value = true
  }
}

function handleRemoveFromCart(itemId, size) {
  removeFromCart(itemId, size)
  if (cartItems.value.length === 0) {
    showEmptyCart.value = true
  }
}

function handleMoveToSaved(itemId, size) {
  moveToSaved(itemId, size)
}

function handleMoveToCart(itemId, size) {
  moveToCart(itemId, size)
}

function handleRemoveFromSaved(itemId, size) {
  removeFromSaved(itemId, size)
}

function continueShopping() {
  router.visit('/categories')
}

function proceedToCheckout() {
  isLoading.value = true
  // Simulate checkout process
  setTimeout(() => {
    router.visit('/checkout')
  }, 1000)
}

function handleClearCart() {
  clearCart()
  showEmptyCart.value = true
}

// Lifecycle
onMounted(() => {
  if (cartItems.value.length === 0) {
    showEmptyCart.value = true
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
          <p class="text-muted-foreground">Processing...</p>
        </div>
      </div>

      <!-- Breadcrumb -->
      <nav class="flex items-center space-x-2 text-sm text-muted-foreground mb-8">
        <a href="/" class="hover:text-primary transition-colors">Home</a>
        <span>/</span>
        <span class="text-foreground">Shopping Cart</span>
      </nav>

      <!-- Empty cart state -->
      <div v-if="showEmptyCart" class="text-center py-16">
        <div class="mx-auto w-24 h-24 bg-muted rounded-full flex items-center justify-center mb-6">
          <ShoppingCart class="w-12 h-12 text-muted-foreground" />
        </div>
        <h2 class="text-2xl font-bold mb-4">Your cart is empty</h2>
        <p class="text-muted-foreground mb-8 max-w-md mx-auto">
          Looks like you haven't added any items to your cart yet. Start shopping to find great deals on sports equipment!
        </p>
        <div class="flex gap-4 justify-center">
          <Button @click="continueShopping" size="lg">
            <ArrowLeft class="w-5 h-5 mr-2" />
            Continue Shopping
          </Button>
        </div>
      </div>

      <!-- Cart content -->
      <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Cart items -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Cart header -->
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-3xl font-bold">Shopping Cart</h1>
              <p class="text-muted-foreground">{{ itemCount }} item{{ itemCount !== 1 ? 's' : '' }}</p>
            </div>
                         <Button variant="outline" @click="handleClearCart" class="text-destructive hover:text-destructive">
               <Trash2 class="w-4 h-4 mr-2" />
               Clear Cart
             </Button>
          </div>

          <!-- Cart items list -->
          <div class="space-y-4">
            <Card v-for="item in cartItems" :key="item.id" class="border-0 shadow-sm">
              <CardContent class="p-6">
                <div class="flex gap-4">
                  <!-- Product image -->
                  <div class="w-24 h-24 rounded-lg overflow-hidden bg-card border flex-shrink-0">
                    <img
                      :src="item.image"
                      :alt="item.name"
                      class="w-full h-full object-cover"
                    />
                  </div>

                  <!-- Product details -->
                  <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between">
                      <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-lg mb-1 truncate">{{ item.name }}</h3>
                        <div class="flex items-center gap-2 mb-2">
                          <Badge variant="secondary">{{ item.category }}</Badge>
                          <span class="text-sm text-muted-foreground">Size: {{ item.size }}</span>
                        </div>
                        <div class="flex items-center gap-2 mb-3">
                          <div class="flex items-center">
                            <Star
                              v-for="i in 5"
                              :key="i"
                              class="h-3 w-3"
                              :class="i <= Math.floor(item.rating) ? 'fill-yellow-400 text-yellow-400' : 'text-muted-foreground'"
                            />
                          </div>
                          <span class="text-sm text-muted-foreground">
                            {{ item.rating }} ({{ item.reviews }})
                          </span>
                        </div>

                        <!-- Stock status -->
                        <div class="flex items-center gap-2 text-sm mb-3">
                          <div class="flex items-center gap-1">
                            <div class="w-2 h-2 rounded-full" :class="item.inStock ? 'bg-green-500' : 'bg-red-500'"></div>
                            <span :class="item.inStock ? 'text-green-600' : 'text-red-600'">
                              {{ item.inStock ? `${item.stockQuantity} in stock` : 'Out of stock' }}
                            </span>
                          </div>
                        </div>
                      </div>

                      <!-- Price -->
                      <div class="text-right ml-4">
                        <div class="flex items-center gap-2 mb-1">
                          <span class="text-xl font-bold text-primary">${{ item.price.toFixed(2) }}</span>
                          <span v-if="item.originalPrice" class="text-sm text-muted-foreground line-through">
                            ${{ item.originalPrice.toFixed(2) }}
                          </span>
                        </div>
                        <div v-if="item.originalPrice" class="text-sm text-green-600 font-medium">
                          Save ${{ ((item.originalPrice - item.price) * item.quantity).toFixed(2) }}
                        </div>
                      </div>
                    </div>

                    <!-- Quantity controls and actions -->
                    <div class="flex items-center justify-between mt-4">
                      <div class="flex items-center gap-3">
                        <div class="flex items-center border rounded-md">
                                                     <Button
                             variant="ghost"
                             size="icon"
                             class="h-8 w-8"
                             @click="handleUpdateQuantity(item.id, item.size, item.quantity - 1)"
                             :disabled="item.quantity <= 1"
                           >
                             <Minus class="h-4 w-4" />
                           </Button>
                           <span class="px-3 py-1 min-w-8 text-center font-medium">{{ item.quantity }}</span>
                           <Button
                             variant="ghost"
                             size="icon"
                             class="h-8 w-8"
                             @click="handleUpdateQuantity(item.id, item.size, item.quantity + 1)"
                             :disabled="!item.inStock || item.quantity >= item.stockQuantity"
                           >
                             <Plus class="h-4 w-4" />
                           </Button>
                        </div>
                        <span class="text-sm text-muted-foreground">
                          Max: {{ item.stockQuantity }}
                        </span>
                      </div>

                      <div class="flex items-center gap-2">
                                                 <Button
                           variant="outline"
                           size="sm"
                           @click="handleMoveToSaved(item.id, item.size)"
                         >
                           <Heart class="w-4 h-4 mr-2" />
                           Save for later
                         </Button>
                         <Button
                           variant="outline"
                           size="sm"
                           @click="handleRemoveFromCart(item.id, item.size)"
                           class="text-destructive hover:text-destructive"
                         >
                           <Trash2 class="w-4 h-4 mr-2" />
                           Remove
                         </Button>
                      </div>
                    </div>
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>

          <!-- Saved items -->
          <div v-if="savedItems.length > 0" class="mt-12">
            <h2 class="text-xl font-bold mb-4">Saved for Later ({{ savedItems.length }})</h2>
            <div class="space-y-4">
              <Card v-for="item in savedItems" :key="item.id" class="border-0 shadow-sm">
                <CardContent class="p-6">
                  <div class="flex gap-4">
                    <div class="w-24 h-24 rounded-lg overflow-hidden bg-card border flex-shrink-0">
                      <img
                        :src="item.image"
                        :alt="item.name"
                        class="w-full h-full object-cover"
                      />
                    </div>
                    <div class="flex-1 min-w-0">
                      <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                          <h3 class="font-semibold text-lg mb-1 truncate">{{ item.name }}</h3>
                          <div class="flex items-center gap-2 mb-2">
                            <Badge variant="secondary">{{ item.category }}</Badge>
                            <span class="text-sm text-muted-foreground">Size: {{ item.size }}</span>
                          </div>
                          <div class="flex items-center gap-2 mb-3">
                            <div class="flex items-center">
                              <Star
                                v-for="i in 5"
                                :key="i"
                                class="h-3 w-3"
                                :class="i <= Math.floor(item.rating) ? 'fill-yellow-400 text-yellow-400' : 'text-muted-foreground'"
                              />
                            </div>
                            <span class="text-sm text-muted-foreground">
                              {{ item.rating }} ({{ item.reviews }})
                            </span>
                          </div>
                        </div>
                        <div class="text-right ml-4">
                          <span class="text-xl font-bold text-primary">${{ item.price.toFixed(2) }}</span>
                        </div>
                      </div>
                      <div class="flex items-center gap-2 mt-4">
                                                 <Button
                           variant="outline"
                           size="sm"
                           @click="handleMoveToCart(item.id, item.size)"
                         >
                           <ShoppingCart class="w-4 h-4 mr-2" />
                           Move to cart
                         </Button>
                         <Button
                           variant="outline"
                           size="sm"
                           @click="handleRemoveFromSaved(item.id, item.size)"
                           class="text-destructive hover:text-destructive"
                         >
                           <Trash2 class="w-4 h-4 mr-2" />
                           Remove
                         </Button>
                      </div>
                    </div>
                  </div>
                </CardContent>
              </Card>
            </div>
          </div>
        </div>

        <!-- Order summary -->
        <div class="lg:col-span-1">
          <div class="sticky top-8">
            <Card class="border-0 shadow-sm">
              <CardHeader>
                <CardTitle>Order Summary</CardTitle>
              </CardHeader>
              <CardContent class="space-y-4">
                <!-- Summary details -->
                <div class="space-y-3">
                  <div class="flex justify-between">
                    <span class="text-muted-foreground">Subtotal ({{ itemCount }} items)</span>
                    <span>${{ subtotal.toFixed(2) }}</span>
                  </div>
                  <div v-if="totalSavings > 0" class="flex justify-between text-green-600">
                    <span>Total Savings</span>
                    <span>-${{ totalSavings.toFixed(2) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-muted-foreground">Shipping</span>
                    <span v-if="shipping === 0" class="text-green-600">Free</span>
                    <span v-else>${{ shipping.toFixed(2) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-muted-foreground">Tax</span>
                    <span>${{ tax.toFixed(2) }}</span>
                  </div>
                  <div class="border-t pt-3">
                    <div class="flex justify-between text-lg font-bold">
                      <span>Total</span>
                      <span>${{ total.toFixed(2) }}</span>
                    </div>
                  </div>
                </div>

                <!-- Shipping info -->
                <div v-if="shipping > 0" class="text-sm text-muted-foreground text-center p-3 bg-muted/50 rounded-lg">
                  Add ${{ (100 - subtotal).toFixed(2) }} more for free shipping
                </div>

                <!-- Checkout button -->
                <Button
                  class="w-full"
                  size="lg"
                  :disabled="!canCheckout"
                  @click="proceedToCheckout"
                >
                  <CreditCard class="w-5 h-5 mr-2" />
                  Proceed to Checkout
                </Button>

                <!-- Continue shopping -->
                <Button
                  variant="outline"
                  class="w-full"
                  @click="continueShopping"
                >
                  <ArrowLeft class="w-5 h-5 mr-2" />
                  Continue Shopping
                </Button>

                <!-- Trust badges -->
                <div class="grid grid-cols-3 gap-4 pt-4 border-t">
                  <div class="text-center">
                    <div class="p-2 bg-primary/10 rounded-full w-8 h-8 mx-auto mb-2 flex items-center justify-center">
                      <Truck class="w-4 h-4 text-primary" />
                    </div>
                    <div class="text-xs text-muted-foreground">Free Shipping</div>
                  </div>
                  <div class="text-center">
                    <div class="p-2 bg-primary/10 rounded-full w-8 h-8 mx-auto mb-2 flex items-center justify-center">
                      <Shield class="w-4 h-4 text-primary" />
                    </div>
                    <div class="text-xs text-muted-foreground">Secure Checkout</div>
                  </div>
                  <div class="text-center">
                    <div class="p-2 bg-primary/10 rounded-full w-8 h-8 mx-auto mb-2 flex items-center justify-center">
                      <RotateCcw class="w-4 h-4 text-primary" />
                    </div>
                    <div class="text-xs text-muted-foreground">Easy Returns</div>
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>
