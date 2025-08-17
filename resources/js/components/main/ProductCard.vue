<script setup lang="ts">
import { ref, computed } from "vue";
import Button from "@/components/ui/Button.vue";
import BaseBadge from "@/components/ui/BaseBadge.vue";
import { Heart, ShoppingCart, Star, Eye } from "lucide-vue-next";
import { Link } from "@inertiajs/vue3";
import { roundPrice } from "@/utils/price";

// Props
const props = defineProps({
  id: Number,
  name: String,
  price: Number,
  original_price: Number,
  image: String,
  rating: Number,
  reviews: Number,
  category: String,
  brand: String,
  isNew: Boolean,
  isSale: Boolean,
  discount: {
    type: Number,
    default: 0
  },
  inStock: {
    type: Boolean,
    default: true
  }
});

// Emits
const emit = defineEmits<{
  addToCart: [product: any];
  addToWishlist: [product: any];
  quickView: [product: any];
}>();

// Local states
const isWishlisted = ref(false);
const isHovered = ref(false);
const isAddingToCart = ref(false);

// Computed discount
const after_discount = computed(() => {
  if (typeof props.original_price !== "undefined" && props.original_price > 0) {
    return roundPrice((props.original_price * (100 - props.discount)) / 100);
  }
  return roundPrice(props.price);
});

// Methods
const handleAddToCart = async (event: Event) => {
  event.preventDefault();
  event.stopPropagation();

  if (!props.inStock) return;

  isAddingToCart.value = true;

  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 500));

    emit('addToCart', {
      id: props.id,
      name: props.name,
      price: props.price,
      image: props.image,
      quantity: 1
    });

    // Show success feedback
    console.log('Added to cart:', props.name);
  } catch (error) {
    console.error('Failed to add to cart:', error);
  } finally {
    isAddingToCart.value = false;
  }
};

const handleWishlist = (event: Event) => {
  event.preventDefault();
  event.stopPropagation();

  isWishlisted.value = !isWishlisted.value;

  emit('addToWishlist', {
    id: props.id,
    name: props.name,
    price: props.price,
    image: props.image,
    isWishlisted: isWishlisted.value
  });

  console.log(isWishlisted.value ? 'Added to wishlist:' : 'Removed from wishlist:', props.name);
};

const handleQuickView = (event: Event) => {
  event.preventDefault();
  event.stopPropagation();

  emit('quickView', {
    id: props.id,
    name: props.name,
    price: props.price,
    original_price: props.original_price,
    image: props.image,
    rating: props.rating,
    reviews: props.reviews,
    category: props.category,
    brand: props.brand,
    isNew: props.isNew,
    isSale: props.isSale,
    inStock: props.inStock
  });
};
</script>

<template>
  <Link :href="`/products/${id}`" as="div"
    class="card-athletic group cursor-pointer transition-all duration-300 hover:scale-[1.02]"
    @mouseenter="isHovered = true"
    @mouseleave="isHovered = false"
  >
    <!-- Image Container -->
    <div class="relative overflow-hidden rounded-t-xl">
      <img
        :src="image || '/images/placeholder-product.jpg'"
        :alt="name"
        class="w-full h-64 object-cover transition-transform duration-500 group-hover:scale-110"
      />

      <!-- Badges -->
      <div class="absolute top-3 left-3 flex flex-col gap-2">
        <BaseBadge v-if="isNew" class="bg-primary text-primary-foreground">NEW</BaseBadge>
        <BaseBadge v-if="isSale" variant="destructive">{{ discount }}%</BaseBadge>
      </div>

      <!-- Stock Status Badge -->
      <div v-if="!inStock" class="absolute top-3 left-3">
        <BaseBadge variant="destructive">Out of Stock</BaseBadge>
      </div>

      <!-- Wishlist Button -->
      <Button
        variant="ghost"
        size="icon"
        class="absolute top-3 right-3 transition-all duration-300"
        :class="isWishlisted
          ? 'bg-primary text-primary-foreground'
          : 'bg-black/20 backdrop-blur-sm text-white hover:bg-primary hover:text-primary-foreground'"
        @click="handleWishlist"
      >
        <Heart :class="['w-4 h-4', isWishlisted ? 'fill-current' : '']" />
      </Button>

      <!-- Quick Actions - Show on Hover -->
      <div
        class="absolute bottom-3 left-3 right-3 flex gap-2 transition-all duration-300"
        :class="isHovered ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
      >
        <Button
          size="sm"
          class="flex-1 bg-primary text-primary-foreground hover:bg-primary/90"
          :disabled="!inStock || isAddingToCart"
          @click="handleAddToCart"
        >
          <ShoppingCart v-if="!isAddingToCart" class="w-4 h-4 mr-2" />
          <span v-if="isAddingToCart" class="animate-spin rounded-full border-2 border-t-transparent border-white h-4 w-4 mr-2"></span>
          {{ isAddingToCart ? 'Adding...' : (inStock ? 'Add to Cart' : 'Out of Stock') }}
        </Button>
        <Button variant="secondary" size="sm" @click="handleQuickView">
          <Eye class="w-4 h-4" />
        </Button>
      </div>
    </div>

    <!-- Content -->
    <div class="p-4">
      <div class="text-sm text-muted-foreground mb-2">{{ category }}</div>

      <h3 class="font-semibold text-foreground mb-2 line-clamp-2 group-hover:text-primary transition-colors">
        {{ name }}
      </h3>

      <!-- Rating -->
      <div class="flex items-center gap-2 mb-3">
        <div class="flex items-center">
          <Star
            v-for="i in 5"
            :key="i"
            class="w-4 h-4"
            :class="i <= Math.floor(rating || 0) ? 'text-yellow-400 fill-current' : 'text-gray-300'"
          />
        </div>
        <span class="text-sm text-muted-foreground">({{ reviews || 0 }})</span>
      </div>

      <!-- Price -->
      <div class="flex items-center gap-2">
        <span class="text-xl font-bold text-primary">${{ after_discount }}</span>
        <span v-if="original_price" class="text-sm text-muted-foreground line-through">
          ${{ original_price }}
        </span>
      </div>
    </div>
  </Link>
</template>
