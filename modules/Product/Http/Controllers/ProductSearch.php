<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Modules\Product\Entities\Product;
use Modules\Category\Entities\Category;
use Modules\Attribute\Entities\Attribute;
use Modules\Product\Filters\ProductFilter;
use Modules\Product\Events\ShowingProductList;

trait ProductSearch
{
    /**
     * Search products for the request.
     *
     * @param Product $model
     * @param ProductFilter $productFilter
     *
     * @return JsonResponse
     */
    public function searchProducts(Product $model, ProductFilter $productFilter)
    {

        $productIds = [];

        if (request()->filled('query')) {
            $model = $model->search(request('query'));
            $productIds = $model->keys();
        }

        $query = $model->filter($productFilter);

        if (request()->filled('category')) {
            $productIds = (clone $query)->select('products.id')->resetOrders()->pluck('id');
        }

        $products = $query->paginate(request('perPage', 50));

        event(new ShowingProductList($products));

        return response()->json([
            'products' => $products,
            'attributes' => $this->getAttributes($productIds),
        ]);
    }

//    public function searchProductsMobile(Product $model, ProductFilter $productFilter)
//    {
//        $productIds = [];
//
//        if (request()->filled('query')) {
//            $model = $model->search(request('query'));
//            $productIds = $model->keys();
//        }
//
//        $query = $model->filter($productFilter);
//        \Log::info('SQL Query: ' . $query->toSql());
//        \Log::info('Bindings: ', $query->getBindings());
//
//        if (request()->filled('category')) {
//            $productIds = (clone $query)->select('products.id')->resetOrders()->pluck('id');
//            \Log::info('Product IDs: ', $productIds->toArray());
//        }
//
//        $products = $query->paginate(request('perPage', 50));
//        \Log::info('Products Count: ' . $products->count());
//
//        return response()->json([
//            'products' => $products,
//            'attributes' => $this->getAttributes($productIds),
//        ]);
//    }

    public function searchProductsMobile(Product $model, ProductFilter $productFilter)
    {
        $productIds = [];

        if (request()->filled('query')) {
            $model = $model->search(request('query'));
            $productIds = $model->keys();
        }

        $query = $model->filter($productFilter);

        if (request()->filled('category')) {
            $productIds = (clone $query)->select('products.id')->resetOrders()->pluck('id');
        }

        // Paginate the query
        $products = $query->paginate(request('perPage', 50));

        // Append all query parameters from the request to the pagination URLs
        $products->appends(request()->query());

        event(new ShowingProductList($products));

        return response()->json([
            'products' => $products,
            'attributes' => $this->getAttributes($productIds),
        ]);
    }

    public function searchProductsforApi(Product $model, ProductFilter $productFilter)
    {
        $productIds = [];

        if (request()->filled('query')) {
            $model = $model->search(request('query'));
            $productIds = $model->keys();
        }

        $query = $model->filter($productFilter);

        if (request()->filled('category')) {
            $productIds = (clone $query)->select('products.id')->resetOrders()->pluck('id');
        }

        // Paginate the query
        $products = $query->paginate(request('perPage', 50));

        // Append all query parameters from the request to the pagination URLs
        $products->appends(request()->query());

        event(new ShowingProductList($products));

        return response()->json([
            'products' => $products,
            'attributes' => $this->getAttributes($productIds),
        ]);
    }

    private function getAttributes($productIds)
    {
        if (!request()->filled('category') || $this->filteringViaRootCategory()) {
            return collect();
        }

        return Attribute::with('values')
            ->where('is_filterable', true)
            ->whereHas('categories', function ($query) use ($productIds) {
                $query->whereIn('id', $this->getProductsCategoryIds($productIds));
            })
            ->get();
    }


    private function filteringViaRootCategory()
    {
        return Category::where('slug', request('category'))
            ->firstOrNew([])
            ->isRoot();
    }


    private function getProductsCategoryIds($productIds)
    {
        return DB::table('product_categories')
            ->whereIn('product_id', $productIds)
            ->distinct()
            ->pluck('category_id');
    }
}
