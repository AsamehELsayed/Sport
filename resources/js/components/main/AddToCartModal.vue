<template>
  <div
    v-if="isOpen"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
    @click="handleBackdropClick"
  >
    <div
      class="bg-card border border-border rounded-lg shadow-xl max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto"
      @click.stop
    >
      <!-- Header -->
      <div class="flex items-center justify-between p-6 border-b border-border">
        <h2 class="text-xl font-semibold text-foreground">Add to Cart</h2>
        <button
          @click="closeModal"
          class="text-muted-foreground hover:text-foreground transition-colors"
        >
          <X class="w-5 h-5" />
        </button>
      </div>

      <!-- Content -->
      <div v-if="isFetchingProduct" class="p-6 space-y-6">
        <div class="flex items-center justify-center py-8">
          <div class="animate-spin rounded-full border-2 border-t-transparent border-primary h-8 w-8"></div>
          <span class="ml-2 text-muted-foreground">Loading product details...</span>
        </div>
      </div>

      <div v-else class="p-6 space-y-6">
        <!-- Product Info -->
        <div class="flex gap-4">
          <img
            :src="product?.image || '/images/placeholder-product.svg'"
            :alt="product?.name"
            class="w-20 h-20 object-cover rounded-lg border border-border"
            @error="$event.target.src = '/images/placeholder-product.svg'"
          />
          <div class="flex-1">
            <h3 class="font-semibold text-foreground">{{ product?.name }}</h3>
            <p class="text-sm text-muted-foreground">{{ product?.category }}</p>
            <div class="flex items-center gap-2 mt-1">
              <span class="text-lg font-bold text-primary">${{ selectedPrice }}</span>
              <span
                v-if="product?.original_price && product.original_price > selectedPrice"
                class="text-sm text-muted-foreground line-through"
              >
                ${{ product.original_price }}
              </span>
            </div>
          </div>
        </div>

        <!-- Size Selection -->
        <div v-if="availableSizes.length > 0">
          <label class="block text-sm font-medium text-foreground mb-3">
            Size <span class="text-red-500">*</span>
          </label>
          <div class="grid grid-cols-3 gap-2">
            <button
              v-for="size in availableSizes"
              :key="size"
              @click="selectedSize = size"
              class="px-4 py-2 text-sm border rounded-md transition-colors"
              :class="
                selectedSize === size
                  ? 'border-primary bg-primary text-primary-foreground'
                  : 'border-border hover:border-primary/50'
              "
            >
              {{ size }}
            </button>
          </div>
          <p v-if="sizeError" class="text-sm text-red-500 mt-1">
            Please select a size
          </p>
        </div>

        <!-- Color Selection -->
        <div v-if="availableColors.length > 0">
          <label class="block text-sm font-medium text-foreground mb-3">
            Color <span class="text-red-500">*</span>
          </label>
          <div class="grid grid-cols-4 gap-2">
            <button
              v-for="color in availableColors"
              :key="color"
              @click="selectedColor = color"
              class="px-4 py-2 text-sm border rounded-md transition-colors"
              :class="
                selectedColor === color
                  ? 'border-primary bg-primary text-primary-foreground'
                  : 'border-border hover:border-primary/50'
              "
            >
              {{ color }}
            </button>
          </div>
          <p v-if="colorError" class="text-sm text-red-500 mt-1">
            Please select a color
          </p>
        </div>

        <!-- No Variants Message -->
        <div v-if="!hasAnyVariants" class="text-sm text-muted-foreground">
          No variants available for this product.
        </div>

        <!-- Quantity Selection -->
        <div>
          <label class="block text-sm font-medium text-foreground mb-3">
            Quantity
          </label>
          <div class="flex items-center gap-3">
            <div class="flex items-center border rounded-md">
              <Button
                variant="ghost"
                size="icon"
                class="h-10 w-10"
                @click="decrementQuantity"
                :disabled="quantity <= 1"
              >
                <Minus class="h-4 w-4" />
              </Button>
              <span class="px-4 py-2 min-w-12 text-center font-medium">{{ quantity }}</span>
              <Button
                variant="ghost"
                size="icon"
                class="h-10 w-10"
                @click="incrementQuantity"
                :disabled="!canIncrement"
              >
                <Plus class="h-4 w-4" />
              </Button>
            </div>
            <span class="text-sm text-muted-foreground">
              Max: {{ maxQuantity }}
            </span>
          </div>
        </div>

        <!-- Stock Warning -->
        <div v-if="showStockWarning" class="bg-red-50 border border-red-200 rounded-md p-3">
          <div class="flex items-center gap-2">
            <AlertCircle class="w-4 h-4 text-red-500" />
            <span class="text-sm text-red-700">
              {{ stockWarningMessage }}
            </span>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="flex gap-3 p-6 border-t border-border">
        <Button
          variant="outline"
          class="flex-1"
          @click="closeModal"
        >
          Cancel
        </Button>
        <Button
          class="flex-1"
          :disabled="isLoading"
          @click="isAuthenticated ? handleAddToCart() : router.visit('/login')"
        >
          <ShoppingCart v-if="!isLoading" class="w-4 h-4 mr-2" />
          <span v-if="isLoading" class="animate-spin rounded-full border-2 border-t-transparent border-white h-4 w-4 mr-2"></span>
          {{ isLoading ? 'Adding...' : (isAuthenticated ? 'Add to Cart' : 'Sign in to Add') }}
        </Button>
      </div>

      <!-- Authentication Notice for Guests -->
      <div v-if="!isAuthenticated" class="px-6 pb-6">
        <div class="bg-blue-50 border border-blue-200 rounded-md p-3">
          <div class="flex items-center gap-2">
            <AlertCircle class="w-4 h-4 text-blue-500" />
            <span class="text-sm text-blue-700">
              Please sign in to add items to your cart
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { X, Minus, Plus, ShoppingCart, AlertCircle } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { useCart } from '@/composables/useCart'
import { usePage, router } from '@inertiajs/vue3'

