<?php

namespace App\Actions\Order;

use App\Models\Order;
use App\Models\UserCartItem;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class CheckoutAction
{
    use AsAction;

    /**
     * Process checkout and create order.
     *
     * @param int $userId
     * @return Order
     */
    public function handle(int $userId): Order
    {
        return DB::transaction(function () use ($userId) {
            // Get all cart items with product details
            $cartItems = UserCartItem::with('product')
                ->where('user_id', $userId)
                ->get();

            if ($cartItems->isEmpty()) {
                throw new \Exception('Cart is empty. Cannot proceed with checkout.');
            }

            // Prepare order items
            $items = $cartItems->map(function ($cartItem) {
                return [
                    'product_id' => $cartItem->product_id,
                    'product_name' => $cartItem->product->name,
                    'product_price' => $cartItem->product->price,
                    'quantity' => $cartItem->quantity,
                    'subtotal' => $cartItem->product->price * $cartItem->quantity,
                ];
            })->toArray();

            // Calculate totals
            $totalPrice = $cartItems->sum(function ($cartItem) {
                return $cartItem->product->price * $cartItem->quantity;
            });

            $totalQuantity = $cartItems->sum('quantity');

            // Create order
            $order = Order::create([
                'id' => \Illuminate\Support\Str::uuid(),
                'user_id' => $userId,
                'items' => $items,
                'total_price' => $totalPrice,
                'quantity' => $totalQuantity,
                'status' => 'pending',
                'payment_method' => 'Cash on Delivery',
            ]);

            // Clear cart
            UserCartItem::where('user_id', $userId)->delete();

            return $order;
        });
    }
}
