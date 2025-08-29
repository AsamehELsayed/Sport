<template>
  <div class="min-h-screen bg-background">
    <div class="container mx-auto px-4 py-8">
      <!-- Header -->
      <div class="flex items-center justify-between mb-8">
        <div>
          <h1 class="text-3xl font-bold">Edit Email Campaign</h1>
          <p class="text-muted-foreground">Update your email campaign settings and content</p>
        </div>
        <Link :href="route('admin.email-campaigns.index')">
          <Button variant="outline">
            <ArrowLeft class="h-4 w-4 mr-2" />
            Back to Campaigns
          </Button>
        </Link>
      </div>

      <!-- Edit Form -->
      <form @submit.prevent="submit" class="space-y-6">
        <!-- Campaign Details -->
        <Card>
          <CardHeader>
            <CardTitle>Campaign Information</CardTitle>
            <CardDescription>Basic details about your email campaign</CardDescription>
          </CardHeader>
          <CardContent class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="space-y-2">
                <Label for="name">Campaign Name *</Label>
                <Input
                  id="name"
                  v-model="form.name"
                  type="text"
                  placeholder="Enter campaign name"
                  required
                />
                <p v-if="form.errors.name" class="text-sm text-destructive">{{ form.errors.name }}</p>
              </div>

              <div class="space-y-2">
                <Label for="subject">Email Subject *</Label>
                <Input
                  id="subject"
                  v-model="form.subject"
                  type="text"
                  placeholder="Enter email subject"
                  required
                />
                <p v-if="form.errors.subject" class="text-sm text-destructive">{{ form.errors.subject }}</p>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="space-y-2">
                <Label for="email_template_id">Email Template</Label>
                <select
                  id="email_template_id"
                  v-model="form.email_template_id"
                  class="w-full px-3 py-2 border border-input bg-background rounded-md text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                  @change="loadTemplate"
                >
                  <option value="">No Template</option>
                  <option v-for="template in templates" :key="template.id" :value="template.id">
                    {{ template.name }}
                  </option>
                </select>
                <p v-if="form.errors.email_template_id" class="text-sm text-destructive">{{ form.errors.email_template_id }}</p>
              </div>

              <div class="space-y-2">
                <Label for="status">Status *</Label>
                <select
                  id="status"
                  v-model="form.status"
                  class="w-full px-3 py-2 border border-input bg-background rounded-md text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                  required
                >
                  <option value="draft">Draft</option>
                  <option value="scheduled">Scheduled</option>
                </select>
                <p v-if="form.errors.status" class="text-sm text-destructive">{{ form.errors.status }}</p>
              </div>
            </div>

            <div v-if="form.status === 'scheduled'" class="space-y-2">
              <Label for="scheduled_at">Schedule Date & Time *</Label>
              <Input
                id="scheduled_at"
                v-model="form.scheduled_at"
                type="datetime-local"
                required
              />
              <p v-if="form.errors.scheduled_at" class="text-sm text-destructive">{{ form.errors.scheduled_at }}</p>
            </div>
          </CardContent>
        </Card>

        <!-- Customer Groups -->
        <Card>
          <CardHeader>
            <CardTitle>Target Audience</CardTitle>
            <CardDescription>Select customer groups to target with this campaign</CardDescription>
          </CardHeader>
          <CardContent>
            <div class="space-y-3 max-h-64 overflow-y-auto border border-border rounded-md p-4">
              <div v-for="group in customerGroups" :key="group.id" class="flex items-center">
                <input
                  :id="'group-' + group.id"
                  v-model="form.customer_group_ids"
                  :value="group.id"
                  type="checkbox"
                  class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary"
                />
                <label :for="'group-' + group.id" class="ml-3 text-sm flex items-center">
                  <span class="w-3 h-3 rounded-full mr-2" :style="{ backgroundColor: group.color }"></span>
                  {{ group.name }}
                  <span class="ml-1 text-muted-foreground">({{ group.customers_count }} customers)</span>
                </label>
              </div>
            </div>
            <p v-if="form.errors.customer_group_ids" class="text-sm text-destructive mt-2">{{ form.errors.customer_group_ids }}</p>
          </CardContent>
        </Card>

        <!-- Email Content -->
        <Card>
          <CardHeader>
            <CardTitle>Email Content</CardTitle>
            <CardDescription>Write your email content with rich text editor and variable placeholders</CardDescription>
          </CardHeader>
          <CardContent>
            <div class="space-y-4">
              <!-- Variable Placeholders -->
              <div class="bg-muted/50 p-4 rounded-lg">
                <h4 class="text-sm font-medium mb-2">Available Variables</h4>
                <p class="text-xs text-muted-foreground mb-3">Click any variable to insert it at the cursor position</p>
                <div class="flex flex-wrap gap-2">
                  <button
                    v-for="variable in availableVariables"
                    :key="variable.key"
                    type="button"
                    @click="insertVariable(variable.key)"
                    class="px-3 py-1.5 text-xs bg-primary text-primary-foreground rounded-md hover:bg-primary/90 transition-colors border border-primary/20 hover:border-primary/40"
                  >
                    {{ variable.label }}
                  </button>
                </div>
                <div class="mt-3 p-2 bg-background rounded border text-xs text-muted-foreground">
                  <strong>Tip:</strong> Variables will be replaced with actual data when the email is sent. For example: {{customer_name}} becomes "John Doe"
                </div>
              </div>

              <!-- Rich Text Editor -->
              <div class="space-y-2">
                <div class="flex items-center justify-between">
                  <Label>Email Content *</Label>
                  <div class="flex items-center gap-2">
                    <Button
                      type="button"
                      variant="outline"
                      size="sm"
                      @click="previewCampaign"
                    >
                      <Eye class="h-4 w-4 mr-2" />
                      Preview
                    </Button>
                    <Button
                      type="button"
                      variant="outline"
                      size="sm"
                      @click="toggleEditorMode"
                    >
                      {{ isHtmlMode ? 'Rich Text' : 'HTML' }}
                    </Button>
                  </div>
                </div>

                <!-- Quill Editor -->
                <div v-if="!isHtmlMode" class="border border-input rounded-md">
                  <div ref="quillEditor" class="min-h-[400px]"></div>
                </div>

                <!-- HTML Editor -->
                <textarea
                  v-else
                  v-model="form.content"
                  rows="20"
                  class="w-full px-3 py-2 border border-input bg-background rounded-md text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 resize-none"
                  placeholder="Enter your email content here. You can use HTML and variables like {{customer_name}}, {{customer_email}}, etc."
                  required
                ></textarea>

                <p v-if="form.errors.content" class="text-sm text-destructive">{{ form.errors.content }}</p>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Form Actions -->
        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-border">
          <Link :href="route('admin.email-campaigns.index')">
            <Button variant="outline">
              Cancel
            </Button>
          </Link>
          <Button
            type="submit"
            :disabled="form.processing"
          >
            <Save class="h-4 w-4 mr-2" />
            {{ form.processing ? 'Updating...' : 'Update Campaign' }}
          </Button>
        </div>
      </form>

      <!-- Preview Modal -->
      <div v-if="showPreview" class="fixed inset-0 bg-black/50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-background">
          <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-medium text-foreground">Campaign Preview</h3>
              <Button
                variant="ghost"
                size="icon"
                @click="showPreview = false"
              >
                <X class="h-4 w-4" />
              </Button>
            </div>
            <div class="border rounded-md p-4 bg-muted max-h-96 overflow-y-auto">
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
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import {
  ArrowLeft,
  Eye,
  Save,
  X
} from 'lucide-vue-next'
import Button from '@/components/ui/button/Button.vue'
import Card from '@/components/ui/card/Card.vue'
import CardContent from '@/components/ui/card/CardContent.vue'
import CardHeader from '@/components/ui/card/CardHeader.vue'
import CardTitle from '@/components/ui/card/CardTitle.vue'
import CardDescription from '@/components/ui/card/CardDescription.vue'
import Input from '@/components/ui/Input.vue'
import Label from '@/components/ui/label/Label.vue'
import Quill from 'quill'
import 'quill/dist/quill.snow.css'

