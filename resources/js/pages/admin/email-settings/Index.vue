<template>
  <AdminLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-900">Email Settings</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <form @submit.prevent="submit" class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
              <!-- SMTP Configuration -->
              <div class="space-y-6">
                <h3 class="text-lg font-medium text-gray-900">SMTP Configuration</h3>

                <div>
                  <label for="mail_driver" class="block text-sm font-medium text-gray-700">Mail Driver</label>
                  <select
                    id="mail_driver"
                    v-model="form.mail_driver"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    required
                  >
                    <option value="smtp">SMTP</option>
                    <option value="mailgun">Mailgun</option>
                    <option value="ses">Amazon SES</option>
                    <option value="postmark">Postmark</option>
                  </select>
                  <div v-if="form.errors.mail_driver" class="mt-1 text-sm text-red-600">{{ form.errors.mail_driver }}</div>
                </div>

                <div v-if="form.mail_driver === 'smtp'" class="space-y-4">
                  <div>
                    <label for="mail_host" class="block text-sm font-medium text-gray-700">SMTP Host</label>
                    <input
                      id="mail_host"
                      v-model="form.mail_host"
                      type="text"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                      placeholder="smtp.gmail.com"
                    />
                    <div v-if="form.errors.mail_host" class="mt-1 text-sm text-red-600">{{ form.errors.mail_host }}</div>
                  </div>

                  <div>
                    <label for="mail_port" class="block text-sm font-medium text-gray-700">SMTP Port</label>
                    <input
                      id="mail_port"
                      v-model="form.mail_port"
                      type="number"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                      placeholder="587"
                    />
                    <div v-if="form.errors.mail_port" class="mt-1 text-sm text-red-600">{{ form.errors.mail_port }}</div>
                  </div>

                  <div>
                    <label for="mail_username" class="block text-sm font-medium text-gray-700">SMTP Username</label>
                    <input
                      id="mail_username"
                      v-model="form.mail_username"
                      type="text"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                      placeholder="your-email@gmail.com"
                    />
                    <div v-if="form.errors.mail_username" class="mt-1 text-sm text-red-600">{{ form.errors.mail_username }}</div>
                  </div>

                  <div>
                    <label for="mail_password" class="block text-sm font-medium text-gray-700">SMTP Password</label>
                    <input
                      id="mail_password"
                      v-model="form.mail_password"
                      type="password"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                      placeholder="your-app-password"
                    />
                    <div v-if="form.errors.mail_password" class="mt-1 text-sm text-red-600">{{ form.errors.mail_password }}</div>
                  </div>

                  <div>
                    <label for="mail_encryption" class="block text-sm font-medium text-gray-700">Encryption</label>
                    <select
                      id="mail_encryption"
                      v-model="form.mail_encryption"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    >
                      <option value="tls">TLS</option>
                      <option value="ssl">SSL</option>
                    </select>
                    <div v-if="form.errors.mail_encryption" class="mt-1 text-sm text-red-600">{{ form.errors.mail_encryption }}</div>
                  </div>
                </div>

                <div>
                  <label for="mail_from_address" class="block text-sm font-medium text-gray-700">From Email Address</label>
                  <input
                    id="mail_from_address"
                    v-model="form.mail_from_address"
                    type="email"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    placeholder="noreply@yourdomain.com"
                    required
                  />
                  <div v-if="form.errors.mail_from_address" class="mt-1 text-sm text-red-600">{{ form.errors.mail_from_address }}</div>
                </div>

                <div>
                  <label for="mail_from_name" class="block text-sm font-medium text-gray-700">From Name</label>
                  <input
                    id="mail_from_name"
                    v-model="form.mail_from_name"
                    type="text"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Your Company Name"
                    required
                  />
                  <div v-if="form.errors.mail_from_name" class="mt-1 text-sm text-red-600">{{ form.errors.mail_from_name }}</div>
                </div>

                <div class="flex items-center space-x-4">
                  <button
                    type="button"
                    @click="testConnection"
                    :disabled="testLoading"
                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-25"
                  >
                    {{ testLoading ? 'Testing...' : 'Test Connection' }}
                  </button>
                  <div v-if="testResult" class="text-sm" :class="testResult.success ? 'text-green-600' : 'text-red-600'">
                    {{ testResult.message }}
                  </div>
                </div>
              </div>

              <!-- Campaign Settings -->
              <div class="space-y-6">
                <h3 class="text-lg font-medium text-gray-900">Campaign Settings</h3>

                <div>
                  <label for="timezone" class="block text-sm font-medium text-gray-700">Timezone</label>
                  <select
                    id="timezone"
                    v-model="form.timezone"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    required
                  >
                    <option v-for="(label, value) in timezones" :key="value" :value="value">
                      {{ label }}
                    </option>
                  </select>
                  <div v-if="form.errors.timezone" class="mt-1 text-sm text-red-600">{{ form.errors.timezone }}</div>
                </div>

                <div>
                  <label for="daily_send_limit" class="block text-sm font-medium text-gray-700">Daily Send Limit</label>
                  <input
                    id="daily_send_limit"
                    v-model="form.daily_send_limit"
                    type="number"
                    min="1"
                    max="10000"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    required
                  />
                  <div v-if="form.errors.daily_send_limit" class="mt-1 text-sm text-red-600">{{ form.errors.daily_send_limit }}</div>
                </div>

                <div class="space-y-4">
                  <h4 class="text-md font-medium text-gray-900">Tracking Options</h4>

                  <div class="flex items-center">
                    <input
                      id="track_opens"
                      v-model="form.track_opens"
                      type="checkbox"
                      class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    />
                    <label for="track_opens" class="ml-2 text-sm text-gray-700">Track email opens</label>
                  </div>

                  <div class="flex items-center">
                    <input
                      id="track_clicks"
                      v-model="form.track_clicks"
                      type="checkbox"
                      class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    />
                    <label for="track_clicks" class="ml-2 text-sm text-gray-700">Track link clicks</label>
                  </div>
                </div>

                <div class="flex items-center">
                  <input
                    id="is_active"
                    v-model="form.is_active"
                    type="checkbox"
                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                  />
                  <label for="is_active" class="ml-2 text-sm text-gray-700">Enable email sending</label>
                </div>

                <!-- Sending Schedule -->
                <div class="space-y-4">
                  <h4 class="text-md font-medium text-gray-900">Sending Schedule</h4>
                  <p class="text-sm text-gray-600">Configure when emails can be sent (optional)</p>

                  <div class="space-y-3">
                    <div v-for="day in ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']" :key="day" class="flex items-center space-x-4">
                      <div class="flex items-center">
                        <input
                          :id="`schedule_${day}`"
                          v-model="form.sending_schedule[day].enabled"
                          type="checkbox"
                          class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        />
                        <label :for="`schedule_${day}`" class="ml-2 text-sm text-gray-700 capitalize">{{ day }}</label>
                      </div>

                      <div v-if="form.sending_schedule[day].enabled" class="flex items-center space-x-2">
                        <input
                          :id="`start_${day}`"
                          v-model="form.sending_schedule[day].start_time"
                          type="time"
                          class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        />
                        <span class="text-sm text-gray-500">to</span>
                        <input
                          :id="`end_${day}`"
                          v-model="form.sending_schedule[day].end_time"
                          type="time"
                          class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex items-center justify-end">
              <button
                type="submit"
                :disabled="form.processing"
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-25"
              >
                {{ form.processing ? 'Saving...' : 'Save Settings' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'

const props = defineProps({
  emailSetting: Object,
  timezones: Object,
})

const testLoading = ref(false)
const testResult = ref(null)

const form = useForm({
  mail_driver: props.emailSetting?.mail_driver || 'smtp',
  mail_host: props.emailSetting?.mail_host || '',
  mail_port: props.emailSetting?.mail_port || '',
  mail_username: props.emailSetting?.mail_username || '',
  mail_password: props.emailSetting?.mail_password || '',
  mail_encryption: props.emailSetting?.mail_encryption || 'tls',
  mail_from_address: props.emailSetting?.mail_from_address || '',
  mail_from_name: props.emailSetting?.mail_from_name || '',
  timezone: props.emailSetting?.timezone || 'UTC',
  daily_send_limit: props.emailSetting?.daily_send_limit || 1000,
  track_opens: props.emailSetting?.track_opens ?? true,
  track_clicks: props.emailSetting?.track_clicks ?? true,
  is_active: props.emailSetting?.is_active ?? false,
  sending_schedule: props.emailSetting?.sending_schedule || {
    monday: { enabled: false, start_time: '09:00', end_time: '17:00' },
    tuesday: { enabled: false, start_time: '09:00', end_time: '17:00' },
    wednesday: { enabled: false, start_time: '09:00', end_time: '17:00' },
    thursday: { enabled: false, start_time: '09:00', end_time: '17:00' },
    friday: { enabled: false, start_time: '09:00', end_time: '17:00' },
    saturday: { enabled: false, start_time: '09:00', end_time: '17:00' },
    sunday: { enabled: false, start_time: '09:00', end_time: '17:00' },
  },
})

const submit = () => {
  form.post(route('admin.email-settings.store'))
}

const testConnection = async () => {
  testLoading.value = true
  testResult.value = null

  try {
    const response = await fetch(route('admin.email-settings.test'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
      body: JSON.stringify({
        mail_driver: form.mail_driver,
        mail_host: form.mail_host,
        mail_port: form.mail_port,
        mail_username: form.mail_username,
        mail_password: form.mail_password,
        mail_encryption: form.mail_encryption,
        mail_from_address: form.mail_from_address,
        mail_from_name: form.mail_from_name,
      }),
    })

    const result = await response.json()
    testResult.value = result
  } catch (error) {
    testResult.value = {
      success: false,
      message: 'Failed to test connection. Please try again.',
    }
  } finally {
    testLoading.value = false
  }
}
</script>
