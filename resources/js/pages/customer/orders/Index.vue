<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div>
      <h1 class="text-3xl font-bold text-foreground">My Orders</h1>
      <p class="text-muted-foreground">Track your order history and status</p>
    </div>

    <!-- Orders List -->
    <div v-if="orders.data && orders.data.length > 0" class="space-y-4">
      <Card v-for="order in orders.data" :key="order.id" class="hover:shadow-md transition-shadow">
        <CardContent class="p-6">
          <div class="flex items-center justify-between">
            <div class="flex-1">
              <div class="flex items-center space-x-4">
                <div>
                  <h3 class="text-lg font-semibold text-foreground">{{ order.order_number }}</h3>
                  <p class="text-sm text-muted-foreground">{{ formatDate(order.created_at) }}</p>
                </div>
                <Badge :variant="getStatusVariant(order.status)">
                  {{ order.status_label }}
                </Badge>
              </div>

              <div class="mt-2">
                <p class="text-sm text-muted-foreground">
                  {{ order.items.length }} item(s) • Total: {{ order.formatted_total }}
                </p>
              </div>
            </div>

            <div class="flex items-center space-x-2">
              <Link
                :href="`/orders/${order.id}`"
                class="text-sm text-primary hover:underline"
              >
                View Details
              </Link>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-12">
      <div class="w-16 h-16 bg-muted rounded-full flex items-center justify-center mx-auto mb-4">
        <Package class="w-8 h-8 text-muted-foreground" />
      </div>
      <h3 class="text-lg font-semibold text-foreground mb-2">No orders yet</h3>
      <p class="text-muted-foreground mb-4">Start shopping to see your orders here</p>
      <Link href="/" class="text-primary hover:underline">
        Browse Products
      </Link>
    </div>

    <!-- Pagination -->
    <div v-if="orders.links && orders.data.length > 0" class="flex items-center justify-center">
      <div class="flex items-center space-x-2">
        <Link
          v-for="link in orders.links"
          :key="link.label"
          :href="link.url"
          :class="[
            'px-3 py-2 text-sm rounded-md',
            link.active
              ? 'bg-primary text-primary-foreground'
              : 'text-muted-foreground hover:text-foreground hover:bg-muted'
          ]"
          v-html="link.label"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { Package } from 'lucide-vue-next'
import Card from '@/components/ui/card/Card.vue'
import CardContent from '@/components/ui/card/CardContent.vue'
import Badge from '@/components/ui/badge/Badge.vue'
import GuestLayout from '@/layouts/GuestLayout.vue'

defineOptions({
  layout: GuestLayout,
})

const props = defineProps({
  orders: Object,
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
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
</script>
