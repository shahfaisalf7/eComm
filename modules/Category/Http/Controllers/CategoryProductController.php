<?php

namespace Modules\Category\Http\Controllers;

use Illuminate\Http\Response;
use Modules\Product\Entities\Product;
use Modules\Category\Entities\Category;
use Modules\Product\Filters\ProductFilter;
use Modules\Product\Http\Controllers\ProductSearch;

class CategoryProductController
{
    use ProductSearch;

    public function index(Category $category, Product $model, ProductFilter $productFilter)
    {
        request()->merge(['category' => $category->slug]);

        if (request()->expectsJson()) {
            return $this->searchProducts($model, $productFilter);
        }

        $category = $category->load('metaData');

        if (!$category->exists) {
            abort(404);
        }

        return view('storefront::public.products.index', [
            'category' => $category,
            'categoryName' => $category->name,
            'categoryBanner' => $category->banner->path ?? null,
            'categories' => Category::searchable(), // Assuming this is passed for the sidebar
            'minPrice' => 0, // Adjust as needed
            'maxPrice' => 1000, // Adjust as needed
        ]);
    }

    public function apiIndex(Category $category, Product $model, ProductFilter $productFilter)
    {
        request()->merge(['category' => $category->slug]);
        if (request()->expectsJson()) {
            return $this->searchProducts($model, $productFilter);
        }

        $category = $category->load('metaData');
        $final_data = [
            'categoryName' => $category->name,
            'categoryBanner' => $category->banner->path ?? null,
            'metaTitle' => $category->metaData->first()->meta_title ?? $category->name,
            'metaDescription' => $category->metaData->first()->meta_description ?? '',
        ];
        return response()->json([
            'status' => 'success',
            'message' => trans('Categories products.'),
            'data' => $final_data
        ]);
    }
}
