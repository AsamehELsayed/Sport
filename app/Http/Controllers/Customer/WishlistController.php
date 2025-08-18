<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WishlistController extends Controller
{
    /**
     * Display the user's wishlist.
     */
    public function index()
    {
        $wishlistItems = auth()->user()->wishlists()
            ->with(['product.category', 'product.brand'])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('customer/wishlist/Index', [
            'wishlistItems' => $wishlistItems,
        ]);
    }

    /**
     * Add a product to wishlist.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $userId = auth()->id();
        $productId = $request->product_id;

        // Check if already in wishlist
        $existingWishlist = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($existingWishlist) {
            return response()->json([
                'message' => 'Product is already in your wishlist',
                'isWishlisted' => true
            ], 200);
        }

        // Add to wishlist
        Wishlist::create([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);

        return response()->json([
            'message' => 'Product added to wishlist successfully',
            'isWishlisted' => true
        ], 201);
    }

    /**
     * Remove a product from wishlist.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $userId = auth()->id();
        $productId = $request->product_id;

        $wishlistItem = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if (!$wishlistItem) {
            return response()->json([
                'message' => 'Product not found in wishlist',
                'isWishlisted' => false
            ], 404);
        }

        $wishlistItem->delete();

        return response()->json([
            'message' => 'Product removed from wishlist successfully',
            'isWishlisted' => false
        ], 200);
    }

    /**
     * Toggle wishlist status for a product.
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $userId = auth()->id();
        $productId = $request->product_id;

        // Debug logging
        \Log::info('Wishlist toggle request', [
            'user_id' => $userId,
            'product_id' => $productId,
            'authenticated' => auth()->check(),
            'user' => auth()->user()
        ]);

        $wishlistItem = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($wishlistItem) {
            $wishlistItem->delete();
            $isWishlisted = false;
            $message = 'Product removed from wishlist';
        } else {
            Wishlist::create([
                'user_id' => $userId,
                'product_id' => $productId,
            ]);
            $isWishlisted = true;
            $message = 'Product added to wishlist';
        }

        return response()->json([
            'message' => $message,
            'isWishlisted' => $isWishlisted
        ], 200);
    }

    /**
     * Check if a product is in user's wishlist.
     */
    public function check(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $userId = auth()->id();
        $productId = $request->product_id;

        $isWishlisted = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();

        return response()->json([
            'isWishlisted' => $isWishlisted
        ], 200);
    }

    /**
     * Get wishlist count for the authenticated user.
     */
    public function count()
    {
        $count = auth()->user()->wishlists()->count();

        return response()->json([
            'count' => $count
        ], 200);
    }
}
