<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
      <div>
        <div class="flex items-center space-x-3">
          <div
            class="w-6 h-6 rounded-full"
            :style="{ backgroundColor: group.color }"
          ></div>
          <h1 class="text-3xl font-bold text-foreground">{{ group.name }}</h1>
          <Badge v-if="!group.is_active" variant="secondary">Inactive</Badge>
        </div>
        <p v-if="group.description" class="text-muted-foreground mt-1">{{ group.description }}</p>
      </div>
      <div class="flex items-center space-x-3">
        <Link
          :href="route('admin.customer-groups.edit', group.id)"
          class="px-4 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 transition-colors"
        >
          Edit Group
        </Link>
        <button
          @click="showAddCustomersModal = true"
          class="px-4 py-2 bg-secondary text-secondary-foreground rounded-md hover:bg-secondary/90 transition-colors"
        >
          Add Customers
        </button>
      </div>
    </div>

    <!-- Group Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <Card>
        <CardContent class="p-6">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
              <Users class="w-5 h-5 text-primary" />
            </div>
            <div>
              <p class="text-sm text-muted-foreground">Total Customers</p>
              <p class="text-2xl font-bold">{{ group.customers_count }}</p>
            </div>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardContent class="p-6">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-green-500/10 rounded-full flex items-center justify-center">
              <ShoppingBag class="w-5 h-5 text-green-500" />
            </div>
            <div>
              <p class="text-sm text-muted-foreground">Total Orders</p>
              <p class="text-2xl font-bold">{{ totalOrders }}</p>
            </div>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardContent class="p-6">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-blue-500/10 rounded-full flex items-center justify-center">
              <DollarSign class="w-5 h-5 text-blue-500" />
            </div>
            <div>
              <p class="text-sm text-muted-foreground">Total Spent</p>
              <p class="text-2xl font-bold">${{ formatNumber(totalSpent) }}</p>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- Customers Table -->
    <Card>
      <CardHeader>
        <CardTitle>Customers in Group</CardTitle>
        <CardDescription>Manage customers in this group</CardDescription>
      </CardHeader>
      <CardContent class="p-0">
        <div v-if="group.customers && group.customers.length > 0" class="overflow-x-auto">
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
              <tr v-for="customer in group.customers" :key="customer.id" class="border-b border-border hover:bg-muted/50">
                <td class="py-3 px-4">
                  <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-red-500 to-yellow-500 rounded-full flex items-center justify-center text-white text-sm font-medium">
                      {{ customer.name?.charAt(0)?.toUpperCase() || 'C' }}
                    </div>
                    <div>
                      <p class="font-medium">{{ customer.name }}</p>
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
                      :href="route('admin.customers.show', customer.id)"
                      class="text-sm text-primary hover:underline"
                    >
                      View
                    </Link>
                    <button
                      @click="removeCustomer(customer)"
                      class="text-sm text-destructive hover:underline"
                    >
                      Remove
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-else class="text-center py-12">
                  <div class="w-16 h-16 mx-auto mb-4 bg-muted rounded-full flex items-center justify-center">
          <Users class="w-8 h-8 text-muted-foreground" />
        </div>
          <h3 class="text-lg font-medium text-foreground mb-2">No customers in this group</h3>
          <p class="text-muted-foreground mb-4">Add customers to this group to get started.</p>
          <button
            @click="showAddCustomersModal = true"
            class="inline-flex items-center px-4 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 transition-colors"
          >
            <Plus class="w-4 h-4 mr-2" />
            Add Customers
          </button>
        </div>
      </CardContent>
    </Card>

    <!-- Add Customers Modal -->
    <div v-if="showAddCustomersModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-background rounded-lg p-6 w-full max-w-2xl max-h-[80vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-medium">Add Customers to Group</h3>
          <button @click="showAddCustomersModal = false" class="text-muted-foreground hover:text-foreground">
            <X class="w-5 h-5" />
          </button>
        </div>

        <div class="space-y-4">
          <div class="space-y-2">
            <label class="text-sm font-medium">Select Customers</label>
            <div class="max-h-60 overflow-y-auto border border-input rounded-md p-2">
              <div v-for="customer in availableCustomers" :key="customer.id" class="flex items-center space-x-3 p-2 hover:bg-muted rounded">
                <input
                  :id="'customer-' + customer.id"
                  v-model="selectedCustomers"
                  :value="customer.id"
                  type="checkbox"
                  class="rounded"
                />
                <label :for="'customer-' + customer.id" class="flex-1 cursor-pointer">
                  <p class="font-medium">{{ customer.name }}</p>
                  <p class="text-sm text-muted-foreground">{{ customer.email }}</p>
                </label>
              </div>
            </div>
          </div>

          <div class="flex items-center justify-end space-x-3">
            <button
              @click="showAddCustomersModal = false"
              class="px-4 py-2 text-sm border border-input rounded-md hover:bg-muted transition-colors"
            >
              Cancel
            </button>
            <button
              @click="addCustomers"
              :disabled="selectedCustomers.length === 0"
              class="px-4 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 transition-colors disabled:opacity-50"
            >
              Add Selected
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import Card from '@/components/ui/card/Card.vue'
import CardHeader from '@/components/ui/card/CardHeader.vue'
import CardTitle from '@/components/ui/card/CardTitle.vue'
import CardDescription from '@/components/ui/card/CardDescription.vue'
import CardContent from '@/components/ui/card/CardContent.vue'
import Badge from '@/components/ui/badge/Badge.vue'
import AdminLayout from '@/layouts/AdminLayout.vue'
import {
  Users,
  ShoppingBag,
  DollarSign,
  Plus,
  X
} from 'lucide-vue-next'

defineOptions({
  layout: AdminLayout,
})

const props = defineProps({
  group: Object,
  availableCustomers: Array,
})

const showAddCustomersModal = ref(false)
const selectedCustomers = ref([])

const totalOrders = computed(() => {
  return props.group.customers?.reduce((total, customer) => total + (customer.orders_count || 0), 0) || 0
})

const totalSpent = computed(() => {
  return props.group.customers?.reduce((total, customer) => total + (customer.orders_sum_total || 0), 0) || 0
})

const formatNumber = (number) => {
  return new Intl.NumberFormat().format(number)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}

const removeCustomer = (customer) => {
  if (confirm(`Are you sure you want to remove "${customer.name}" from this group?`)) {
    router.post(route('admin.customer-groups.remove-customers', props.group.id), {
      customer_ids: [customer.id]
    })
  }
}

const addCustomers = () => {
  if (selectedCustomers.value.length > 0) {
    router.post(route('admin.customer-groups.add-customers', props.group.id), {
      customer_ids: selectedCustomers.value
    }, {
      onSuccess: () => {
        showAddCustomersModal.value = false
        selectedCustomers.value = []
      }
    })
  }
}
</script>
