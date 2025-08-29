<template>
  <div class="min-h-screen bg-background">
    <div class="container mx-auto px-4 py-8">
      <!-- Header -->
      <div class="flex items-center justify-between mb-8">
        <div>
          <h1 class="text-3xl font-bold">Email Campaigns</h1>
          <p class="text-muted-foreground">Manage and track your email marketing campaigns</p>
        </div>
        <Link :href="route('admin.email-campaigns.create')">
          <Button>
            <Plus class="h-4 w-4 mr-2" />
            Create Campaign
          </Button>
        </Link>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <Card>
          <CardContent class="p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-muted-foreground">Total Campaigns</p>
                <p class="text-2xl font-bold">{{ campaigns.total || 0 }}</p>
              </div>
              <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                <Mail class="h-4 w-4 text-blue-600" />
              </div>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardContent class="p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-muted-foreground">Active Campaigns</p>
                <p class="text-2xl font-bold">{{ activeCampaigns }}</p>
              </div>
              <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                <Play class="h-4 w-4 text-green-600" />
              </div>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardContent class="p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-muted-foreground">Avg. Open Rate</p>
                <p class="text-2xl font-bold">{{ averageOpenRate }}%</p>
              </div>
              <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                <Eye class="h-4 w-4 text-purple-600" />
              </div>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardContent class="p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-muted-foreground">Avg. Click Rate</p>
                <p class="text-2xl font-bold">{{ averageClickRate }}%</p>
              </div>
              <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                <MousePointer class="h-4 w-4 text-orange-600" />
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      <!-- Campaigns Table -->
      <div class="bg-card border border-border rounded-lg overflow-hidden">
        <div v-if="campaigns.data.length === 0" class="text-center py-12">
          <div class="w-16 h-16 bg-muted rounded-full flex items-center justify-center mx-auto mb-4">
            <Mail class="h-8 w-8 text-muted-foreground" />
          </div>
          <h3 class="text-lg font-medium mb-2">No email campaigns yet</h3>
          <p class="text-muted-foreground mb-6">Get started by creating your first email campaign to reach your customers.</p>
          <Link :href="route('admin.email-campaigns.create')">
            <Button>
              <Plus class="h-4 w-4 mr-2" />
              Create Your First Campaign
            </Button>
          </Link>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-muted/50">
              <tr>
                <th class="text-left p-4 font-medium">Campaign</th>
                <th class="text-left p-4 font-medium">Status</th>
                <th class="text-left p-4 font-medium">Recipients</th>
                <th class="text-left p-4 font-medium">Performance</th>
                <th class="text-left p-4 font-medium">Scheduled</th>
                <th class="text-left p-4 font-medium">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="campaign in campaigns.data" :key="campaign.id" class="border-t border-border hover:bg-muted/30">
                <td class="p-4">
                  <div>
                    <div class="font-medium">{{ campaign.name }}</div>
                    <div class="text-sm text-muted-foreground">{{ campaign.subject }}</div>
                    <div v-if="campaign.template" class="text-xs text-muted-foreground mt-1">
                      Template: {{ campaign.template.name }}
                    </div>
                  </div>
                </td>
                <td class="p-4">
                  <Badge
                    :variant="getStatusVariant(campaign.status)"
                    class="capitalize"
                  >
                    {{ campaign.status }}
                  </Badge>
                </td>
                <td class="p-4">
                  <div class="text-sm">
                    <div class="font-medium">{{ campaign.sent_count }} / {{ campaign.total_recipients }}</div>
                    <div class="text-muted-foreground">
                      {{ Math.round((campaign.sent_count / campaign.total_recipients) * 100) }}% sent
                    </div>
                  </div>
                </td>
                <td class="p-4">
                  <div class="space-y-2">
                    <div class="flex items-center justify-between text-sm">
                      <span class="text-muted-foreground">Open Rate</span>
                      <span class="font-medium">{{ campaign.open_rate }}%</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                      <span class="text-muted-foreground">Click Rate</span>
                      <span class="font-medium">{{ campaign.click_rate }}%</span>
                    </div>
                  </div>
                </td>
                <td class="p-4">
                  <div class="text-sm">
                    <div v-if="campaign.scheduled_at" class="text-foreground">
                      {{ formatDate(campaign.scheduled_at) }}
                    </div>
                    <div v-else-if="campaign.sent_at" class="text-muted-foreground">
                      Sent: {{ formatDate(campaign.sent_at) }}
                    </div>
                    <div v-else class="text-muted-foreground">
                      -
                    </div>
                  </div>
                </td>
                <td class="p-4">
                  <div class="flex items-center gap-2">
                    <Link :href="route('admin.email-campaigns.show', campaign.id)">
                      <Button variant="ghost" size="icon">
                        <Eye class="h-4 w-4" />
                      </Button>
                    </Link>
                    <Link v-if="campaign.status === 'draft'" :href="route('admin.email-campaigns.edit', campaign.id)">
                      <Button variant="ghost" size="icon">
                        <Edit class="h-4 w-4" />
                      </Button>
                    </Link>
                    <Button
                      v-if="campaign.status === 'draft'"
                      variant="ghost"
                      size="icon"
                      @click="sendCampaign(campaign)"
                    >
                      <Send class="h-4 w-4" />
                    </Button>
                    <Button
                      variant="ghost"
                      size="icon"
                      @click="deleteCampaign(campaign)"
                    >
                      <Trash2 class="h-4 w-4" />
                    </Button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="campaigns.links && campaigns.links.length > 3" class="mt-6">
        <nav class="flex items-center justify-between">
          <div class="flex-1 flex justify-between sm:hidden">
            <Link
              v-if="campaigns.prev_page_url"
              :href="campaigns.prev_page_url"
              class="relative inline-flex items-center px-4 py-2 border border-border text-sm font-medium rounded-md text-foreground bg-card hover:bg-muted"
            >
              Previous
            </Link>
            <Link
              v-if="campaigns.next_page_url"
              :href="campaigns.next_page_url"
              class="ml-3 relative inline-flex items-center px-4 py-2 border border-border text-sm font-medium rounded-md text-foreground bg-card hover:bg-muted"
            >
              Next
            </Link>
          </div>
          <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
              <p class="text-sm text-muted-foreground">
                Showing
                <span class="font-medium">{{ campaigns.from }}</span>
                to
                <span class="font-medium">{{ campaigns.to }}</span>
                of
                <span class="font-medium">{{ campaigns.total }}</span>
                results
              </p>
            </div>
            <div>
              <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                <Link
                  v-for="(link, index) in campaigns.links"
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
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import {
  Plus,
  Mail,
  Play,
  Eye,
  MousePointer,
  Edit,
  Send,
  Trash2
} from 'lucide-vue-next'
import Button from '@/components/ui/button/Button.vue'
import Badge from '@/components/ui/badge/Badge.vue'
import Card from '@/components/ui/card/Card.vue'
import CardContent from '@/components/ui/card/CardContent.vue'
import AdminLayout from '@/layouts/AdminLayout.vue'

