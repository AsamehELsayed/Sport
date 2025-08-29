<script setup lang="ts">
import HeroSection from '@/components/main/HeroSection.vue';
import ProductGrid from '@/components/main/ProductGrid.vue';
import CategorySection from '@/components/main/CategorySection.vue';
import BrandSection from '@/components/main/BrandSection.vue';
import SaleSection from '@/components/main/SaleSection.vue';
import GuestLayout from '@/layouts/GuestLayout.vue';

defineOptions({
    layout: GuestLayout,
});

// Props from Inertia
const props = defineProps({
  featuredProducts: Array,
  popularCategories: Array,
  topBrands: Array,
  latestProducts: Array,
  saleProducts: Array,
  stats: Object,
  websiteSettings: Object
});
</script>

<template>
    <HeroSection :stats="stats" :website-settings="websiteSettings" />
    <ProductGrid :products="featuredProducts" title="Featured Products" />
    <CategorySection :categories="popularCategories" />
    <BrandSection :brands="topBrands" />
    <SaleSection :products="saleProducts" />
    <ProductGrid :products="latestProducts" title="Latest Products" />
</template>