interface ProductVariant {
  id: number
  size?: string
  color?: string
  stock: number
  price_adjustment: number
  is_active: boolean
  is_default?: boolean
  images?: string[]
  main_image?: string
  image_urls?: string[]
  has_images?: boolean
}

interface Product {
  id: number
  name: string
  price: number
  original_price?: number
  image: string
  category?: string
  variants?: ProductVariant[]
  activeVariants?: ProductVariant[]
  total_stock?: number
  available_sizes?: string[]
  available_colors?: string[]
  has_stock?: boolean
}

const props = defineProps<{
  isOpen: boolean
  product: Product | null
}>()

const emit = defineEmits<{
  close: []
  added: [item: any]
}>()

const { addToCart } = useCart()

// Get auth user from Inertia page props
const page = usePage()
const user = computed(() => page.props.auth?.user)
const isAuthenticated = computed(() => !!user.value)

// Local state
const selectedSize = ref('')
const selectedColor = ref('')
const selectedVariant = ref(null)
const quantity = ref(1)
const isLoading = ref(false)
const sizeError = ref(false)
const colorError = ref(false)
const fullProduct = ref<Product | null>(null)
const isFetchingProduct = ref(false)
const currentImageIndex = ref(0)

// Computed properties
const hasAnyVariants = computed(() => {
  return availableSizes.value.length > 0 || availableColors.value.length > 0 ||
         (fullProduct.value?.variants && fullProduct.value.variants.length > 0) ||
         (props.product?.variants && props.product.variants.length > 0)
})

const availableSizes = computed(() => {
  // Use full product data if available
  if (fullProduct.value?.available_sizes && fullProduct.value.available_sizes.length > 0) {
    return fullProduct.value.available_sizes
  }

  // Use backend data if available
  if (props.product?.available_sizes && props.product.available_sizes.length > 0) {
    return props.product.available_sizes
  }

  // Fallback to variants
  if (!fullProduct.value?.variants && !props.product?.variants) return []

  const variants = fullProduct.value?.variants || props.product?.variants
  return [...new Set(variants
    .filter((v: any) => v.stock > 0)
    .map((v: any) => v.size)
    .filter(Boolean))]
})

