<template>
  <AdminLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-900">Create Invoice for Order {{ order.order_number }}</h2>
        <Link :href="route('admin.orders.show', order.id)">
          <Button variant="outline">Back to Order</Button>
        </Link>
      </div>
    </template>

    <div class="space-y-6">
      <Card>
        <CardHeader>
          <CardTitle>Invoice Details</CardTitle>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="submit" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Due Date</label>
                <Input v-model="form.due_date" type="date" :min="today" />
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
              <textarea
                v-model="form.notes"
                rows="3"
                class="w-full border border-gray-300 rounded-md px-3 py-2"
                placeholder="Additional notes..."
              ></textarea>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Terms & Conditions</label>
              <textarea
                v-model="form.terms_conditions"
                rows="4"
                class="w-full border border-gray-300 rounded-md px-3 py-2"
                placeholder="Payment terms..."
              ></textarea>
            </div>

            <div class="flex justify-end space-x-3">
              <Link :href="route('admin.orders.show', order.id)">
                <Button type="button" variant="outline">Cancel</Button>
              </Link>
              <Button type="submit" :disabled="form.processing">Create Invoice</Button>
            </div>
          </form>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Order Summary</CardTitle>
        </CardHeader>
        <CardContent>
          <div class="space-y-4">
            <div>
              <span class="font-medium">Customer:</span> {{ order.customer_name }} ({{ order.customer_email }})
            </div>
            <div>
              <span class="font-medium">Total:</span> {{ order.formatted_total }}
            </div>
            <div>
              <span class="font-medium">Items:</span> {{ order.items.length }} items
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import Card from '@/components/ui/card/Card.vue'
import CardHeader from '@/components/ui/card/CardHeader.vue'
import CardTitle from '@/components/ui/card/CardTitle.vue'
import CardContent from '@/components/ui/card/CardContent.vue'
import Button from '@/components/ui/button/Button.vue'
import Input from '@/components/ui/input/Input.vue'

const props = defineProps({
  order: Object,
})

const form = ref({
  due_date: '',
  notes: '',
  terms_conditions: '',
  processing: false,
})

const today = computed(() => {
  const date = new Date()
  return date.toISOString().split('T')[0]
})

const submit = () => {
  form.value.processing = true
  router.post(route('admin.invoices.store', props.order.id), form.value, {
    onFinish: () => {
      form.value.processing = false
    }
  })
}
</script>
