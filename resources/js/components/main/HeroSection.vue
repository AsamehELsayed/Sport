<script setup lang="ts">
import { ArrowRight, Play } from "lucide-vue-next";
import heroImage from "@/assets/hero-sports-equipment.jpg";
import Button from "@/components/ui/Button.vue";
import { Link } from "@inertiajs/vue3";

// Props
const props = defineProps({
  stats: Object,
  websiteSettings: Object
});

const websiteSettings = props.websiteSettings || {};

// Get hero settings with fallbacks
const heroBackgroundImage = websiteSettings.hero_background_image
    ? `/storage/${websiteSettings.hero_background_image}`
    : heroImage;
const heroBadgeText = websiteSettings.hero_badge_text || '🔥 New Collection Available';
const heroTitleLine1 = websiteSettings.hero_title_line1 || 'Unleash Your';
const heroTitleLine2 = websiteSettings.hero_title_line2 || 'Athletic Power';
const heroSubtitle = websiteSettings.hero_subtitle || 'Discover premium sports equipment designed for champions. From professional-grade gear to everyday athletic essentials, elevate your performance with our cutting-edge collection.';
const heroCtaPrimaryText = websiteSettings.hero_cta_primary_text || 'Shop Collection';
const heroCtaPrimaryUrl = websiteSettings.hero_cta_primary_url || '/categories';
const heroCtaSecondaryText = websiteSettings.hero_cta_secondary_text || 'Watch Stories';
const heroCtaSecondaryUrl = websiteSettings.hero_cta_secondary_url || '#';
</script>

<template>
  <section class="relative min-h-[80vh] flex items-center overflow-hidden">
    <!-- Background Image with Overlay -->
    <div class="absolute inset-0">
      <img
        :src="heroBackgroundImage"
        alt="Sports equipment hero"
        class="w-full h-full object-cover"
      />
      <div class="absolute inset-0 bg-gradient-to-r from-background/90 via-background/70 to-transparent"></div>
    </div>

    <!-- Content -->
    <div class="container mx-auto px-4 relative z-10">
      <div class="max-w-3xl">

        <!-- Badge -->
        <div class="mb-6">
          <span class="inline-block px-4 py-2 bg-primary/20 text-primary font-semibold rounded-full text-sm backdrop-blur-sm border border-primary/30">
            {{ heroBadgeText }}
          </span>
        </div>

        <!-- Heading -->
        <h1 class="text-5xl md:text-7xl font-bold mb-6 leading-tight">
          <span class="text-foreground">{{ heroTitleLine1 }}</span>
          <br />
          <span class="text-glow bg-gradient-to-r from-primary to-red-500 bg-clip-text text-transparent">
            {{ heroTitleLine2 }}
          </span>
        </h1>

        <!-- Subtitle -->
        <p class="text-xl text-muted-foreground mb-8 max-w-2xl leading-relaxed">
          {{ heroSubtitle }}
        </p>

        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 mb-12">
          <Link :href="heroCtaPrimaryUrl">
            <Button size="lg" class="btn-hero group">
              {{ heroCtaPrimaryText }}
              <ArrowRight class="ml-2 w-5 h-5 transition-transform group-hover:translate-x-1" />
            </Button>
          </Link>

          <Link v-if="heroCtaSecondaryText && heroCtaSecondaryUrl" :href="heroCtaSecondaryUrl">
            <Button variant="outline" size="lg" class="btn-ghost-red group">
              <Play class="mr-2 w-5 h-5" />
              {{ heroCtaSecondaryText }}
            </Button>
          </Link>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-3 gap-8 max-w-md">
          <div class="text-center">
            <div class="text-2xl font-bold text-primary mb-1">{{ stats?.total_products || 0 }}+</div>
            <div class="text-sm text-muted-foreground">Products</div>
          </div>
          <div class="text-center">
            <div class="text-2xl font-bold text-primary mb-1">{{ stats?.total_customers || 0 }}+</div>
            <div class="text-sm text-muted-foreground">Athletes</div>
          </div>
          <div class="text-center">
            <div class="text-2xl font-bold text-primary mb-1">{{ stats?.average_rating || 4.9 }}★</div>
            <div class="text-sm text-muted-foreground">Reviews</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Decorative Elements -->
    <div class="absolute top-1/4 right-10 w-32 h-32 bg-primary/10 rounded-full blur-3xl"></div>
    <div class="absolute bottom-1/4 right-20 w-24 h-24 bg-red-500/10 rounded-full blur-2xl"></div>
  </section>
</template>