const availableColors = computed(() => {
  // Use full product data if available
  if (fullProduct.value?.available_colors && fullProduct.value.available_colors.length > 0) {
    return fullProduct.value.available_colors
  }

  // Use backend data if available
  if (props.product?.available_colors && props.product.available_colors.length > 0) {
    return props.product.available_colors
  }

  // Fallback to variants
  if (!fullProduct.value?.variants && !props.product?.variants) return []

  const variants = fullProduct.value?.variants || props.product?.variants
  return [...new Set(variants
    .filter((v: any) => v.stock > 0)
    .map((v: any) => v.color)
    .filter(Boolean))]
})

const availableVariants = computed(() => {
  const variants = fullProduct.value?.variants || props.product?.variants || []
  return variants.filter((v: any) => v.is_active && v.stock > 0)
})

const currentImage = computed(() => {
  // If a variant is selected and has images, use variant image
  if (selectedVariant.value && selectedVariant.value.has_images) {
    return selectedVariant.value.main_image
  }

  // Otherwise use product image
  return props.product?.image || '/images/placeholder-product.svg'
})

const availableImages = computed(() => {
  // If a variant is selected and has images, use variant images
  if (selectedVariant.value && selectedVariant.value.has_images) {
    return selectedVariant.value.image_urls || []
  }

  // Otherwise return empty array (no additional images)
  return []
})

const selectedPrice = computed(() => {
  if (!props.product) return 0

  const basePrice = props.product.price
  const adjustment = selectedVariant.value?.price_adjustment || 0
  return (basePrice + adjustment).toFixed(2)
})

const maxQuantity = computed(() => {
  // If we have backend data indicating stock, use total stock
  if (props.product?.has_stock && props.product?.total_stock) {
    return props.product.total_stock
  }

  // If we have available sizes/colors from backend, assume reasonable stock
  if (availableSizes.value.length > 0 || availableColors.value.length > 0) {
    return props.product?.total_stock || 999
  }

  // If we have variants, use selected variant stock
  if (selectedVariant.value) {
    return selectedVariant.value.stock
  }

  // Fallback
  return props.product?.total_stock || 0
})

const hasStock = computed(() => {
  // If backend says it has stock, trust it
  if (props.product?.has_stock) {
    return true
  }

  // If we have available sizes/colors, assume stock
  if (availableSizes.value.length > 0 || availableColors.value.length > 0) {
    return true
  }

  // Check variants
  if (props.product?.variants && props.product.variants.length > 0) {
    if (!selectedSize.value) {
      return props.product.variants.some(v => v.stock > 0)
    }
    return selectedVariant.value?.stock > 0 || false
  }

  // Fallback to total stock
  return (props.product?.total_stock || 0) > 0
})

const showStockWarning = computed(() => {
  // Don't show warning if we have available options
  if (availableSizes.value.length > 0 || availableColors.value.length > 0) {
    return false
  }

  // Don't show warning if backend says it has stock
  if (props.product?.has_stock) {
    return false
  }

  // Show warning if no stock
  return !hasStock.value
})

const stockWarningMessage = computed(() => {
  if (selectedSize.value) {
    return 'This variant is currently out of stock'
  }
  return 'This product is currently out of stock'
})

const canIncrement = computed(() => {
  return quantity.value < maxQuantity.value
})

const canAddToCart = computed(() => {
  if (!props.product) return false

  // If we have available sizes but none selected, can't add
  if (availableSizes.value.length > 0 && !selectedSize.value) {
    return false
  }

  // If we have available colors but none selected, can't add
  if (availableColors.value.length > 0 && !selectedColor.value) {
    return false
  }

  // Check quantity
  if (quantity.value <= 0 || quantity.value > maxQuantity.value) {
    return false
  }

  // Check stock
  return hasStock.value
})

