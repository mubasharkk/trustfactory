<?php

namespace App\Actions\Cart;

use App\Models\UserCartItem;
use Lorisleiva\Actions\Concerns\AsAction;

class GetUserCartAction
{
    use AsAction;

    /**
     * Get cart items for a user.
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function handle(int $userId)
    {
        return UserCartItem::with('product.category')
            ->where('user_id', $userId)
            ->get();
    }
}
