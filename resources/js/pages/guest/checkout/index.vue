<script setup>
import { ref, computed } from "vue"
import { router } from "@inertiajs/vue3"
import { useCart } from "@/composables/useCart"
import GuestLayout from '@/layouts/GuestLayout.vue'

// UI components
import Button from "@/components/ui/button/Button.vue"
import Card from "@/components/ui/card/Card.vue"
import CardContent from "@/components/ui/card/CardContent.vue"
import CardHeader from "@/components/ui/card/CardHeader.vue"
import CardTitle from "@/components/ui/card/CardTitle.vue"
import Input from "@/components/ui/Input.vue"

// Icons
import {
  ArrowLeft,
  CreditCard,
  Lock,
  Shield,
  Truck,
  RotateCcw,
  CheckCircle
} from "lucide-vue-next"

defineOptions({
  layout: GuestLayout,
})

const { cartItems, subtotal, totalSavings, shipping, tax, total, clearCart } = useCart()

const isLoading = ref(false)
const showSuccess = ref(false)

// Form data
const form = ref({
  email: '',
  firstName: '',
  lastName: '',
  address: '',
  city: '',
  state: '',
  zipCode: '',
  cardNumber: '',
  expiryDate: '',
  cvv: '',
  nameOnCard: ''
})

const errors = ref({})

// Computed
const itemCount = computed(() => {
  return cartItems.value.reduce((sum, item) => sum + item.quantity, 0)
})

// Methods
function validateForm() {
  errors.value = {}

  if (!form.value.email) errors.value.email = 'Email is required'
  if (!form.value.firstName) errors.value.firstName = 'First name is required'
  if (!form.value.lastName) errors.value.lastName = 'Last name is required'
  if (!form.value.address) errors.value.address = 'Address is required'
  if (!form.value.city) errors.value.city = 'City is required'
  if (!form.value.state) errors.value.state = 'State is required'
  if (!form.value.zipCode) errors.value.zipCode = 'ZIP code is required'
  if (!form.value.cardNumber) errors.value.cardNumber = 'Card number is required'
  if (!form.value.expiryDate) errors.value.expiryDate = 'Expiry date is required'
  if (!form.value.cvv) errors.value.cvv = 'CVV is required'
  if (!form.value.nameOnCard) errors.value.nameOnCard = 'Name on card is required'

  return Object.keys(errors.value).length === 0
}

function handleSubmit() {
  if (!validateForm()) return

  isLoading.value = true

  // Prepare order data
  const orderData = {
    customer_name: `${form.value.firstName} ${form.value.lastName}`,
    customer_email: form.value.email,
    customer_phone: '',
    shipping_address_line_1: form.value.address,
    shipping_address_line_2: '',
    shipping_city: form.value.city,
    shipping_state: form.value.state,
    shipping_postal_code: form.value.zipCode,
    shipping_country: 'United States',
    payment_method: 'credit_card',
    notes: '',
    items: cartItems.value.map(item => ({
      id: item.id,
      name: item.name,
      price: item.price,
      quantity: item.quantity,
      size: item.size
    })),
    subtotal: subtotal.value,
    tax: tax.value,
    shipping: shipping.value,
    total: total.value
  }

  // Submit order
  router.post('/orders', orderData, {
    onSuccess: () => {
      isLoading.value = false
      showSuccess.value = true
      clearCart()
    },
    onError: (errors) => {
      isLoading.value = false
      // Handle errors if needed
    }
  })
}

function goBack() {
  router.visit('/cart')
}
</script>

