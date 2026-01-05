<?php

namespace App\Observers;

use App\Jobs\LowStockNotificationJob;
use App\Models\Product;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        $this->checkLowStock($product);
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        // Only check if stock_quantity was changed
        if ($product->isDirty('stock_quantity')) {
            $lowStockThreshold = config('trustfactory.low_stock_threshold');
            $originalStock = $product->getOriginal('stock_quantity');
            $currentStock = $product->stock_quantity;
            
            // Only trigger notification if stock is now below threshold
            // and the previous stock was above threshold (to avoid duplicate notifications)
            if ($currentStock <= $lowStockThreshold && $originalStock > $lowStockThreshold) {
                LowStockNotificationJob::dispatch($product);
            }
        }
    }

    /**
     * Check if product stock is low and send notification.
     */
    protected function checkLowStock(Product $product): void
    {
        $lowStockThreshold = config('trustfactory.low_stock_threshold');
        
        if ($product->stock_quantity <= $lowStockThreshold) {
            LowStockNotificationJob::dispatch($product);
        }
    }
}
