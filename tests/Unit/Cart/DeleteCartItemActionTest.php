<?php

namespace Tests\Unit\Cart;

use App\Actions\Cart\DeleteCartItemAction;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\UserCartItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteCartItemActionTest extends TestCase
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
            'category_id' => $this->category->id,
        ]);
    }

    public function test_can_delete_cart_item(): void
    {
        $cartItem = UserCartItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        $result = DeleteCartItemAction::run($cartItem);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('user_cart_items', [
            'id' => $cartItem->id,
        ]);
    }

    public function test_deleting_nonexistent_cart_item_returns_false(): void
    {
        $cartItem = new UserCartItem();
        $cartItem->id = 99999;

        $result = DeleteCartItemAction::run($cartItem);

        $this->assertFalse($result);
    }
}
