<?php

namespace App\Actions\Order;

use App\Models\Order;
use Lorisleiva\Actions\Concerns\AsAction;

class GetUserOrdersAction
{
    use AsAction;

    /**
     * Get orders for a user.
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function handle(int $userId)
    {
        return Order::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
