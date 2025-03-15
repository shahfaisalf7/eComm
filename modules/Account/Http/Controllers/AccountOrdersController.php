<?php

namespace Modules\Account\Http\Controllers;

use Illuminate\Http\Response;

class AccountOrdersController
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $orders = auth()->user()
            ->orders()
            ->latest()
            ->paginate(20);

        return view('storefront::public.account.orders.index', compact('orders'));
    }

    public function apiIndex()
    {
        $orders = auth()->user()->orders()->latest()->paginate(20);
        $final_data = ['orders' => $orders];
        return response()->json([
            'status' => 'success',
            'message' => trans('Order list.'),
            'data' => $final_data
        ]);
    }


    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $order = auth()->user()
            ->orders()
            ->with(['products', 'coupon', 'taxes'])
            ->where('id', $id)
            ->firstOrFail();

        return view('storefront::public.account.orders.show', compact('order'));
    }

    public function apiShow($id)
    {
        $orders = auth()->user()->orders()->with(['products', 'coupon', 'taxes'])->where('id', $id)->firstOrFail();
        $final_data = ['orders' => $orders];
        return response()->json([
            'status' => 'success',
            'message' => trans('Order details.'),
            'data' => $final_data
        ]);
    }
}
