<template>
  <div class="min-h-screen bg-background">
    <div class="container mx-auto px-4 py-8">
      <!-- Header -->
      <div class="flex items-center justify-between mb-8">
        <div>
          <h1 class="text-3xl font-bold">Email Templates</h1>
          <p class="text-muted-foreground">Manage your email templates for campaigns</p>
        </div>
        <Link :href="route('admin.email-templates.create')">
          <Button>
            <Plus class="h-4 w-4 mr-2" />
            Create Template
          </Button>
        </Link>
      </div>

      <!-- Templates Grid -->
      <div v-if="templates.data.length === 0" class="text-center py-12">
        <div class="w-16 h-16 bg-muted rounded-full flex items-center justify-center mx-auto mb-4">
          <FileText class="h-8 w-8 text-muted-foreground" />
        </div>
        <h3 class="text-lg font-medium mb-2">No email templates yet</h3>
        <p class="text-muted-foreground mb-6">Create your first email template to get started with campaigns.</p>
        <Link :href="route('admin.email-templates.create')">
          <Button>
            <Plus class="h-4 w-4 mr-2" />
            Create Your First Template
          </Button>
        </Link>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <Card v-for="template in templates.data" :key="template.id" class="hover:shadow-lg transition-shadow">
          <CardHeader>
            <div class="flex items-center justify-between">
              <CardTitle class="text-lg">{{ template.name }}</CardTitle>
              <Badge :variant="template.is_active ? 'default' : 'secondary'">
                {{ template.is_active ? 'Active' : 'Inactive' }}
              </Badge>
            </div>
            <CardDescription>{{ template.description || 'No description' }}</CardDescription>
          </CardHeader>
          <CardContent>
            <div class="space-y-4">
              <div class="text-sm text-muted-foreground">
                <div class="flex items-center justify-between mb-2">
                  <span>Subject:</span>
                  <span class="font-medium text-foreground">{{ template.subject }}</span>
                </div>
                <div class="flex items-center justify-between">
                  <span>Used in campaigns:</span>
                  <span class="font-medium text-foreground">{{ template.campaigns_count || 0 }}</span>
                </div>
              </div>

              <div class="border rounded-md p-3 bg-muted/30 max-h-32 overflow-y-auto">
                <div v-html="template.content" class="text-sm text-muted-foreground"></div>
              </div>
            </div>
          </CardContent>
          <CardFooter class="flex items-center justify-between">
            <div class="text-xs text-muted-foreground">
              Created {{ formatDate(template.created_at) }}
            </div>
            <div class="flex items-center gap-2">
              <Link :href="route('admin.email-templates.show', template.id)">
                <Button variant="ghost" size="icon">
                  <Eye class="h-4 w-4" />
                </Button>
              </Link>
              <Link :href="route('admin.email-templates.edit', template.id)">
                <Button variant="ghost" size="icon">
                  <Edit class="h-4 w-4" />
                </Button>
              </Link>
              <Button
                variant="ghost"
                size="icon"
                @click="deleteTemplate(template)"
              >
                <Trash2 class="h-4 w-4" />
              </Button>
            </div>
          </CardFooter>
        </Card>
      </div>

      <!-- Pagination -->
      <div v-if="templates.links && templates.links.length > 3" class="mt-8">
        <nav class="flex items-center justify-between">
          <div class="flex-1 flex justify-between sm:hidden">
            <Link
              v-if="templates.prev_page_url"
              :href="templates.prev_page_url"
              class="relative inline-flex items-center px-4 py-2 border border-border text-sm font-medium rounded-md text-foreground bg-card hover:bg-muted"
            >
              Previous
            </Link>
            <Link
              v-if="templates.next_page_url"
              :href="templates.next_page_url"
              class="ml-3 relative inline-flex items-center px-4 py-2 border border-border text-sm font-medium rounded-md text-foreground bg-card hover:bg-muted"
            >
              Next
            </Link>
          </div>
          <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
              <p class="text-sm text-muted-foreground">
                Showing
                <span class="font-medium">{{ templates.from }}</span>
                to
                <span class="font-medium">{{ templates.to }}</span>
                of
                <span class="font-medium">{{ templates.total }}</span>
                results
              </p>
            </div>
            <div>
              <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                <Link
                  v-for="(link, index) in templates.links"
                  :key="index"
                  :href="link.url"
                  class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                  :class="[
                    link.url === null
                      ? 'bg-muted border-border text-muted-foreground cursor-not-allowed'
                      : link.active
                      ? 'z-10 bg-primary border-primary text-primary-foreground'
                      : 'bg-card border-border text-foreground hover:bg-muted'
                  ]"
                >
                  <span v-html="link.label"></span>
                </Link>
              </nav>
            </div>
          </div>
        </nav>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import {
  Plus,
  FileText,
  Eye,
  Edit,
  Trash2
} from 'lucide-vue-next'
import Button from '@/components/ui/button/Button.vue'
import Badge from '@/components/ui/badge/Badge.vue'
import Card from '@/components/ui/card/Card.vue'
import CardContent from '@/components/ui/card/CardContent.vue'
import CardHeader from '@/components/ui/card/CardHeader.vue'
import CardTitle from '@/components/ui/card/CardTitle.vue'
import CardDescription from '@/components/ui/card/CardDescription.vue'
import CardFooter from '@/components/ui/card/CardFooter.vue'
import AdminLayout from '@/layouts/AdminLayout.vue'

defineOptions({
  layout: AdminLayout,
})

const props = defineProps({
  templates: Object,
})

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString()
}

const deleteTemplate = (template) => {
  if (confirm(`Are you sure you want to delete "${template.name}"?`)) {
    router.delete(route('admin.email-templates.destroy', template.id))
  }
}
</script>
