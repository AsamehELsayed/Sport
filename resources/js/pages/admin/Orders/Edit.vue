<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-foreground">Edit Order {{ order.order_number }}</h1>
        <p class="text-muted-foreground">Update order status and information</p>
      </div>
      <div class="flex items-center space-x-2">
        <Link href="/admin/orders" class="text-sm text-primary hover:underline">
          ← Back to Orders
        </Link>
        <Link
          :href="`/admin/orders/${order.id}`"
          class="text-sm text-primary hover:underline"
        >
          View Order
        </Link>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Edit Form -->
      <div class="lg:col-span-2">
        <Card>
          <CardHeader>
            <CardTitle>Update Order</CardTitle>
          </CardHeader>
          <CardContent>
            <form @submit.prevent="updateOrder" class="space-y-6">
              <!-- Order Status -->
              <div>
                <label class="block text-sm font-medium text-foreground mb-2">
                  Order Status
                </label>
                <select
                  v-model="form.status"
                  class="w-full px-3 py-2 border border-border rounded-md bg-background"
                >
                  <option value="pending">Pending</option>
                  <option value="processing">Processing</option>
                  <option value="shipped">Shipped</option>
                  <option value="delivered">Delivered</option>
                  <option value="cancelled">Cancelled</option>
                </select>
                <p v-if="errors.status" class="text-sm text-red-600 mt-1">{{ errors.status }}</p>
              </div>

              <!-- Payment Status -->
              <div>
                <label class="block text-sm font-medium text-foreground mb-2">
                  Payment Status
                </label>
                <select
                  v-model="form.payment_status"
                  class="w-full px-3 py-2 border border-border rounded-md bg-background"
                >
                  <option value="pending">Pending</option>
                  <option value="paid">Paid</option>
                  <option value="failed">Failed</option>
                  <option value="refunded">Refunded</option>
                </select>
                <p v-if="errors.payment_status" class="text-sm text-red-600 mt-1">{{ errors.payment_status }}</p>
              </div>

              <!-- Transaction ID -->
              <div>
                <label class="block text-sm font-medium text-foreground mb-2">
                  Transaction ID
                </label>
                <Input
                  v-model="form.transaction_id"
                  placeholder="Enter transaction ID"
                  class="w-full"
                />
                <p v-if="errors.transaction_id" class="text-sm text-red-600 mt-1">{{ errors.transaction_id }}</p>
              </div>

              <!-- Admin Notes -->
              <div>
                <label class="block text-sm font-medium text-foreground mb-2">
                  Admin Notes
                </label>
                <textarea
                  v-model="form.admin_notes"
                  rows="4"
                  placeholder="Add internal notes about this order..."
                  class="w-full px-3 py-2 border border-border rounded-md bg-background resize-none"
                ></textarea>
                <p v-if="errors.admin_notes" class="text-sm text-red-600 mt-1">{{ errors.admin_notes }}</p>
              </div>

              <!-- Submit Button -->
              <div class="flex items-center space-x-2">
                <Button type="submit" :disabled="isLoading">
                  <LoaderCircle v-if="isLoading" class="w-4 h-4 mr-2 animate-spin" />
                  Update Order
                </Button>
                <Button type="button" variant="outline" @click="resetForm">
                  Reset
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>

      <!-- Order Summary -->
      <div class="space-y-6">
        <!-- Current Order Info -->
        <Card>
          <CardHeader>
            <CardTitle>Current Order Info</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="space-y-3">
              <div class="flex items-center justify-between">
                <span class="text-sm text-muted-foreground">Order Number</span>
                <span class="text-sm font-medium">{{ order.order_number }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-sm text-muted-foreground">Customer</span>
                <span class="text-sm font-medium">{{ order.customer_name }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-sm text-muted-foreground">Total Amount</span>
                <span class="text-sm font-medium">{{ order.formatted_total }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-sm text-muted-foreground">Created</span>
                <span class="text-sm font-medium">{{ formatDate(order.created_at) }}</span>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Status History -->
        <Card>
          <CardHeader>
            <CardTitle>Status History</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="space-y-3">
              <div class="flex items-start space-x-3">
                <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                <div>
                  <p class="text-sm font-medium text-foreground">Order Created</p>
                  <p class="text-xs text-muted-foreground">{{ formatDateTime(order.created_at) }}</p>
                </div>
              </div>

              <div v-if="order.processed_at" class="flex items-start space-x-3">
                <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                <div>
                  <p class="text-sm font-medium text-foreground">Order Processed</p>
                  <p class="text-xs text-muted-foreground">{{ formatDateTime(order.processed_at) }}</p>
                </div>
              </div>

              <div v-if="order.shipped_at" class="flex items-start space-x-3">
                <div class="w-2 h-2 bg-purple-500 rounded-full mt-2"></div>
                <div>
                  <p class="text-sm font-medium text-foreground">Order Shipped</p>
                  <p class="text-xs text-muted-foreground">{{ formatDateTime(order.shipped_at) }}</p>
                </div>
              </div>

              <div v-if="order.delivered_at" class="flex items-start space-x-3">
                <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                <div>
                  <p class="text-sm font-medium text-foreground">Order Delivered</p>
                  <p class="text-xs text-muted-foreground">{{ formatDateTime(order.delivered_at) }}</p>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Current Notes -->
        <Card v-if="order.notes || order.admin_notes">
          <CardHeader>
            <CardTitle>Current Notes</CardTitle>
          </CardHeader>
          <CardContent>
            <div v-if="order.notes" class="mb-4">
              <h4 class="font-medium text-foreground mb-2">Customer Notes</h4>
              <p class="text-sm text-muted-foreground">{{ order.notes }}</p>
            </div>
            <div v-if="order.admin_notes">
              <h4 class="font-medium text-foreground mb-2">Admin Notes</h4>
              <p class="text-sm text-muted-foreground">{{ order.admin_notes }}</p>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { LoaderCircle } from 'lucide-vue-next'
import Card from '@/components/ui/card/Card.vue'
import CardContent from '@/components/ui/card/CardContent.vue'
import CardHeader from '@/components/ui/card/CardHeader.vue'
import CardTitle from '@/components/ui/card/CardTitle.vue'
import Button from '@/components/ui/button/Button.vue'
import Input from '@/components/ui/Input.vue'
import AdminLayout from '@/layouts/AdminLayout.vue'

defineOptions({
  layout: AdminLayout,
})

const props = defineProps({
  order: Object,
  errors: Object,
})

const isLoading = ref(false)

const form = ref({
  status: props.order.status,
  payment_status: props.order.payment_status,
  transaction_id: props.order.transaction_id || '',
  admin_notes: props.order.admin_notes || '',
})

const updateOrder = () => {
  isLoading.value = true

  router.put(`/admin/orders/${props.order.id}`, form.value, {
    onSuccess: () => {
      isLoading.value = false
    },
    onError: () => {
      isLoading.value = false
    }
  })
}

const resetForm = () => {
  form.value = {
    status: props.order.status,
    payment_status: props.order.payment_status,
    transaction_id: props.order.transaction_id || '',
    admin_notes: props.order.admin_notes || '',
  }
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}

const formatDateTime = (date) => {
  return new Date(date).toLocaleString()
}
</script>
