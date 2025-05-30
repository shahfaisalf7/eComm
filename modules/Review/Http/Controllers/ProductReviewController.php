<?php

namespace Modules\Review\Http\Controllers;

use Illuminate\Http\Response;
use Modules\Review\Entities\Review;
use Modules\Product\Entities\Product;
use Modules\Review\Http\Requests\StoreReviewRequest;

class ProductReviewController
{
    /**
     * Display a listing of the resource.
     *
     * @param int $productId
     *
     * @return Response
     */
    public function index($productId)
    {
        return Review::where('product_id', $productId)->latest()->paginate(5);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param int $productId
     * @param StoreReviewRequest $request
     *
     * @return Response
     */
    public function store($productId, StoreReviewRequest $request)
    {
        if (!setting('reviews_enabled')) {
            return;
        }

        return Product::findOrFail($productId)
            ->reviews()
            ->create([
                'reviewer_id' => auth()->id(),
                'rating' => $request->rating,
                'reviewer_name' => $request->reviewer_name,
                'comment' => $request->comment,
                'is_approved' => setting('auto_approve_reviews', 0),
            ]);
    }

    /**
     * Store a newly created resource in storage for API.
     *
     * @param int $productId
     * @param StoreReviewRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiStore($productId, StoreReviewRequest $request): \Illuminate\Http\JsonResponse
    {
        if (!setting('reviews_enabled')) {
            return response()->json(['message' => 'Reviews are disabled.'], 403);
        }

        $review = Product::findOrFail($productId)
            ->reviews()
            ->create([
                'reviewer_id' => auth()->id(),
                'rating' => $request->rating,
                'reviewer_name' => $request->reviewer_name,
                'comment' => $request->comment,
                'is_approved' => setting('auto_approve_reviews', 0),
            ]);

        return response()->json([
            'message' => 'Review created successfully.',
            //'data' => new ReviewResource($review),
        ], 201);
    }


}
