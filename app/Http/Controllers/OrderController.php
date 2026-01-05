<?php

namespace App\Http\Controllers;

use App\Actions\Order\GetUserOrdersAction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    /**
     * Display a listing of user orders.
     */
    public function index(Request $request)
    {
        $orders = GetUserOrdersAction::run($request->user()->id);

        return Inertia::render('Orders', [
            'orders' => $orders,
        ]);
    }
}
