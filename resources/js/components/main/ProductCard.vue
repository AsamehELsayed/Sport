<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import Button from "@/components/ui/Button.vue";
import BaseBadge from "@/components/ui/BaseBadge.vue";
import { Heart, ShoppingCart, Star, Eye } from "lucide-vue-next";
import { Link, usePage } from "@inertiajs/vue3";
import { roundPrice } from "@/utils/price";
import { useWishlist } from "@/composables/useWishlist";

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
  },
  available_colors: {
    type: Array,
    default: () => []
  },
  available_sizes: {
    type: Array,
    default: () => []
  }
});

// Emits
const emit = defineEmits<{
  addToCart: [product: any];
  addToWishlist: [product: any];
  quickView: [product: any];
  openAddToCartModal: [product: any];
}>();

// Get auth user from Inertia page props
const page = usePage();
const user = computed(() => page.props.auth?.user);
const isAuthenticated = computed(() => !!user.value);

// Use wishlist composable
const { toggleWishlist, checkWishlistStatus, isLoading: isWishlistLoading } = useWishlist();

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

// Computed for showing sale badge
const showSaleBadge = computed(() => {
  return props.isSale && props.discount > 0;
});

// Methods
const handleAddToCart = async (event: Event) => {
  event.preventDefault();
  event.stopPropagation();

  if (!props.inStock) return;

  // Emit both events for backward compatibility
  emit('openAddToCartModal', {
    id: props.id,
    name: props.name,
    price: props.price,
    original_price: props.original_price,
    discount: props.discount,
    available_colors: props.available_colors,
    available_sizes: props.available_sizes,
    image: props.image,
    category: props.category,
    brand: props.brand,
    rating: props.rating,
    reviews: props.reviews,
    isNew: props.isNew,
    isSale: props.isSale,
    inStock: props.inStock
  });

  // Also emit the old event for backward compatibility
  emit('addToCart', {
    id: props.id,
    name: props.name,
    price: props.price,
    image: props.image,
    quantity: 1
  });
};

