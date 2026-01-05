<?php

namespace App\Providers;

use App\Models\UserCartItem;
use App\Policies\CartItemPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        UserCartItem::class => CartItemPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        // Route model binding for cart items
        \Illuminate\Support\Facades\Route::bind('cartItem', function ($value) {
            return \App\Models\UserCartItem::findOrFail($value);
        });
    }
}
