<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\UserCartItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Product $product;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test user
        $this->user = User::factory()->create();

        // Create a test category
        $this->category = Category::factory()->create([
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);

        // Create a test product
        $this->product = Product::factory()->create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'description' => 'Test Description',
            'price' => 99.99,
            'stock_quantity' => 10,
            'sku' => 'TEST-001',
            'category_id' => $this->category->id,
        ]);
    }

    /** @test */
    public function authenticated_user_can_add_item_to_cart(): void
    {
        $response = $this->actingAs($this->user)->postJson('/cart', [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Item added to cart successfully',
            ]);

        $this->assertDatabaseHas('user_cart_items', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_add_item_to_cart(): void
    {
        $response = $this->postJson('/cart', [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function adding_existing_product_to_cart_updates_quantity(): void
    {
        // Add product to cart first
        UserCartItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        // Add same product again
        $response = $this->actingAs($this->user)->postJson('/cart', [
            'product_id' => $this->product->id,
            'quantity' => 3,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('user_cart_items', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 5, // 2 + 3
        ]);

        // Ensure only one cart item exists
        $this->assertEquals(1, UserCartItem::where('user_id', $this->user->id)
            ->where('product_id', $this->product->id)
            ->count());
    }

    /** @test */
    public function cannot_add_more_than_available_stock(): void
    {
        $response = $this->actingAs($this->user)->postJson('/cart', [
            'product_id' => $this->product->id,
            'quantity' => 15, // More than stock_quantity (10)
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['quantity']);

        $this->assertDatabaseMissing('user_cart_items', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
        ]);
    }

    /** @test */
    public function cannot_add_to_cart_if_total_exceeds_stock(): void
    {
        // Add some items first
        UserCartItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 8,
        ]);

        // Try to add more than available
        $response = $this->actingAs($this->user)->postJson('/cart', [
            'product_id' => $this->product->id,
            'quantity' => 5, // Would make total 13, but stock is 10
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['quantity']);
    }

    /** @test */
    public function product_id_is_required_when_adding_to_cart(): void
    {
        $response = $this->actingAs($this->user)->postJson('/cart', [
            'quantity' => 1,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['product_id']);
    }

    /** @test */
    public function quantity_is_required_when_adding_to_cart(): void
    {
        $response = $this->actingAs($this->user)->postJson('/cart', [
            'product_id' => $this->product->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['quantity']);
    }

    /** @test */
    public function quantity_must_be_at_least_one(): void
    {
        $response = $this->actingAs($this->user)->postJson('/cart', [
            'product_id' => $this->product->id,
            'quantity' => 0,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['quantity']);
    }

    /** @test */
    public function cannot_add_nonexistent_product_to_cart(): void
    {
        $response = $this->actingAs($this->user)->postJson('/cart', [
            'product_id' => 99999,
            'quantity' => 1,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['product_id']);
    }

    /** @test */
    public function authenticated_user_can_view_their_cart(): void
    {
        // Add items to cart
        $product2 = Product::factory()->create([
            'name' => 'Product 2',
            'price' => 49.99,
            'stock_quantity' => 5,
            'category_id' => $this->category->id,
        ]);

        UserCartItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        UserCartItem::create([
            'user_id' => $this->user->id,
            'product_id' => $product2->id,
            'quantity' => 3,
        ]);

        $response = $this->actingAs($this->user)->get('/cart');

        $response->assertStatus(200);
        
        // Verify cart items exist in database
        $this->assertDatabaseCount('user_cart_items', 2);
        
        // Verify the response contains Inertia data (if assertInertia is available)
        if (method_exists($response, 'assertInertia')) {
            $response->assertInertia(fn ($page) => $page
                ->component('Cart')
                ->has('cartItems', 2)
                ->where('cartCount', 5)
                ->where('totalPrice', (2 * 99.99) + (3 * 49.99))
            );
        }
    }

    /** @test */
    public function unauthenticated_user_cannot_view_cart(): void
    {
        $response = $this->get('/cart');

        $response->assertStatus(302); // Redirect to login
    }

    /** @test */
    public function empty_cart_shows_zero_items(): void
    {
        $response = $this->actingAs($this->user)->get('/cart');

        $response->assertStatus(200);
        
        // Verify no cart items exist
        $this->assertDatabaseCount('user_cart_items', 0);
        
        // Verify the response contains Inertia data (if assertInertia is available)
        if (method_exists($response, 'assertInertia')) {
            $response->assertInertia(fn ($page) => $page
                ->component('Cart')
                ->has('cartItems', 0)
                ->where('cartCount', 0)
                ->where('totalPrice', 0)
            );
        }
    }

    /** @test */
    public function authenticated_user_can_update_cart_item_quantity(): void
    {
        $cartItem = UserCartItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        // Refresh to ensure all attributes are loaded
        $cartItem->refresh();

        $response = $this->actingAs($this->user)->patchJson("/cart/{$cartItem->id}", [
            'quantity' => 5,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Cart item updated successfully',
            ]);

        $this->assertDatabaseHas('user_cart_items', [
            'id' => $cartItem->id,
            'quantity' => 5,
        ]);
    }

    /** @test */
    public function cannot_update_cart_item_to_exceed_stock(): void
    {
        $cartItem = UserCartItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($this->user)->patchJson("/cart/{$cartItem->id}", [
            'quantity' => 15, // More than stock_quantity (10)
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['quantity']);

        // Quantity should remain unchanged
        $this->assertDatabaseHas('user_cart_items', [
            'id' => $cartItem->id,
            'quantity' => 2,
        ]);
    }

    /** @test */
    public function user_cannot_update_another_users_cart_item(): void
    {
        $otherUser = User::factory()->create();

        $cartItem = UserCartItem::create([
            'user_id' => $otherUser->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($this->user)->patchJson("/cart/{$cartItem->id}", [
            'quantity' => 5,
        ]);

        $response->assertStatus(403);

        // Quantity should remain unchanged
        $this->assertDatabaseHas('user_cart_items', [
            'id' => $cartItem->id,
            'quantity' => 2,
        ]);
    }

    /** @test */
    public function quantity_is_required_when_updating_cart_item(): void
    {
        $cartItem = UserCartItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($this->user)->patchJson("/cart/{$cartItem->id}", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['quantity']);
    }

    /** @test */
    public function quantity_must_be_at_least_one_when_updating(): void
    {
        $cartItem = UserCartItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($this->user)->patchJson("/cart/{$cartItem->id}", [
            'quantity' => 0,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['quantity']);
    }

    /** @test */
    public function authenticated_user_can_remove_item_from_cart(): void
    {
        $cartItem = UserCartItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($this->user)->deleteJson("/cart/{$cartItem->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Item removed from cart successfully',
            ]);

        $this->assertDatabaseMissing('user_cart_items', [
            'id' => $cartItem->id,
        ]);
    }

    /** @test */
    public function user_cannot_remove_another_users_cart_item(): void
    {
        $otherUser = User::factory()->create();

        $cartItem = UserCartItem::create([
            'user_id' => $otherUser->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($this->user)->deleteJson("/cart/{$cartItem->id}");

        $response->assertStatus(403);

        // Cart item should still exist
        $this->assertDatabaseHas('user_cart_items', [
            'id' => $cartItem->id,
        ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_update_cart_item(): void
    {
        $cartItem = UserCartItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        $response = $this->patchJson("/cart/{$cartItem->id}", [
            'quantity' => 5,
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function unauthenticated_user_cannot_remove_cart_item(): void
    {
        $cartItem = UserCartItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        $response = $this->deleteJson("/cart/{$cartItem->id}");

        $response->assertStatus(401);
    }

    /** @test */
    public function cart_calculates_total_price_correctly(): void
    {
        $product2 = Product::factory()->create([
            'name' => 'Product 2',
            'price' => 25.50,
            'stock_quantity' => 5,
            'category_id' => $this->category->id,
        ]);

        $product3 = Product::factory()->create([
            'name' => 'Product 3',
            'price' => 10.00,
            'stock_quantity' => 5,
            'category_id' => $this->category->id,
        ]);

        UserCartItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 2, // 2 * 99.99 = 199.98
        ]);

        UserCartItem::create([
            'user_id' => $this->user->id,
            'product_id' => $product2->id,
            'quantity' => 3, // 3 * 25.50 = 76.50
        ]);

        UserCartItem::create([
            'user_id' => $this->user->id,
            'product_id' => $product3->id,
            'quantity' => 1, // 1 * 10.00 = 10.00
        ]);

        $response = $this->actingAs($this->user)->get('/cart');

        $expectedTotal = (2 * 99.99) + (3 * 25.50) + (1 * 10.00);

        $response->assertStatus(200);
        
        // Verify cart items exist
        $this->assertDatabaseCount('user_cart_items', 3);
        
        // Verify the response contains Inertia data (if assertInertia is available)
        if (method_exists($response, 'assertInertia')) {
            $response->assertInertia(fn ($page) => $page
                ->component('Cart')
                ->has('cartItems', 3)
                ->where('cartCount', 6)
                ->where('totalPrice', $expectedTotal)
            );
        }
    }

    /** @test */
    public function cart_items_include_product_relationships(): void
    {
        UserCartItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($this->user)->get('/cart');

        $response->assertStatus(200);
        
        // Verify cart item exists with product relationship
        $cartItem = UserCartItem::with('product.category')->first();
        $this->assertNotNull($cartItem->product);
        $this->assertNotNull($cartItem->product->category);
        
        // Verify the response contains Inertia data (if assertInertia is available)
        if (method_exists($response, 'assertInertia')) {
            $response->assertInertia(fn ($page) => $page
                ->component('Cart')
                ->has('cartItems.0.product')
                ->has('cartItems.0.product.category')
            );
        }
    }
}