// Custom Quill blot for variables
const Inline = Quill.import('blots/inline')

class VariableBlot extends Inline {
  static create(value) {
    let node = super.create()
    node.setAttribute('data-variable', value)
    node.textContent = `{{${value}}}`
    return node
  }

  static formats(node) {
    return node.getAttribute('data-variable')
  }
}

VariableBlot.blotName = 'variable'
VariableBlot.tagName = 'span'
VariableBlot.className = 'ql-variable'

Quill.register(VariableBlot)
import AdminLayout from '@/layouts/AdminLayout.vue'

defineOptions({
  layout: AdminLayout,
})

const props = defineProps({
  campaign: Object,
  templates: Array,
  customerGroups: Array,
})

const showPreview = ref(false)
const isHtmlMode = ref(false)
const quillEditor = ref(null)
const quill = ref(null)
const previewData = ref({
  subject: '',
  content: '',
})

const availableVariables = [
  { key: 'customer_name', label: 'Customer Name' },
  { key: 'customer_email', label: 'Customer Email' },
  { key: 'order_number', label: 'Order Number' },
  { key: 'order_total', label: 'Order Total' },
  { key: 'website_name', label: 'Website Name' },
  { key: 'unsubscribe_link', label: 'Unsubscribe Link' },
]

const form = useForm({
  name: props.campaign.name,
  subject: props.campaign.subject,
  content: props.campaign.content,
  email_template_id: props.campaign.email_template_id || '',
  status: props.campaign.status,
  scheduled_at: props.campaign.scheduled_at ? props.campaign.scheduled_at.slice(0, 16) : '',
  customer_group_ids: props.campaign.customer_groups?.map(group => group.id) || [],
  recipient_filters: props.campaign.recipient_filters || {},
})

