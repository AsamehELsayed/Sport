<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import Button from "@/components/ui/Button.vue";

// Props
const props = defineProps({
  brands: {
    type: Array,
    default: () => []
  }
});
</script>

<template>
  <section class="relative py-16 overflow-hidden">
    <!-- Background with Overlay -->
    <div class="absolute inset-0 bg-gradient-to-br from-background/90 via-background/95 to-background"></div>

    <div class="container mx-auto px-4 relative z-10">
      <!-- Heading -->
      <div class="text-center mb-16">
        <div class="flex items-center justify-center gap-3 mb-6">
          <span class="text-3xl">🏆</span>
          <h2 class="text-4xl font-bold">
            <span class="text-foreground">Top </span>
            <span class="text-glow bg-gradient-to-r from-primary to-red-500 bg-clip-text text-transparent">Brands</span>
          </h2>
        </div>
        <p class="text-muted-foreground text-lg max-w-3xl mx-auto leading-relaxed">
          Shop from the world's leading sports equipment brands, trusted by professional athletes worldwide.
          Quality, innovation, and performance guaranteed.
        </p>
      </div>

      <!-- Brands Grid -->
      <div v-if="brands && brands.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 mb-16">
        <div
          v-for="brand in brands"
          :key="brand.id"
          class="group"
        >
          <div class="bg-card/50 backdrop-blur-sm border border-border/50 rounded-xl p-8 hover:shadow-xl transition-all duration-300 hover:scale-105 hover:border-primary/50 relative overflow-hidden">
            <!-- Background gradient on hover -->
            <div class="absolute inset-0 bg-gradient-to-br from-primary/10 to-red-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

            <!-- Content -->
            <div class="relative z-10 text-center">
              <div class="text-4xl font-bold text-foreground mb-4 group-hover:text-primary transition-colors">
                {{ brand.name }}
              </div>
              <div class="text-sm text-muted-foreground mb-6 font-medium">
                {{ brand.products_count || 0 }} products available
              </div>
              <Link :href="`/categories?brand=${brand.slug || brand.id}`">
                <Button variant="outline" class="w-full btn-ghost-red group">
                  View Products
                </Button>
              </Link>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-16">
        <div class="text-8xl mb-6">🏆</div>
        <h3 class="text-2xl font-semibold text-foreground mb-3">No brands available</h3>
        <p class="text-muted-foreground mb-6 max-w-md mx-auto">
          We're working on adding amazing brands. Check back soon for new sports equipment brands.
        </p>
      </div>

      <!-- Button -->
      <div class="text-center">
        <Link href="/categories">
          <Button class="btn-hero group">
            Explore All Brands
          </Button>
        </Link>
      </div>
    </div>

    <!-- Decorative Elements -->
    <div class="absolute top-1/4 left-10 w-32 h-32 bg-primary/10 rounded-full blur-3xl"></div>
    <div class="absolute bottom-1/4 right-20 w-24 h-24 bg-red-500/10 rounded-full blur-2xl"></div>
  </section>
</template>
