<template>
  <div class="min-h-screen bg-background">
    <div class="container mx-auto px-4 py-8">
      <!-- Header -->
      <div class="flex items-center justify-between mb-8">
        <div>
          <h1 class="text-3xl font-bold">{{ campaign.name }}</h1>
          <p class="text-muted-foreground">Email campaign details and performance</p>
        </div>
        <div class="flex items-center gap-2">
          <Link :href="route('admin.email-campaigns.edit', campaign.id)" v-if="campaign.status === 'draft'">
            <Button variant="outline">
              <Edit class="h-4 w-4 mr-2" />
              Edit
            </Button>
          </Link>
          <Button
            v-if="campaign.status === 'draft'"
            @click="sendCampaign"
            variant="default"
          >
            <Send class="h-4 w-4 mr-2" />
            Send Campaign
          </Button>
          <Link :href="route('admin.email-campaigns.index')">
            <Button variant="outline">
              <ArrowLeft class="h-4 w-4 mr-2" />
              Back to Campaigns
            </Button>
          </Link>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <Card>
          <CardContent class="p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-muted-foreground">Total Recipients</p>
                <p class="text-2xl font-bold">{{ campaign.total_recipients }}</p>
              </div>
              <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                <Users class="h-4 w-4 text-blue-600" />
              </div>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardContent class="p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-muted-foreground">Sent</p>
                <p class="text-2xl font-bold">{{ campaign.sent_count }}</p>
              </div>
              <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                <Send class="h-4 w-4 text-green-600" />
              </div>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardContent class="p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-muted-foreground">Open Rate</p>
                <p class="text-2xl font-bold">{{ campaign.open_rate }}%</p>
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
                <p class="text-sm font-medium text-muted-foreground">Click Rate</p>
                <p class="text-2xl font-bold">{{ campaign.click_rate }}%</p>
              </div>
              <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                <MousePointer class="h-4 w-4 text-orange-600" />
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Campaign Details -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Campaign Information -->
          <Card>
            <CardHeader>
              <CardTitle>Campaign Information</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <Label class="text-sm font-medium text-muted-foreground">Campaign Name</Label>
                  <p class="text-foreground">{{ campaign.name }}</p>
                </div>
                <div>
                  <Label class="text-sm font-medium text-muted-foreground">Subject Line</Label>
                  <p class="text-foreground">{{ campaign.subject }}</p>
                </div>
                <div>
                  <Label class="text-sm font-medium text-muted-foreground">Status</Label>
                  <Badge :variant="getStatusVariant(campaign.status)" class="capitalize">
                    {{ campaign.status }}
                  </Badge>
                </div>
                <div>
                  <Label class="text-sm font-medium text-muted-foreground">Template</Label>
                  <p class="text-foreground">{{ campaign.template?.name || 'No template' }}</p>
                </div>
                <div v-if="campaign.scheduled_at">
                  <Label class="text-sm font-medium text-muted-foreground">Scheduled For</Label>
                  <p class="text-foreground">{{ formatDate(campaign.scheduled_at) }}</p>
                </div>
                <div v-if="campaign.sent_at">
                  <Label class="text-sm font-medium text-muted-foreground">Sent At</Label>
                  <p class="text-foreground">{{ formatDate(campaign.sent_at) }}</p>
                </div>
              </div>
            </CardContent>
          </Card>

          <!-- Target Audience -->
          <Card>
            <CardHeader>
              <CardTitle>Target Audience</CardTitle>
            </CardHeader>
            <CardContent>
              <div v-if="campaign.customer_groups && campaign.customer_groups.length > 0" class="space-y-3">
                <div v-for="group in campaign.customer_groups" :key="group.id" class="flex items-center justify-between p-3 border border-border rounded-lg">
                  <div class="flex items-center">
                    <span class="w-3 h-3 rounded-full mr-3" :style="{ backgroundColor: group.color }"></span>
                    <div>
                      <p class="font-medium">{{ group.name }}</p>
                      <p class="text-sm text-muted-foreground">{{ group.customers_count }} customers</p>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else class="text-center py-8 text-muted-foreground">
                <Users class="h-12 w-12 mx-auto mb-4 opacity-50" />
                <p>No customer groups selected</p>
              </div>
            </CardContent>
          </Card>

          <!-- Email Content -->
          <Card>
            <CardHeader>
              <CardTitle>Email Content</CardTitle>
              <CardDescription>Preview of the email content</CardDescription>
            </CardHeader>
            <CardContent>
              <div class="border rounded-lg p-4 bg-muted/30">
                <div class="mb-4">
                  <strong>Subject:</strong> {{ campaign.subject }}
                </div>
                <div class="border-t pt-4">
                  <div v-html="campaign.content"></div>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
          <!-- Performance Chart -->
          <Card>
            <CardHeader>
              <CardTitle>Performance Overview</CardTitle>
            </CardHeader>
            <CardContent>
              <div class="space-y-4">
                <div class="flex items-center justify-between">
                  <span class="text-sm text-muted-foreground">Delivery Rate</span>
                  <span class="font-medium">{{ Math.round((campaign.sent_count / campaign.total_recipients) * 100) }}%</span>
                </div>
                <div class="w-full bg-muted rounded-full h-2">
                  <div
                    class="bg-primary h-2 rounded-full"
                    :style="{ width: Math.round((campaign.sent_count / campaign.total_recipients) * 100) + '%' }"
                  ></div>
                </div>

                <div class="flex items-center justify-between">
                  <span class="text-sm text-muted-foreground">Open Rate</span>
                  <span class="font-medium">{{ campaign.open_rate }}%</span>
                </div>
                <div class="w-full bg-muted rounded-full h-2">
                  <div
                    class="bg-purple-500 h-2 rounded-full"
                    :style="{ width: campaign.open_rate + '%' }"
                  ></div>
                </div>

                <div class="flex items-center justify-between">
                  <span class="text-sm text-muted-foreground">Click Rate</span>
                  <span class="font-medium">{{ campaign.click_rate }}%</span>
                </div>
                <div class="w-full bg-muted rounded-full h-2">
                  <div
                    class="bg-orange-500 h-2 rounded-full"
                    :style="{ width: campaign.click_rate + '%' }"
                  ></div>
                </div>
              </div>
            </CardContent>
          </Card>

          <!-- Quick Actions -->
          <Card>
            <CardHeader>
              <CardTitle>Quick Actions</CardTitle>
            </CardHeader>
            <CardContent class="space-y-2">
              <Button
                v-if="campaign.status === 'draft'"
                @click="sendCampaign"
                class="w-full"
                variant="default"
              >
                <Send class="h-4 w-4 mr-2" />
                Send Campaign
              </Button>
              <Link :href="route('admin.email-campaigns.edit', campaign.id)" v-if="campaign.status === 'draft'">
                <Button class="w-full" variant="outline">
                  <Edit class="h-4 w-4 mr-2" />
                  Edit Campaign
                </Button>
              </Link>
              <Button
                @click="duplicateCampaign"
                class="w-full"
                variant="outline"
              >
                <Copy class="h-4 w-4 mr-2" />
                Duplicate Campaign
              </Button>
              <Button
                @click="deleteCampaign"
                class="w-full"
                variant="destructive"
              >
                <Trash2 class="h-4 w-4 mr-2" />
                Delete Campaign
              </Button>
            </CardContent>
          </Card>

          <!-- Campaign Timeline -->
          <Card>
            <CardHeader>
              <CardTitle>Campaign Timeline</CardTitle>
            </CardHeader>
            <CardContent>
              <div class="space-y-4">
                <div class="flex items-start space-x-3">
                  <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                  <div>
                    <p class="text-sm font-medium">Campaign Created</p>
                    <p class="text-xs text-muted-foreground">{{ formatDate(campaign.created_at) }}</p>
                  </div>
                </div>
                <div v-if="campaign.scheduled_at" class="flex items-start space-x-3">
                  <div class="w-2 h-2 bg-yellow-500 rounded-full mt-2"></div>
                  <div>
                    <p class="text-sm font-medium">Scheduled</p>
                    <p class="text-xs text-muted-foreground">{{ formatDate(campaign.scheduled_at) }}</p>
                  </div>
                </div>
                <div v-if="campaign.sent_at" class="flex items-start space-x-3">
                  <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                  <div>
                    <p class="text-sm font-medium">Sent</p>
                    <p class="text-xs text-muted-foreground">{{ formatDate(campaign.sent_at) }}</p>
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import {
  ArrowLeft,
  Edit,
  Send,
  Users,
  Eye,
  MousePointer,
  Copy,
  Trash2
} from 'lucide-vue-next'
import Button from '@/components/ui/button/Button.vue'
import Badge from '@/components/ui/badge/Badge.vue'
import Card from '@/components/ui/card/Card.vue'
import CardContent from '@/components/ui/card/CardContent.vue'
import CardHeader from '@/components/ui/card/CardHeader.vue'
import CardTitle from '@/components/ui/card/CardTitle.vue'
import CardDescription from '@/components/ui/card/CardDescription.vue'
import Label from '@/components/ui/label/Label.vue'
import AdminLayout from '@/layouts/AdminLayout.vue'

defineOptions({
  layout: AdminLayout,
})

const props = defineProps({
  campaign: Object,
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

const sendCampaign = () => {
  if (confirm(`Are you sure you want to send "${props.campaign.name}" to all recipients?`)) {
    router.post(route('admin.email-campaigns.send', props.campaign.id))
  }
}

const duplicateCampaign = () => {
  if (confirm(`Are you sure you want to duplicate "${props.campaign.name}"?`)) {
    router.post(route('admin.email-campaigns.duplicate', props.campaign.id))
  }
}

const deleteCampaign = () => {
  if (confirm(`Are you sure you want to delete "${props.campaign.name}"?`)) {
    router.delete(route('admin.email-campaigns.destroy', props.campaign.id))
  }
}
</script>
