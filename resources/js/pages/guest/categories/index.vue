<script setup lang="ts">
import { ref, computed, watch, onMounted } from "vue";
import { Link, router } from "@inertiajs/vue3";
import ProductCard from "@/components/main/ProductCard.vue";
import Button from "@/components/ui/Button.vue";
import BaseBadge from "@/components/ui/BaseBadge.vue";
import CartNotification from "@/components/main/CartNotification.vue";
import { Filter, Grid, List, Star, Zap, Search, X } from "lucide-vue-next";
import GuestLayout from '@/layouts/GuestLayout.vue';

defineOptions({
    layout: GuestLayout,
});

// Props from Inertia
const props = defineProps({
  category: {
    type: String,
    default: 'All Categories'
  },
  products: {
    type: Object,
    default: () => ({ data: [] })
  },
  categories: {
    type: Array,
    default: () => []
  },
  brands: {
    type: Array,
    default: () => []
  },
  filters: {
    type: Object,
    default: () => ({})
  },
  searchQuery: {
    type: String,
    default: ''
  }
});

// Local state
const viewMode = ref('grid'); // 'grid' or 'list'
const showFilters = ref(false);
const searchInput = ref(props.searchQuery || '');
const selectedBrands = ref(props.filters.brand ? props.filters.brand.split(',') : []);
const selectedPriceRange = ref([
  props.filters.min_price ? parseInt(props.filters.min_price) : 0,
  props.filters.max_price ? parseInt(props.filters.max_price) : 500
]);
const sortBy = ref(props.filters.sort || 'featured');

// Notification state
const showNotification = ref(false);
const notificationMessage = ref('');
const notificationType = ref<'success' | 'error' | 'info'>('success');

// Computed sorted products (server-side sorting is handled by backend)
const sortedProducts = computed(() => {
  return props.products.data || [];
});

// Computed for active filters count
const activeFiltersCount = computed(() => {
  let count = 0;
  if (selectedBrands.value.length > 0) count++;
  if (selectedPriceRange.value[0] > 0 || selectedPriceRange.value[1] < 500) count++;
  if (sortBy.value !== 'featured') count++;
  if (searchInput.value) count++;
  return count;
});

// Methods
const toggleBrand = (brandSlug: string) => {
  const index = selectedBrands.value.indexOf(brandSlug);
  if (index > -1) {
    selectedBrands.value.splice(index, 1);
  } else {
    selectedBrands.value.push(brandSlug);
  };
};

const clearFilters = () => {
  selectedBrands.value = [];
  selectedPriceRange.value = [0, 500];
  sortBy.value = 'featured';
  searchInput.value = '';

  // Redirect to base URL without filters
  router.visit(window.location.pathname, { preserveState: true, preserveScroll: true });
};

const handleSearch = () => {
  const params = new URLSearchParams(window.location.search);

  if (searchInput.value.trim()) {
    params.set('search', searchInput.value.trim());
  } else {
    params.delete('search');
  }

  // Preserve other filters
  if (selectedBrands.value.length > 0) {
    params.set('brand', selectedBrands.value.join(','));
  }
  if (sortBy.value !== 'featured') {
    params.set('sort', sortBy.value);
  }
  if (selectedPriceRange.value[0] > 0) {
    params.set('min_price', selectedPriceRange.value[0].toString());
  }
  if (selectedPriceRange.value[1] < 500) {
    params.set('max_price', selectedPriceRange.value[1].toString());
  }

  const url = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
  router.visit(url, { preserveState: true, preserveScroll: true });
};

// Debounced search function
let searchTimeout: NodeJS.Timeout;
const debouncedSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    handleSearch();
  }, 500);
};

const clearSearch = () => {
  searchInput.value = '';
  handleSearch();
};

// Watch for filter changes
watch([selectedBrands, sortBy], () => {
  // Debounce the filter changes to avoid too many requests
  clearTimeout(window.filterTimeout);
  window.filterTimeout = setTimeout(() => {
    const params = new URLSearchParams(window.location.search);

    if (selectedBrands.value.length > 0) {
      params.set('brand', selectedBrands.value.join(','));
    } else {
      params.delete('brand');
    }

    if (sortBy.value !== 'featured') {
      params.set('sort', sortBy.value);
    } else {
      params.delete('sort');
    }

    // Add price range filters
    if (selectedPriceRange.value[0] > 0) {
      params.set('min_price', selectedPriceRange.value[0].toString());
    } else {
      params.delete('min_price');
    }

    if (selectedPriceRange.value[1] < 500) {
      params.set('max_price', selectedPriceRange.value[1].toString());
    } else {
      params.delete('max_price');
    }

    // Preserve search
    if (searchInput.value.trim()) {
      params.set('search', searchInput.value.trim());
    }

    const url = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
    router.visit(url, { preserveState: true, preserveScroll: true });
  }, 300);
}, { deep: true });

