<script setup lang="ts">
import { ref } from "vue";
import ProductCard from "./ProductCard.vue";
import AddToCartModal from "./AddToCartModal.vue";
import CartNotification from "./CartNotification.vue";
import Button from "@/components/ui/Button.vue";
import { Link } from "@inertiajs/vue3";

// Props
const props = defineProps({
  products: {
    type: Array,
    default: () => []
  },
  title: {
    type: String,
    default: 'Featured Products'
  }
});

// Modal and notification state
const showAddToCartModal = ref(false);
const selectedProduct = ref(null);
const showNotification = ref(false);
const notificationMessage = ref('');
const notificationType = ref<'success' | 'error' | 'info'>('success');

// Event handlers
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
</script>

<template>
  <section class="py-16">
    <div class="container mx-auto px-4">
      <!-- Heading -->
      <div class="text-center mb-12">
        <h2 class="text-4xl font-bold mb-4">
          <span class="text-foreground">{{ title }}</span>
        </h2>
        <p class="text-muted-foreground text-lg max-w-2xl mx-auto">
          Discover our handpicked selection of premium sports equipment, chosen by athletes for athletes.
        </p>
      </div>

      <!-- Grid -->
      <div v-if="products && products.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <ProductCard
          v-for="product in products"
          :key="product.id"
          :id="product.id"
          :name="product.name"
          :price="product.final_price || product.price"
          :original-price="product.discount > 0 ? product.price : null"
          :image="product.main_image || '/images/placeholder-product.jpg'"
          :discount="product.discount_percentage || product.discount"
          :rating="product.rating"
          :reviews="product.reviews_count"
          :category="product.category?.name"
          :brand="product.brand?.name"
          :is-new="product.is_featured"
          :is-sale="product.discount > 0"
          :in-stock="product.has_stock"
          :available-colors="product.available_colors || []"
          :available-sizes="product.available_sizes || []"
          @open-add-to-cart-modal="handleOpenAddToCartModal"
          @add-to-wishlist="handleAddToWishlist"
          @quick-view="handleQuickView"
        />
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-12">
        <div class="text-6xl mb-4">🏃</div>
        <h3 class="text-xl font-semibold text-foreground mb-2">No products available</h3>
        <p class="text-muted-foreground mb-4">
          Check back soon for new products
        </p>
      </div>

      <!-- Button -->
      <div class="text-center mt-12">
        <Link href="/categories">
          <Button class="btn-hero">
            View All Products
          </Button>
        </Link>
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
  </section>
</template>
