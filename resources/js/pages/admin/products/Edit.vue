<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Plus, ArrowLeft } from 'lucide-vue-next'
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
});

const props = defineProps({
  product: Object,
  categories: Array,
  brands: Array
})

const selectedDefaultIndex = ref(0)

  const form = useForm({
    name: props.product?.name || '',
    description: props.product?.description || '',
    price: props.product?.price || '',
    category_id: props.product?.category_id || '',
  brand_id: props.product?.brand_id || '',
  sku: props.product?.sku || '',
  is_active: props.product?.is_active ?? true,
  is_featured: props.product?.is_featured ?? false,
  discount: props.product?.discount || 0,
  variants: props.product?.variants?.map((v, index) => {
    if (v.is_default) {
      selectedDefaultIndex.value = index
    }
    return {
      id: v.id,
      size: v.size,
      color: v.color,
      stock: v.stock,
      sku: v.sku,
      price_adjustment: v.price_adjustment || 0,
      is_default: v.is_default ?? false,
      images: v.images || [],
    }
  }) || [
    { size: '', color: '', stock: 0, sku: '', price_adjustment: 0, is_default: true, images: [] }
  ]
})

const addVariant = () => {
  form.variants.push({ size: '', color: '', stock: 0, sku: '', price_adjustment: 0, is_default: false, images: [] })
}

const removeVariant = (index) => {
  form.variants.splice(index, 1)
  // Adjust selected default index if needed
  if (selectedDefaultIndex.value >= form.variants.length) {
    selectedDefaultIndex.value = form.variants.length - 1
  }
  // Update default flags
  form.variants.forEach((variant, i) => {
    variant.is_default = i === selectedDefaultIndex.value
  })
}

const setDefaultVariant = (index) => {
  selectedDefaultIndex.value = index
  form.variants.forEach((variant, i) => {
    variant.is_default = i === index
  })
}

const handleImageError = (error) => {
  console.error('Image upload error:', error)
}

const submit = () => {
  form.put(route('admin.products.update', props.product.id))
}
</script>

<template>
  <div class="max-w-4xl mx-auto mt-8 space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
      <Button variant="ghost" size="icon" @click="$inertia.visit(route('admin.products.index'))">
        <ArrowLeft class="h-4 w-4" />
      </Button>
      <div>
        <h1 class="text-3xl font-bold text-foreground">Edit Product</h1>
        <p class="text-muted-foreground">Update product information and variants</p>
      </div>
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

      <!-- Product Images - Now handled by variants -->
      <Card>
        <CardHeader>
          <CardTitle>Product Images</CardTitle>
          <CardDescription>Images are now managed through variants. Upload images for each variant below.</CardDescription>
        </CardHeader>
        <CardContent>
          <div class="p-4 bg-muted/50 rounded-lg">
            <p class="text-sm text-muted-foreground">
              Product images are automatically generated from the default variant.
              Please upload images for each variant in the Variants section below.
            </p>
          </div>
        </CardContent>
      </Card>

      <!-- Product Variants -->
      <Card>
        <CardHeader>
          <CardTitle>Product Variants</CardTitle>
          <CardDescription>Add different sizes and colors with individual stock levels and images</CardDescription>
        </CardHeader>
        <CardContent>
          <div class="space-y-4">
            <div v-for="(variant, index) in form.variants" :key="index" class="border border-border rounded-lg p-4">
              <div class="flex items-center justify-between mb-4">
                <h4 class="font-medium">Variant {{ index + 1 }}</h4>
                                  <div class="flex items-center gap-2">
                    <label class="flex items-center space-x-2">
                      <input
                        v-model="selectedDefaultIndex"
                        type="radio"
                        :name="'default_variant'"
                        :value="index"
                        @change="setDefaultVariant(index)"
                        class="rounded border-border"
                      />
                      <span class="text-sm font-medium text-foreground">Default</span>
                    </label>
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

              <div class="mt-4">
                <label class="block text-sm font-medium text-foreground mb-2">Variant SKU</label>
                <Input v-model="variant.sku" placeholder="Optional variant-specific SKU" />
              </div>

              <div class="mt-4">
                <label class="block text-sm font-medium text-foreground mb-2">Variant Images</label>
                <ImageUpload
                  v-model="variant.images"
                  :multiple="true"
                  :max-files="3"
                  @error="handleImageError"
                />
                <p class="text-xs text-muted-foreground mt-1">Upload specific images for this variant (optional)</p>
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
          <span v-if="form.processing">Updating...</span>
          <span v-else>Update Product</span>
        </Button>
      </div>
    </form>
  </div>
</template>
