<?php

namespace Modules\Compare\Http\Controllers;

use Modules\Compare\Compare;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CompareController
{
//    public function index(Compare $compare)
//    {
//        return view('storefront::public.compare.index', compact('compare'));
//    }
    public function index(Compare $compare)
    {
        \Log::info('Compare web index session data', [
            'session_content' => session()->get('compare_cart_items', []),
            'products' => $compare->products()->toArray(),
            'count' => $compare->count(),
        ]);
        return view('storefront::public.compare.index', compact('compare'));
    }
    public function store(Compare $compare)
    {
        $compare->store(request('productId'));
    }

    public function destroy($productId, Compare $compare)
    {
        $compare->remove($productId);
    }

    public function apiIndex(Compare $compare): JsonResponse
    {
        $products = $compare->products();
        $attributes = $compare->attributes();
        \Log::info('Compare apiIndex session data', [
            'session_content' => session()->all(), // Log all session data
            'compare_cart_items' => session()->get('compare_cart_items', []),
            'products' => $products->toArray(),
            'count' => $compare->count(),
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'products' => $products,
                'attributes' => $attributes,
                'count' => $compare->count(),
            ],
            'message' => 'Compared products retrieved successfully.',
        ], 200);
    }

    public function apiStore(Request $request, Compare $compare): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'productId' => 'required|integer|exists:products,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed.',
            ], 422);
        }

        try {
            $compare->store($request->input('productId'));
            \Log::info('Compare apiStore session data', [
                'productId' => $request->input('productId'),
                'session_content' => session()->all(), // Log all session data
                'products' => $compare->products()->toArray(),
                'count' => $compare->count(),
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'products' => $compare->products(),
                    'count' => $compare->count(),
                ],
                'message' => 'Product added to comparison list.',
            ], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add product to comparison list.',
            ], 500);
        }
    }

    public function apiDestroy($productId, Compare $compare): JsonResponse
    {
        $productExists = $compare->getContent()->contains('id', $productId);

        if (!$productExists) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found in comparison list.',
            ], 404);
        }

        $compare->remove($productId);

        return response()->json([
            'success' => true,
            'data' => [
                'products' => $compare->products(),
                'count' => $compare->count(),
            ],
            'message' => 'Product removed from comparison list.',
        ], 200);
    }
}
