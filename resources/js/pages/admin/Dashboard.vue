<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div>
      <h1 class="text-3xl font-bold text-foreground">Dashboard</h1>
      <p class="text-muted-foreground">Welcome to your admin dashboard</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <Card>
        <CardContent class="p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-muted-foreground">Total Orders</p>
              <p class="text-2xl font-bold text-foreground">{{ stats.total_orders }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
              <Package class="w-6 h-6 text-blue-600" />
            </div>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardContent class="p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-muted-foreground">Total Revenue</p>
              <p class="text-2xl font-bold text-foreground">${{ formatNumber(stats.total_revenue) }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
              <DollarSign class="w-6 h-6 text-green-600" />
            </div>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardContent class="p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-muted-foreground">Total Customers</p>
              <p class="text-2xl font-bold text-foreground">{{ stats.total_customers }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
              <Users class="w-6 h-6 text-purple-600" />
            </div>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardContent class="p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-muted-foreground">Pending Orders</p>
              <p class="text-2xl font-bold text-foreground">{{ stats.pending_orders }}</p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
              <Clock class="w-6 h-6 text-yellow-600" />
            </div>
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- Charts and Recent Orders -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Orders by Status Chart -->
      <Card>
        <CardHeader>
          <CardTitle>Orders by Status</CardTitle>
        </CardHeader>
        <CardContent>
          <div class="space-y-4">
            <div v-for="(count, status) in ordersByStatus" :key="status" class="flex items-center justify-between">
              <div class="flex items-center space-x-2">
                <div class="w-3 h-3 rounded-full" :class="getStatusColor(status)"></div>
                <span class="text-sm font-medium">{{ status }}</span>
              </div>
              <span class="text-sm text-muted-foreground">{{ count }}</span>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Monthly Revenue Chart -->
      <Card>
        <CardHeader>
          <CardTitle>Monthly Revenue</CardTitle>
        </CardHeader>
        <CardContent>
          <div class="space-y-4">
            <div v-for="item in monthlyRevenue" :key="item.month" class="flex items-center justify-between">
              <span class="text-sm font-medium">{{ item.month }}</span>
              <span class="text-sm text-muted-foreground">${{ formatNumber(item.revenue) }}</span>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- Recent Orders -->
    <Card>
      <CardHeader>
        <div class="flex items-center justify-between">
          <CardTitle>Recent Orders</CardTitle>
          <Link href="/admin/orders" class="text-sm text-primary hover:underline">
            View all orders
          </Link>
        </div>
      </CardHeader>
      <CardContent>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="border-b border-border">
                <th class="text-left py-3 px-4 font-medium text-muted-foreground">Order #</th>
                <th class="text-left py-3 px-4 font-medium text-muted-foreground">Customer</th>
                <th class="text-left py-3 px-4 font-medium text-muted-foreground">Status</th>
                <th class="text-left py-3 px-4 font-medium text-muted-foreground">Total</th>
                <th class="text-left py-3 px-4 font-medium text-muted-foreground">Date</th>
                <th class="text-left py-3 px-4 font-medium text-muted-foreground">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="order in recentOrders" :key="order.id" class="border-b border-border hover:bg-muted/50">
                <td class="py-3 px-4">
                  <span class="font-medium">{{ order.order_number }}</span>
                </td>
                <td class="py-3 px-4">
                  <div>
                    <p class="font-medium">{{ order.customer_name }}</p>
                    <p class="text-sm text-muted-foreground">{{ order.customer_email }}</p>
                  </div>
                </td>
                <td class="py-3 px-4">
                  <Badge :variant="getStatusVariant(order.status)">
                    {{ order.status_label }}
                  </Badge>
                </td>
                <td class="py-3 px-4 font-medium">{{ order.formatted_total }}</td>
                <td class="py-3 px-4 text-sm text-muted-foreground">
                  {{ formatDate(order.created_at) }}
                </td>
                <td class="py-3 px-4">
                  <Link 
                    :href="`/admin/orders/${order.id}`"
                    class="text-sm text-primary hover:underline"
                  >
                    View
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </CardContent>
    </Card>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { 
  Package, 
  DollarSign, 
  Users, 
  Clock 
} from 'lucide-vue-next'
import Card from '@/components/ui/card/Card.vue'
import CardContent from '@/components/ui/card/CardContent.vue'
import CardHeader from '@/components/ui/card/CardHeader.vue'
import CardTitle from '@/components/ui/card/CardTitle.vue'
import Badge from '@/components/ui/badge/Badge.vue'
import AdminLayout from '@/layouts/AdminLayout.vue'

defineOptions({
  layout: AdminLayout,
})

const props = defineProps({
  stats: Object,
  recentOrders: Array,
  ordersByStatus: Object,
  monthlyRevenue: Array,
})

const formatNumber = (number) => {
  return new Intl.NumberFormat().format(number)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}

const getStatusColor = (status) => {
  const colors = {
    'Pending': 'bg-yellow-500',
    'Processing': 'bg-blue-500',
    'Shipped': 'bg-purple-500',
    'Delivered': 'bg-green-500',
    'Cancelled': 'bg-red-500',
  }
  return colors[status] || 'bg-gray-500'
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