// Watch for price range changes and trigger server request
watch(selectedPriceRange, (newRange) => {
  // Validate price range
  if (newRange[0] > newRange[1]) {
    // Swap values if min is greater than max
    const temp = newRange[0];
    newRange[0] = newRange[1];
    newRange[1] = temp;
  }

  // Ensure values are within bounds
  newRange[0] = Math.max(0, Math.min(500, newRange[0]));
  newRange[1] = Math.max(0, Math.min(500, newRange[1]));

  // Debounce the price filter to avoid too many requests
  clearTimeout(window.priceFilterTimeout);
  window.priceFilterTimeout = setTimeout(() => {
    const params = new URLSearchParams(window.location.search);

    if (newRange[0] > 0) {
      params.set('min_price', newRange[0].toString());
    } else {
      params.delete('min_price');
    }

    if (newRange[1] < 500) {
      params.set('max_price', newRange[1].toString());
    } else {
      params.delete('max_price');
    }

    // Add other current filters
    if (selectedBrands.value.length > 0) {
      params.set('brand', selectedBrands.value.join(','));
    }
    if (sortBy.value !== 'featured') {
      params.set('sort', sortBy.value);
    }
    if (searchInput.value.trim()) {
      params.set('search', searchInput.value.trim());
    }

    const url = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
    router.visit(url, { preserveState: true, preserveScroll: true });
  }, 500);
}, { deep: true });

// Watch for search input changes (debounced)
watch(searchInput, () => {
  debouncedSearch();
});

// Card event handlers
const handleAddToCart = (product: any) => {
  console.log('Adding to cart:', product);

  // Show success notification
  notificationMessage.value = `${product.name} added to cart!`;
  notificationType.value = 'success';
  showNotification.value = true;
};

const handleNotificationClose = () => {
  showNotification.value = false;
};

const handleAddToWishlist = (product: any) => {
  console.log('Wishlist action:', product);
};

const handleQuickView = (product: any) => {
  console.log('Quick view:', product);
  window.location.href = `/products/${product.id}`;
};

// Initialize search input from URL params on mount
onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search);
  const searchParam = urlParams.get('search');
  if (searchParam) {
    searchInput.value = searchParam;
  }
});
</script>

