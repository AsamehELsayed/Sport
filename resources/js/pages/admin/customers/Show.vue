<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-foreground">{{ customer.name }}</h1>
        <p class="text-muted-foreground">Customer details and information</p>
      </div>
      <div class="flex items-center space-x-3">
        <Link
          :href="route('admin.customers.edit', customer.id)"
          class="px-4 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 transition-colors"
        >
          Edit Customer
        </Link>
        <button
          @click="showAddToGroupModal = true"
          class="px-4 py-2 bg-secondary text-secondary-foreground rounded-md hover:bg-secondary/90 transition-colors"
        >
          Add to Group
        </button>
      </div>
    </div>

    <!-- Customer Information -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Customer Details -->
      <div class="lg:col-span-2 space-y-6">
        <Card>
          <CardHeader>
            <CardTitle>Customer Information</CardTitle>
          </CardHeader>
          <CardContent class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="text-sm font-medium text-muted-foreground">Name</label>
                <p class="text-sm">{{ customer.name }}</p>
              </div>
              <div>
                <label class="text-sm font-medium text-muted-foreground">Email</label>
                <p class="text-sm">{{ customer.email }}</p>
              </div>
              <div>
                <label class="text-sm font-medium text-muted-foreground">Phone</label>
                <p class="text-sm">{{ customer.phone || 'Not provided' }}</p>
              </div>
              <div>
                <label class="text-sm font-medium text-muted-foreground">Date of Birth</label>
                <p class="text-sm">{{ customer.date_of_birth ? formatDate(customer.date_of_birth) : 'Not provided' }}</p>
              </div>
              <div>
                <label class="text-sm font-medium text-muted-foreground">Gender</label>
                <p class="text-sm">{{ customer.gender ? customer.gender.charAt(0).toUpperCase() + customer.gender.slice(1) : 'Not specified' }}</p>
              </div>
              <div>
                <label class="text-sm font-medium text-muted-foreground">Joined</label>
                <p class="text-sm">{{ formatDate(customer.created_at) }}</p>
              </div>
            </div>

            <div v-if="customer.address_line_1">
              <label class="text-sm font-medium text-muted-foreground">Address</label>
              <p class="text-sm">
                {{ customer.address_line_1 }}<br>
                <span v-if="customer.address_line_2">{{ customer.address_line_2 }}<br></span>
                {{ customer.city }}, {{ customer.state }} {{ customer.postal_code }}<br>
                {{ customer.country }}
              </p>
            </div>

            <div v-if="customer.bio">
              <label class="text-sm font-medium text-muted-foreground">Bio</label>
              <p class="text-sm">{{ customer.bio }}</p>
            </div>
          </CardContent>
        </Card>

        <!-- Customer Groups -->
        <Card>
          <CardHeader>
            <CardTitle>Customer Groups</CardTitle>
            <CardDescription>Groups this customer belongs to</CardDescription>
          </CardHeader>
          <CardContent>
            <div v-if="customer.customer_groups && customer.customer_groups.length > 0" class="space-y-3">
              <div
                v-for="group in customer.customer_groups"
                :key="group.id"
                class="flex items-center justify-between p-3 border border-border rounded-md"
              >
                <div class="flex items-center space-x-3">
                  <div
                    class="w-4 h-4 rounded-full"
                    :style="{ backgroundColor: group.color }"
                  ></div>
                  <div>
                    <p class="font-medium">{{ group.name }}</p>
                    <p v-if="group.description" class="text-sm text-muted-foreground">{{ group.description }}</p>
                  </div>
                </div>
                <button
                  @click="removeFromGroup(group)"
                  class="text-sm text-destructive hover:underline"
                >
                  Remove
                </button>
              </div>
            </div>
            <div v-else class="text-center py-8">
                      <div class="w-12 h-12 mx-auto mb-3 bg-muted rounded-full flex items-center justify-center">
          <Users class="w-6 h-6 text-muted-foreground" />
        </div>
              <p class="text-muted-foreground">This customer is not in any groups</p>
            </div>
          </CardContent>
        </Card>
      </div>

      <!-- Stats Sidebar -->
      <div class="space-y-6">
        <Card>
          <CardHeader>
            <CardTitle>Statistics</CardTitle>
          </CardHeader>
          <CardContent class="space-y-4">
            <div class="flex items-center justify-between">
              <span class="text-sm text-muted-foreground">Total Orders</span>
              <span class="font-medium">{{ customer.orders_count || 0 }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-muted-foreground">Total Spent</span>
              <span class="font-medium">${{ formatNumber(customer.orders_sum_total || 0) }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-muted-foreground">Average Order</span>
              <span class="font-medium">
                ${{ customer.orders_count > 0 ? formatNumber((customer.orders_sum_total || 0) / customer.orders_count) : '0' }}
              </span>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>Preferences</CardTitle>
          </CardHeader>
          <CardContent class="space-y-4">
            <div class="flex items-center justify-between">
              <span class="text-sm text-muted-foreground">Marketing Emails</span>
              <Badge :variant="customer.marketing_emails ? 'default' : 'secondary'">
                {{ customer.marketing_emails ? 'Enabled' : 'Disabled' }}
              </Badge>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-muted-foreground">Order Updates</span>
              <Badge :variant="customer.order_updates ? 'default' : 'secondary'">
                {{ customer.order_updates ? 'Enabled' : 'Disabled' }}
              </Badge>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>

    <!-- Recent Orders -->
    <Card>
      <CardHeader>
        <CardTitle>Recent Orders</CardTitle>
      </CardHeader>
      <CardContent class="p-0">
        <div v-if="customer.orders && customer.orders.length > 0" class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="border-b border-border">
                <th class="text-left py-3 px-4 font-medium text-muted-foreground">Order ID</th>
                <th class="text-left py-3 px-4 font-medium text-muted-foreground">Date</th>
                <th class="text-left py-3 px-4 font-medium text-muted-foreground">Status</th>
                <th class="text-left py-3 px-4 font-medium text-muted-foreground">Total</th>
                <th class="text-left py-3 px-4 font-medium text-muted-foreground">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="order in customer.orders" :key="order.id" class="border-b border-border hover:bg-muted/50">
                <td class="py-3 px-4">
                  <span class="font-medium">#{{ order.id }}</span>
                </td>
                <td class="py-3 px-4">
                  <span class="text-sm">{{ formatDate(order.created_at) }}</span>
                </td>
                <td class="py-3 px-4">
                  <Badge :variant="getOrderStatusVariant(order.status)">
                    {{ order.status }}
                  </Badge>
                </td>
                <td class="py-3 px-4">
                  <span class="font-medium">${{ formatNumber(order.total) }}</span>
                </td>
                <td class="py-3 px-4">
                  <Link
                    :href="route('admin.orders.show', order.id)"
                    class="text-sm text-primary hover:underline"
                  >
                    View
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-else class="text-center py-12">
                  <div class="w-16 h-16 mx-auto mb-4 bg-muted rounded-full flex items-center justify-center">
          <ShoppingBag class="w-8 h-8 text-muted-foreground" />
        </div>
          <h3 class="text-lg font-medium text-foreground mb-2">No orders yet</h3>
          <p class="text-muted-foreground">This customer hasn't placed any orders.</p>
        </div>
      </CardContent>
    </Card>

    <!-- Add to Group Modal -->
    <div v-if="showAddToGroupModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-background rounded-lg p-6 w-full max-w-md">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-medium">Add to Group</h3>
          <button @click="showAddToGroupModal = false" class="text-muted-foreground hover:text-foreground">
            <X class="w-5 h-5" />
          </button>
        </div>

        <div class="space-y-4">
          <div class="space-y-2">
            <label class="text-sm font-medium">Select Group</label>
            <div class="max-h-60 overflow-y-auto border border-input rounded-md p-2">
              <div v-for="group in availableGroups" :key="group.id" class="flex items-center space-x-3 p-2 hover:bg-muted rounded">
                <input
                  :id="'group-' + group.id"
                  v-model="selectedGroup"
                  :value="group.id"
                  type="radio"
                  name="group"
                  class="rounded"
                />
                <label :for="'group-' + group.id" class="flex-1 cursor-pointer">
                  <div class="flex items-center space-x-2">
                    <div
                      class="w-3 h-3 rounded-full"
                      :style="{ backgroundColor: group.color }"
                    ></div>
                    <p class="font-medium">{{ group.name }}</p>
                  </div>
                  <p v-if="group.description" class="text-sm text-muted-foreground">{{ group.description }}</p>
                </label>
              </div>
            </div>
          </div>

          <div class="flex items-center justify-end space-x-3">
            <button
              @click="showAddToGroupModal = false"
              class="px-4 py-2 text-sm border border-input rounded-md hover:bg-muted transition-colors"
            >
              Cancel
            </button>
            <button
              @click="addToGroup"
              :disabled="!selectedGroup"
              class="px-4 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 transition-colors disabled:opacity-50"
            >
              Add to Group
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link, router, ref } from '@inertiajs/vue3'
import Card from '@/components/ui/card/Card.vue'
import CardHeader from '@/components/ui/card/CardHeader.vue'
import CardTitle from '@/components/ui/card/CardTitle.vue'
import CardDescription from '@/components/ui/card/CardDescription.vue'
import CardContent from '@/components/ui/card/CardContent.vue'
import Badge from '@/components/ui/badge/Badge.vue'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { Users, ShoppingBag, X } from 'lucide-vue-next'

defineOptions({
  layout: AdminLayout,
})

const props = defineProps({
  customer: Object,
  availableGroups: Array,
})

const showAddToGroupModal = ref(false)
const selectedGroup = ref('')

const formatNumber = (number) => {
  return new Intl.NumberFormat().format(number)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}

const getOrderStatusVariant = (status) => {
  const variants = {
    'pending': 'secondary',
    'processing': 'default',
    'shipped': 'default',
    'delivered': 'default',
    'cancelled': 'destructive',
  }
  return variants[status] || 'secondary'
}

const removeFromGroup = (group) => {
  if (confirm(`Are you sure you want to remove this customer from "${group.name}"?`)) {
    router.post(route('admin.customer-groups.remove-customers', group.id), {
      customer_ids: [props.customer.id]
    })
  }
}

const addToGroup = () => {
  if (selectedGroup.value) {
    router.post(route('admin.customer-groups.add-customers', selectedGroup.value), {
      customer_ids: [props.customer.id]
    }, {
      onSuccess: () => {
        showAddToGroupModal.value = false
        selectedGroup.value = ''
      }
    })
  }
}
</script>
