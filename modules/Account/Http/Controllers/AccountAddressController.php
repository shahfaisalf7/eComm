<?php

namespace Modules\Account\Http\Controllers;

use Modules\Support\Country;
use Illuminate\Routing\Controller;
use Modules\Account\Entities\Address;
use Modules\Account\Entities\DefaultAddress;
use Modules\Account\Http\Requests\SaveAddressRequest;
use Modules\DeliveryCharge\Entities\City;
use Modules\DeliveryCharge\Entities\Division;
use Modules\DeliveryCharge\Entities\Zone;

class AccountAddressController extends Controller
{
    public function index()
    {
        return view('storefront::public.account.addresses.index', [
            'addresses' => auth()->user()->addresses->keyBy('id'),
            'defaultAddress' => auth()->user()->defaultAddress,
            'countries' => Country::supported(),
        ]);
    }

//    public function apiIndex()
//    {
//        $user = auth('api')->user(); // Use 'api' guard
//        if (!$user) {
//            return response()->json([
//                'status' => 'error',
//                'message' => trans('Unauthenticated'),
//                'code' => 401
//            ], 401);
//        }
//        $address = $user->addresses->keyBy('id');
//        $default_address = $user->defaultAddress;
//        $countries = Country::supported();
//        $final_data = ['address' => $address->toArray(), 'defaultAddress' => $default_address ? $default_address->toArray() : null, 'countries' => $countries];
//        return responseWithData(trans('account::messages.address_data'), $final_data);
//    }

    private function prepareAddressData($request_data)
    {
        $m_zone = new Zone();
        $zone = $m_zone->getZone($request_data['zone']);

        $data['id'] = $request_data['id'] ?? null;
        // $data['first_name'] = $request_data['first_name'] ?? auth()->user()->first_name;
        // $data['last_name'] = $request_data['last_name'] ?? auth()->user()->last_name;
        $data['name'] = $request_data['full_name'] ?? auth()->user()->full_name;
        $data['address_1'] = $request_data['address_1'] ?? '';
        $data['address_2'] = $request_data['address_2'] ?? '';
        $data['country'] = $request_data['country'] ?? "BD";
        $data['zone_id'] = $request_data['zone'];
        $data['zone'] = $zone->name;
        $data['city'] = $zone->city->name;
        $data['state'] = $zone->city->division->name;
        $data['zip'] = '';
        return $data;
    }

    public function store(SaveAddressRequest $request)
    {
        $request_data = $request->all();
        $saving_data = $this->prepareAddressData($request_data);
        $address = auth()->user()->addresses()->create($saving_data);
        if (auth()->user()->addresses()->count() == 1) {
            request()->merge(['address_id' => $address->id]);
            $this->changeDefault();
        }

        return response()->json([
            'address' => $address,
            'message' => trans('account::messages.address_created'),
        ]);
    }

//    public function storeApi(SaveAddressRequest $request)
//    {
//        $user = auth('api')->user(); // Use 'api' guard
//        if (!$user) {
//            return response()->json([
//                'status' => 'error',
//                'message' => trans('Unauthenticated'),
//                'code' => 401
//            ], 401);
//        }
//        $request_data = $request->all();
//        $saving_data = $this->prepareAddressData($request_data);
//        $address = $user->addresses()->create($saving_data);
//        $data = ['address' => $address];
//        return responseWithData(trans('account::messages.address_created'), $data);
//    }

    public function update(SaveAddressRequest $request, $id)
    {
        $address = Address::find($id);
        $request_data = $request->all();
        $request_data['id'] = $address->id;
        $saving_data = $this->prepareAddressData($request_data);
        $address->update($saving_data);

        return response()->json([
            'address' => $address,
            'message' => trans('account::messages.address_updated'),
        ]);
    }

//    public function updateApi(SaveAddressRequest $request, $id)
//    {
//        $address = Address::find($id);
//        if (empty($address)) {
//            return responseNotFound(trans('account::messages.address_data_not_found'));
//        }
//        $request_data = $request->all();
//        $request_data['id'] = $id;
//        $saving_data = $this->prepareAddressData($request_data);
//        $saving_data['updated_at'] = now();
//        $address->update($saving_data);
//        $data = ['address' => $address];
//        return responseWithData(trans('account::messages.address_updated'), $data);
//    }

