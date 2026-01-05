<?php

namespace App\Actions\Cart;

use App\Models\UserCartItem;
use Lorisleiva\Actions\Concerns\AsAction;

class GetCartCountAction
{
    use AsAction;

    /**
     * Get cart count for a user.
     *
     * @param int $userId
     * @return int
     */
    public function handle(int $userId): int
    {
        return UserCartItem::where('user_id', $userId)->sum('quantity');
    }
}
