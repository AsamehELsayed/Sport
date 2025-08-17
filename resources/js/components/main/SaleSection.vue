<script setup lang="ts">
import ProductCard from "./ProductCard.vue";
import Button from "@/components/ui/Button.vue";
import { Link } from "@inertiajs/vue3";

// Props
const props = defineProps({
  products: {
    type: Array,
    default: () => []
  }
});
</script>

<template>
  <section class="relative py-16 overflow-hidden">
    <!-- Background with Overlay -->
    <div class="absolute inset-0 bg-gradient-to-br from-background/95 via-background/90 to-background/95"></div>

    <div class="container mx-auto px-4 relative z-10">
      <!-- Heading -->
      <div class="text-center mb-16">
        <div class="flex items-center justify-center gap-3 mb-6">
          <span class="text-3xl">⚡</span>
          <h2 class="text-4xl font-bold">
            <span class="text-foreground">Special </span>
            <span class="text-glow bg-gradient-to-r from-primary to-red-500 bg-clip-text text-transparent">Offers</span>
          </h2>
        </div>
        <p class="text-muted-foreground text-lg max-w-3xl mx-auto leading-relaxed">
          Don't miss out on these incredible deals! Limited time offers on premium sports equipment.
          Quality gear at unbeatable prices for serious athletes.
        </p>
      </div>

      <!-- Sale Products Grid -->
      <div v-if="products && products.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-16">
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
      <div v-else class="text-center py-16">
        <div class="text-8xl mb-6">🔥</div>
        <h3 class="text-2xl font-semibold text-foreground mb-3">No special offers available</h3>
        <p class="text-muted-foreground mb-6 max-w-md mx-auto">
          Check back soon for amazing deals on premium sports equipment.
        </p>
      </div>

      <!-- Button -->
      <div class="text-center">
        <Link href="/categories">
          <Button class="btn-hero group">
            Shop All Deals
          </Button>
        </Link>
      </div>
    </div>

    <!-- Decorative Elements -->
    <div class="absolute top-1/4 right-10 w-32 h-32 bg-primary/10 rounded-full blur-3xl"></div>
    <div class="absolute bottom-1/4 left-20 w-24 h-24 bg-red-500/10 rounded-full blur-2xl"></div>
  </section>
</template>
