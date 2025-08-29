import { ref, computed, watch, readonly } from 'vue'
import { roundPrice } from '@/utils/price'

export interface CartItem {
  id: number
  name: string
  price: number
  originalPrice?: number
  image: string
  size: string
  color: string
  variantId?: number
  quantity: number
  inStock: boolean
  stockQuantity: number
  rating: number
  reviews: number
  category: string
  brand?: string
}

export interface SavedItem extends Omit<CartItem, 'quantity'> {
  quantity: 1
}

const cartItems = ref<CartItem[]>([])
const savedItems = ref<SavedItem[]>([])

// Load cart from localStorage on initialization
const loadCart = () => {
  try {
    const savedCart = localStorage.getItem('sportApp-cart')
    const savedSavedItems = localStorage.getItem('sportApp-savedItems')

    if (savedCart) {
      cartItems.value = JSON.parse(savedCart)
    }

    if (savedSavedItems) {
      savedItems.value = JSON.parse(savedSavedItems)
    }
  } catch (error) {
    console.error('Error loading cart from localStorage:', error)
  }
}

// Save cart to localStorage whenever it changes
watch(cartItems, (newCart) => {
  try {
    localStorage.setItem('sportApp-cart', JSON.stringify(newCart))
  } catch (error) {
    console.error('Error saving cart to localStorage:', error)
  }
}, { deep: true })

watch(savedItems, (newSavedItems) => {
  try {
    localStorage.setItem('sportApp-savedItems', JSON.stringify(newSavedItems))
  } catch (error) {
    console.error('Error saving saved items to localStorage:', error)
  }
}, { deep: true })

// Computed properties
const itemCount = computed(() => {
  return cartItems.value.reduce((sum, item) => sum + item.quantity, 0)
})

const subtotal = computed(() => {
  return roundPrice(cartItems.value.reduce((sum, item) => {
    return sum + (item.price * item.quantity)
  }, 0))
})

const totalSavings = computed(() => {
  return roundPrice(cartItems.value.reduce((sum, item) => {
    if (item.originalPrice) {
      return sum + ((item.originalPrice - item.price) * item.quantity)
    }
    return sum
  }, 0))
})

const shipping = computed(() => {
  return subtotal.value >= 100 ? 0 : 9.99
})

const tax = computed(() => {
  return roundPrice(subtotal.value * 0.08) // 8% tax rate
})

const total = computed(() => {
  return roundPrice(subtotal.value + shipping.value + tax.value)
})

const canCheckout = computed(() => {
  return cartItems.value.length > 0 && cartItems.value.every(item => item.inStock)
})

// Methods
const addToCart = (item: Omit<CartItem, 'quantity'>, quantity: number = 1) => {
  const existingItem = cartItems.value.find(cartItem =>
    cartItem.id === item.id &&
    cartItem.size === item.size &&
    cartItem.color === item.color
  )

  if (existingItem) {
    const newQuantity = existingItem.quantity + quantity
    if (newQuantity <= item.stockQuantity) {
      existingItem.quantity = newQuantity
    }
  } else {
    cartItems.value.push({ ...item, quantity })
  }
}

const updateQuantity = (itemId: number, size: string, color: string, newQuantity: number) => {
  const item = cartItems.value.find(item =>
    item.id === itemId &&
    item.size === size &&
    item.color === color
  )
  if (item) {
    if (newQuantity <= 0) {
      removeFromCart(itemId, size, color)
    } else if (newQuantity <= item.stockQuantity) {
      item.quantity = newQuantity
    }
  }
}

const removeFromCart = (itemId: number, size: string, color: string) => {
  cartItems.value = cartItems.value.filter(item =>
    !(item.id === itemId && item.size === size && item.color === color)
  )
}

const moveToSaved = (itemId: number, size: string, color: string) => {
  const item = cartItems.value.find(item =>
    item.id === itemId &&
    item.size === size &&
    item.color === color
  )
  if (item) {
    const { quantity, ...savedItem } = item
    savedItems.value.push({ ...savedItem, quantity: 1 })
    removeFromCart(itemId, size, color)
  }
}

const moveToCart = (itemId: number, size: string, color: string) => {
  const item = savedItems.value.find(item =>
    item.id === itemId &&
    item.size === size &&
    item.color === color
  )
  if (item) {
    const { quantity, ...cartItem } = item
    addToCart(cartItem, 1)
    removeFromSaved(itemId, size, color)
  }
}

const removeFromSaved = (itemId: number, size: string, color: string) => {
  savedItems.value = savedItems.value.filter(item =>
    !(item.id === itemId && item.size === size && item.color === color)
  )
}

const clearCart = () => {
  cartItems.value = []
}

const clearSaved = () => {
  savedItems.value = []
}

// Initialize cart on module load
loadCart()

export function useCart() {
  return {
    // State
    cartItems: readonly(cartItems),
    savedItems: readonly(savedItems),

    // Computed
    itemCount,
    subtotal,
    totalSavings,
    shipping,
    tax,
    total,
    canCheckout,

    // Methods
    addToCart,
    updateQuantity,
    removeFromCart,
    moveToSaved,
    moveToCart,
    removeFromSaved,
    clearCart,
    clearSaved
  }
}
