<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WishlistTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_add_product_to_wishlist()
    {
        $user = User::factory()->create(['is_admin' => false]);
        $product = Product::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/wishlist', [
                'product_id' => $product->id
            ]);

        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'Product added to wishlist successfully',
            'isWishlisted' => true
        ]);

        $this->assertDatabaseHas('wishlists', [
            'user_id' => $user->id,
            'product_id' => $product->id
        ]);
    }

    public function test_user_cannot_add_same_product_twice()
    {
        $user = User::factory()->create(['is_admin' => false]);
        $product = Product::factory()->create();

        // Add product to wishlist first time
        $this->actingAs($user)
            ->postJson('/wishlist', [
                'product_id' => $product->id
            ]);

        // Try to add the same product again
        $response = $this->actingAs($user)
            ->postJson('/wishlist', [
                'product_id' => $product->id
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Product is already in your wishlist',
            'isWishlisted' => true
        ]);

        // Should only have one wishlist entry
        $this->assertDatabaseCount('wishlists', 1);
    }

    public function test_user_can_remove_product_from_wishlist()
    {
        $user = User::factory()->create(['is_admin' => false]);
        $product = Product::factory()->create();

        // Add product to wishlist
        Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $product->id
        ]);

        $response = $this->actingAs($user)
            ->deleteJson('/wishlist', [
                'product_id' => $product->id
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Product removed from wishlist successfully',
            'isWishlisted' => false
        ]);

        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $user->id,
            'product_id' => $product->id
        ]);
    }

    public function test_user_can_toggle_wishlist_status()
    {
        $user = User::factory()->create(['is_admin' => false]);
        $product = Product::factory()->create();

        // Toggle to add
        $response = $this->actingAs($user)
            ->postJson('/wishlist/toggle', [
                'product_id' => $product->id
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Product added to wishlist',
            'isWishlisted' => true
        ]);

        // Toggle to remove
        $response = $this->actingAs($user)
            ->postJson('/wishlist/toggle', [
                'product_id' => $product->id
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Product removed from wishlist',
            'isWishlisted' => false
        ]);
    }

    public function test_user_can_check_wishlist_status()
    {
        $user = User::factory()->create(['is_admin' => false]);
        $product = Product::factory()->create();

        // Check when not in wishlist
        $response = $this->actingAs($user)
            ->postJson('/wishlist/check', [
                'product_id' => $product->id
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'isWishlisted' => false
        ]);

        // Add to wishlist
        Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $product->id
        ]);

        // Check when in wishlist
        $response = $this->actingAs($user)
            ->postJson('/wishlist/check', [
                'product_id' => $product->id
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'isWishlisted' => true
        ]);
    }

    public function test_user_can_get_wishlist_count()
    {
        $user = User::factory()->create(['is_admin' => false]);
        $products = Product::factory()->count(3)->create();

        // Add products to wishlist
        foreach ($products as $product) {
            Wishlist::create([
                'user_id' => $user->id,
                'product_id' => $product->id
            ]);
        }

        $response = $this->actingAs($user)
            ->getJson('/wishlist/count');

        $response->assertStatus(200);
        $response->assertJson([
            'count' => 3
        ]);
    }

    public function test_user_can_view_wishlist_page()
    {
        $user = User::factory()->create(['is_admin' => false]);
        $product = Product::factory()->create();

        Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $product->id
        ]);

        $response = $this->actingAs($user)
            ->get('/wishlist');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) =>
            $page->component('customer/wishlist/Index')
                ->has('wishlistItems', 1)
        );
    }

    public function test_unauthenticated_user_cannot_access_wishlist()
    {
        $response = $this->get('/wishlist');

        $response->assertRedirect(route('customer.login'));
    }

    public function test_unauthenticated_user_cannot_add_to_wishlist()
    {
        $product = Product::factory()->create();

        $response = $this->postJson('/wishlist', [
            'product_id' => $product->id
        ]);

        $response->assertStatus(401);
    }

    public function test_validation_requires_product_id()
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)
            ->postJson('/wishlist', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['product_id']);
    }

    public function test_validation_requires_valid_product_id()
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)
            ->postJson('/wishlist', [
                'product_id' => 99999
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['product_id']);
    }

    public function test_wishlist_toggle_works_for_authenticated_user()
    {
        $user = User::factory()->create(['is_admin' => false]);
        $product = Product::factory()->create();

        // Test adding to wishlist
        $response = $this->actingAs($user)
            ->postJson('/wishlist/toggle', [
                'product_id' => $product->id
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Product added to wishlist',
            'isWishlisted' => true
        ]);

        // Verify it's in the database
        $this->assertDatabaseHas('wishlists', [
            'user_id' => $user->id,
            'product_id' => $product->id
        ]);

        // Test removing from wishlist
        $response = $this->actingAs($user)
            ->postJson('/wishlist/toggle', [
                'product_id' => $product->id
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Product removed from wishlist',
            'isWishlisted' => false
        ]);

        // Verify it's removed from the database
        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $user->id,
            'product_id' => $product->id
        ]);
    }
}
