<template>
  <AdminLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-900">Invoices</h2>
        <div class="flex items-center space-x-3">
          <Button @click="exportOrders" variant="outline">Export Orders</Button>
          <Button @click="exportCustomers" variant="outline">Export Customers</Button>
        </div>
      </div>
    </template>

    <div class="space-y-6">
      <!-- Filters -->
      <Card>
        <CardHeader>
          <CardTitle>Filters</CardTitle>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="applyFilters" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
              <Input v-model="filters.search" placeholder="Invoice #, Customer name or email" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
              <select v-model="filters.status" class="w-full border border-gray-300 rounded-md px-3 py-2">
                <option value="">All Statuses</option>
                <option value="draft">Draft</option>
                <option value="sent">Sent</option>
                <option value="paid">Paid</option>
                <option value="overdue">Overdue</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
              <Input v-model="filters.date_from" type="date" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
              <Input v-model="filters.date_to" type="date" />
            </div>
            <div class="md:col-span-4 flex justify-end space-x-2">
              <Button type="button" variant="outline" @click="clearFilters">Clear</Button>
              <Button type="submit">Apply Filters</Button>
            </div>
          </form>
        </CardContent>
      </Card>

      <!-- Invoices Table -->
      <Card>
        <CardHeader>
          <CardTitle>Invoices ({{ invoices.total }})</CardTitle>
        </CardHeader>
        <CardContent>
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="border-b border-gray-200">
                  <th class="text-left py-3 px-4 font-medium">Invoice #</th>
                  <th class="text-left py-3 px-4 font-medium">Customer</th>
                  <th class="text-left py-3 px-4 font-medium">Order #</th>
                  <th class="text-left py-3 px-4 font-medium">Date</th>
                  <th class="text-left py-3 px-4 font-medium">Status</th>
                  <th class="text-left py-3 px-4 font-medium">Total</th>
                  <th class="text-left py-3 px-4 font-medium">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="invoice in invoices.data" :key="invoice.id" class="border-b border-gray-100 hover:bg-gray-50">
                  <td class="py-3 px-4">
                    <Link :href="route('admin.invoices.show', invoice.id)" class="text-blue-600 hover:text-blue-800 font-medium">
                      {{ invoice.invoice_number }}
                    </Link>
                  </td>
                  <td class="py-3 px-4">
                    <div>
                      <div class="font-medium">{{ invoice.order.customer_name }}</div>
                      <div class="text-sm text-gray-500">{{ invoice.order.customer_email }}</div>
                    </div>
                  </td>
                  <td class="py-3 px-4">
                    <Link :href="route('admin.orders.show', invoice.order.id)" class="text-blue-600 hover:text-blue-800">
                      {{ invoice.order.order_number }}
                    </Link>
                  </td>
                  <td class="py-3 px-4 text-sm">{{ formatDate(invoice.invoice_date) }}</td>
                  <td class="py-3 px-4">
                    <Badge :variant="getStatusVariant(invoice.status)">{{ invoice.status_label }}</Badge>
                  </td>
                  <td class="py-3 px-4 font-medium">{{ invoice.formatted_total }}</td>
                  <td class="py-3 px-4">
                    <div class="flex items-center space-x-2">
                      <Link :href="route('admin.invoices.preview', invoice.id)" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">Preview</Link>
                      <Link :href="route('admin.invoices.pdf', invoice.id)" class="text-green-600 hover:text-green-800 text-sm">PDF</Link>
                      <Link :href="route('admin.invoices.edit', invoice.id)" class="text-gray-600 hover:text-gray-800 text-sm">Edit</Link>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div v-if="invoices.links" class="mt-6">
            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-700">
                Showing {{ invoices.from }} to {{ invoices.to }} of {{ invoices.total }} results
              </div>
              <div class="flex space-x-2">
                <Link v-for="link in invoices.links" :key="link.label" :href="link.url" 
                      :class="['px-3 py-2 text-sm rounded-md', link.active ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100']">
                  <span v-html="link.label"></span>
                </Link>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import Card from '@/components/ui/card/Card.vue'
import CardHeader from '@/components/ui/card/CardHeader.vue'
import CardTitle from '@/components/ui/card/CardTitle.vue'
import CardContent from '@/components/ui/card/CardContent.vue'
import Button from '@/components/ui/button/Button.vue'
import Input from '@/components/ui/input/Input.vue'
import Badge from '@/components/ui/badge/Badge.vue'

const props = defineProps({
  invoices: Object,
  filters: Object,
})

const filters = ref({
  search: props.filters.search || '',
  status: props.filters.status || '',
  date_from: props.filters.date_from || '',
  date_to: props.filters.date_to || '',
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
    paid: 'success',
    overdue: 'destructive',
    cancelled: 'secondary',
  }
  return variants[status] || 'default'
}
</script>
