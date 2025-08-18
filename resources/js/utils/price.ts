/**
 * Utility functions for price formatting and calculations
 */

/**
 * Rounds a number to 2 decimal places
 */
export function roundPrice(price: number): number {
  return Math.round(price * 100) / 100;
}

/**
 * Formats a price as a currency string with 2 decimal places
 */
export function formatPrice(price: number): string {
  return `$${roundPrice(price) }`;
}

/**
 * Calculates the final price after discount
 */
export function calculateFinalPrice(originalPrice: number, discountPercentage: number): number {
  if (discountPercentage <= 0) {
    return roundPrice(originalPrice);
  }
  return roundPrice(originalPrice * (100 - discountPercentage) / 100);
}

/**
 * Formats the final price after discount
 */
export function formatFinalPrice(originalPrice: number, discountPercentage: number): string {
  return formatPrice(calculateFinalPrice(originalPrice, discountPercentage));
}

/**
 * Calculates the discount amount
 */
export function calculateDiscountAmount(originalPrice: number, discountPercentage: number): number {
  if (discountPercentage <= 0) {
    return 0;
  }
  return roundPrice(originalPrice * discountPercentage / 100);
}

/**
 * Formats the discount amount
 */
export function formatDiscountAmount(originalPrice: number, discountPercentage: number): string {
  return formatPrice(calculateDiscountAmount(originalPrice, discountPercentage));
}
