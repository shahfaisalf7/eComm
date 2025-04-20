<?php

namespace Modules\Cart\Providers;

use Modules\Cart\Cart;
use Modules\Cart\Storages\Database;
use Illuminate\Support\ServiceProvider;

class CartServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Cart::class, function ($app) {
            $cartId = $this->getCartId();
            return new Cart(
                new Database(),
                $app['events'],
                'cart',
                $cartId,
                config('fleetcart.modules.cart.config')
            );
        });

        $this->app->alias(Cart::class, 'cart');
    }

    private function getCartId()
    {
        if (request()->is('api/v1/*')) {
            // API: Use user_id from token (via ApiAuthMiddleware)
            $user = auth('api')->user();
            if (!$user) {
                throw new \Exception('Unauthenticated API request'); // Handled by middleware
            }
            return (string) $user->id;
        }
        // Web: Use user_id if authenticated, else session ID
        return auth()->check() ? (string) auth()->user()->id : session()->getId();
    }
}
