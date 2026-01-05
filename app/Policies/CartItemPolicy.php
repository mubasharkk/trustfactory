<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserCartItem;

class CartItemPolicy
{
    /**
     * Determine if the user can update the cart item.
     */
    public function update(User $user, UserCartItem $cartItem): bool
    {
        return $user->id === $cartItem->user_id;
    }

    /**
     * Determine if the user can delete the cart item.
     */
    public function delete(User $user, UserCartItem $cartItem): bool
    {
        return $user->id === $cartItem->user_id;
    }
}