// Methods
const closeModal = () => {
  emit('close')
}

const handleBackdropClick = () => {
  closeModal()
}

const incrementQuantity = () => {
  if (canIncrement.value) {
    quantity.value++
  }
}

const decrementQuantity = () => {
  quantity.value = Math.max(1, quantity.value - 1)
}

const validateSelection = () => {
  sizeError.value = availableSizes.value.length > 0 && !selectedSize.value
  colorError.value = availableColors.value.length > 0 && !selectedColor.value
  return !sizeError.value && !colorError.value
}

const selectVariant = (variant) => {
  selectedVariant.value = variant
  selectedSize.value = variant.size
  selectedColor.value = variant.color
  currentImageIndex.value = 0 // Reset to first image
}

const selectSize = (size) => {
  selectedSize.value = size
  // Find variant with this size and current color
  const variant = availableVariants.value.find(v =>
    v.size === size &&
    (selectedColor.value ? v.color === selectedColor.value : true)
  )
  if (variant) {
    selectVariant(variant)
  } else {
    selectedVariant.value = null
  }
}

const selectColor = (color) => {
  selectedColor.value = color
  // Find variant with this color and current size
  const variant = availableVariants.value.find(v =>
    v.color === color &&
    (selectedSize.value ? v.size === selectedSize.value : true)
  )
  if (variant) {
    selectVariant(variant)
  } else {
    selectedVariant.value = null
  }
}

const handleAddToCart = async () => {
  if (!validateSelection() || !canAddToCart.value) return

  isLoading.value = true

  try {
    const cartItem = {
      id: props.product!.id,
      name: props.product!.name,
      price: parseFloat(selectedPrice.value),
      originalPrice: props.product!.original_price || undefined,
      image: currentImage.value,
      size: selectedVariant.value?.size || selectedSize.value || 'N/A',
      color: selectedVariant.value?.color || selectedColor.value || 'N/A',
      inStock: hasStock.value,
      stockQuantity: selectedVariant.value?.stock || maxQuantity.value,
      rating: 0,
      reviews: 0,
      category: props.product!.category || '',
      brand: props.product!.brand || '',
      variantId: selectedVariant.value?.id
    }

    addToCart(cartItem, quantity.value)

    emit('added', {
      ...cartItem,
      quantity: quantity.value,
      variant: selectedVariant.value
    })

    closeModal()

  } catch (error) {
    console.error('Failed to add to cart:', error)
  } finally {
    isLoading.value = false
  }
}

// Fetch full product data when modal opens
const fetchProductDetails = async (productId: number) => {
  if (!productId) return

  isFetchingProduct.value = true
  try {
    const response = await fetch(`/api/products/${productId}/details`)
    if (response.ok) {
      const data = await response.json()
      fullProduct.value = data.product
    }
  } catch (error) {
    console.error('Failed to fetch product details:', error)
  } finally {
    isFetchingProduct.value = false
  }
}

// Reset form when modal opens/closes
watch(() => props.isOpen, async (isOpen: boolean) => {
  if (isOpen && props.product?.id) {
    // Reset form
    selectedSize.value = ''
    selectedColor.value = ''
    quantity.value = 1
    sizeError.value = false
    colorError.value = false
    fullProduct.value = null

    // Fetch full product data
    await fetchProductDetails(props.product.id)

    // Auto-select first size if only one available
    if (availableSizes.value.length === 1) {
      selectedSize.value = availableSizes.value[0]
    }

    // Auto-select first color if only one available
    if (availableColors.value.length === 1) {
      selectedColor.value = availableColors.value[0]
    }
  }
})

// Watch for variant changes and adjust quantity if needed
watch([selectedSize, selectedColor], () => {
  if (quantity.value > maxQuantity.value) {
    quantity.value = maxQuantity.value
  }
})
</script>
