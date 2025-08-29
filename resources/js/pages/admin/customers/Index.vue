<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div>
      <h1 class="text-3xl font-bold text-foreground">Customers</h1>
      <p class="text-muted-foreground">Manage customer accounts and view their information</p>
    </div>

    <!-- Search and Filters -->
    <Card>
      <CardContent class="p-6">
        <form @submit.prevent="search" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="space-y-2">
              <label for="search" class="text-sm font-medium">Search</label>
              <Input
                id="search"
                v-model="searchForm.search"
                type="text"
                placeholder="Search by name, email, or phone..."
              />
            </div>
            <div class="space-y-2">
              <label for="group" class="text-sm font-medium">Filter by Group</label>
              <select
                id="group"
                v-model="searchForm.group_id"
                class="w-full px-3 py-2 border border-input bg-background rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
              >
                <option value="">All Groups</option>
                <option v-for="group in groups" :key="group.id" :value="group.id">
                  {{ group.name }}
                </option>
              </select>
            </div>
            <div class="flex items-end space-x-2">
              <button
                type="submit"
                class="px-4 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 transition-colors"
              >
                Search
              </button>
              <button
                type="button"
                @click="clearFilters"
                class="px-4 py-2 border border-input rounded-md hover:bg-muted transition-colors"
              >
                Clear
              </button>
            </div>
          </div>
        </form>
      </CardContent>
    </Card>

    <!-- Customers Table -->
    <Card>
      <CardContent class="p-0">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="border-b border-border">
                <th class="text-left py-3 px-4 font-medium text-muted-foreground">Customer</th>
                <th class="text-left py-3 px-4 font-medium text-muted-foreground">Contact</th>
                <th class="text-left py-3 px-4 font-medium text-muted-foreground">Orders</th>
                <th class="text-left py-3 px-4 font-medium text-muted-foreground">Total Spent</th>
                <th class="text-left py-3 px-4 font-medium text-muted-foreground">Joined</th>
                <th class="text-left py-3 px-4 font-medium text-muted-foreground">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="customer in customers.data" :key="customer.id" class="border-b border-border hover:bg-muted/50">
                <td class="py-3 px-4">
                  <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-red-500 to-yellow-500 rounded-full flex items-center justify-center text-white text-sm font-medium">
                      {{ customer.name?.charAt(0)?.toUpperCase() || 'C' }}
                    </div>
                    <div>
                      <p class="font-medium">{{ customer.name }}</p>
                      <div v-if="customer.customer_groups && customer.customer_groups.length > 0" class="flex items-center space-x-1 mt-1">
                        <div
                          v-for="group in customer.customer_groups"
                          :key="group.id"
                          class="w-2 h-2 rounded-full"
                          :style="{ backgroundColor: group.color }"
                          :title="group.name"
                        ></div>
                      </div>
                    </div>
                  </div>
                </td>
                <td class="py-3 px-4">
                  <div class="space-y-1">
                    <p class="text-sm">{{ customer.email }}</p>
                    <p v-if="customer.phone" class="text-sm text-muted-foreground">{{ customer.phone }}</p>
                  </div>
                </td>
                <td class="py-3 px-4">
                  <span class="text-sm text-muted-foreground">{{ customer.orders_count || 0 }} orders</span>
                </td>
                <td class="py-3 px-4">
                  <span v-if="customer.orders_sum_total" class="font-medium">
                    ${{ formatNumber(customer.orders_sum_total) }}
                  </span>
                  <span v-else class="text-sm text-muted-foreground">-</span>
                </td>
                <td class="py-3 px-4">
                  <span class="text-sm text-muted-foreground">{{ formatDate(customer.created_at) }}</span>
                </td>
                <td class="py-3 px-4">
                  <div class="flex items-center space-x-2">
                    <Link
                      :href="`/admin/customers/${customer.id}`"
                      class="text-sm text-primary hover:underline"
                    >
                      View
                    </Link>
                    <Link
                      :href="`/admin/customers/${customer.id}/edit`"
                      class="text-sm text-primary hover:underline"
                    >
                      Edit
                    </Link>
                    <button
                      @click="deleteCustomer(customer)"
                      class="text-sm text-destructive hover:underline"
                    >
                      Delete
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </CardContent>
    </Card>

    <!-- Pagination -->
    <div v-if="customers.links && customers.links.length > 3" class="flex items-center justify-center">
      <div class="flex space-x-1">
        <Link
          v-for="(link, index) in customers.links"
          :key="index"
          :href="link.url"
          :class="[
            'px-3 py-2 text-sm rounded-md transition-colors',
            link.active
              ? 'bg-primary text-primary-foreground'
              : 'text-muted-foreground hover:text-foreground hover:bg-muted',
            !link.url ? 'opacity-50 cursor-not-allowed' : ''
          ]"
        >
          <span v-html="link.label"></span>
        </Link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link, router, useForm } from '@inertiajs/vue3'
import Card from '@/components/ui/card/Card.vue'
import CardContent from '@/components/ui/card/CardContent.vue'
import Input from '@/components/ui/input/Input.vue'
import AdminLayout from '@/layouts/AdminLayout.vue'

defineOptions({
  layout: AdminLayout,
})

const props = defineProps({
  customers: Object,
  groups: Array,
  filters: Object,
})

const searchForm = useForm({
  search: props.filters?.search || '',
  group_id: props.filters?.group_id || '',
})

const search = () => {
  searchForm.get(route('admin.customers.index'), {
    preserveState: true,
    preserveScroll: true,
  })
}

const clearFilters = () => {
  searchForm.reset()
  searchForm.get(route('admin.customers.index'), {
    preserveState: true,
    preserveScroll: true,
  })
}

const formatNumber = (number) => {
  return new Intl.NumberFormat().format(number)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}

const deleteCustomer = (customer) => {
  if (confirm(`Are you sure you want to delete "${customer.name}"?`)) {
    router.delete(`/admin/customers/${customer.id}`)
  }
}
</script>
