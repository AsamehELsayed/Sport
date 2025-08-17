<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import { Link, usePage, router } from "@inertiajs/vue3"; // For navigation with Inertia
import { Zap, Search, ShoppingCart, User, Menu, Heart, LogOut, Settings } from "lucide-vue-next"; // install: npm i lucide-vue-next
import { useCart } from "@/composables/useCart";

const isMenuOpen = ref(false);
const { itemCount } = useCart();

// Get auth user from Inertia page props
const page = usePage();
const user = computed(() => page.props.auth?.user);
const isAuthenticated = computed(() => !!user.value);

// Close dropdown when clicking outside
const closeDropdown = () => {
  isMenuOpen.value = false;
};

// Close dropdown when clicking outside
onMounted(() => {
  document.addEventListener('click', (e) => {
    const target = e.target as HTMLElement;
    if (!target.closest('.user-dropdown')) {
      isMenuOpen.value = false;
    }
  });
});

// Logout function
const logout = () => {
  router.post('/logout');
  isMenuOpen.value = false;
};
</script>

<template>
  <nav class="sticky top-0 z-50 bg-background/95 backdrop-blur-sm border-b border-border">
    <div class="container mx-auto px-4">
      <div class="flex items-center justify-between h-16">

        <!-- Logo -->
        <div class="flex items-center space-x-2">
          <div class="w-8 h-8 bg-gradient-to-r from-red-500 to-yellow-500 rounded-lg flex items-center justify-center">
            <Zap class="w-5 h-5 text-white" />
          </div>
          <span class="text-xl font-bold text-foreground">NitroSports</span>
        </div>

        <!-- Search Bar - Hidden on mobile -->
        <div class="hidden md:flex flex-1 max-w-lg mx-8">
          <div class="relative w-full">
            <Search class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground w-4 h-4" />
            <input
              type="text"
              placeholder="Search sports equipment..."
              class="w-full pl-10 bg-card border border-border rounded-md focus:border-primary h-9 px-3"
            />
          </div>
        </div>

        <!-- Desktop Navigation -->
        <div class="hidden md:flex items-center space-x-6">
          <Link href="/categories" class="text-foreground hover:text-primary">Categories</Link>
          <Link href="#" class="text-foreground hover:text-primary">Brands</Link>
          <Link href="#" class="text-foreground hover:text-primary">Sale</Link>
        </div>

        <!-- User Actions -->
        <div class="flex items-center space-x-4">
          <button class="hidden md:flex p-2 hover:text-primary">
            <Heart class="w-5 h-5" />
          </button>

          <Link href="/cart" class="relative p-2 hover:text-primary">
            <ShoppingCart class="w-5 h-5" />
            <span
              v-if="itemCount > 0"
              class="absolute -top-2 -right-2 h-5 w-5 flex items-center justify-center rounded-full text-xs bg-primary text-white"
            >
              {{ itemCount }}
            </span>
          </Link>

          <!-- Guest User Actions -->
          <div v-if="!isAuthenticated" class="flex items-center space-x-2">
            <Link href="/login" class="text-sm text-foreground hover:text-primary">
              Sign in
            </Link>
            <Link href="/register" class="text-sm bg-primary text-primary-foreground px-3 py-1 rounded-md hover:bg-primary/90">
              Sign up
            </Link>
          </div>

          <!-- Authenticated User Actions -->
          <div v-else class="relative user-dropdown">
            <button
              class="flex items-center space-x-2 p-2 hover:text-primary rounded-md"
              @click="isMenuOpen = !isMenuOpen"
            >
              <div class="w-8 h-8 bg-gradient-to-r from-red-500 to-yellow-500 rounded-full flex items-center justify-center text-white text-sm font-medium">
                {{ user?.name?.charAt(0)?.toUpperCase() || 'U' }}
              </div>
              <span class="hidden md:block text-sm">{{ user?.name }}</span>
            </button>

            <!-- User Dropdown Menu -->
            <div
              v-if="isMenuOpen"
              class="absolute right-0 mt-2 w-48 bg-background border border-border rounded-md shadow-lg z-50"
            >
              <div class="py-1">
                <Link
                  href="/profile"
                  class="flex items-center px-4 py-2 text-sm text-foreground hover:bg-muted"
                  @click="isMenuOpen = false"
                >
                  <User class="w-4 h-4 mr-2" />
                  My Profile
                </Link>
                <Link
                  href="/profile"
                  class="flex items-center px-4 py-2 text-sm text-foreground hover:bg-muted"
                  @click="isMenuOpen = false"
                >
                  <Settings class="w-4 h-4 mr-2" />
                  Settings
                </Link>
                <div class="border-t border-border my-1"></div>
                <button
                  type="button"
                  class="flex items-center w-full px-4 py-2 text-sm text-foreground hover:bg-muted"
                  @click="logout"
                >
                  <LogOut class="w-4 h-4 mr-2" />
                  Sign out
                </button>
              </div>
            </div>
          </div>

          <!-- Mobile Menu Button -->
          <button
            class="md:hidden p-2 hover:text-primary"
            @click="isMenuOpen = !isMenuOpen"
          >
            <Menu class="w-5 h-5" />
          </button>
        </div>
      </div>

      <!-- Mobile Menu -->
      <div v-if="isMenuOpen" class="md:hidden py-4 border-t border-border">
        <div class="flex flex-col space-y-4">
          <div class="relative">
            <Search class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground w-4 h-4" />
            <input
              type="text"
              placeholder="Search sports equipment..."
              class="w-full pl-10 bg-card border border-border rounded-md h-9 px-3"
            />
          </div>
          <Link href="/categories" class="text-foreground hover:text-primary text-left">Categories</Link>
          <Link href="#" class="text-foreground hover:text-primary text-left">Brands</Link>
          <Link href="#" class="text-foreground hover:text-primary text-left">Sale</Link>
          <Link href="#" class="flex items-center text-foreground hover:text-primary">
            <Heart class="w-4 h-4 mr-2" /> Wishlist
          </Link>
          <Link href="/cart" class="flex items-center text-foreground hover:text-primary">
            <ShoppingCart class="w-4 h-4 mr-2" /> Cart ({{ itemCount }})
          </Link>

          <!-- Mobile Auth Links -->
          <div v-if="!isAuthenticated" class="space-y-2 border-t border-border pt-4">
            <Link href="/login" class="flex items-center text-foreground hover:text-primary">
              <User class="w-4 h-4 mr-2" /> Sign in
            </Link>
            <Link href="/register" class="flex items-center text-foreground hover:text-primary">
              <User class="w-4 h-4 mr-2" /> Sign up
            </Link>
          </div>

          <div v-else class="space-y-2 border-t border-border pt-4">
            <Link href="/profile" class="flex items-center text-foreground hover:text-primary">
              <User class="w-4 h-4 mr-2" /> My Profile
            </Link>
                        <button
              type="button"
              class="flex items-center w-full text-left text-foreground hover:text-primary"
              @click="logout"
            >
              <LogOut class="w-4 h-4 mr-2" /> Sign out
            </button>
          </div>
        </div>
      </div>
    </div>
  </nav>

</template>
