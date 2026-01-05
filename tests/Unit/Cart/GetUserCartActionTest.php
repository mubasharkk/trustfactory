<?php

namespace Tests\Unit\Cart;

use App\Actions\Cart\GetUserCartAction;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\UserCartItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetUserCartActionTest extends TestCase
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

    public function test_returns_empty_collection_for_user_with_no_cart_items(): void
    {
        $cartItems = GetUserCartAction::run($this->user->id);

        $this->assertCount(0, $cartItems);
    }

    public function test_returns_cart_items_for_user(): void
    {
        $product2 = Product::factory()->create([
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

        $cartItems = GetUserCartAction::run($this->user->id);

        $this->assertCount(2, $cartItems);
        $this->assertEquals($this->product->id, $cartItems->first()->product_id);
    }

    public function test_only_returns_cart_items_for_specified_user(): void
    {
        $otherUser = User::factory()->create();

        UserCartItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        UserCartItem::create([
            'user_id' => $otherUser->id,
            'product_id' => $this->product->id,
            'quantity' => 5,
        ]);

        $cartItems = GetUserCartAction::run($this->user->id);

        $this->assertCount(1, $cartItems);
        $this->assertEquals($this->user->id, $cartItems->first()->user_id);
    }

    public function test_loads_product_and_category_relationships(): void
    {
        UserCartItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        $cartItems = GetUserCartAction::run($this->user->id);

        $this->assertTrue($cartItems->first()->relationLoaded('product'));
        $this->assertTrue($cartItems->first()->product->relationLoaded('category'));
        $this->assertNotNull($cartItems->first()->product);
        $this->assertNotNull($cartItems->first()->product->category);
    }
}
