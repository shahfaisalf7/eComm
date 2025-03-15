<?php
namespace Modules\Account\Http\Controllers;
use Illuminate\Http\Response;

class AccountDashboardController
{
    public function index()
    {
        return view('storefront::public.account.dashboard.index', [
            'account' => auth()->user(), // Web uses default 'web' guard
            'recentOrders' => auth()->user()->recentOrders(5),
        ]);
    }

    public function apiIndex()
    {
        $account = auth('api')->user(); // Use 'api' guard explicitly
        if (!$account) {
            return response()->json([
                'status' => 'error',
                'message' => trans('Unauthenticated'),
                'code' => 401
            ], 401);
        }
        $recentOrders = $account->recentOrders(5); // Use $account, not auth()->user()
        $final_data = ['account' => $account, 'recentOrders' => $recentOrders];
        return response()->json([
            'status' => 'success',
            'message' => trans('Accounts Data.'),
            'data' => $final_data
        ]);
    }
}
