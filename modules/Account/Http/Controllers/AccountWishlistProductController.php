<?php

namespace Modules\Account\Http\Controllers;

use Illuminate\Http\Response;
use Modules\Product\Entities\Product;

class AccountWishlistProductController
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return auth()->user()
            ->wishlist()
            ->with('files')
            ->orderByPivot('created_at', 'desc')
            ->paginate(10);
    }

    public function indexApi()
    {
        $data = auth()->user()->wishlist()->with('files')->orderByPivot('created_at', 'desc')->paginate(10);
        return responseWithData(trans('account::messages.account_wishlist_data'), $data);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        if (auth()->user()->wishlistHas(request('productId'))) {
            if (isAPI()) {
                return responseSuccess(trans('account::messages.product_already_in_wishlist'));
            }
        } else {
            auth()->user()->wishlist()->attach(request('productId'));
            if (isAPI()) {
                return responseSuccess(trans('account::messages.product_add_in_wishlist'));
            }
        }
    }

    public function storeApi()
    {
        if (empty(request('productId'))) {
            return responseWithFailed(trans('account::messages.invalid_request'));
        }
        $model_product = new Product();
        $exists = $model_product->find(request('productId'));
        if (empty($exists)) {
            return responseNotFound(trans('account::messages.product_not_found'));
        }

        if (auth()->user()->wishlistHas(request('productId'))) {
            if (isAPI()) {
                return responseSuccess(trans('account::messages.product_already_in_wishlist'));
            }
        } else {
            auth()->user()->wishlist()->attach(request('productId'));
            if (isAPI()) {
                return responseSuccess(trans('account::messages.product_add_in_wishlist'));
            }
        }
    }

    /**
     * Destroy resources by the given id.
     *
     * @param Product $product
     *
     * @return void
     */
    public function destroy(Product $product)
    {
        // auth()->user()->wishlist()->detach($product);
        if (auth()->user()->wishlist()->where('product_id', $product->id)->exists()) {
            auth()->user()->wishlist()->detach($product);
            if (isAPI()) {
                return responseSuccess(trans('account::messages.product_removed_from_wishlist'));
            }
        } else {
            if (isAPI()) {
                return responseNotFound(trans('account::messages.product_not_found'));
            }
        }
    }
}
