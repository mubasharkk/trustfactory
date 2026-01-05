<?php

namespace Tests\Unit\Cart;

use App\Actions\Cart\AddToCartAction;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\UserCartItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AddToCartActionTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Product $product;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();
        $this->product = Product::factory()->create([
            'price' => 99.99,
            'stock_quantity' => 10,
            'category_id' => $this->category->id,
        ]);
    }

    public function test_can_add_new_item_to_cart(): void
    {
        $cartItem = AddToCartAction::run(
            $this->user->id,
            $this->product->id,
            2
        );

        $this->assertInstanceOf(UserCartItem::class, $cartItem);
        $this->assertEquals($this->user->id, $cartItem->user_id);
        $this->assertEquals($this->product->id, $cartItem->product_id);
        $this->assertEquals(2, $cartItem->quantity);

        $this->assertDatabaseHas('user_cart_items', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);
    }

    public function test_adding_existing_product_updates_quantity(): void
    {
        // Add product to cart first
        UserCartItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        // Add same product again
        $cartItem = AddToCartAction::run(
            $this->user->id,
            $this->product->id,
            3
        );

        $this->assertEquals(5, $cartItem->quantity);

        // Ensure only one cart item exists
        $this->assertDatabaseCount('user_cart_items', 1);
        $this->assertDatabaseHas('user_cart_items', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 5,
        ]);
    }

    public function test_throws_exception_when_quantity_exceeds_stock(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Insufficient stock');

        AddToCartAction::run(
            $this->user->id,
            $this->product->id,
            15 // More than stock_quantity (10)
        );

        $this->assertDatabaseMissing('user_cart_items', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
        ]);
    }

    public function test_throws_exception_when_total_quantity_exceeds_stock(): void
    {
        // Add some items first
        UserCartItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 8,
        ]);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Cannot add more items');

        // Try to add more than available (would make total 13, but stock is 10)
        AddToCartAction::run(
            $this->user->id,
            $this->product->id,
            5
        );
    }

    public function test_throws_exception_for_nonexistent_product(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        AddToCartAction::run(
            $this->user->id,
            99999, // Non-existent product ID
            1
        );
    }
}
