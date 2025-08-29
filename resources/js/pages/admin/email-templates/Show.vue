<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-4">
        <Link
          :href="route('admin.email-templates.index')"
          class="flex items-center gap-2 text-muted-foreground hover:text-foreground transition-colors"
        >
          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          Back to Templates
        </Link>
        <div class="h-6 w-px bg-border"></div>
        <h1 class="text-3xl font-bold text-foreground">{{ template?.name }}</h1>
      </div>
      <div class="flex items-center gap-3">
        <Link :href="route('admin.email-templates.edit', template?.id)">
          <button class="px-4 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 transition-colors">
            <svg class="h-4 w-4 mr-2 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Edit Template
          </button>
        </Link>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Template Details -->
      <div class="lg:col-span-1 space-y-6">
        <!-- Basic Information -->
        <Card>
          <CardHeader>
            <CardTitle>Template Information</CardTitle>
            <CardDescription>Basic details about this email template</CardDescription>
          </CardHeader>
          <CardContent class="space-y-4">
            <div class="space-y-2">
              <label class="text-sm font-medium text-muted-foreground">Name</label>
              <p class="text-sm text-foreground">{{ template?.name }}</p>
            </div>

            <div class="space-y-2">
              <label class="text-sm font-medium text-muted-foreground">Type</label>
              <Badge
                :variant="getTypeVariant(template?.type)"
                class="capitalize"
              >
                {{ template?.type?.replace('_', ' ') }}
              </Badge>
            </div>

            <div class="space-y-2">
              <label class="text-sm font-medium text-muted-foreground">Status</label>
              <Badge
                :variant="template?.is_active ? 'default' : 'secondary'"
              >
                {{ template?.is_active ? 'Active' : 'Inactive' }}
              </Badge>
            </div>

            <div class="space-y-2">
              <label class="text-sm font-medium text-muted-foreground">Created</label>
              <p class="text-sm text-foreground">{{ new Date(template?.created_at).toLocaleDateString() }}</p>
            </div>

            <div class="space-y-2">
              <label class="text-sm font-medium text-muted-foreground">Last Updated</label>
              <p class="text-sm text-foreground">{{ new Date(template?.updated_at).toLocaleDateString() }}</p>
            </div>
          </CardContent>
        </Card>

        <!-- Available Variables -->
        <Card>
          <CardHeader>
            <CardTitle>Available Variables</CardTitle>
            <CardDescription>Variables you can use in this template</CardDescription>
          </CardHeader>
          <CardContent>
            <div class="space-y-2">
              <div v-for="(label, variable) in availableVariables" :key="variable" class="flex justify-between items-center py-1">
                <code class="text-xs bg-muted px-2 py-1 rounded">{{ '{' + '{' + variable + '}' + '}' }}</code>
                <span class="text-xs text-muted-foreground">{{ label }}</span>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Usage Statistics -->
        <Card>
          <CardHeader>
            <CardTitle>Usage Statistics</CardTitle>
            <CardDescription>How this template has been used</CardDescription>
          </CardHeader>
          <CardContent>
            <div class="space-y-4">
              <div class="flex justify-between items-center">
                <span class="text-sm text-muted-foreground">Campaigns Created</span>
                <span class="text-sm font-medium">{{ template?.campaigns_count || 0 }}</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-sm text-muted-foreground">Total Recipients</span>
                <span class="text-sm font-medium">{{ template?.total_recipients || 0 }}</span>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      <!-- Email Content -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Email Subject -->
        <Card>
          <CardHeader>
            <CardTitle>Email Subject</CardTitle>
            <CardDescription>The subject line that will appear in recipients' inboxes</CardDescription>
          </CardHeader>
          <CardContent>
            <div class="bg-muted p-4 rounded-md">
              <p class="text-sm font-medium">{{ template?.subject }}</p>
            </div>
          </CardContent>
        </Card>

        <!-- Email Content Preview -->
        <Card>
          <CardHeader>
            <CardTitle>Email Content</CardTitle>
            <CardDescription>Preview of the email content with sample data</CardDescription>
          </CardHeader>
          <CardContent>
            <div class="space-y-4">
              <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-foreground">HTML Preview</span>
                <button
                  @click="refreshPreview"
                  class="text-sm text-primary hover:text-primary/80"
                >
                  Refresh Preview
                </button>
              </div>
              <div class="border rounded-md p-4 bg-muted max-h-96 overflow-y-auto">
                <div v-html="previewContent"></div>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Raw HTML -->
        <Card>
          <CardHeader>
            <CardTitle>Raw HTML</CardTitle>
            <CardDescription>The raw HTML content of the template</CardDescription>
          </CardHeader>
          <CardContent>
            <pre class="bg-muted p-4 rounded-md text-xs overflow-x-auto max-h-64 overflow-y-auto">{{ template?.content }}</pre>
          </CardContent>
        </Card>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import Card from '@/components/ui/card/Card.vue'
import CardContent from '@/components/ui/card/CardContent.vue'
import CardHeader from '@/components/ui/card/CardHeader.vue'
import CardTitle from '@/components/ui/card/CardTitle.vue'
import CardDescription from '@/components/ui/card/CardDescription.vue'
import Badge from '@/components/ui/badge/Badge.vue'
import AdminLayout from '@/layouts/AdminLayout.vue'

defineOptions({
  layout: AdminLayout,
})

const props = defineProps({
  template: Object,
  availableVariables: Object,
})

const previewContent = ref('')

const getTypeVariant = (type) => {
  const variants = {
    'custom': 'default',
    'sales': 'default',
    'newsletter': 'secondary',
    'welcome': 'outline',
    'abandoned_cart': 'destructive'
  }
  return variants[type] || 'default'
}

const generatePreview = () => {
  if (!props.template?.content) {
    previewContent.value = ''
    return
  }

  // Sample data for preview
  const sampleData = {
    customer_name: 'John Doe',
    customer_email: 'john@example.com',
    customer_phone: '+1 (555) 123-4567',
    customer_address: '123 Main St, City, State 12345',
    order_number: 'ORD-12345',
    order_total: '$99.99',
    order_date: new Date().toLocaleDateString(),
    product_name: 'Premium Sports Equipment',
    product_price: '$49.99',
    discount_code: 'SAVE20',
    discount_amount: '$10.00',
    website_name: 'SportApp',
    company_name: 'SportApp Inc.',
    unsubscribe_link: '<a href="#">Unsubscribe</a>',
  }

  let content = props.template.content

  // Replace variables with sample data
  Object.entries(sampleData).forEach(([key, value]) => {
    content = content.replace(new RegExp(`{{${key}}}`, 'g'), value)
  })

  previewContent.value = content
}

const refreshPreview = () => {
  generatePreview()
}

onMounted(() => {
  generatePreview()
})
</script>
