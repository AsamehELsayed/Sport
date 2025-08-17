<script setup lang="ts">
import ProductCard from "./ProductCard.vue";
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
          :original_price="product.discount > 0 ? product.price : null"
          :image="product.images[0] || '/images/placeholder-product.svg'"
          :discount="product.discount"
          :rating="product.rating"
          :reviews="product.reviews_count"
          :category="product.category?.name"
          :brand="product.brand?.name"
          :isNew="product.is_featured"
          :isSale="product.discount > 0"
          :inStock="product.has_stock"
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
  </section>
</template>
