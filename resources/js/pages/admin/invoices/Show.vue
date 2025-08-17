<template>
  <AdminLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-xl font-semibold text-gray-900">Invoice {{ invoice.invoice_number }}</h2>
          <p class="text-sm text-gray-600">Order {{ invoice.order.order_number }}</p>
        </div>
        <div class="flex items-center space-x-3">
          <Button @click="markAsPaid" variant="outline" v-if="invoice.status !== 'paid'">
            Mark as Paid
          </Button>
          <Button @click="sendInvoice" variant="outline" v-if="invoice.status === 'draft'">
            Send Invoice
          </Button>
          <Link :href="route('admin.invoices.preview', invoice.id)" target="_blank">
            <Button variant="outline">Preview</Button>
          </Link>
          <Link :href="route('admin.invoices.pdf', invoice.id)">
            <Button>Download PDF</Button>
          </Link>
        </div>
      </div>
    </template>

    <div class="space-y-6">
      <!-- Invoice Details -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Customer Information -->
        <Card>
          <CardHeader>
            <CardTitle>Customer Information</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="space-y-3">
              <div>
                <label class="text-sm font-medium text-gray-500">Name</label>
                <p class="text-sm">{{ invoice.order.customer_name }}</p>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-500">Email</label>
                <p class="text-sm">{{ invoice.order.customer_email }}</p>
              </div>
              <div v-if="invoice.order.customer_phone">
                <label class="text-sm font-medium text-gray-500">Phone</label>
                <p class="text-sm">{{ invoice.order.customer_phone }}</p>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-500">Address</label>
                <p class="text-sm">
                  {{ invoice.order.shipping_address_line_1 }}<br>
                  <span v-if="invoice.order.shipping_address_line_2">{{ invoice.order.shipping_address_line_2 }}<br></span>
                  {{ invoice.order.shipping_city }}, {{ invoice.order.shipping_state }} {{ invoice.order.shipping_postal_code }}<br>
                  {{ invoice.order.shipping_country }}
                </p>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Invoice Information -->
        <Card>
          <CardHeader>
            <CardTitle>Invoice Information</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="space-y-3">
              <div>
                <label class="text-sm font-medium text-gray-500">Invoice Number</label>
                <p class="text-sm font-medium">{{ invoice.invoice_number }}</p>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-500">Invoice Date</label>
                <p class="text-sm">{{ formatDate(invoice.invoice_date) }}</p>
              </div>
              <div v-if="invoice.due_date">
                <label class="text-sm font-medium text-gray-500">Due Date</label>
                <p class="text-sm" :class="invoice.is_overdue ? 'text-red-600 font-medium' : ''">
                  {{ formatDate(invoice.due_date) }}
                  <span v-if="invoice.is_overdue" class="ml-2">({{ invoice.days_overdue }} days overdue)</span>
                </p>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-500">Status</label>
                <div class="mt-1">
                  <Badge :variant="getStatusVariant(invoice.status)">{{ invoice.status_label }}</Badge>
                </div>
              </div>
              <div v-if="invoice.payment_method">
                <label class="text-sm font-medium text-gray-500">Payment Method</label>
                <p class="text-sm">{{ invoice.payment_method }}</p>
              </div>
              <div v-if="invoice.payment_reference">
                <label class="text-sm font-medium text-gray-500">Payment Reference</label>
                <p class="text-sm">{{ invoice.payment_reference }}</p>
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
            <div class="space-y-3">
              <div>
                <label class="text-sm font-medium text-gray-500">Order Number</label>
                <p class="text-sm">
                  <Link :href="route('admin.orders.show', invoice.order.id)" class="text-blue-600 hover:text-blue-800">
                    {{ invoice.order.order_number }}
                  </Link>
                </p>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-500">Order Date</label>
                <p class="text-sm">{{ formatDate(invoice.order.created_at) }}</p>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-500">Order Status</label>
                <div class="mt-1">
                  <Badge :variant="getOrderStatusVariant(invoice.order.status)">
                    {{ invoice.order.status_label }}
                  </Badge>
                </div>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-500">Payment Status</label>
                <div class="mt-1">
                  <Badge :variant="getPaymentStatusVariant(invoice.order.payment_status)">
                    {{ invoice.order.payment_status_label }}
                  </Badge>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      <!-- Invoice Items -->
      <Card>
        <CardHeader>
          <CardTitle>Invoice Items</CardTitle>
        </CardHeader>
        <CardContent>
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="border-b border-gray-200">
                  <th class="text-left py-3 px-4 font-medium">Item</th>
                  <th class="text-left py-3 px-4 font-medium">SKU</th>
                  <th class="text-left py-3 px-4 font-medium">Size</th>
                  <th class="text-left py-3 px-4 font-medium">Color</th>
                  <th class="text-left py-3 px-4 font-medium">Qty</th>
                  <th class="text-left py-3 px-4 font-medium">Price</th>
                  <th class="text-left py-3 px-4 font-medium">Discount</th>
                  <th class="text-left py-3 px-4 font-medium">Total</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in invoice.order.items" :key="item.id" class="border-b border-gray-100">
                  <td class="py-3 px-4">
                    <div>
                      <div class="font-medium">{{ item.product_name }}</div>
                      <div class="text-sm text-gray-500">{{ item.product_sku }}</div>
                    </div>
                  </td>
                  <td class="py-3 px-4 text-sm">{{ item.product_sku }}</td>
                  <td class="py-3 px-4 text-sm">{{ item.size || '-' }}</td>
                  <td class="py-3 px-4 text-sm">{{ item.color || '-' }}</td>
                  <td class="py-3 px-4 text-sm">{{ item.quantity }}</td>
                  <td class="py-3 px-4 text-sm">{{ item.formatted_price }}</td>
                  <td class="py-3 px-4 text-sm">
                    <span v-if="item.discount_amount > 0">{{ item.formatted_discount_amount }}</span>
                    <span v-else>-</span>
                  </td>
                  <td class="py-3 px-4 font-medium">{{ item.formatted_subtotal }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </CardContent>
      </Card>

      <!-- Invoice Totals -->
      <Card>
        <CardHeader>
          <CardTitle>Invoice Totals</CardTitle>
        </CardHeader>
        <CardContent>
          <div class="flex justify-end">
            <div class="w-80 space-y-3">
              <div class="flex justify-between">
                <span class="text-sm text-gray-600">Subtotal:</span>
                <span class="text-sm">{{ invoice.formatted_subtotal }}</span>
              </div>
              <div v-if="invoice.tax > 0" class="flex justify-between">
                <span class="text-sm text-gray-600">Tax:</span>
                <span class="text-sm">{{ invoice.formatted_tax }}</span>
              </div>
              <div v-if="invoice.shipping > 0" class="flex justify-between">
                <span class="text-sm text-gray-600">Shipping:</span>
                <span class="text-sm">{{ invoice.formatted_shipping }}</span>
              </div>
              <div v-if="invoice.discount > 0" class="flex justify-between">
                <span class="text-sm text-gray-600">Discount:</span>
                <span class="text-sm text-red-600">-{{ invoice.formatted_discount }}</span>
              </div>
              <div class="flex justify-between border-t border-gray-200 pt-3">
                <span class="font-medium">Total:</span>
                <span class="font-medium text-lg">{{ invoice.formatted_total }}</span>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Notes and Terms -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <Card v-if="invoice.notes">
          <CardHeader>
            <CardTitle>Notes</CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-sm text-gray-700">{{ invoice.notes }}</p>
          </CardContent>
        </Card>

        <Card v-if="invoice.terms_conditions">
          <CardHeader>
            <CardTitle>Terms & Conditions</CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-sm text-gray-700">{{ invoice.terms_conditions }}</p>
          </CardContent>
        </Card>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import Card from '@/components/ui/card/Card.vue'
import CardHeader from '@/components/ui/card/CardHeader.vue'
import CardTitle from '@/components/ui/card/CardTitle.vue'
import CardContent from '@/components/ui/card/CardContent.vue'
import Button from '@/components/ui/button/Button.vue'
import Badge from '@/components/ui/badge/Badge.vue'

const props = defineProps({
  invoice: Object,
})

const markAsPaid = () => {
  router.post(route('admin.invoices.mark-paid', props.invoice.id))
}

const sendInvoice = () => {
  router.post(route('admin.invoices.send', props.invoice.id))
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}

const getStatusVariant = (status) => {
  const variants = {
    draft: 'secondary',
    sent: 'default',
    paid: 'success',
    overdue: 'destructive',
    cancelled: 'secondary',
  }
  return variants[status] || 'default'
}

const getOrderStatusVariant = (status) => {
  const variants = {
    pending: 'secondary',
    processing: 'default',
    shipped: 'default',
    delivered: 'success',
    cancelled: 'destructive',
  }
  return variants[status] || 'default'
}

const getPaymentStatusVariant = (status) => {
  const variants = {
    pending: 'secondary',
    paid: 'success',
    failed: 'destructive',
    refunded: 'secondary',
  }
  return variants[status] || 'default'
}
</script>
