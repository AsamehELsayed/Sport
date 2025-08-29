<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import Button from "@/components/ui/Button.vue";

// Props
const props = defineProps({
  categories: {
    type: Array,
    default: () => []
  }
});

// Category icons mapping
const categoryIcons = {
  'Running': '🏃',
  'Basketball': '🏀',
  'Soccer': '⚽',
  'Tennis': '🎾',
  'Fitness': '💪',
  'Swimming': '🏊',
  'Golf': '⛳',
  'Cycling': '🚴',
  'default': '🏆'
};

const getCategoryIcon = (categoryName) => {
  return categoryIcons[categoryName] || categoryIcons.default;
};
</script>

<template>
  <section class="relative py-16 overflow-hidden">
    <!-- Background with Overlay -->
    <div class="absolute inset-0 bg-gradient-to-br from-background via-background/95 to-background/90"></div>

    <div class="container mx-auto px-4 relative z-10">
      <!-- Heading -->
      <div class="text-center mb-16">
        <div class="flex items-center justify-center gap-3 mb-6">
          <span class="text-3xl">🎯</span>
          <h2 class="text-4xl font-bold">
            <span class="text-foreground">Popular </span>
            <span class="text-glow bg-gradient-to-r from-primary to-blue-500 bg-clip-text text-transparent">Categories</span>
          </h2>
        </div>
        <p class="text-muted-foreground text-lg max-w-3xl mx-auto leading-relaxed">
          Explore our most popular sports categories and find the perfect equipment for your favorite activities.
          From running to team sports, we've got everything you need to excel.
        </p>
      </div>

      <!-- Categories Grid -->
      <div v-if="categories && categories.length > 0" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-6 mb-16">
        <Link
          v-for="category in categories"
          :key="category.id"
          :href="`/categories/${category.slug || category.id}`"
          class="group"
        >
          <div class="bg-card/50 backdrop-blur-sm border border-border/50 rounded-xl p-6 text-center hover:shadow-xl transition-all duration-300 hover:scale-105 hover:border-primary/50 relative overflow-hidden">
            <!-- Background gradient on hover -->
            <div class="absolute inset-0 bg-gradient-to-br from-primary/10 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

            <!-- Content -->
            <div class="relative z-10">
              <div class="text-5xl mb-4 group-hover:scale-110 transition-transform duration-300">
                {{category.icon }}
              </div>
              <h3 class="font-semibold text-foreground mb-3 group-hover:text-primary transition-colors text-lg">
                {{ category.name }}
              </h3>
              <p class="text-sm text-muted-foreground font-medium">
                {{ category.products_count || 0 }} products
              </p>
            </div>
          </div>
        </Link>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-16">
        <div class="text-8xl mb-6">🏆</div>
        <h3 class="text-2xl font-semibold text-foreground mb-3">No categories available</h3>
        <p class="text-muted-foreground mb-6 max-w-md mx-auto">
          We're working on adding amazing categories. Check back soon for new sports equipment categories.
        </p>
      </div>

      <!-- Button -->
      <div class="text-center">
        <Link href="/categories">
          <Button class="btn-hero group">
            Explore All Categories
          </Button>
        </Link>
      </div>
    </div>

    <!-- Decorative Elements -->
    <div class="absolute top-1/4 right-10 w-32 h-32 bg-primary/10 rounded-full blur-3xl"></div>
    <div class="absolute bottom-1/4 left-20 w-24 h-24 bg-blue-500/10 rounded-full blur-2xl"></div>
  </section>
</template>
