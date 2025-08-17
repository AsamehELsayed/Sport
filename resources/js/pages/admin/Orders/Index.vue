<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-foreground">Orders</h1>
        <p class="text-muted-foreground">Manage and track all customer orders</p>
      </div>
      <div class="flex items-center space-x-2">
        <Button variant="outline" @click="exportOrders">
          <Download class="w-4 h-4 mr-2" />
          Export
        </Button>
      </div>
    </div>

    <!-- Filters -->
    <Card>
      <CardContent class="p-6">
        <form @submit.prevent="applyFilters" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-foreground mb-2">Search</label>
            <Input 
              v-model="filters.search" 
              placeholder="Order #, customer name, email..."
              class="w-full"
            />
          </div>
          
          <div>
            <label class="block text-sm font-medium text-foreground mb-2">Status</label>
            <select v-model="filters.status" class="w-full px-3 py-2 border border-border rounded-md bg-background">
              <option value="">All Statuses</option>
              <option value="pending">Pending</option>
              <option value="processing">Processing</option>
              <option value="shipped">Shipped</option>
              <option value="delivered">Delivered</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-foreground mb-2">Date From</label>
            <Input 
              v-model="filters.date_from" 
              type="date"
              class="w-full"
            />
          </div>
          
          <div>
            <label class="block text-sm font-medium text-foreground mb-2">Date To</label>
            <Input 
              v-model="filters.date_to" 
              type="date"
              class="w-full"
            />
          </div>
          
          <div class="md:col-span-2 lg:col-span-4 flex items-center space-x-2">
            <Button type="submit" class="flex-1">
              <Search class="w-4 h-4 mr-2" />
              Apply Filters
            </Button>
            <Button type="button" variant="outline" @click="clearFilters">
              <X class="w-4 h-4 mr-2" />
              Clear
            </Button>
          </div>
        </form>
      </CardContent>
    </Card>

    <!-- Bulk Actions -->
    <div v-if="selectedOrders.length > 0" class="bg-muted p-4 rounded-lg">
      <div class="flex items-center justify-between">
        <p class="text-sm text-muted-foreground">
          {{ selectedOrders.length }} order(s) selected
        </p>
        <div class="flex items-center space-x-2">
          <select v-model="bulkAction" class="px-3 py-2 border border-border rounded-md bg-background">
            <option value="">Select Action</option>
            <option value="processing">Mark as Processing</option>
            <option value="shipped">Mark as Shipped</option>
            <option value="delivered">Mark as Delivered</option>
            <option value="cancelled">Mark as Cancelled</option>
          </select>
          <Button @click="applyBulkAction" :disabled="!bulkAction">
            Apply
          </Button>
        </div>
      </div>
    </div>

    <!-- Orders Table -->
    <Card>
      <CardContent class="p-0">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="border-b border-border bg-muted/50">
                <th class="text-left py-3 px-4 font-medium text-muted-foreground">
                  <input 
                    type="checkbox" 
                    :checked="allSelected"
                    @change="toggleSelectAll"
                    class="rounded border-border"
                  />
                </th>
                <th class="text-left py-3 px-4 font-medium text-muted-foreground">Order #</th>
                <th class="text-left py-3 px-4 font-medium text-muted-foreground">Customer</th>
                <th class="text-left py-3 px-4 font-medium text-muted-foreground">Status</th>
                <th class="text-left py-3 px-4 font-medium text-muted-foreground">Payment</th>
                <th class="text-left py-3 px-4 font-medium text-muted-foreground">Total</th>
                <th class="text-left py-3 px-4 font-medium text-muted-foreground">Date</th>
                <th class="text-left py-3 px-4 font-medium text-muted-foreground">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr 
                v-for="order in orders?.data || []" 
                :key="order.id" 
                class="border-b border-border hover:bg-muted/50"
              >
                <td class="py-3 px-4">
                  <input 
                    type="checkbox" 
                    :value="order.id"
                    v-model="selectedOrders"
                    class="rounded border-border"
                  />
                </td>
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
                    {{ order.status ? order.status.charAt(0).toUpperCase() + order.status.slice(1) : 'Unknown' }}
                  </Badge>
                </td>
                <td class="py-3 px-4">
                  <Badge :variant="getPaymentStatusVariant(order.payment_status)">
                    {{ order.payment_status ? order.payment_status.charAt(0).toUpperCase() + order.payment_status.slice(1) : 'Unknown' }}
                  </Badge>
                </td>
                <td class="py-3 px-4 font-medium">
                  {{ order.total ? '$' + parseFloat(order.total).toFixed(2) : '$0.00' }}
                </td>
                <td class="py-3 px-4 text-sm text-muted-foreground">
                  {{ formatDate(order.created_at) }}
                </td>
                <td class="py-3 px-4">
                  <div class="flex items-center space-x-2">
                    <Link 
                      :href="`/admin/orders/${order.id}`"
                      class="text-sm text-primary hover:underline"
                    >
                      View
                    </Link>
                    <Link 
                      :href="`/admin/orders/${order.id}/edit`"
                      class="text-sm text-primary hover:underline"
                    >
                      Edit
                    </Link>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </CardContent>
    </Card>

    <!-- Pagination -->
    <div v-if="orders?.links" class="flex items-center justify-between">
      <p class="text-sm text-muted-foreground">
        Showing {{ orders.from }} to {{ orders.to }} of {{ orders.total }} results
      </p>
      <div class="flex items-center space-x-2">
        <template v-for="link in orders.links" :key="link.label">
          <Link 
            v-if="link.url" 
            :href="link.url"
            :class="[
              'px-3 py-2 text-sm rounded-md',
              link.active 
                ? 'bg-primary text-primary-foreground' 
                : 'text-muted-foreground hover:text-foreground hover:bg-muted'
            ]"
            v-html="link.label"
          />
          <span 
            v-else 
            class="px-3 py-2 text-sm rounded-md text-muted-foreground opacity-50 cursor-not-allowed"
            v-html="link.label"
          />
        </template>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { Search, X, Download } from 'lucide-vue-next'