    public function destroy($id)
    {
        auth()->user()->addresses()->find($id)->delete();
        return response()->json([
            'message' => trans('account::messages.address_deleted'),
        ]);
    }

//    public function destroyApi($id)
//    {
//        $exists_address = auth()->user()->addresses()->find($id);
//        if (empty($exists_address)) {
//            return responseNotFound(trans('account::messages.address_data_not_found'));
//        } else {
//            $exists_address->delete();
//            return responseSuccess(trans('account::messages.address_deleted'));
//        }
//    }


    public function changeDefault()
    {
        DefaultAddress::updateOrCreate(
            ['customer_id' => auth()->id()],
            ['address_id' => request('address_id')]
        );

        return trans('account::messages.default_address_updated');
    }

//    public function changeDefaultApi()
//    {
//        $exists_address = auth()->user()->addresses()->find(request('address_id'));
//        if (empty($exists_address)) {
//            return responseNotFound(trans('account::messages.address_data_not_found'));
//        } else {
//            DefaultAddress::updateOrCreate(
//                ['customer_id' => auth()->id()],
//                ['address_id' => request('address_id')]
//            );
//            return responseSuccess(trans('account::messages.default_address_updated'));
//        }
//    }
    public function apiIndex()
    {
        $user = auth('api')->user();
        if (!$user) return response()->json(['status' => 'error', 'message' => trans('Unauthenticated'), 'code' => 401], 401);
        $address = $user->addresses->keyBy('id');
        $default_address = $user->defaultAddress;
        $countries = Country::supported();
        $final_data = ['address' => $address->toArray(), 'defaultAddress' => $default_address ? $default_address->toArray() : null, 'countries' => $countries];
        return responseWithData(trans('account::messages.address_data'), $final_data);
    }

    public function storeApi(SaveAddressRequest $request)
    {
        $user = auth('api')->user();
        if (!$user) return response()->json(['status' => 'error', 'message' => trans('Unauthenticated'), 'code' => 401], 401);
        $request_data = $request->all();
        $saving_data = $this->prepareAddressData($request_data);
        $address = $user->addresses()->create($saving_data);
        $data = ['address' => $address];
        return responseWithData(trans('account::messages.address_created'), $data);
    }

    public function updateApi(SaveAddressRequest $request, $id)
    {
        $user = auth('api')->user();
        if (!$user) return response()->json(['status' => 'error', 'message' => trans('Unauthenticated'), 'code' => 401], 401);
        $address = Address::find($id);
        if (empty($address)) return responseNotFound(trans('account::messages.address_data_not_found'));
        $request_data = $request->all();
        $request_data['id'] = $id;
        $saving_data = $this->prepareAddressData($request_data);
        $saving_data['updated_at'] = now();
        $address->update($saving_data);
        $data = ['address' => $address];
        return responseWithData(trans('account::messages.address_updated'), $data);
    }

    public function destroyApi($id)
    {
        $user = auth('api')->user();
        if (!$user) return response()->json(['status' => 'error', 'message' => trans('Unauthenticated'), 'code' => 401], 401);
        $exists_address = $user->addresses()->find($id);
        if (empty($exists_address)) return responseNotFound(trans('account::messages.address_data_not_found'));
        $exists_address->delete();
        return responseSuccess(trans('account::messages.address_deleted'));
    }

    public function changeDefaultApi()
    {
        $user = auth('api')->user();
        if (!$user) return response()->json(['status' => 'error', 'message' => trans('Unauthenticated'), 'code' => 401], 401);
        $exists_address = $user->addresses()->find(request('address_id'));
        if (empty($exists_address)) return responseNotFound(trans('account::messages.address_data_not_found'));
        DefaultAddress::updateOrCreate(
            ['customer_id' => $user->id],
            ['address_id' => request('address_id')]
        );
        return responseSuccess(trans('account::messages.default_address_updated'));
    }
}
