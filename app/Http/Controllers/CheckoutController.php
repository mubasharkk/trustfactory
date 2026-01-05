<?php

namespace App\Http\Controllers;

use App\Actions\Order\CheckoutAction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    /**
     * Process checkout.
     */
    public function store(Request $request)
    {
        try {
            $order = CheckoutAction::run($request->user()->id);

            return response()->json([
                'message' => 'Order placed successfully',
                'order' => $order,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