<template>
  <div class="min-h-screen bg-background">
    <main class="container mx-auto px-4 py-8">
      <!-- Loading state -->
      <div v-if="isLoading" class="fixed inset-0 bg-background/80 backdrop-blur-sm z-50 flex items-center justify-center">
        <div class="text-center">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto mb-4"></div>
          <p class="text-muted-foreground">Processing your order...</p>
        </div>
      </div>

      <!-- Success state -->
      <div v-if="showSuccess" class="text-center py-16">
        <div class="mx-auto w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mb-6">
          <CheckCircle class="w-12 h-12 text-green-600" />
        </div>
        <h2 class="text-2xl font-bold mb-4">Order Confirmed!</h2>
        <p class="text-muted-foreground mb-8 max-w-md mx-auto">
          Thank you for your purchase! You will receive an email confirmation shortly.
        </p>
        <p class="text-sm text-muted-foreground">Redirecting to home page...</p>
      </div>

      <!-- Checkout form -->
      <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Checkout form -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Header -->
          <div class="flex items-center gap-4">
            <Button variant="outline" size="sm" @click="goBack">
              <ArrowLeft class="w-4 h-4 mr-2" />
              Back to Cart
            </Button>
            <div>
              <h1 class="text-3xl font-bold">Checkout</h1>
              <p class="text-muted-foreground">{{ itemCount }} item{{ itemCount !== 1 ? 's' : '' }}</p>
            </div>
          </div>

          <!-- Contact Information -->
          <Card class="border-0 shadow-sm">
            <CardHeader>
              <CardTitle>Contact Information</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
              <div>
                <label class="block text-sm font-medium mb-2">Email</label>
                <Input
                  v-model="form.email"
                  type="email"
                  placeholder="your@email.com"
                  :class="{ 'border-red-500': errors.email }"
                />
                <p v-if="errors.email" class="text-sm text-red-600 mt-1">{{ errors.email }}</p>
              </div>
            </CardContent>
          </Card>

          <!-- Shipping Information -->
          <Card class="border-0 shadow-sm">
            <CardHeader>
              <CardTitle>Shipping Information</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium mb-2">First Name</label>
                  <Input
                    v-model="form.firstName"
                    placeholder="John"
                    :class="{ 'border-red-500': errors.firstName }"
                  />
                  <p v-if="errors.firstName" class="text-sm text-red-600 mt-1">{{ errors.firstName }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium mb-2">Last Name</label>
                  <Input
                    v-model="form.lastName"
                    placeholder="Doe"
                    :class="{ 'border-red-500': errors.lastName }"
                  />
                  <p v-if="errors.lastName" class="text-sm text-red-600 mt-1">{{ errors.lastName }}</p>
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium mb-2">Address</label>
                <Input
                  v-model="form.address"
                  placeholder="123 Main St"
                  :class="{ 'border-red-500': errors.address }"
                />
                <p v-if="errors.address" class="text-sm text-red-600 mt-1">{{ errors.address }}</p>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label class="block text-sm font-medium mb-2">City</label>
                  <Input
                    v-model="form.city"
                    placeholder="New York"
                    :class="{ 'border-red-500': errors.city }"
                  />
                  <p v-if="errors.city" class="text-sm text-red-600 mt-1">{{ errors.city }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium mb-2">State</label>
                  <Input
                    v-model="form.state"
                    placeholder="NY"
                    :class="{ 'border-red-500': errors.state }"
                  />
                  <p v-if="errors.state" class="text-sm text-red-600 mt-1">{{ errors.state }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium mb-2">ZIP Code</label>
                  <Input
                    v-model="form.zipCode"
                    placeholder="10001"
                    :class="{ 'border-red-500': errors.zipCode }"
                  />
                  <p v-if="errors.zipCode" class="text-sm text-red-600 mt-1">{{ errors.zipCode }}</p>
                </div>
              </div>
            </CardContent>
          </Card>

          <!-- Payment Information -->
          <Card class="border-0 shadow-sm">
            <CardHeader>
              <CardTitle class="flex items-center gap-2">
                <CreditCard class="w-5 h-5" />
                Payment Information
              </CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
              <div>
                <label class="block text-sm font-medium mb-2">Card Number</label>
                <Input
                  v-model="form.cardNumber"
                  placeholder="1234 5678 9012 3456"
                  :class="{ 'border-red-500': errors.cardNumber }"
                />
                <p v-if="errors.cardNumber" class="text-sm text-red-600 mt-1">{{ errors.cardNumber }}</p>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label class="block text-sm font-medium mb-2">Expiry Date</label>
                  <Input
                    v-model="form.expiryDate"
                    placeholder="MM/YY"
                    :class="{ 'border-red-500': errors.expiryDate }"
                  />
                  <p v-if="errors.expiryDate" class="text-sm text-red-600 mt-1">{{ errors.expiryDate }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium mb-2">CVV</label>
                  <Input
                    v-model="form.cvv"
                    placeholder="123"
                    :class="{ 'border-red-500': errors.cvv }"
                  />
                  <p v-if="errors.cvv" class="text-sm text-red-600 mt-1">{{ errors.cvv }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium mb-2">Name on Card</label>
                  <Input
                    v-model="form.nameOnCard"
                    placeholder="John Doe"
                    :class="{ 'border-red-500': errors.nameOnCard }"
                  />
                  <p v-if="errors.nameOnCard" class="text-sm text-red-600 mt-1">{{ errors.nameOnCard }}</p>
                </div>
              </div>
            </CardContent>
          </Card>
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

                <!-- Place order button -->
                <Button
                  class="w-full"
                  size="lg"
                  @click="handleSubmit"
                  :disabled="cartItems.length === 0"
                >
                  <Lock class="w-5 h-5 mr-2" />
                  Place Order
                </Button>

                <!-- Trust badges -->
                <div class="grid grid-cols-3 gap-4 pt-4 border-t">
                  <div class="text-center">
                    <div class="p-2 bg-primary/10 rounded-full w-8 h-8 mx-auto mb-2 flex items-center justify-center">
                      <Shield class="w-4 h-4 text-primary" />
                    </div>
                    <div class="text-xs text-muted-foreground">Secure</div>
                  </div>
                  <div class="text-center">
                    <div class="p-2 bg-primary/10 rounded-full w-8 h-8 mx-auto mb-2 flex items-center justify-center">
                      <Truck class="w-4 h-4 text-primary" />
                    </div>
                    <div class="text-xs text-muted-foreground">Fast Shipping</div>
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
