<template>
  <AdminLayout>
    <div class="max-w-4xl mx-auto mt-8 space-y-6">
      <div>
        <h1 class="text-3xl font-bold text-foreground">Create Product</h1>
        <p class="text-muted-foreground">Add a new product to your inventory</p>
      </div>

      <form @submit.prevent="submit" class="space-y-6">
        <!-- Basic Information -->
        <Card>
          <CardHeader>
            <CardTitle>Basic Information</CardTitle>
            <CardDescription>Product name, description, and pricing</CardDescription>
          </CardHeader>
          <CardContent class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-foreground mb-2">Name *</label>
                <Input v-model="form.name" placeholder="Product name" required />
                <p v-if="form.errors.name" class="text-sm text-destructive mt-1">{{ form.errors.name }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-foreground mb-2">SKU</label>
                <Input v-model="form.sku" placeholder="Stock keeping unit" />
                <p v-if="form.errors.sku" class="text-sm text-destructive mt-1">{{ form.errors.sku }}</p>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-foreground mb-2">Description</label>
              <textarea
                v-model="form.description"
                rows="4"
                class="w-full px-3 py-2 border border-border rounded-md bg-background text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                placeholder="Product description..."
              ></textarea>
              <p v-if="form.errors.description" class="text-sm text-destructive mt-1">{{ form.errors.description }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label class="block text-sm font-medium text-foreground mb-2">Base Price *</label>
                <Input v-model="form.price" type="number" step="0.01" min="0" placeholder="0.00" required />
                <p v-if="form.errors.price" class="text-sm text-destructive mt-1">{{ form.errors.price }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-foreground mb-2">Discount</label>
                <Input v-model="form.discount" type="number" step="0.01" min="0" placeholder="0.00" />
                <p v-if="form.errors.discount" class="text-sm text-destructive mt-1">{{ form.errors.discount }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-foreground mb-2">Category</label>
                <select v-model="form.category_id" class="w-full px-3 py-2 border border-border rounded-md bg-background text-foreground">
                  <option value="">Select Category</option>
                  <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.name }}
                  </option>
                </select>
                <p v-if="form.errors.category_id" class="text-sm text-destructive mt-1">{{ form.errors.category_id }}</p>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-foreground mb-2">Brand</label>
                <select v-model="form.brand_id" class="w-full px-3 py-2 border border-border rounded-md bg-background text-foreground">
                  <option value="">Select Brand</option>
                  <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                    {{ brand.name }}
                  </option>
                </select>
                <p v-if="form.errors.brand_id" class="text-sm text-destructive mt-1">{{ form.errors.brand_id }}</p>
              </div>

              <div class="flex items-center space-x-4">
                <label class="flex items-center space-x-2">
                  <input v-model="form.is_active" type="checkbox" class="rounded border-border" />
                  <span class="text-sm font-medium text-foreground">Active</span>
                </label>
                <label class="flex items-center space-x-2">
                  <input v-model="form.is_featured" type="checkbox" class="rounded border-border" />
                  <span class="text-sm font-medium text-foreground">Featured</span>
                </label>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Product Images -->
        <Card>
          <CardHeader>
            <CardTitle>Product Images</CardTitle>
            <CardDescription>Upload multiple images for your product</CardDescription>
          </CardHeader>
          <CardContent>
            <ImageUpload
              v-model="form.images"
              :multiple="true"
              :max-files="5"
              @error="handleImageError"
            />
            <p v-if="form.errors.images" class="text-sm text-destructive mt-2">{{ form.errors.images }}</p>
          </CardContent>
        </Card>

        <!-- Product Variants -->
        <Card>
          <CardHeader>
            <CardTitle>Product Variants</CardTitle>
            <CardDescription>Add different sizes and colors with individual stock levels</CardDescription>
          </CardHeader>
          <CardContent>
            <div class="space-y-4">
              <div v-for="(variant, index) in form.variants" :key="index" class="border border-border rounded-lg p-4">
                <div class="flex items-center justify-between mb-4">
                  <h4 class="font-medium">Variant {{ index + 1 }}</h4>
                  <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="removeVariant(index)"
                    :disabled="form.variants.length === 1"
                  >
                    Remove
                  </Button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Size *</label>
                    <Input v-model="variant.size" placeholder="S, M, L, XL..." required />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Color</label>
                    <Input v-model="variant.color" placeholder="Red, Blue..." />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Stock *</label>
                    <Input v-model="variant.stock" type="number" min="0" placeholder="0" required />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Price Adjustment</label>
                    <Input v-model="variant.price_adjustment" type="number" step="0.01" placeholder="0.00" />
                    <p class="text-xs text-muted-foreground mt-1">Additional cost for this variant</p>
                  </div>
                </div>

                <div class="mt-2">
                  <label class="block text-sm font-medium text-foreground mb-2">Variant SKU</label>
                  <Input v-model="variant.sku" placeholder="Optional variant-specific SKU" />
                </div>
              </div>

              <Button type="button" variant="outline" @click="addVariant">
                <Plus class="w-4 h-4 mr-2" />
                Add Variant
              </Button>
            </div>

            <p v-if="form.errors.variants" class="text-sm text-destructive mt-2">{{ form.errors.variants }}</p>
          </CardContent>
        </Card>

        <!-- Form Actions -->
        <div class="flex items-center justify-end space-x-4">
          <Button type="button" variant="outline" @click="$inertia.visit(route('admin.products.index'))">
            Cancel
          </Button>
          <Button type="submit" :disabled="form.processing">
            <span v-if="form.processing">Creating...</span>
            <span v-else>Create Product</span>
          </Button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Plus } from 'lucide-vue-next'
import AdminLayout from '@/layouts/AdminLayout.vue'
import Card from '@/components/ui/card/Card.vue'
import CardHeader from '@/components/ui/card/CardHeader.vue'
import CardTitle from '@/components/ui/card/CardTitle.vue'
import CardDescription from '@/components/ui/card/CardDescription.vue'
import CardContent from '@/components/ui/card/CardContent.vue'
import Button from '@/components/ui/button/Button.vue'
import Input from '@/components/ui/Input.vue'
import ImageUpload from '@/components/ui/ImageUpload.vue'

defineOptions({
  layout: AdminLayout,
})

const props = defineProps({
  categories: Array,
  brands: Array,
})

const form = useForm({
  name: '',
  description: '',
  price: '',
  images: [],
  category_id: '',
  brand_id: '',
  sku: '',
  is_active: true,
  is_featured: false,
  discount: 0,
  variants: [
    {
      size: '',
      color: '',
      stock: 0,
      sku: '',
      price_adjustment: 0,
    }
  ],
})

const addVariant = () => {
  form.variants.push({
    size: '',
    color: '',
    stock: 0,
    sku: '',
    price_adjustment: 0,
  })
}

const removeVariant = (index) => {
  if (form.variants.length > 1) {
    form.variants.splice(index, 1)
  }
}

const handleImageError = (error) => {
  console.error('Image upload error:', error)
}

const submit = () => {
  form.post(route('admin.products.store'))
}
</script>
