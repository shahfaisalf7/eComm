<?php

namespace Modules\Compare\Http\Controllers;

use Modules\Compare\Compare;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

class CompareRelatedProductController
{
    /**
     * Display a listing of the resource (Web).
     *
     * @param Compare $compare
     * @return Response
     */
    public function index(Compare $compare)
    {
        return $compare->relatedProducts();
    }

    /**
     * Display a listing of related products for the compared items for API.
     *
     * @param Compare $compare
     * @return JsonResponse
     */
    public function apiRelatedProductsIndex(Compare $compare): JsonResponse
    {
        $relatedProducts = $compare->relatedProducts();

        return response()->json([
            'success' => true,
            'data' => $relatedProducts,
            'message' => 'Related products retrieved successfully.',
        ], 200);
    }
}
