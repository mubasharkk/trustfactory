<?php

namespace Tests\Unit\Cart;

use App\Models\User;
use App\Models\UserCartItem;
use App\Policies\CartItemPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartItemPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected CartItemPolicy $policy;
    protected User $user;
    protected User $otherUser;
    protected UserCartItem $cartItem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new CartItemPolicy();
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
        $this->cartItem = UserCartItem::factory()->create([
            'user_id' => $this->user->id,
        ]);
    }

    public function test_user_can_update_own_cart_item(): void
    {
        $result = $this->policy->update($this->user, $this->cartItem);

        $this->assertTrue($result);
    }

    public function test_user_cannot_update_another_users_cart_item(): void
    {
        $result = $this->policy->update($this->otherUser, $this->cartItem);

        $this->assertFalse($result);
    }

    public function test_user_can_delete_own_cart_item(): void
    {
        $result = $this->policy->delete($this->user, $this->cartItem);

        $this->assertTrue($result);
    }

    public function test_user_cannot_delete_another_users_cart_item(): void
    {
        $result = $this->policy->delete($this->otherUser, $this->cartItem);

        $this->assertFalse($result);
    }
}