// UI components
import Card from '@/components/ui/card/Card.vue'
import CardContent from '@/components/ui/card/CardContent.vue'
import Button from '@/components/ui/button/Button.vue'
import Input from '@/components/ui/input/Input.vue'   // ✅ fixed casing
import Badge from '@/components/ui/badge/Badge.vue'
import AdminLayout from '@/layouts/AdminLayout.vue'

defineOptions({
  layout: AdminLayout,
})

const props = defineProps({
  orders: Object,
  filters: Object,
})

const selectedOrders = ref([])
const bulkAction = ref('')

const filters = ref({
  search: props.filters?.search || '',
  status: props.filters?.status || '',
  date_from: props.filters?.date_from || '',
  date_to: props.filters?.date_to || '',
})

const allSelected = computed(() => {
  return props.orders?.data?.length > 0 &&
    selectedOrders.value.length === props.orders.data.length
})

const applyFilters = () => {
  router.get('/admin/orders', filters.value, {
    preserveState: true,
    preserveScroll: true,
  })
}

const clearFilters = () => {
  filters.value = {
    search: '',
    status: '',
    date_from: '',
    date_to: '',
  }
  applyFilters()
}

const toggleSelectAll = () => {
  if (allSelected.value) {
    selectedOrders.value = []
  } else {
    selectedOrders.value = props.orders?.data?.map(order => order.id) || []
  }
}

const applyBulkAction = () => {
  if (!bulkAction.value || selectedOrders.value.length === 0) return

  router.post('/admin/orders/bulk-update', {
    order_ids: selectedOrders.value,
    status: bulkAction.value,
  }, {
    onSuccess: () => {
      selectedOrders.value = []
      bulkAction.value = ''
    }
  })
}

const exportOrders = () => {
  const queryParams = new URLSearchParams(filters.value).toString();
  const url = `/admin/orders/export?${queryParams}`;
  window.open(url, '_blank');
}

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
