<?php

namespace Tests\Unit\Cart;

use App\Actions\Cart\UpdateCartItemAction;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\UserCartItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class UpdateCartItemActionTest extends TestCase
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
            'stock_quantity' => 10,
            'category_id' => $this->category->id,
        ]);
    }

    public function test_can_update_cart_item_quantity(): void
    {
        $cartItem = UserCartItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        $updatedCartItem = UpdateCartItemAction::run($cartItem, 5);

        $this->assertEquals(5, $updatedCartItem->quantity);
        $this->assertDatabaseHas('user_cart_items', [
            'id' => $cartItem->id,
            'quantity' => 5,
        ]);
    }

    public function test_throws_exception_when_quantity_exceeds_stock(): void
    {
        $cartItem = UserCartItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Insufficient stock');

        UpdateCartItemAction::run($cartItem, 15); // More than stock_quantity (10)

        // Quantity should remain unchanged
        $this->assertDatabaseHas('user_cart_items', [
            'id' => $cartItem->id,
            'quantity' => 2,
        ]);
    }

    public function test_can_update_to_maximum_stock_quantity(): void
    {
        $cartItem = UserCartItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        $updatedCartItem = UpdateCartItemAction::run($cartItem, 10); // Exactly stock_quantity

        $this->assertEquals(10, $updatedCartItem->quantity);
    }
}