<template>
  <div class="min-h-screen bg-background">
    <!-- Header -->
    <div class="bg-gradient-to-r from-primary/10 to-secondary/10 border-b border-border">
      <div class="container mx-auto px-4 py-8">
        <div class="flex items-center gap-3 mb-4">
          <Link href="/" class="text-muted-foreground hover:text-primary">
            Home
          </Link>
          <span class="text-muted-foreground">/</span>
          <span class="text-foreground font-medium">Categories</span>
          <span v-if="category !== 'All Categories'" class="text-muted-foreground">/</span>
          <span v-if="category !== 'All Categories'" class="text-foreground font-medium">
            {{ category }}
          </span>
        </div>

        <h1 class="text-4xl font-bold mb-2">
          <span class="text-foreground">{{ category }}</span>
          <span v-if="searchQuery" class="text-2xl font-normal text-muted-foreground ml-2">
            for "{{ searchQuery }}"
          </span>
        </h1>
        <p class="text-muted-foreground text-lg">
          Discover the best sports equipment for your needs
        </p>

        <!-- Search Bar -->
        <div class="mt-6 max-w-md">
          <div class="relative">
            <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground w-4 h-4" />
            <input
              v-model="searchInput"
              type="text"
              placeholder="Search products..."
              class="w-full pl-10 pr-10 py-2 bg-card border border-border rounded-lg focus:border-primary focus:outline-none"
              @keyup.enter="handleSearch"
            />
            <button
              v-if="searchInput"
              @click="clearSearch"
              class="absolute right-3 top-1/2 transform -translate-y-1/2 text-muted-foreground hover:text-foreground"
            >
              <X class="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="container mx-auto px-4 py-8">
      <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar Filters -->
        <div class="lg:w-80 space-y-6">
          <!-- Categories -->
          <div class="bg-card border border-border rounded-lg p-6">
            <h3 class="font-semibold text-foreground mb-4 flex items-center gap-2">
              <Zap class="w-4 h-4" />
              Categories
            </h3>
            <div class="space-y-3">
              <Link
                v-for="cat in categories"
                :key="cat.id"
                :href="`/categories/${cat.slug}`"
                class="flex items-center justify-between p-3 rounded-md hover:bg-accent transition-colors"
                :class="category === cat.name ? 'bg-primary/10 text-primary' : 'text-foreground'"
              >
                <div class="flex items-center gap-3">
                  <span class="text-lg">{{ cat.icon || '🏃' }}</span>
                  <span>{{ cat.name }}</span>
                </div>
                <BaseBadge variant="secondary" class="text-xs">
                  {{ cat.products_count || 0 }}
                </BaseBadge>
              </Link>
            </div>
          </div>

          <!-- Filters -->
          <div class="bg-card border border-border rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
              <h3 class="font-semibold text-foreground flex items-center gap-2">
                <Filter class="w-4 h-4" />
                Filters
                <BaseBadge v-if="activeFiltersCount > 0" variant="primary" class="text-xs">
                  {{ activeFiltersCount }}
                </BaseBadge>
              </h3>
              <Button
                variant="ghost"
                size="sm"
                @click="clearFilters"
                class="text-xs"
              >
                Clear All
              </Button>
            </div>

            <!-- Brands -->
            <div class="mb-6">
              <h4 class="font-medium text-foreground mb-3">Brands</h4>
              <div class="space-y-2 max-h-48 overflow-y-auto">
                <label
                  v-for="brand in brands"
                  :key="brand.id"
                  class="flex items-center gap-3 cursor-pointer hover:bg-accent p-2 rounded transition-colors"
                >
                  <input
                    type="checkbox"
                    :value="brand.slug"
                    v-model="selectedBrands"
                    class="rounded border-border"
                  />
                  <span class="text-sm text-foreground">{{ brand.name }}</span>
                  <span class="text-xs text-muted-foreground">({{ brand.products_count || 0 }})</span>
                </label>
              </div>
            </div>

            <!-- Price Range -->
            <div class="mb-6">
              <h4 class="font-medium text-foreground mb-3">Price Range</h4>
              <div class="space-y-4">
                <div class="flex items-center justify-between text-sm text-muted-foreground">
                  <span>${{ selectedPriceRange[0] }}</span>
                  <span>${{ selectedPriceRange[1] }}</span>
                </div>
                <div class="relative">
                  <input
                    type="range"
                    v-model="selectedPriceRange[0]"
                    min="0"
                    max="500"
                    class="range-slider range-slider-min"
                    :style="{ zIndex: selectedPriceRange[0] > selectedPriceRange[1] - 10 ? 5 : 3 }"
                  />
                  <input
                    type="range"
                    v-model="selectedPriceRange[1]"
                    min="0"
                    max="500"
                    class="range-slider range-slider-max"
                    :style="{ zIndex: selectedPriceRange[1] < selectedPriceRange[0] + 10 ? 5 : 4 }"
                  />
                </div>
                <div class="flex gap-2">
                  <input
                    type="number"
                    v-model="selectedPriceRange[0]"
                    min="0"
                    max="500"
                    class="flex-1 px-2 py-1 text-sm bg-card border border-border rounded focus:border-primary focus:outline-none"
                    placeholder="Min"
                  />
                  <input
                    type="number"
                    v-model="selectedPriceRange[1]"
                    min="0"
                    max="500"
                    class="flex-1 px-2 py-1 text-sm bg-card border border-border rounded focus:border-primary focus:outline-none"
                    placeholder="Max"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1">
          <!-- Toolbar -->
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-4">
              <span class="text-sm text-muted-foreground">
                {{ sortedProducts.length }} products found
              </span>
              <span v-if="activeFiltersCount > 0" class="text-sm text-primary">
                ({{ activeFiltersCount }} filter{{ activeFiltersCount > 1 ? 's' : '' }} applied)
              </span>
            </div>

            <div class="flex items-center gap-4">
              <!-- Sort -->
              <div class="flex items-center gap-2">
                <span class="text-sm text-muted-foreground">Sort by:</span>
                <select
                  v-model="sortBy"
                  class="bg-card border border-border rounded-md px-3 py-1 text-sm focus:border-primary focus:outline-none"
                >
                  <option value="featured">Featured</option>
                  <option value="price-low">Price: Low to High</option>
                  <option value="price-high">Price: High to Low</option>
                  <option value="newest">Newest</option>
                </select>
              </div>

              <!-- View Mode -->
              <div class="flex items-center gap-1 bg-card border border-border rounded-md p-1">
                <Button
                  variant="ghost"
                  size="sm"
                  :class="viewMode === 'grid' ? 'bg-accent' : ''"
                  @click="viewMode = 'grid'"
                >
                  <Grid class="w-4 h-4" />
                </Button>
                <Button
                  variant="ghost"
                  size="sm"
                  :class="viewMode === 'list' ? 'bg-accent' : ''"
                  @click="viewMode = 'list'"
                >
                  <List class="w-4 h-4" />
                </Button>
              </div>
            </div>
          </div>

          <!-- Products Grid -->
          <div
            v-if="viewMode === 'grid'"
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
          >
            <ProductCard
              v-for="product in sortedProducts"
              :key="product.id"
              :id="product.id"
              :name="product.name"
              :price="product.final_price || product.price"
              :original-price="product.discount > 0 ? product.price : null"
              :image="product.main_image || '/images/placeholder-product.jpg'"
              :rating="product.rating"
              :reviews="product.reviews_count"
              :category="product.category?.name"
              :brand="product.brand?.name"
              :is-new="product.is_featured"
              :is-sale="product.discount > 0"
              :in-stock="product.has_stock"
              @add-to-cart="handleAddToCart"
              @add-to-wishlist="handleAddToWishlist"
              @quick-view="handleQuickView"
            />
          </div>

          <!-- Products List -->
          <div v-else class="space-y-4">
            <div
              v-for="product in sortedProducts"
              :key="product.id"
              class="bg-card border border-border rounded-lg p-4 hover:shadow-md transition-shadow"
            >
              <div class="flex gap-4">
                <img
                  :src="product.main_image || '/images/placeholder-product.jpg'"
                  :alt="product.name"
                  class="w-24 h-24 object-cover rounded-md"
                />
                <div class="flex-1">
                  <div class="flex items-start justify-between">
                    <div>
                      <h3 class="font-semibold text-foreground mb-1">{{ product.name }}</h3>
                      <p class="text-sm text-muted-foreground mb-2">
                        {{ product.category?.name }} • {{ product.brand?.name }}
                      </p>
                      <div class="flex items-center gap-2 mb-2">
                        <div class="flex items-center">
                          <Star
                            v-for="i in 5"
                            :key="i"
                            class="w-4 h-4"
                            :class="i <= Math.floor(product.rating) ? 'text-yellow-400 fill-current' : 'text-gray-300'"
                          />
                        </div>
                        <span class="text-sm text-muted-foreground">({{ product.reviews_count }})</span>
                      </div>
                    </div>
                    <div class="text-right">
                      <div class="text-xl font-bold text-primary">${{ (product.final_price || product.price).toFixed(2) }}</div>
                      <div v-if="product.discount > 0" class="text-sm text-muted-foreground line-through">
                        ${{ product.price.toFixed(2) }}
                      </div>
                    </div>
                  </div>
                  <div class="flex items-center gap-2 mt-3">
                    <Button
                      size="sm"
                      class="bg-primary text-primary-foreground"
                      :disabled="!product.has_stock"
                      @click="handleAddToCart(product)"
                    >
                      {{ product.has_stock ? 'Add to Cart' : 'Out of Stock' }}
                    </Button>
                    <Button
                      variant="outline"
                      size="sm"
                      @click="handleQuickView(product)"
                    >
                      View Details
                    </Button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div
            v-if="sortedProducts.length === 0"
            class="text-center py-12"
          >
            <div class="text-6xl mb-4">🏃</div>
            <h3 class="text-xl font-semibold text-foreground mb-2">No products found</h3>
            <p class="text-muted-foreground mb-4">
              Try adjusting your filters or browse all categories
            </p>
            <Button @click="clearFilters">
              Clear Filters
            </Button>
          </div>
        </div>
      </div>
    </div>

    <!-- Cart Notification -->
    <CartNotification
      :show="showNotification"
      :message="notificationMessage"
      :type="notificationType"
      @close="handleNotificationClose"
    />
  </div>
