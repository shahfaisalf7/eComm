<?php

namespace Modules\GeoLocation\Http\Controllers;

use Barryvdh\Debugbar\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Admin\Ui\Facades\TabManager;
use Modules\DeliveryCharge\Entities\City;
use Modules\DeliveryCharge\Entities\Division;
use Modules\DeliveryCharge\Entities\Zone;

class GeoLocationController extends BaseController
{
    public function indexDivision()
    {
        return view('geolocation::division.index');
    }
    public function tableDivision(Request $request)
    {
        $request_data = $request->all();
        $page = $request->has('draw') ? $request->get('draw') : 1;
        $limit = $request->has('length') ? $request->get('length') : 10;
        $start = $request->has('start') ? $request->get('start') : 0;
        $search = $request->has('search') ? $request->get('search') : [];
        $m_division = new Division();
        return response()->json($m_division->getList($page, $limit, $start, $search, $request_data));
    }
    public function editDivision($id)
    {
        $division = Division::find($id);
        return view('geolocation::division.edit', compact('division'));
    }
    public function createDivision()
    {
        return view('geolocation::division.create');
    }
    public function storeDivision(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable',
        ]);
        if (isset($validatedData['status'])) {
            $validatedData['status'] = intval($validatedData['status']);
        } else {
            $validatedData['status'] = "0";
        }

        Division::create($validatedData);

        return redirect()->route('admin.geo.division.sidebar')
            ->with('success', 'Division created successfully');
    }
    public function updateDivision(Request $request, $id)
    {
        $request_data = $request->all();
        if (isset($request_data['status'])) {
            $request_data['status'] = intval($request_data['status']);
        } else {
            $request_data['status'] = "0";
        }
        $division = Division::find($id);

        if (!$division) {
            return response()->json(['message' => 'Division not found'], 404);
        }

        $division->update($request_data);

        return redirect()->route('admin.geo.division.sidebar')
            ->with('success', 'Division updated successfully');
    }
    public function destroyDivision(Request $request)
    {
        $ids = $request->input('ids');
        $ids = explode(',', $ids);
        $division = Division::whereIn('id', $ids);

        if (!$division) {
            return response()->json(['message' => 'Division not found'], 404);
        }

        $division->delete();

        return response()->json(['message' => 'Division deleted successfully'], 200);
    }
    public function indexCities()
    {
        return view('geolocation::city.index');
    }
    public function tableCities(Request $request)
    {
        $request_data = $request->all();
        $page = $request->has('draw') ? $request->get('draw') : 1;
        $limit = $request->has('length') ? $request->get('length') : 10;
        $start = $request->has('start') ? $request->get('start') : 0;
        $search = $request->has('search') ? $request->get('search') : [];
        $m_city = new City();
        return response()->json($m_city->getList($page, $limit, $start, $search, $request_data));
    }
    public function editCities($id)
    {
        $city = City::find($id);
        $divisions = Division::where('status', 1)->get();
        return view('geolocation::city.edit', compact('city', 'divisions'));
    }
    public function createCities()
    {
        $divisions = Division::where('status', 1)->get();
        return view('geolocation::city.create', compact('divisions'));
    }
    public function storeCities(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'division_id' => 'required',
            'status' => 'nullable',
        ]);
        if (isset($validatedData['status'])) {
            $validatedData['status'] = intval($validatedData['status']);
        } else {
            $validatedData['status'] = "0";
        }

        City::create($validatedData);

        return redirect()->route('admin.geo.cities.sidebar')
            ->with('success', 'City created successfully');
    }
    public function updateCities(Request $request, $id)
    {
        $request_data = $request->all();
        if (!isset($request_data['status'])) {
            $request_data['status'] = "0";
        }
        $city = City::find($id);

        if (!$city) {
            return response()->json(['message' => 'City not found'], 404);
        }

        $city->update($request_data);

        return redirect()->route('admin.geo.cities.sidebar')
            ->with('success', 'City updated successfully');
    }
    public function destroyCities(Request $request)
    {
        $ids = $request->input('ids');
        $ids = explode(',', $ids);
        $city = City::whereIn('id', $ids);

        if (!$city) {
            return response()->json(['message' => 'City not found'], 404);
        }

        $city->delete();

        return response()->json(['message' => 'City deleted successfully'], 200);
    }
    public function indexZones()
    {
        return view('geolocation::zone.index');
    }
    public function tableZones(Request $request)
    {
        $request_data = $request->all();
        $page = $request->has('draw') ? $request->get('draw') : 1;
        $limit = $request->has('length') ? $request->get('length') : 10;
        $start = $request->has('start') ? $request->get('start') : 0;
        $search = $request->has('search') ? $request->get('search') : [];
        $m_zone = new Zone();
        return response()->json($m_zone->getList($page, $limit, $start, $search, $request_data));
    }
    public function createZones()
    {
        $divisions = Division::where('status', 1)->get();
        return view('geolocation::zone.create', compact('divisions'));
    }
    public function citiesbyDivision(Request $request)
    {
        $city_id = $request->input('city_id');
        $division_id = $request->input('division_id');
        $cities = City::where('division_id', $division_id)->get();
        return view('geolocation::options.city', compact('cities', 'city_id'));
    }
    public function storeZones(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'city_id' => 'required',
            'status' => 'nullable',
        ]);
        if (isset($validatedData['status'])) {
            $validatedData['status'] = intval($validatedData['status']);
        } else {
            $validatedData['status'] = "0";
        }

        Zone::create($validatedData);

        return redirect()->route('admin.geo.zones.sidebar')
            ->with('success', 'Zone created successfully');
    }
    public function editZones($id)
    {
        $zone = Zone::find($id);
        $selected_division_id = City::find($zone->city_id)['division_id'];
        $divisions = Division::where('status', 1)->get();
        return view('geolocation::zone.edit', compact('zone', 'divisions', 'selected_division_id'));
    }
    public function updateZones(Request $request, $id)
    {
        $request_data = $request->all();
        if (!isset($request_data['status'])) {
            $request_data['status'] = "0";
        }
        $zone = Zone::find($id);

        if (!$zone) {
            return response()->json(['message' => 'Zone not found'], 404);
        }

        $zone->update($request_data);

        return redirect()->route('admin.geo.zones.sidebar')
            ->with('success', 'Zone updated successfully');
    }
    public function destroyZones(Request $request)
    {
        $ids = $request->input('ids');
        $ids = explode(',', $ids);
        $zone = Zone::whereIn('id', $ids);

        if (!$zone) {
            return response()->json(['message' => 'Zone not found'], 404);
        }

        $zone->delete();

        return response()->json(['message' => 'Zone deleted successfully'], 200);
    }

    public function getGeoDivision()
    {
        $division = Division::get()->toArray();
        return responseWithData(__("geolocation::messages.regions_data_list"), ['data' => $division]);
    }
    public function getGeoCities(Request $request)
    {
        $request->validate([
            'division_id' => 'required',
        ]);
        $division_id = $request->division_id;
        $cities = City::where('division_id', $division_id)->get()->toArray();
        return responseWithData(__("geolocation::messages.cities_data_list"), ['data' => $cities]);
    }
    public function getGeoZones(Request $request)
    {
        // $request->validate([
        //     'city_id' => 'required',
        // ]);
         $city_id = $request->city_id;
//         return $city_id;
//        $city_id = 1;
        $zones = Zone::where('city_id', $city_id)->get()->toArray();
        //dd($zones);
        return responseWithData(__("geolocation::messages.zones_data_list"), ['data' => $zones]);
    }
}
