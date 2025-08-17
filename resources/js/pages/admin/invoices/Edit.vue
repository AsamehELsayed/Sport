<template>
  <AdminLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-900">Edit Invoice {{ invoice.invoice_number }}</h2>
        <Link :href="route('admin.invoices.show', invoice.id)">
          <Button variant="outline">Back to Invoice</Button>
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
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select v-model="form.status" class="w-full border border-gray-300 rounded-md px-3 py-2">
                  <option value="draft">Draft</option>
                  <option value="sent">Sent</option>
                  <option value="paid">Paid</option>
                  <option value="overdue">Overdue</option>
                  <option value="cancelled">Cancelled</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Due Date</label>
                <Input v-model="form.due_date" type="date" />
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                <Input v-model="form.payment_method" placeholder="e.g., Credit Card, Bank Transfer" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Payment Reference</label>
                <Input v-model="form.payment_reference" placeholder="Transaction ID or reference" />
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
              <Link :href="route('admin.invoices.show', invoice.id)">
                <Button type="button" variant="outline">Cancel</Button>
              </Link>
              <Button type="submit" :disabled="form.processing">Update Invoice</Button>
            </div>
          </form>
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

const props = defineProps({
  invoice: Object,
})

const form = ref({
  status: props.invoice.status,
  due_date: props.invoice.due_date || '',
  payment_method: props.invoice.payment_method || '',
  payment_reference: props.invoice.payment_reference || '',
  notes: props.invoice.notes || '',
  terms_conditions: props.invoice.terms_conditions || '',
  processing: false,
})

const submit = () => {
  form.value.processing = true
  router.put(route('admin.invoices.update', props.invoice.id), form.value, {
    onFinish: () => {
      form.value.processing = false
    }
  })
}
</script>
