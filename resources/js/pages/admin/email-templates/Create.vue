<template>
  <AdminLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-900">Create Email Template</h2>
        <Link
          :href="route('admin.email-templates.index')"
          class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
        >
          Back to Templates
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <form @submit.prevent="submit" class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
              <!-- Template Details -->
              <div class="lg:col-span-1 space-y-6">
                <div>
                  <label for="name" class="block text-sm font-medium text-gray-700">Template Name</label>
                  <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    required
                  />
                  <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</div>
                </div>

                <div>
                  <label for="type" class="block text-sm font-medium text-gray-700">Template Type</label>
                  <select
                    id="type"
                    v-model="form.type"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    required
                  >
                    <option value="custom">Custom</option>
                    <option value="sales">Sales</option>
                    <option value="newsletter">Newsletter</option>
                    <option value="welcome">Welcome</option>
                    <option value="abandoned_cart">Abandoned Cart</option>
                  </select>
                  <div v-if="form.errors.type" class="mt-1 text-sm text-red-600">{{ form.errors.type }}</div>
                </div>

                <div>
                  <label for="subject" class="block text-sm font-medium text-gray-700">Email Subject</label>
                  <input
                    id="subject"
                    v-model="form.subject"
                    type="text"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    required
                  />
                  <div v-if="form.errors.subject" class="mt-1 text-sm text-red-600">{{ form.errors.subject }}</div>
                </div>

                <div>
                  <label class="flex items-center">
                    <input
                      v-model="form.is_active"
                      type="checkbox"
                      class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    />
                    <span class="ml-2 text-sm text-gray-700">Active</span>
                  </label>
                </div>

                <!-- Available Variables -->
                <div>
                  <h3 class="text-lg font-medium text-gray-900 mb-3">Available Variables</h3>
                  <div class="bg-gray-50 p-4 rounded-md">
                    <p class="text-sm text-gray-600 mb-3">Use these variables in your template:</p>
                    <div class="grid grid-cols-1 gap-2">
                      <div v-for="(label, variable) in availableVariables" :key="variable" class="flex justify-between">
                                                 <span class="text-sm font-mono text-gray-700">{{ '{' + '{' + variable + '}' + '}' }}</span>
                        <span class="text-sm text-gray-500">{{ label }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Email Content -->
              <div class="lg:col-span-2">
                <div>
                  <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Email Content</label>
                  <div class="border border-gray-300 rounded-md">
                    <div class="bg-gray-50 px-4 py-2 border-b border-gray-300">
                      <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">HTML Editor</span>
                        <button
                          type="button"
                          @click="previewTemplate"
                          class="text-sm text-blue-600 hover:text-blue-800"
                        >
                          Preview
                        </button>
                      </div>
                    </div>
                    <textarea
                      id="content"
                      v-model="form.content"
                      rows="20"
                      class="block w-full border-0 focus:ring-0 resize-none"
                      placeholder="Enter your email content here. You can use HTML and the variables listed on the left."
                      required
                    ></textarea>
                  </div>
                  <div v-if="form.errors.content" class="mt-1 text-sm text-red-600">{{ form.errors.content }}</div>
                </div>

                <!-- Preview Modal -->
                <div v-if="showPreview" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                  <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                    <div class="mt-3">
                      <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Email Preview</h3>
                        <button
                          @click="showPreview = false"
                          class="text-gray-400 hover:text-gray-600"
                        >
                          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                          </svg>
                        </button>
                      </div>
                      <div class="border rounded-md p-4 bg-gray-50">
                        <div class="mb-2">
                          <strong>Subject:</strong> {{ previewData.subject }}
                        </div>
                        <div class="border-t pt-2">
                          <div v-html="previewData.content"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex items-center justify-end space-x-4">
              <Link
                :href="route('admin.email-templates.index')"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
              >
                Cancel
              </Link>
              <button
                type="submit"
                :disabled="form.processing"
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-25"
              >
                {{ form.processing ? 'Creating...' : 'Create Template' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'

const props = defineProps({
  availableVariables: Object,
})

const showPreview = ref(false)
const previewData = ref({
  subject: '',
  content: '',
})

const form = useForm({
  name: '',
  subject: '',
  content: '',
  type: 'custom',
  variables: [],
  is_active: true,
})

const submit = () => {
  form.post(route('admin.email-templates.store'))
}

const previewTemplate = () => {
  // Sample data for preview
  const sampleData = {
    customer_name: 'John Doe',
    customer_email: 'john@example.com',
    order_number: 'ORD-12345',
    order_total: '$99.99',
    website_name: 'SportApp',
  }

  let content = form.content
  Object.entries(sampleData).forEach(([key, value]) => {
    content = content.replace(new RegExp(`{{${key}}}`, 'g'), value)
  })

  previewData.value = {
    subject: form.subject,
    content: content,
  }
  showPreview.value = true
}
</script>
