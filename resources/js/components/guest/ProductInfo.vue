<template>
  <div class="space-y-6">
    <!-- Product Header -->
    <div>
      <div class="flex items-center gap-2 mb-3">
        <Badge v-if="product.is_featured" variant="secondary">Featured</Badge>
        <Badge v-if="product.discount > 0" class="bg-destructive text-destructive-foreground">
          -{{ product.discount_percentage }}%
        </Badge>
        <Badge v-if="!product.has_stock" variant="destructive">Out of Stock</Badge>
      </div>
      <h1 class="text-3xl font-bold mb-3">{{ product.name }}</h1>
      <div class="flex items-center gap-2 mb-4">
        <div class="flex items-center">
          <Star
            v-for="i in 5"
            :key="i"
            class="h-4 w-4"
            :class="i <= Math.floor(product.rating) ? 'fill-yellow-400 text-yellow-400' : 'text-muted-foreground'"
          />
        </div>
        <span class="text-sm text-muted-foreground">
          {{ product.rating }} ({{ product.reviews_count }} reviews)
        </span>
      </div>
    </div>

    <!-- Price -->
    <div class="flex items-center gap-2">
      <span class="text-3xl font-bold text-primary">${{ selectedPrice }}</span>
      <span v-if="product.discount > 0" class="text-lg text-muted-foreground line-through">
        ${{ product.price }}
      </span>
    </div>

    <!-- Stock Status -->
    <div class="flex items-center gap-2 text-sm">
      <div class="flex items-center gap-1">
        <div class="w-2 h-2 rounded-full" :class="product.has_stock ? 'bg-green-500' : 'bg-red-500'"></div>
        <span :class="product.has_stock ? 'text-green-600' : 'text-red-600'">
          {{ product.has_stock ? `${selectedVariant?.stock || product.total_stock} in stock` : 'Out of stock' }}
        </span>
      </div>
    </div>

    <!-- Description -->
    <p class="text-muted-foreground leading-relaxed">{{ product.description }}</p>

    <!-- Variant Selection -->
    <VariantSelection
      :available-sizes="availableSizes"
      :available-colors="availableColors"
      :available-variants="availableVariants"
      :selected-size="selectedSize"
      :selected-color="selectedColor"
      :selected-variant="selectedVariant"
      @select-size="$emit('select-size', $event)"
      @select-color="$emit('select-color', $event)"
      @select-variant="$emit('select-variant', $event)"
    />

    <!-- Quantity -->
    <div>
      <h3 class="font-semibold mb-3">Quantity</h3>
      <div class="flex items-center gap-3">
        <div class="flex items-center border rounded-md">
          <Button
            variant="ghost"
            size="icon"
            class="h-10 w-10"
            @click="$emit('decrement-quantity')"
            :disabled="quantity <= 1"
          >
            <Minus class="h-4 w-4" />
          </Button>
          <span class="px-4 py-2 min-w-12 text-center font-medium">{{ quantity }}</span>
          <Button
            variant="ghost"
            size="icon"
            class="h-10 w-10"
            @click="$emit('increment-quantity')"
            :disabled="!canIncrement"
          >
            <Plus class="h-4 w-4" />
          </Button>
        </div>
        <span class="text-sm text-muted-foreground">
          Max: {{ selectedVariant?.stock || product.total_stock }}
        </span>
      </div>
    </div>

    <!-- Actions -->
    <div class="flex gap-3">
      <Button
        class="flex-1"
        size="lg"
        :disabled="!canAddToCart"
        @click="$emit('add-to-cart')"
      >
        <ShoppingCart class="h-5 w-5 mr-2" />
        {{ product.has_stock ? 'Add to Cart' : 'Out of Stock' }}
      </Button>
      <Button
        variant="outline"
        size="lg"
        @click="$emit('toggle-favorite')"
        :class="isFavorite ? 'text-destructive border-destructive hover:bg-destructive/10' : ''"
      >
        <Heart class="h-5 w-5" :class="isFavorite ? 'fill-current' : ''" />
      </Button>
    </div>

    <!-- Validation Messages -->
    <div v-if="!selectedSize && availableSizes.length > 0" class="flex items-center gap-2 text-sm text-amber-600">
      <AlertCircle class="h-4 w-4" />
      <span>Please select a size to add to cart</span>
    </div>
    <div v-if="!selectedColor && availableColors.length > 0" class="flex items-center gap-2 text-sm text-amber-600">
      <AlertCircle class="h-4 w-4" />
      <span>Please select a color to add to cart</span>
    </div>

    <!-- Features -->
    <Card class="border-0 shadow-sm">
      <CardContent class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
          <div class="flex flex-col items-center gap-3">
            <div class="p-3 bg-primary/10 rounded-full">
              <Truck class="h-6 w-6 text-primary" />
            </div>
            <div>
              <div class="font-semibold">Free Shipping</div>
              <div class="text-sm text-muted-foreground">On orders over $100</div>
            </div>
          </div>
          <div class="flex flex-col items-center gap-3">
            <div class="p-3 bg-primary/10 rounded-full">
              <Shield class="h-6 w-6 text-primary" />
            </div>
            <div>
              <div class="font-semibold">2 Year Warranty</div>
              <div class="text-sm text-muted-foreground">Official warranty</div>
            </div>
          </div>
          <div class="flex flex-col items-center gap-3">
            <div class="p-3 bg-primary/10 rounded-full">
              <RotateCcw class="h-6 w-6 text-primary" />
            </div>
            <div>
              <div class="font-semibold">30-Day Returns</div>
              <div class="text-sm text-muted-foreground">Easy returns</div>
            </div>
          </div>
        </div>
      </CardContent>
    </Card>
  </div>
</template>

<script setup>
import { Star, Heart, ShoppingCart, Minus, Plus, Truck, Shield, RotateCcw, AlertCircle } from "lucide-vue-next"
import Button from "@/components/ui/button/Button.vue"
import Badge from "@/components/ui/badge/Badge.vue"
import Card from "@/components/ui/card/Card.vue"
import CardContent from "@/components/ui/card/CardContent.vue"
import VariantSelection from "./VariantSelection.vue"

defineProps({
  product: {
    type: Object,
    required: true
  },
  selectedPrice: {
    type: String,
    required: true
  },
  selectedVariant: {
    type: Object,
    default: null
  },
  availableSizes: {
    type: Array,
    default: () => []
  },
  availableColors: {
    type: Array,
    default: () => []
  },
  availableVariants: {
    type: Array,
    default: () => []
  },
  selectedSize: {
    type: String,
    default: ""
  },
  selectedColor: {
    type: String,
    default: ""
  },
  quantity: {
    type: Number,
    default: 1
  },
  canIncrement: {
    type: Boolean,
    default: false
  },
  canAddToCart: {
    type: Boolean,
    default: false
  },
  isFavorite: {
    type: Boolean,
    default: false
  }
})

defineEmits([
  'select-size',
  'select-color',
  'select-variant',
  'increment-quantity',
  'decrement-quantity',
  'add-to-cart',
  'toggle-favorite'
])
</script>