const handleWishlist = async (event: Event) => {
  event.preventDefault();
  event.stopPropagation();

  if (!isAuthenticated.value) {
    console.log('User not authenticated, redirecting to login...');
    window.location.href = '/login';
    return;
  }

  if (isWishlistLoading.value) return;

  try {
    // Use the composable to toggle wishlist
    const result = await toggleWishlist(props.id);

    if (result && result.success) {
      isWishlisted.value = result.isWishlisted;

      emit('addToWishlist', {
        id: props.id,
        name: props.name,
        price: props.price,
        image: props.image,
        isWishlisted: isWishlisted.value
      });

      console.log(isWishlisted.value ? 'Added to wishlist:' : 'Removed from wishlist:', props.name);
    } else if (result) {
      console.error('Failed to toggle wishlist:', result.message);
      // If unauthorized, redirect to login
      if (result.message.includes('Authentication required')) {
        window.location.href = '/login';
      }
    }
  } catch (error) {
    console.error('Error toggling wishlist:', error);
  }
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

// Check initial wishlist status when component mounts
const checkInitialWishlistStatus = async () => {
  if (!isAuthenticated.value) return;

  try {
    const isInWishlist = await checkWishlistStatus(props.id);
    isWishlisted.value = isInWishlist;
  } catch (error) {
    console.error('Error checking wishlist status:', error);
  }
};

// Initialize wishlist status on mount
onMounted(() => {
  checkInitialWishlistStatus();
});
</script>

<template>
  <Link :href="`/products/${id}`" as="div"
    class="group cursor-pointer transition-all duration-300 hover:scale-[1.02] bg-card border border-border rounded-xl overflow-hidden shadow-sm hover:shadow-lg"
    @mouseenter="isHovered = true"
    @mouseleave="isHovered = false"
  >
    <!-- Image Container -->
    <div class="relative overflow-hidden">
      <img
        :src="image"
        :alt="name"
        class="w-full h-64 object-cover transition-transform duration-500 group-hover:scale-110"
        @error="$event.target.src = '/images/placeholder-product.svg'"
      />

      <!-- Overlay for better text readability -->
      <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

      <!-- Badges Container - Left Side -->
      <div class="absolute top-3 left-3 flex flex-col gap-2 z-10">
        <!-- New Badge -->
        <BaseBadge v-if="isNew" class="bg-primary text-primary-foreground text-xs font-medium shadow-sm">
          NEW
        </BaseBadge>

        <!-- Sale Badge - Only show if discount > 0 -->
        <BaseBadge v-if="showSaleBadge" variant="destructive" class="text-xs font-medium shadow-sm">
          -{{ discount }}% OFF
        </BaseBadge>
      </div>

      <!-- Stock Status Badge - Right Side -->
      <div v-if="!inStock" class="absolute top-3 right-3 z-10">
        <BaseBadge variant="destructive" class="text-xs font-medium shadow-sm bg-red-600 text-white">
          Out of Stock
        </BaseBadge>
      </div>

      <!-- Wishlist Button - Only show if authenticated and in stock -->
      <Button
        v-if="isAuthenticated && inStock"
        variant="ghost"
        size="icon"
        class="absolute top-3 right-3 transition-all duration-300 z-10"
        :class="isWishlisted
          ? 'bg-primary text-primary-foreground shadow-sm'
          : 'bg-white/90 backdrop-blur-sm text-gray-700 hover:bg-primary hover:text-primary-foreground shadow-sm'"
        :disabled="isWishlistLoading"
        @click="handleWishlist"
      >
        <Heart v-if="!isWishlistLoading" :class="['w-4 h-4', isWishlisted ? 'fill-current' : '']" />
        <span v-if="isWishlistLoading" class="animate-spin rounded-full border-2 border-t-transparent border-current h-4 w-4"></span>
      </Button>

      <!-- Quick Actions - Show on Hover -->
      <div
        class="absolute bottom-3 left-3 right-3 flex gap-2 transition-all duration-300 z-10"
        :class="isHovered ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
      >
        <Button
          size="sm"
          class="flex-1 bg-primary text-primary-foreground hover:bg-primary/90 shadow-sm"
          :disabled="!inStock || isAddingToCart"
          @click="handleAddToCart"
        >
          <ShoppingCart v-if="!isAddingToCart" class="w-4 h-4 mr-2" />
          <span v-if="isAddingToCart" class="animate-spin rounded-full border-2 border-t-transparent border-white h-4 w-4 mr-2"></span>
          {{ isAddingToCart ? 'Adding...' : (inStock ? 'Add to Cart' : 'Out of Stock') }}
        </Button>

      </div>
    </div>

    <!-- Content -->
    <div class="p-4 space-y-3">
      <!-- Category and Brand -->
      <div class="flex items-center justify-between text-xs text-muted-foreground">
        <span class="truncate">{{ category }}</span>
        <span v-if="brand" class="truncate ml-2 text-primary font-medium">{{ brand }}</span>
      </div>

      <!-- Product Name -->
      <h3 class="font-semibold text-foreground text-sm leading-tight line-clamp-2 group-hover:text-primary transition-colors">
        {{ name }}
      </h3>

      <!-- Rating -->
      <div class="flex items-center gap-2">
        <div class="flex items-center">
          <Star
            v-for="i in 5"
            :key="i"
            class="w-3 h-3"
            :class="i <= Math.floor(rating || 0) ? 'text-yellow-400 fill-current' : 'text-gray-300'"
          />
        </div>
        <span class="text-xs text-muted-foreground">({{ reviews || 0 }})</span>
      </div>

      <!-- Price -->
      <div class="flex items-center gap-2">
        <span class="text-lg font-bold text-primary">${{ after_discount }}</span>
        <span v-if="original_price && original_price > after_discount" class="text-sm text-muted-foreground line-through">
          ${{ original_price }}
        </span>
      </div>

      <!-- Stock Indicator -->
      <div v-if="!inStock" class="flex items-center gap-2 text-xs text-red-600">
        <div class="w-2 h-2 bg-red-500 rounded-full"></div>
        <span>Currently unavailable</span>
      </div>
    </div>
  </Link>
</template>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
