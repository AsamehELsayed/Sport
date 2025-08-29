<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-foreground">Invoices</h1>
        <p class="text-muted-foreground">Manage and track all customer invoices</p>
      </div>
      <div class="flex items-center space-x-2">
        <Button variant="outline" @click="exportOrders">
          <Download class="w-4 h-4 mr-2" />
          Export Orders
        </Button>
        <Button variant="outline" @click="exportCustomers">
          <Users class="w-4 h-4 mr-2" />
          Export Customers
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
              placeholder="Invoice #, customer name, email..."
              class="w-full"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-foreground mb-2">Status</label>
            <select v-model="filters.status" class="w-full px-3 py-2 border border-border rounded-md bg-background">
              <option value="">All Statuses</option>
              <option value="draft">Draft</option>
              <option value="sent">Sent</option>
              <option value="paid">Paid</option>
              <option value="overdue">Overdue</option>
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

    <!-- Invoices Table -->
    <Card>
      <CardContent class="p-0">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-muted/50">
              <tr>
                <th class="text-left p-4 font-medium">Invoice #</th>
                <th class="text-left p-4 font-medium">Customer</th>
                <th class="text-left p-4 font-medium">Order #</th>
                <th class="text-left p-4 font-medium">Date</th>
                <th class="text-left p-4 font-medium">Status</th>
                <th class="text-left p-4 font-medium">Total</th>
                <th class="text-left p-4 font-medium">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="invoice in invoices?.data || []" :key="invoice.id" class="border-t border-border hover:bg-muted/30">
                <td class="p-4">
                  <Link :href="route('admin.invoices.show', invoice.id)" class="font-medium text-primary hover:underline">
                    {{ invoice.invoice_number }}
                  </Link>
                </td>
                <td class="p-4">
                  <div v-if="invoice.order" class="space-y-1">
                    <div class="font-medium">{{ invoice.order.customer_name }}</div>
                    <div class="text-sm text-muted-foreground">{{ invoice.order.customer_email }}</div>
                  </div>
                  <div v-else class="text-muted-foreground italic">No order data</div>
                </td>
                <td class="p-4">
                  <Link v-if="invoice.order" :href="route('admin.orders.show', invoice.order.id)" class="text-primary hover:underline">
                    {{ invoice.order.order_number }}
                  </Link>
                  <span v-else class="text-muted-foreground">-</span>
                </td>
                <td class="p-4">
                  <div class="text-sm">{{ formatDate(invoice.invoice_date) }}</div>
                </td>
                <td class="p-4">
                  <Badge :variant="getStatusVariant(invoice.status)">
                    {{ invoice.status_label }}
                  </Badge>
                </td>
                <td class="p-4">
                  <div class="font-medium">{{ invoice.formatted_total }}</div>
                </td>
                <td class="p-4">
                  <div class="flex items-center gap-2">
                    <Link :href="route('admin.invoices.show', invoice.id)" target="_blank">
                      <Button variant="ghost" size="icon">
                        <Eye class="h-4 w-4" />
                      </Button>
                    </Link>
                    <a :href="route('admin.invoices.pdf', invoice.id)" download>
                      <Button variant="ghost" size="icon">
                        <Download class="h-4 w-4" />
                      </Button>
                    </a>
                    <Link :href="route('admin.invoices.edit', invoice.id)">
                      <Button variant="ghost" size="icon">
                        <Edit class="h-4 w-4" />
                      </Button>
                    </Link>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="invoices?.links" class="border-t border-border bg-muted/30 px-6 py-4">
          <div class="flex items-center justify-between">
            <div class="text-sm text-muted-foreground">
              Showing {{ invoices.from }} to {{ invoices.to }} of {{ invoices.total }} results
            </div>
            <div class="flex items-center space-x-2">
              <Link
                v-for="link in invoices.links"
                :key="link.label"
                :href="link.url || '#'"
                :class="[
                  'px-3 py-2 text-sm rounded-md transition-colors',
                  link.active
                    ? 'bg-primary text-primary-foreground'
                    : 'text-muted-foreground hover:text-foreground hover:bg-muted'
                ]"
                v-html="link.label"
              />
            </div>
          </div>
        </div>
      </CardContent>
    </Card>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Card, CardContent } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Download, Users, Search, X, Eye, Edit } from 'lucide-vue-next'
 import AdminLayout from '@/layouts/AdminLayout.vue'

defineOptions({
  layout: AdminLayout,
})

const props = defineProps({
  invoices: Object,
  filters: Object,
})

const filters = ref({
  search: props.filters?.search || '',
  status: props.filters?.status || '',
  date_from: props.filters?.date_from || '',
  date_to: props.filters?.date_to || '',
})

const applyFilters = () => {
  router.get(route('admin.invoices.index'), filters.value, {
    preserveState: true,
    preserveScroll: true,
  })
}

const clearFilters = () => {
  filters.value = { search: '', status: '', date_from: '', date_to: '' }
  applyFilters()
}

const exportOrders = () => {
  const params = new URLSearchParams(filters.value)
  window.open(route('admin.invoices.export-orders') + '?' + params.toString())
}

const exportCustomers = () => {
  const params = new URLSearchParams(filters.value)
  window.open(route('admin.invoices.export-customers') + '?' + params.toString())
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}

const getStatusVariant = (status) => {
  const variants = {
    draft: 'secondary',
    sent: 'default',
    paid: 'default',
    overdue: 'destructive',
    cancelled: 'secondary',
  }
  return variants[status] || 'default'
}
</script>