defineOptions({
  layout: AdminLayout,
})

const props = defineProps({
  campaigns: Object,
})

const activeCampaigns = computed(() => {
  return props.campaigns.data.filter(campaign =>
    ['scheduled', 'sending'].includes(campaign.status)
  ).length
})

const averageOpenRate = computed(() => {
  const campaigns = props.campaigns.data.filter(c => c.open_rate !== null)
  if (campaigns.length === 0) return 0
  const total = campaigns.reduce((sum, c) => sum + parseFloat(c.open_rate), 0)
  return Math.round(total / campaigns.length)
})

const averageClickRate = computed(() => {
  const campaigns = props.campaigns.data.filter(c => c.click_rate !== null)
  if (campaigns.length === 0) return 0
  const total = campaigns.reduce((sum, c) => sum + parseFloat(c.click_rate), 0)
  return Math.round(total / campaigns.length)
})

const getStatusVariant = (status) => {
  switch (status) {
    case 'draft': return 'secondary'
    case 'scheduled': return 'default'
    case 'sending': return 'default'
    case 'sent': return 'default'
    case 'cancelled': return 'destructive'
    default: return 'secondary'
  }
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
}

const deleteCampaign = (campaign) => {
  if (confirm(`Are you sure you want to delete "${campaign.name}"?`)) {
    router.delete(route('admin.email-campaigns.destroy', campaign.id))
  }
}

const sendCampaign = (campaign) => {
  if (confirm(`Are you sure you want to send "${campaign.name}" to all recipients?`)) {
    router.post(route('admin.email-campaigns.send', campaign.id))
  }
}
</script>
