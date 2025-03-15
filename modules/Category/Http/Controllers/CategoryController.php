<?php

namespace Modules\Category\Http\Controllers;

use Illuminate\Http\Response;
use Modules\Category\Entities\Category;

class CategoryController
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('storefront::public.categories.index', [
            'categories' => Category::all()->nest(),
        ]);
    }

    public function apiIndex()
    {
        $categories = Category::all()->nest();
        $final_data = ['categories' => $categories];
        return response()->json([
            'status' => 'success',
            'message' => trans('Categorires'),
            'data' => $final_data
        ]);
    }
}
