<template>
  <div class="min-h-screen bg-background">
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 z-50 w-64 bg-card border-r border-border transform transition-transform duration-300 ease-in-out" :class="{ '-translate-x-full': !sidebarOpen }">
      <div class="flex items-center justify-between h-16 px-6 border-b border-border">
        <div class="flex items-center space-x-2">
          <div class="w-8 h-8 bg-gradient-to-r from-red-500 to-yellow-500 rounded-lg flex items-center justify-center">
            <Zap class="w-5 h-5 text-white" />
          </div>
          <span class="text-lg font-semibold text-foreground">Admin Panel</span>
        </div>
        <button @click="sidebarOpen = false" class="lg:hidden p-1 rounded-md hover:bg-muted">
          <X class="w-5 h-5" />
        </button>
      </div>

      <nav class="mt-6 px-3">
        <div class="space-y-1">
          <Link 
            href="/admin" 
            class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors"
            :class="route().current('admin.dashboard') ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:text-foreground hover:bg-muted'"
          >
            <LayoutDashboard class="w-5 h-5 mr-3" />
            Dashboard
          </Link>

          <Link 
            href="/admin/orders" 
            class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors"
            :class="route().current('admin.orders.*') ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:text-foreground hover:bg-muted'"
          >
            <Package class="w-5 h-5 mr-3" />
            Orders
          </Link>

          <Link 
            href="/admin/products" 
            class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors"
            :class="route().current('admin.products.*') ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:text-foreground hover:bg-muted'"
          >
            <ShoppingBag class="w-5 h-5 mr-3" />
            Products
          </Link>

          <Link 
            href="/admin/categories" 
            class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors"
            :class="route().current('admin.categories.*') ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:text-foreground hover:bg-muted'"
          >
            <Tag class="w-5 h-5 mr-3" />
            Categories
          </Link>

          <Link 
            href="/admin/brands" 
            class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors"
            :class="route().current('admin.brands.*') ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:text-foreground hover:bg-muted'"
          >
            <Award class="w-5 h-5 mr-3" />
            Brands
          </Link>

          <Link 
            href="/admin/customers" 
            class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors"
            :class="route().current('admin.customers.*') ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:text-foreground hover:bg-muted'"
          >
            <Users class="w-5 h-5 mr-3" />
            Customers
          </Link>

          <Link 
            href="/admin/invoices" 
            class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors"
            :class="route().current('admin.invoices.*') ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:text-foreground hover:bg-muted'"
          >
            <FileText class="w-5 h-5 mr-3" />
            Invoices
          </Link>

          <Link 
            href="#" 
            class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors text-muted-foreground hover:text-foreground hover:bg-muted"
          >
            <BarChart3 class="w-5 h-5 mr-3" />
            Analytics
          </Link>

          <Link 
            href="#" 
            class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors text-muted-foreground hover:text-foreground hover:bg-muted"
          >
            <Settings class="w-5 h-5 mr-3" />
            Settings
          </Link>
        </div>
      </nav>
    </div>

    <!-- Main content -->
    <div class="lg:pl-64">
      <!-- Header -->
      <header class="bg-card border-b border-border h-16 flex items-center justify-between px-6">
        <div class="flex items-center space-x-4">
          <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-md hover:bg-muted">
            <Menu class="w-5 h-5" />
          </button>
          <h1 class="text-xl font-semibold text-foreground">{{ pageTitle }}</h1>
        </div>

        <div class="flex items-center space-x-4">
          <button class="p-2 rounded-md hover:bg-muted">
            <Bell class="w-5 h-5" />
          </button>
          <div class="relative">
            <button @click="userMenuOpen = !userMenuOpen" class="flex items-center space-x-2 p-2 rounded-md hover:bg-muted">
              <div class="w-8 h-8 bg-gradient-to-r from-red-500 to-yellow-500 rounded-full flex items-center justify-center text-white text-sm font-medium">
                {{ user?.name?.charAt(0)?.toUpperCase() || 'A' }}
              </div>
              <span class="hidden md:block text-sm">{{ user?.name }}</span>
              <ChevronDown class="w-4 h-4" />
            </button>

            <div v-if="userMenuOpen" class="absolute right-0 mt-2 w-48 bg-card border border-border rounded-md shadow-lg z-50">
              <div class="py-1">
                <Link href="/profile" class="flex items-center px-4 py-2 text-sm text-foreground hover:bg-muted">
                  <User class="w-4 h-4 mr-2" />
                  Profile
                </Link>
                <button @click="logout" class="flex items-center w-full px-4 py-2 text-sm text-foreground hover:bg-muted">
                  <LogOut class="w-4 h-4 mr-2" />
                  Sign out
                </button>
              </div>
            </div>
          </div>
        </div>
      </header>

      <!-- Page content -->
      <main class="p-6">
        <slot />
      </main>
    </div>

    <!-- Overlay for mobile -->
    <div v-if="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-black bg-opacity-50 lg:hidden"></div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import { 
  Zap, 
  X, 
  Menu, 
  Bell, 
  ChevronDown, 
  User, 
  LogOut,
  LayoutDashboard,
  Package,
  Users,
  ShoppingBag,
  BarChart3,
  Settings,
  Tag,
  Award,
  FileText
} from 'lucide-vue-next'

const sidebarOpen = ref(false)
const userMenuOpen = ref(false)

const page = usePage()
const user = computed(() => page.props.auth?.user)

const pageTitle = computed(() => {
  const route = page.url
  if (route.includes('/admin/orders')) return 'Orders'
  if (route.includes('/admin/products')) return 'Products'
  if (route.includes('/admin/categories')) return 'Categories'
  if (route.includes('/admin/brands')) return 'Brands'
  if (route.includes('/admin/customers')) return 'Customers'
  if (route.includes('/admin/invoices')) return 'Invoices'
  if (route.includes('/admin/analytics')) return 'Analytics'
  if (route.includes('/admin/settings')) return 'Settings'
  return 'Dashboard'
})

const logout = () => {
  router.post('/logout')
  userMenuOpen.value = false
}

// Close dropdowns when clicking outside
onMounted(() => {
  document.addEventListener('click', (e) => {
    const target = e.target
    if (!target.closest('.user-dropdown')) {
      userMenuOpen.value = false
    }
  })
})
</script> 