</template>

<style scoped>
/* Custom range slider styles */
.range-slider {
  -webkit-appearance: none;
  appearance: none;
  height: 6px;
  border-radius: 3px;
  background: hsl(var(--border));
  outline: none;
  position: absolute;
  width: 100%;
  pointer-events: none;
}

.range-slider::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background: hsl(var(--primary));
  cursor: pointer;
  pointer-events: auto;
  border: 2px solid hsl(var(--background));
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.range-slider::-moz-range-thumb {
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background: hsl(var(--primary));
  cursor: pointer;
  pointer-events: auto;
  border: 2px solid hsl(var(--background));
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.range-slider-min {
  background: linear-gradient(to right, hsl(var(--primary)) 0%, hsl(var(--primary)) 50%, hsl(var(--border)) 50%, hsl(var(--border)) 100%);
}

.range-slider-max {
  background: linear-gradient(to right, hsl(var(--border)) 0%, hsl(var(--border)) 50%, hsl(var(--primary)) 50%, hsl(var(--primary)) 100%);
}

/* Custom scrollbar for brands list */
.overflow-y-auto::-webkit-scrollbar {
  width: 4px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: hsl(var(--border));
  border-radius: 2px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: hsl(var(--muted-foreground));
  border-radius: 2px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: hsl(var(--foreground));
}
</style>
