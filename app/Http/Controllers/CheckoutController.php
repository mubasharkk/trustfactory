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
            CheckoutAction::run($request->user()->id);

            return redirect()->route('orders.index')->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['checkout' => $e->getMessage()]);
        }
    }
}
