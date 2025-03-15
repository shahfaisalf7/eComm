<?php

namespace Modules\Support\Http\Controllers;

use Modules\Support\State;
use Illuminate\Http\Response;
use Modules\DeliveryCharge\Entities\City;
use Modules\DeliveryCharge\Entities\Division;
use Modules\DeliveryCharge\Entities\Zone;

class CountryStateController
{
    /**
     * Display a listing of the resource.
     *
     * @param string $countryCode
     *
     * @return Response
     */
    public function index($countryCode)
    {
        $model_division = new Division();
        $response_divisions = $model_division->getDivisions()->pluck('name', 'id');
        $divisions = [];
        if (!empty($response_divisions)) {
            $divisions = $response_divisions->toArray();
        }
        return response()->json($divisions);

        // $states = State::get($countryCode);

        // return response()->json($states);
    }
    public function getStateCities($divisionId)
    {
        $model_city = new City();
        $response_cities = $model_city->getCities($divisionId)->pluck('name', 'id');
        $cities = [];
        if (!empty($response_cities)) {
            $cities = $response_cities->toArray();
        }
        return response()->json($cities);
    }
    public function getCitiesZones($cityId)
    {
        $model_zone = new Zone();
        $response_zones = $model_zone->getCityZones($cityId)->pluck('name', 'id');
        $zones = [];
        if (!empty($response_zones)) {
            $zones = $response_zones->toArray();
        }
        return response()->json($zones);
    }
}
