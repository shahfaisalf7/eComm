<?php

namespace Modules\Product\Http\Controllers\Admin;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Modules\Product\Entities\Product;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Modules\Admin\Traits\HasCrudActions;
use Modules\Product\Http\Requests\SaveProductRequest;
use Modules\Product\Transformers\ProductEditResource;

class ProductController
{
    use HasCrudActions;

    /**
     * Model for the resource.
     *
     * @var string
     */
    protected string $model = Product::class;

    /**
     * Label of the resource.
     *
     * @var string
     */
    protected string $label = 'product::products.product';

    /**
     * View path of the resource.
     *
     * @var string
     */
    protected string $viewPath = 'product::admin.products';

    /**
     * Form requests for the resource.
     *
     * @var array|string
     */
    protected string|array $validation = SaveProductRequest::class;


    /**
     * Store a newly created resource in storage.
     *
     * @return Response|JsonResponse
     */
//    public function store()
//    {
//        $this->disableSearchSyncing();
//        if (empty(request()->get('sku'))) {
//            // Generate SKU from product name (first 3 letters + 6 unique digits)
//            $productName = request()->get('name', 'PRD'); // Fallback to 'PRD' if name is missing
//            $skuPrefix = strtoupper(substr($productName, 0, 3)); // Take first 3 letters and convert to uppercase
//            $uniqueDigits = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT); // Generate 6-digit number with leading zeros
//            $generatedSku = $skuPrefix . $uniqueDigits;
//            request()->merge(['sku' => $generatedSku]);
//        }
//
//        $entity = $this->getModel()->create(
//            $this->getRequest('store')->all()
//        );
//
//        $this->searchable($entity);
//
//        $message = trans('admin::messages.resource_created', ['resource' => $this->getLabel()]);
//
//        if (request()->query('exit_flash')) {
//            session()->flash('exit_flash', $message);
//        }
//
//        if (request()->wantsJson()) {
//            return response()->json(
//                [
//                    'success' => true,
//                    'message' => $message,
//                    'product_id' => $entity->id,
//                ], 200
//            );
//        }
//
//        return redirect()->route("{$this->getRoutePrefix()}.index")
//            ->withSuccess($message);
//    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return Factory|View|Application
     */
    public function edit($id): Factory|View|Application
    {
        $entity = $this->getEntity($id);
        $productEditResource = new ProductEditResource($entity);

        return view("{$this->viewPath}.edit",
            [
                'product' => $entity,
                'product_resource' => $productEditResource->response()->content(),
            ]
        );
    }


    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     */
//    public function update($id)
//    {
//        $entity = $this->getEntity($id);
//
//        $this->disableSearchSyncing();
//
//        if (empty(request()->get('sku'))) {
//            // Generate SKU from product name (first 3 letters + 6 unique digits)
//            $productName = request()->get('name', 'PRD'); // Fallback to 'PRD' if name is missing
//            $skuPrefix = strtoupper(substr($productName, 0, 3)); // Take first 3 letters and convert to uppercase
//            $uniqueDigits = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT); // Generate 6-digit number with leading zeros
//            $generatedSku = $skuPrefix . $uniqueDigits;
//            request()->merge(['sku' => $generatedSku]);
//        }
//        $entity->update(
//            $this->getRequest('update')->all()
//        );
//
//        $entity->withoutEvents(function () use ($entity) {
//            $entity->touch();
//        });
//
//        $productEditResource = new ProductEditResource($entity);
//
//        $this->searchable($entity);
//
//        $message = trans('admin::messages.resource_updated', ['resource' => $this->getLabel()]);
//
//        if (request()->query('exit_flash')) {
//            session()->flash('exit_flash', $message);
//        }
//
//        if (request()->wantsJson()) {
//            return response()->json(
//                [
//                    'success' => true,
//                    'message' => $message,
//                    'product_resource' => $productEditResource,
//                ], 200
//            );
//        }
//    }

    public function store()
    {
        $this->disableSearchSyncing();

        if (empty(request()->get('sku'))) {
            $productName = request()->get('name', 'PRD');
            $skuPrefix = strtoupper(substr($productName, 0, 3));
            $uniqueDigits = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $generatedSku = $skuPrefix . $uniqueDigits;
            request()->merge(['sku' => $generatedSku]);
        }

        $entity = $this->getModel()->create(
            $this->getRequest('store')->all()
        );

        $this->searchable($entity);

        $message = trans('admin::messages.resource_created', ['resource' => $this->getLabel()]);
        if (request()->query('exit_flash')) {
            session()->flash('exit_flash', $message);
        }
        if (request()->wantsJson()) {
            return response()->json(
                ['success' => true, 'message' => $message, 'product_id' => $entity->id], 200
            );
        }
        return redirect()->route("{$this->getRoutePrefix()}.index")
            ->withSuccess($message);
    }

    public function update($id)
    {
        $this->disableSearchSyncing();

        $entity = $this->getModel()->findOrFail($id);

        $entity->update(
            $this->getRequest('update')->all()
        );

        $this->searchable($entity);

        $message = trans('admin::messages.resource_updated', ['resource' => $this->getLabel()]);
        if (request()->query('exit_flash')) {
            session()->flash('exit_flash', $message);
        }
        if (request()->wantsJson()) {
            return response()->json(
                ['success' => true, 'message' => $message], 200
            );
        }
        return redirect()->route("{$this->getRoutePrefix()}.index")
            ->withSuccess($message);
    }

}
