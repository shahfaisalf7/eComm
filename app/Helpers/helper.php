<?php

use Illuminate\Support\Facades\Config;
use Modules\Shipping\Facades\ShippingMethod;
use Illuminate\Http\Response as HttpResponse;

if (!function_exists('isAPI')) {
    function isAPI()
    {
        return request()->is('api/*') || request()->is('*/api/*');
    }
}
function apiResponse($data, $message = '', $status = 200)
{
    return response()->json([
        'status' => $status,
        'message' => $message,
        'data' => $data,
    ], $status);
}
function getAuthMethod()
{
    return Config::get('auth_method.auth_type');
}
if (!function_exists('isFloraMom')) {
    function isFloraMom($app_module)
    {
        return (strtolower($app_module) == 'flora_mom');
    }
}
if (!function_exists('isStoreFront')) {
    function isStoreFront($app_module)
    {
        return (strtolower($app_module) == 'store_front');
    }
}
if (!function_exists('getShippingCharge')) {
    function getShippingCharge()
    {
        $request_data = request()->all();
        if (!empty($request_data['shipping'])) {
            $city_id = $request_data['shipping']['city'] ?? 0;
            $zone_id = $request_data['shipping']['zone_id'] ?? 0;
        } else {
            $city_id = $request_data['billing']['city'] ?? 0;
            $zone_id = $request_data['billing']['zone_id'] ?? 0;
        }
        $shipping_charge = 0;
        $shipping_method_facade = ShippingMethod::available();
        if (isset($shipping_method_facade['flat_rate'])) {
            $shipping_charge = $shipping_method_facade['flat_rate']->cost->amount();
        }
        if ($zone_id > 0) {
            $zone = \Modules\DeliveryCharge\Entities\Zone::find($zone_id);
            $delvery_charge = $zone->city->delivery_charge;
            if (!empty($delvery_charge)) {
                $shipping_charge = $delvery_charge->charge;
            }
        } else {
            if ($city_id > 0) {
                $city = \Modules\DeliveryCharge\Entities\City::find($city_id);
                if ($city) {
                    $delvery_charge = $city->delivery_charge;
                    if (!empty($delvery_charge)) {
                        $shipping_charge = $delvery_charge->charge;
                    }
                }
            }
        }
        request()->merge(['delivery_charge' => $shipping_charge]);
        return $shipping_charge;
    }
}

if (! function_exists('csrf_token')) {
    /**
     * Get the CSRF token value.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    function csrf_token()
    {
        $session = app('session');

        if (isset($session)) {
            return $session->token();
        }

        throw new RuntimeException('Application session store not set.');
    }
}

// api response
function responseWithData($message, $data = [])
{
    return response()->json([
        'status' => 'success',
        'message' => $message,
        'data' => $data
    ], HttpResponse::HTTP_OK);
}

function responseWithFailed($message, $details = [])
{
    return response()->json([
        'status' => 'error',
        'message' => $message,
        'details' => $details
    ], HttpResponse::HTTP_EXPECTATION_FAILED);
}

function responseWithError($message, $details = [])
{
    return response()->json([
        'status' => 'error',
        'message' => $message,
        'details' => $details
    ], HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
}
function respondUnauthorized($message)
{
    return response()->json([
        'status' => 'error',
        'message' => $message,
    ], HttpResponse::HTTP_UNAUTHORIZED);
}
function responseNotFound($message = 'Data Not found!')
{
    return response()->json([
        'status' => 'error',
        'message' => $message,
    ], HttpResponse::HTTP_NOT_FOUND);
}
function responseSuccess($message)
{
    return response()->json([
        'status' => 'success',
        'message' => $message,
    ], HttpResponse::HTTP_OK);
}
function responseInvalidRequest($message = 'Sorry! Required field is missing')
{
    return response()->json([
        'status' => 'error',
        'message' => $message,
    ], HttpResponse::HTTP_FORBIDDEN);
}
