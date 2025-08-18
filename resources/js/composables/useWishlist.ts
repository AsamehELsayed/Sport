import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'

interface WishlistItem {
  id: number
  product: {
    id: number
    name: string
    price: number
    main_image: string
    category?: {
      name: string
    }
    brand?: {
      name: string
    }
  }
  created_at: string
}

// Singleton state
const wishlistItems = ref<WishlistItem[]>([])
const wishlistCount = ref(0)
const isLoading = ref(false)

export function useWishlist() {
  // Add product to wishlist
  const addToWishlist = async (productId: number) => {
    if (isLoading.value) return

    isLoading.value = true
    try {
      const response = await fetch('/wishlist', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        body: JSON.stringify({ product_id: productId }),
      })

      const data = await response.json()

      if (response.ok) {
        // Update wishlist count
        await getWishlistCount()
        return { success: true, message: data.message, isWishlisted: data.isWishlisted }
      } else {
        return { success: false, message: data.message || 'Failed to add to wishlist' }
      }
    } catch (error) {
      console.error('Error adding to wishlist:', error)
      return { success: false, message: 'Network error occurred' }
    } finally {
      isLoading.value = false
    }
  }

  // Remove product from wishlist
  const removeFromWishlist = async (productId: number) => {
    if (isLoading.value) return

    isLoading.value = true
    try {
      const response = await fetch('/wishlist', {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        body: JSON.stringify({ product_id: productId }),
      })

      const data = await response.json()

      if (response.ok) {
        // Update wishlist count
        await getWishlistCount()
        return { success: true, message: data.message, isWishlisted: data.isWishlisted }
      } else {
        return { success: false, message: data.message || 'Failed to remove from wishlist' }
      }
    } catch (error) {
      console.error('Error removing from wishlist:', error)
      return { success: false, message: 'Network error occurred' }
    } finally {
      isLoading.value = false
    }
  }

  // Toggle wishlist status
  const toggleWishlist = async (productId: number) => {
    if (isLoading.value) return

    isLoading.value = true
    try {
      const response = await fetch('/wishlist/toggle', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        body: JSON.stringify({ product_id: productId }),
      })

      const data = await response.json()

      if (response.ok) {
        // Update wishlist count
        await getWishlistCount()
        return { success: true, message: data.message, isWishlisted: data.isWishlisted }
      } else {
        return { success: false, message: data.message || 'Failed to toggle wishlist' }
      }
    } catch (error) {
      console.error('Error toggling wishlist:', error)
      return { success: false, message: 'Network error occurred' }
    } finally {
      isLoading.value = false
    }
  }

  // Check if product is in wishlist
  const checkWishlistStatus = async (productId: number) => {
    try {
      const response = await fetch('/wishlist/check', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        body: JSON.stringify({ product_id: productId }),
      })

      const data = await response.json()

      if (response.ok) {
        return data.isWishlisted
      } else {
        return false
      }
    } catch (error) {
      console.error('Error checking wishlist status:', error)
      return false
    }
  }

  // Get wishlist count
  const getWishlistCount = async () => {
    try {
      const response = await fetch('/wishlist/count')
      const data = await response.json()

      if (response.ok) {
        wishlistCount.value = data.count
      }
    } catch (error) {
      console.error('Error getting wishlist count:', error)
    }
  }

  // Load wishlist items
  const loadWishlist = async () => {
    try {
      const response = await fetch('/wishlist')
      const data = await response.json()

      if (response.ok) {
        wishlistItems.value = data.wishlistItems || []
      }
    } catch (error) {
      console.error('Error loading wishlist:', error)
    }
  }

  // Navigate to wishlist page
  const goToWishlist = () => {
    router.visit('/wishlist')
  }

  return {
    wishlistItems: computed(() => wishlistItems.value),
    wishlistCount: computed(() => wishlistCount.value),
    isLoading: computed(() => isLoading.value),
    addToWishlist,
    removeFromWishlist,
    toggleWishlist,
    checkWishlistStatus,
    getWishlistCount,
    loadWishlist,
    goToWishlist,
  }
}
