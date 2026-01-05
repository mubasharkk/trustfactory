<?php

namespace App\Jobs;

use App\Mail\DailySalesReport;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class GenerateDailySalesReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $today = Carbon::today();
        
        // Get all orders created today without any filters
        $orders = Order::whereDate('created_at', $today)->get();

        // Calculate report data
        $totalOrders = $orders->count();
        $totalRevenue = $orders->sum('total_price');
        $totalQuantity = $orders->sum('quantity');

        // Aggregate products sold
        $productsSold = [];
        foreach ($orders as $order) {
            if (is_array($order->items)) {
                foreach ($order->items as $item) {
                    $productId = $item['product_id'] ?? null;
                    $productName = $item['product_name'] ?? 'Unknown Product';
                    $quantity = $item['quantity'] ?? 0;
                    $price = $item['product_price'] ?? 0;
                    $subtotal = $item['subtotal'] ?? ($quantity * $price);

                    if (!isset($productsSold[$productId])) {
                        $productsSold[$productId] = [
                            'product_id' => $productId,
                            'product_name' => $productName,
                            'quantity' => 0,
                            'revenue' => 0,
                        ];
                    }

                    $productsSold[$productId]['quantity'] += $quantity;
                    $productsSold[$productId]['revenue'] += $subtotal;
                }
            }
        }

        // Convert associative array to indexed array for easier template handling
        $productsSold = array_values($productsSold);

        // Get admin email from config
        $adminEmail = config('trustfactory.admin_email');

        if (!$adminEmail) {
            \Log::warning('Admin email not configured for daily sales report.');
            return;
        }

        // Send email report
        Mail::to($adminEmail)->send(new DailySalesReport(
            $today,
            $totalOrders,
            $totalRevenue,
            $totalQuantity,
            $productsSold
        ));
    }
}
