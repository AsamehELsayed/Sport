<script setup lang="ts">
import { ref, computed, watch, onMounted } from "vue";
import { Link, router } from "@inertiajs/vue3";
import ProductCard from "@/components/main/ProductCard.vue";
import Button from "@/components/ui/Button.vue";
import BaseBadge from "@/components/ui/BaseBadge.vue";
import CartNotification from "@/components/main/CartNotification.vue";
import AddToCartModal from "@/components/main/AddToCartModal.vue";
import { Filter, Grid, List, Star, Zap, Search, X, Award, Percent, TrendingDown } from "lucide-vue-next";
import GuestLayout from '@/layouts/GuestLayout.vue';

defineOptions({
    layout: GuestLayout,
});

// Props from Inertia
const props = defineProps({
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
  },
  stats: {
    type: Object,
    default: () => ({
      total_products: 0,
      average_discount: 0,
      max_discount: 0
    })
  }
});

// Local state
const viewMode = ref('grid'); // 'grid' or 'list'
const showFilters = ref(false);
const searchInput = ref(props.searchQuery || '');
const selectedCategories = ref(props.filters.category ? props.filters.category.split(',') : []);
const selectedBrands = ref(props.filters.brand ? props.filters.brand.split(',') : []);
const selectedPriceRange = ref([
  props.filters.min_price ? parseInt(props.filters.min_price) : 0,
  props.filters.max_price ? parseInt(props.filters.max_price) : 500
]);
const selectedDiscountRange = ref([
  props.filters.min_discount ? parseInt(props.filters.min_discount) : 0,
  props.filters.max_discount ? parseInt(props.filters.max_discount) : 100
]);
const sortBy = ref(props.filters.sort || 'discount-high');

// Notification state
const showNotification = ref(false);
const notificationMessage = ref('');
const notificationType = ref<'success' | 'error' | 'info'>('success');

// Modal state
const showAddToCartModal = ref(false);
const selectedProduct = ref(null);

// Computed sorted products (server-side sorting is handled by backend)
const sortedProducts = computed(() => {
  return props.products.data || [];
});

// Computed for active filters count
const activeFiltersCount = computed(() => {
  let count = 0;
  if (selectedCategories.value.length > 0) count++;
  if (selectedBrands.value.length > 0) count++;
  if (selectedPriceRange.value[0] > 0 || selectedPriceRange.value[1] < 500) count++;
  if (selectedDiscountRange.value[0] > 0 || selectedDiscountRange.value[1] < 100) count++;
  if (sortBy.value !== 'discount-high') count++;
  if (searchInput.value) count++;
  return count;
});

// Methods
const toggleCategory = (categorySlug: string) => {
  const index = selectedCategories.value.indexOf(categorySlug);
  if (index > -1) {
    selectedCategories.value.splice(index, 1);
  } else {
    selectedCategories.value.push(categorySlug);
  };
};

const toggleBrand = (brandSlug: string) => {
  const index = selectedBrands.value.indexOf(brandSlug);
  if (index > -1) {
    selectedBrands.value.splice(index, 1);
  } else {
    selectedBrands.value.push(brandSlug);
  };
};