onMounted(async () => {
  await nextTick()
  initializeQuill()
})

const initializeQuill = () => {
  if (quillEditor.value) {
    quill.value = new Quill(quillEditor.value, {
      theme: 'snow',
      modules: {
        toolbar: [
          [{ 'header': [1, 2, 3, false] }],
          ['bold', 'italic', 'underline', 'strike'],
          [{ 'color': [] }, { 'background': [] }],
          [{ 'list': 'ordered'}, { 'list': 'bullet' }],
          [{ 'align': [] }],
          ['link', 'image'],
          ['clean']
        ]
      },
      placeholder: 'Enter your email content here...'
    })

    // Wait for Quill to be fully initialized
    nextTick(() => {
      // Set initial content
      if (form.content) {
        quill.value.root.innerHTML = form.content
      }

      // Sync Quill content with form
      quill.value.on('text-change', () => {
        form.content = quill.value.root.innerHTML
      })

      // Handle selection changes to prevent errors
      quill.value.on('selection-change', (range) => {
        // This helps maintain proper selection state
        if (range) {
          // Selection exists, do nothing special
        } else {
          // No selection, focus lost
        }
      })
    })
  }
}

const toggleEditorMode = () => {
  isHtmlMode.value = !isHtmlMode.value
  if (!isHtmlMode.value) {
    nextTick(() => {
      initializeQuill()
      if (quill.value) {
        quill.value.root.innerHTML = form.content
      }
    })
  }
}

