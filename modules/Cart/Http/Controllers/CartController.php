<?php

namespace Modules\Cart\Http\Controllers;

use Modules\Cart\Facades\Cart;

class CartController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('storefront::public.cart.index')->with([
            'crossSellProducts' => Cart::crossSellProducts()
        ]);
    }


    /**
     * Clear the cart.
     *
     * @return \Modules\Cart\Cart
     */
    public function clear()
    {
        Cart::clear();

        return Cart::instance();
    }

    public function clearApi()
    {
        Cart::clear();
        $data = Cart::instance();
        return responseWithData(__("Clear Successfully"), $data);
    }
}