const clearFilters = () => {
  selectedCategories.value = [];
  selectedBrands.value = [];
  selectedPriceRange.value = [0, 500];
  selectedDiscountRange.value = [0, 100];
  sortBy.value = 'discount-high';
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
  if (selectedCategories.value.length > 0) {
    params.set('category', selectedCategories.value.join(','));
  }
  if (selectedBrands.value.length > 0) {
    params.set('brand', selectedBrands.value.join(','));
  }
  if (sortBy.value !== 'discount-high') {
    params.set('sort', sortBy.value);
  }
  if (selectedPriceRange.value[0] > 0) {
    params.set('min_price', selectedPriceRange.value[0].toString());
  }
  if (selectedPriceRange.value[1] < 500) {
    params.set('max_price', selectedPriceRange.value[1].toString());
  }
  if (selectedDiscountRange.value[0] > 0) {
    params.set('min_discount', selectedDiscountRange.value[0].toString());
  }
  if (selectedDiscountRange.value[1] < 100) {
    params.set('max_discount', selectedDiscountRange.value[1].toString());
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
watch([selectedCategories, selectedBrands, sortBy], () => {
  // Debounce the filter changes to avoid too many requests
  clearTimeout(window.filterTimeout);
  window.filterTimeout = setTimeout(() => {
    const params = new URLSearchParams(window.location.search);

    if (selectedCategories.value.length > 0) {
      params.set('category', selectedCategories.value.join(','));
    } else {
      params.delete('category');
    }

    if (selectedBrands.value.length > 0) {
      params.set('brand', selectedBrands.value.join(','));
    } else {
      params.delete('brand');
    }

    if (sortBy.value !== 'discount-high') {
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

    // Add discount range filters
    if (selectedDiscountRange.value[0] > 0) {
      params.set('min_discount', selectedDiscountRange.value[0].toString());
    } else {
      params.delete('min_discount');
    }

    if (selectedDiscountRange.value[1] < 100) {
      params.set('max_discount', selectedDiscountRange.value[1].toString());
    } else {
      params.delete('max_discount');
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
    if (selectedCategories.value.length > 0) {
      params.set('category', selectedCategories.value.join(','));
    }
    if (selectedBrands.value.length > 0) {
      params.set('brand', selectedBrands.value.join(','));
    }
    if (sortBy.value !== 'discount-high') {
      params.set('sort', sortBy.value);
    }
    if (searchInput.value.trim()) {
      params.set('search', searchInput.value.trim());
    }

    const url = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
    router.visit(url, { preserveState: true, preserveScroll: true });
  }, 500);
}, { deep: true });

// Watch for discount range changes
watch(selectedDiscountRange, (newRange) => {
  // Validate discount range
  if (newRange[0] > newRange[1]) {
    const temp = newRange[0];
    newRange[0] = newRange[1];
    newRange[1] = temp;
  }

  // Ensure values are within bounds
  newRange[0] = Math.max(0, Math.min(100, newRange[0]));
  newRange[1] = Math.max(0, Math.min(100, newRange[1]));

  // Debounce the discount filter to avoid too many requests
  clearTimeout(window.discountFilterTimeout);
  window.discountFilterTimeout = setTimeout(() => {
    const params = new URLSearchParams(window.location.search);

    if (newRange[0] > 0) {
      params.set('min_discount', newRange[0].toString());
    } else {
      params.delete('min_discount');
    }

    if (newRange[1] < 100) {
      params.set('max_discount', newRange[1].toString());
    } else {
      params.delete('max_discount');
    }

    // Add other current filters
    if (selectedCategories.value.length > 0) {
      params.set('category', selectedCategories.value.join(','));
    }
    if (selectedBrands.value.length > 0) {
      params.set('brand', selectedBrands.value.join(','));
    }
    if (sortBy.value !== 'discount-high') {
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

const handleOpenAddToCartModal = (product: any) => {
  selectedProduct.value = product;
  showAddToCartModal.value = true;
};

const handleCloseAddToCartModal = () => {
  showAddToCartModal.value = false;
  selectedProduct.value = null;
};

const handleAddToCartSuccess = (item: any) => {
  // Show success notification
  notificationMessage.value = `${item.name} added to cart!`;
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
    <div class="bg-gradient-to-r from-red-500/10 to-orange-500/10 border-b border-border">
      <div class="container mx-auto px-4 py-8">
        <div class="flex items-center gap-3 mb-4">
          <Link href="/" class="text-muted-foreground hover:text-primary">
            Home
          </Link>
          <span class="text-muted-foreground">/</span>
          <span class="text-foreground font-medium">Sales</span>
        </div>

        <div class="flex items-center gap-4 mb-6">
          <div class="flex items-center gap-3">
            <div class="p-3 bg-red-500/10 rounded-full">
              <TrendingDown class="w-8 h-8 text-red-500" />
            </div>
            <div>
              <h1 class="text-4xl font-bold mb-2">
                <span class="text-foreground">Sale</span>
                <span v-if="searchQuery" class="text-2xl font-normal text-muted-foreground ml-2">
                  for "{{ searchQuery }}"
                </span>
              </h1>
              <p class="text-muted-foreground text-lg">
                Amazing deals on premium sports equipment
              </p>
            </div>
          </div>
        </div>

        <!-- Sale Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
          <div class="bg-card border border-border rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-primary">{{ stats.total_products }}</div>
            <div class="text-sm text-muted-foreground">Products on Sale</div>
          </div>
          <div class="bg-card border border-border rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-red-500">{{ stats.average_discount }}%</div>
            <div class="text-sm text-muted-foreground">Average Discount</div>
          </div>
          <div class="bg-card border border-border rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-red-600">{{ stats.max_discount }}%</div>
            <div class="text-sm text-muted-foreground">Max Discount</div>
          </div>
        </div>

        <!-- Search Bar -->
        <div class="mt-6 max-w-md">
          <div class="relative">
            <div class="absolute left-3 top-0 bottom-0 flex items-center">
              <Search class="text-muted-foreground w-4 h-4" />
            </div>
            <input
              v-model="searchInput"
              type="text"
              placeholder="Search sale products..."
              class="w-full pl-10 pr-10 py-2 bg-card border border-border rounded-lg focus:border-primary focus:outline-none"
              @keyup.enter="handleSearch"
            />
            <div class="absolute right-3 top-0 bottom-0 flex items-center">
              <button
                v-if="searchInput"
                @click="clearSearch"
                class="text-muted-foreground hover:text-foreground"
              >
                <X class="w-4 h-4" />
              </button>
            </div>
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
            <div class="space-y-2 max-h-48 overflow-y-auto">
              <label
                v-for="category in categories"
                :key="category.id"
                class="flex items-center gap-3 cursor-pointer hover:bg-accent p-2 rounded transition-colors"
              >
                <input
                  type="checkbox"
                  :value="category.slug"
                  v-model="selectedCategories"
                  class="rounded border-border"
                />
                <span class="text-sm text-foreground">{{ category.name }}</span>
                <span class="text-xs text-muted-foreground">({{ category.products_count || 0 }})</span>
              </label>
            </div>
          </div>

          <!-- Brands -->
          <div class="bg-card border border-border rounded-lg p-6">
            <h3 class="font-semibold text-foreground mb-4 flex items-center gap-2">
              <Award class="w-4 h-4" />
              Brands
            </h3>
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

            <!-- Discount Range -->
            <div class="mb-6">
              <h4 class="font-medium text-foreground mb-3 flex items-center gap-2">
                <Percent class="w-4 h-4" />
                Discount Range
              </h4>
              <div class="space-y-4">
                <div class="flex items-center justify-between text-sm text-muted-foreground">
                  <span>{{ selectedDiscountRange[0] }}%</span>
                  <span>{{ selectedDiscountRange[1] }}%</span>
                </div>
                <div class="relative">
                  <input
                    type="range"
                    v-model="selectedDiscountRange[0]"
                    min="0"
                    max="100"
                    class="range-slider range-slider-min"
                    :style="{ zIndex: selectedDiscountRange[0] > selectedDiscountRange[1] - 10 ? 5 : 3 }"
                  />
                  <input
                    type="range"
                    v-model="selectedDiscountRange[1]"
                    min="0"
                    max="100"
                    class="range-slider range-slider-max"
                    :style="{ zIndex: selectedDiscountRange[1] < selectedDiscountRange[0] + 10 ? 5 : 4 }"
                  />
                </div>
                <div class="flex gap-2">
                  <input
                    type="number"
                    v-model="selectedDiscountRange[0]"
                    min="0"
                    max="100"
                    class="flex-1 px-2 py-1 text-sm bg-card border border-border rounded focus:border-primary focus:outline-none"
                    placeholder="Min %"
                  />
                  <input
                    type="number"
                    v-model="selectedDiscountRange[1]"
                    min="0"
                    max="100"
                    class="flex-1 px-2 py-1 text-sm bg-card border border-border rounded focus:border-primary focus:outline-none"
                    placeholder="Max %"
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
                  <option value="discount-high">Highest Discount</option>
                  <option value="discount-low">Lowest Discount</option>
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
              :image="product.images && product.images.length > 0 ? `${product.images[0]}` : '/images/placeholder-product.svg'"
              :rating="product.rating"
              :reviews="product.reviews_count"
              :category="product.category?.name"
              :brand="product.brand?.name"
              :is-new="product.is_featured"
              :is-sale="product.discount > 0"
              :discount="product.discount_percentage || product.discount"
              :in-stock="product.has_stock"
              @add-to-wishlist="handleAddToWishlist"
              @quick-view="handleQuickView"
              @open-add-to-cart-modal="handleOpenAddToCartModal"
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
                  :src="product.images && product.images.length > 0 ? `${product.images[0]}` : '/images/placeholder-product.svg'"
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
                      <div class="text-xl font-bold text-primary">${{ (product.final_price || product.price)  }}</div>
                      <div v-if="product.discount > 0" class="text-sm text-muted-foreground line-through">
                        ${{ product.price  }}
                      </div>
                      <div v-if="product.discount_percentage" class="text-sm text-red-500 font-semibold">
                        -{{ product.discount_percentage }}% OFF
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
            <div class="text-6xl mb-4">🏷️</div>
            <h3 class="text-xl font-semibold text-foreground mb-2">No sale products found</h3>
            <p class="text-muted-foreground mb-4">
              Try adjusting your filters or check back later for new deals
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

    <!-- Add to Cart Modal -->
    <AddToCartModal
      :is-open="showAddToCartModal"
      :product="selectedProduct"
      @close="handleCloseAddToCartModal"
      @added="handleAddToCartSuccess"
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

/* Custom scrollbar for lists */
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
