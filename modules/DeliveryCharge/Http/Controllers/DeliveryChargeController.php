<?php

namespace Modules\DeliveryCharge\Http\Controllers;

use Barryvdh\Debugbar\Controllers\BaseController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\DeliveryCharge\Entities\BoxWeightCharge;
use Modules\DeliveryCharge\Entities\City;
use Modules\DeliveryCharge\Entities\DeliveryCharge;
use Modules\DeliveryCharge\Entities\ProductWeightCharge;

class DeliveryChargeController extends BaseController
{
    ########## delivery ##########
    public function indexDelivery()
    {
        return view('deliverycharge::delivery.index');
    }
    public function createDelivery()
    {
        $cities = City::where('status', 1)->get();
        return view('deliverycharge::delivery.create', compact('cities'));
    }
    public function storeDelivery(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'charge' => 'required|integer',
            'city_id' => 'required|integer',
            'status' => 'nullable',
        ]);
        if (isset($validatedData['status'])) {
            $validatedData['status'] = intval($validatedData['status']);
        } else {
            $validatedData['status'] = "0";
        }

        DeliveryCharge::create($validatedData);

        return redirect()->route('admin.delivery.charge')
            ->with('success', 'Delivery charge created successfully');
    }
    public function showDelivery(Request $request)
    {
        $request_data = $request->all();
        $page = $request->has('draw') ? $request->get('draw') : 1;
        $limit = $request->has('length') ? $request->get('length') : 10;
        $start = $request->has('start') ? $request->get('start') : 0;
        $search = $request->has('search') ? $request->get('search') : [];
        $m_dc = new DeliveryCharge();
        return response()->json($m_dc->getList($page, $limit, $start, $search, $request_data));
    }
    public function editDelivery($id)
    {
        $delivery_charge = DeliveryCharge::find($id);
        $cities = City::where('status', 1)->get();
        return view('deliverycharge::delivery.edit', compact('cities', 'delivery_charge'));
    }
    public function updateDelivery(Request $request, $id): RedirectResponse
    {
        $request_data = $request->all();
        if (!isset($request_data['status'])) {
            $request_data['status'] = "0";
        }
        $delivery_charge = DeliveryCharge::find($id);

        if (!$delivery_charge) {
            return response()->json(['message' => 'Charge not found'], 404);
        }

        $delivery_charge->update($request_data);

        return redirect()->route('admin.delivery.charge')
            ->with('success', 'Delivery charge updated successfully');
    }
    public function destroyDelivery(Request $request)
    {
        $ids = $request->input('ids');
        $ids = explode(',', $ids);
        $d_charge = DeliveryCharge::whereIn('id', $ids);

        if (!$d_charge) {
            return response()->json(['message' => 'Delivery charge not found'], 404);
        }

        $d_charge->delete();

        return response()->json(['message' => 'Delivery charge deleted successfully'], 200);
    }
    ########## product ##########
    public function indexProduct()
    {
        return view('deliverycharge::product.index');
    }
    public function createProduct()
    {
        return view('deliverycharge::product.create');
    }
    public function storeProduct(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'charge' => 'required|integer',
            'weight' => 'required|integer',
            'status' => 'nullable',
        ]);
        if (isset($validatedData['status'])) {
            $validatedData['status'] = intval($validatedData['status']);
        } else {
            $validatedData['status'] = "0";
        }
        $validatedData['created_by'] = auth()->user()->id;
        $validatedData['updated_by'] = auth()->user()->id;
        ProductWeightCharge::create($validatedData);

        return redirect()->route('admin.product.charge')
            ->with('success', 'Product charge created successfully');
    }
    public function showProduct(Request $request)
    {
        $request_data = $request->all();
        $page = $request->has('draw') ? $request->get('draw') : 1;
        $limit = $request->has('length') ? $request->get('length') : 10;
        $start = $request->has('start') ? $request->get('start') : 0;
        $search = $request->has('search') ? $request->get('search') : [];
        $m_pwc = new ProductWeightCharge();
        return response()->json($m_pwc->getList($page, $limit, $start, $search, $request_data));
    }
    public function editProduct($id)
    {
        $product_charge = ProductWeightCharge::find($id);
        return view('deliverycharge::product.edit', compact('product_charge'));
    }
    public function updateProduct(Request $request, $id): RedirectResponse
    {
        $request_data = $request->all();
        if (!isset($request_data['status'])) {
            $request_data['status'] = "0";
        }
        $request_data['created_by'] = auth()->user()->id;
        $product_charge = ProductWeightCharge::find($id);

        if (!$product_charge) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product_charge->update($request_data);

        return redirect()->route('admin.product.charge')
            ->with('success', 'Product charge updated successfully');
    }
    public function destroyProduct(Request $request)
    {
        $ids = $request->input('ids');
        $ids = explode(',', $ids);
        $p_charge = ProductWeightCharge::whereIn('id', $ids);

        if (!$p_charge) {
            return response()->json(['message' => 'Product charge not found'], 404);
        }

        $p_charge->delete();

        return response()->json(['message' => 'Product charge deleted successfully'], 200);
    }

    ########## box ##########
    public function indexBox()
    {
        return view('deliverycharge::box.index');
    }
    public function createBox()
    {
        return view('deliverycharge::box.create');
    }
    public function storeBox(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'charge' => 'required|integer',
            'weight' => 'required|integer',
            'status' => 'nullable',
        ]);
        if (isset($validatedData['status'])) {
            $validatedData['status'] = intval($validatedData['status']);
        } else {
            $validatedData['status'] = "0";
        }
        $validatedData['created_by'] = auth()->user()->id;
        $validatedData['updated_by'] = auth()->user()->id;
        BoxWeightCharge::create($validatedData);

        return redirect()->route('admin.box.charge')
            ->with('success', 'Box charge created successfully');
    }
    public function showBox(Request $request)
    {
        $request_data = $request->all();
        $page = $request->has('draw') ? $request->get('draw') : 1;
        $limit = $request->has('length') ? $request->get('length') : 10;
        $start = $request->has('start') ? $request->get('start') : 0;
        $search = $request->has('search') ? $request->get('search') : [];
        $m_bwc = new BoxWeightCharge();
        return response()->json($m_bwc->getList($page, $limit, $start, $search, $request_data));
    }
    public function editBox($id)
    {
        $box_charge = BoxWeightCharge::find($id);
        return view('deliverycharge::box.edit', compact('box_charge'));
    }
    public function updateBox(Request $request, $id): RedirectResponse
    {
        $request_data = $request->all();
        if (!isset($request_data['status'])) {
            $request_data['status'] = "0";
        }
        $request_data['created_by'] = auth()->user()->id;
        $box_charge = BoxWeightCharge::find($id);

        if (!$box_charge) {
            return response()->json(['message' => 'Box not found'], 404);
        }

        $box_charge->update($request_data);

        return redirect()->route('admin.box.charge')
            ->with('success', 'Box charge updated successfully');
    }
    public function destroyBox(Request $request)
    {
        $ids = $request->input('ids');
        $ids = explode(',', $ids);
        $b_charge = BoxWeightCharge::whereIn('id', $ids);

        if (!$b_charge) {
            return response()->json(['message' => 'Box charge not found'], 404);
        }

        $b_charge->delete();

        return response()->json(['message' => 'Box charge deleted successfully'], 200);
    }
}
