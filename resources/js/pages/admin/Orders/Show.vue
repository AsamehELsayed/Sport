<template>
    <div class="space-y-6">
      <!-- Page Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-foreground">Order {{ order.order_number }}</h1>
          <p class="text-muted-foreground">Order details and tracking</p>
        </div>
        <div class="flex items-center space-x-3">
          <a :href="route('admin.orders.generate-invoice', order.id)">
            <Button variant="outline">Generate Invoice</Button>
          </a>
          <Link :href="route('admin.orders.index')" class="text-sm text-primary hover:underline">
            ← Back to Orders
          </Link>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Details -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Order Status -->
          <Card>
            <CardHeader>
              <CardTitle>Order Status</CardTitle>
            </CardHeader>
            <CardContent>
              <div class="flex items-center justify-between">
                <div>
                  <Badge :variant="getStatusVariant(order.status)" class="mb-2">
                    {{ order.status_label }}
                  </Badge>
                  <p class="text-sm text-muted-foreground">
                    Placed on {{ formatDate(order.created_at) }}
                  </p>
                </div>
                <div class="text-right">
                  <p class="text-2xl font-bold text-foreground">{{ order.formatted_total }}</p>
                  <p class="text-sm text-muted-foreground">Total Amount</p>
                </div>
              </div>
            </CardContent>
          </Card>

          <!-- Order Items -->
          <Card>
            <CardHeader>
              <CardTitle>Order Items</CardTitle>
            </CardHeader>
            <CardContent>
              <div class="space-y-4">
                <div v-for="item in order.items" :key="item.id" class="flex items-center justify-between p-4 border border-border rounded-lg">
                  <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-muted rounded-lg flex items-center justify-center">
                      <Package class="w-8 h-8 text-muted-foreground" />
                    </div>
                    <div>
                      <p class="font-medium">{{ item.product_name }}</p>
                      <p class="text-sm text-muted-foreground">
                        Quantity: {{ item.quantity }}
                        <span v-if="item.size"> • Size: {{ item.size }}</span>
                      </p>
                      <p class="text-sm text-muted-foreground">SKU: {{ item.product_sku || 'N/A' }}</p>
                    </div>
                  </div>
                  <div class="text-right">
                    <!-- Price Display -->
                    <div v-if="item.has_discount" class="mb-1">
                      <p class="text-sm line-through text-muted-foreground">{{ item.formatted_original_price }}</p>
                      <p class="font-medium text-green-600">{{ item.formatted_price }}</p>
                      <p class="text-xs text-green-600">Save {{ item.formatted_discount_amount }}</p>
                    </div>
                    <div v-else>
                      <p class="font-medium">{{ item.formatted_price }}</p>
                    </div>
                    <p class="text-sm text-muted-foreground">each</p>

                    <!-- Subtotal Display -->
                    <div v-if="item.has_discount" class="mt-1">
                      <p class="text-sm line-through text-muted-foreground">{{ item.formatted_original_subtotal }}</p>
                      <p class="font-medium">{{ item.formatted_subtotal }}</p>
                    </div>
                    <div v-else>
                      <p class="font-medium">{{ item.formatted_subtotal }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>

          <!-- Order Notes -->
          <Card v-if="order.notes">
            <CardHeader>
              <CardTitle>Order Notes</CardTitle>
            </CardHeader>
            <CardContent>
              <p class="text-sm text-muted-foreground">{{ order.notes }}</p>
            </CardContent>
          </Card>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
          <!-- Shipping Address -->
          <Card>
            <CardHeader>
              <CardTitle>Shipping Address</CardTitle>
            </CardHeader>
            <CardContent>
              <div class="space-y-1">
                <p class="text-sm text-foreground">{{ order.shipping_address_line_1 }}</p>
                <p v-if="order.shipping_address_line_2" class="text-sm text-foreground">{{ order.shipping_address_line_2 }}</p>
                <p class="text-sm text-foreground">
                  {{ order.shipping_city }}, {{ order.shipping_state }} {{ order.shipping_postal_code }}
                </p>
                <p class="text-sm text-foreground">{{ order.shipping_country }}</p>
              </div>
            </CardContent>
          </Card>

          <!-- Payment Information -->
          <Card>
            <CardHeader>
              <CardTitle>Payment Information</CardTitle>
            </CardHeader>
            <CardContent>
              <div class="space-y-3">
                <div class="flex items-center justify-between">
                  <span class="text-sm text-muted-foreground">Payment Method</span>
                  <span class="text-sm font-medium">{{ order.payment_method }}</span>
                </div>
                <div class="flex items-center justify-between">
                  <span class="text-sm text-muted-foreground">Payment Status</span>
                  <Badge :variant="getPaymentStatusVariant(order.payment_status)">
                    {{ order.payment_status_label }}
                  </Badge>
                </div>
              </div>
            </CardContent>
          </Card>

          <!-- Order Summary -->
          <Card>
            <CardHeader>
              <CardTitle>Order Summary</CardTitle>
            </CardHeader>
            <CardContent>
              <div class="space-y-2">
                <div class="flex items-center justify-between">
                  <span class="text-sm text-muted-foreground">Original Subtotal</span>
                  <span class="text-sm font-medium">{{ order.formatted_original_subtotal }}</span>
                </div>
                <div v-if="order.has_discounts" class="flex items-center justify-between">
                  <span class="text-sm text-muted-foreground">Discount</span>
                  <span class="text-sm font-medium text-green-600">-{{ order.formatted_total_discount }}</span>
                </div>
                <div class="flex items-center justify-between">
                  <span class="text-sm text-muted-foreground">Subtotal</span>
                  <span class="text-sm font-medium">{{ order.formatted_subtotal }}</span>
                </div>
                <div class="flex items-center justify-between">
                  <span class="text-sm text-muted-foreground">Tax</span>
                  <span class="text-sm font-medium">{{ order.formatted_tax }}</span>
                </div>
                <div class="flex items-center justify-between">
                  <span class="text-sm text-muted-foreground">Shipping</span>
                  <span class="text-sm font-medium">{{ order.formatted_shipping }}</span>
                </div>
                <div class="border-t border-border pt-2">
                  <div class="flex items-center justify-between">
                    <span class="font-medium text-foreground">Total</span>
                    <span class="font-bold text-foreground">{{ order.formatted_total }}</span>
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>

          <!-- Order Timeline -->
          <Card>
            <CardHeader>
              <CardTitle>Order Timeline</CardTitle>
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
        </div>
      </div>
    </div>
  </template>

  <script setup>
  import { Link } from '@inertiajs/vue3'
  import { Package } from 'lucide-vue-next'
  import Card from '@/components/ui/card/Card.vue'
  import CardContent from '@/components/ui/card/CardContent.vue'
  import CardHeader from '@/components/ui/card/CardHeader.vue'
  import CardTitle from '@/components/ui/card/CardTitle.vue'
  import Badge from '@/components/ui/badge/Badge.vue'
  import Button from '@/components/ui/button/Button.vue'
  import AdminLayout from '@/layouts/AdminLayout.vue'

  defineOptions({
    layout: AdminLayout,
  })

  defineProps({
    order: Object,
  })

  const formatDate = (date) => {
    return new Date(date).toLocaleDateString()
  }

  const formatDateTime = (date) => {
    return new Date(date).toLocaleString()
  }

  const getStatusVariant = (status) => {
    const variants = {
      'pending': 'secondary',
      'processing': 'default',
      'shipped': 'default',
      'delivered': 'default',
      'cancelled': 'destructive',
    }
    return variants[status] || 'secondary'
  }

  const getPaymentStatusVariant = (status) => {
    const variants = {
      'pending': 'secondary',
      'paid': 'default',
      'failed': 'destructive',
      'refunded': 'secondary',
    }
    return variants[status] || 'secondary'
  }
  </script>