const insertVariable = (variable) => {
  const placeholder = `{{${variable}}}`
  if (isHtmlMode.value) {
    // For HTML mode, insert into textarea
    const textarea = document.querySelector('textarea')
    if (textarea) {
      const start = textarea.selectionStart
      const end = textarea.selectionEnd
      const currentContent = form.content
      form.content = currentContent.substring(0, start) + placeholder + currentContent.substring(end)

      // Update cursor position after insertion
      nextTick(() => {
        textarea.focus()
        textarea.setSelectionRange(start + placeholder.length, start + placeholder.length)
      })
    }
  } else {
    // For Quill mode, insert as custom variable blot
    if (quill.value && quill.value.root) {
      try {
        // First focus the editor to ensure proper selection state
        quill.value.focus()

        // Small delay to ensure focus is set
        nextTick(() => {
          let range = null
          try {
            range = quill.value.getSelection(true)
          } catch (e) {
            console.warn('Could not get selection, using end of document')
          }

          if (range && range.index !== null) {
            // Insert variable as custom blot for special styling
            quill.value.insertEmbed(range.index, 'variable', variable)
            // Move cursor after the inserted variable
            quill.value.setSelection(range.index + 1)
          } else {
            // If no valid selection, append to the end
            const length = quill.value.getLength()
            if (length > 0) {
              quill.value.insertEmbed(length - 1, 'variable', variable)
              quill.value.setSelection(length)
            } else {
              quill.value.insertEmbed(0, 'variable', variable)
              quill.value.setSelection(1)
            }
          }
        })
      } catch (error) {
        console.warn('Error inserting variable:', error)
        // Fallback: insert as regular text with special class
        try {
          const range = quill.value.getSelection(true) || { index: quill.value.getLength() - 1 }
          quill.value.insertText(range.index, placeholder, { 'color': '#3B82F6', 'background': '#EFF6FF' })
          quill.value.setSelection(range.index + placeholder.length)
        } catch (fallbackError) {
          // Ultimate fallback: append to form content directly
          form.content += `<span class="ql-variable">${placeholder}</span>`
          if (quill.value) {
            quill.value.root.innerHTML = form.content
          }
        }
      }
    }
  }
}

const loadTemplate = () => {
  if (form.email_template_id) {
    const template = props.templates.find(t => t.id == form.email_template_id)
    if (template) {
      form.content = template.content
      if (quill.value) {
        quill.value.root.innerHTML = template.content
      }
    }
  }
}

const submit = () => {
  // Ensure content is synced from Quill if in rich text mode
  if (!isHtmlMode.value && quill.value) {
    form.content = quill.value.root.innerHTML
  }
  form.put(route('admin.email-campaigns.update', props.campaign.id))
}

const previewCampaign = () => {
  // Sample data for preview
  const sampleData = {
    customer_name: 'John Doe',
    customer_email: 'john@example.com',
    order_number: 'ORD-12345',
    order_total: '$99.99',
    website_name: 'SportApp',
    unsubscribe_link: '<a href="#">Unsubscribe</a>'
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

<style>
.ql-editor {
  min-height: 300px;
  font-size: 14px;
}

.ql-toolbar {
  border-top: 1px solid #e2e8f0;
  border-left: 1px solid #e2e8f0;
  border-right: 1px solid #e2e8f0;
  border-bottom: none;
}

.ql-container {
  border-bottom: 1px solid #e2e8f0;
  border-left: 1px solid #e2e8f0;
  border-right: 1px solid #e2e8f0;
  border-top: none;
}

/* Custom styling for variables */
.ql-variable {
  background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%);
  color: white !important;
  padding: 2px 6px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
  text-decoration: none !important;
  border: 1px solid #2563EB;
  box-shadow: 0 1px 2px rgba(59, 130, 246, 0.2);
  display: inline-block;
  margin: 0 1px;
  cursor: default;
  white-space: nowrap;
}

.ql-variable:hover {
  background: linear-gradient(135deg, #2563EB 0%, #1E40AF 100%);
  box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
}

/* Remove any default underlines from variables */
.ql-editor .ql-variable {
  text-decoration: none !important;
  border-bottom: none !important;
}

/* Ensure variables don't inherit link styling */
.ql-editor a .ql-variable {
  color: white !important;
  text-decoration: none !important;
}
</style>
