<?php

namespace Modules\DeliveryCharge\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $fillable = ['name', 'city_id', 'status'];
    private function prepareSearchQuery($query, $request)
    {
        $search_keys = ['id', 'name', 'city_id', 'status'];
        foreach ($search_keys as $key) {
            if (isset($request[$key]) && !empty($request[$key])) {
                $query->whereIn($key, is_array(($request[$key])) ? $request[$key] : [$request[$key]]);
            }
        }
        if (isset($request['search_string']) && strlen($request['search_string']) > 0) {
            $query->where('name', 'like', "%" . $request['search_string'] . "%");
            $query->orWhere(function ($q) use ($request) {
                $q->whereHas('city', function ($q) use ($request) {
                    $q->where('name', 'like', "%" . $request['search_string'] . "%");
                });
                $q->orWhereHas('city.division', function ($q) use ($request) {
                    $q->where('name', 'like', "%" . $request['search_string'] . "%");
                });
            });
        }
        return $query;
    }
    // Define relationships if needed
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function deliveryCharges()
    {
        return $this->hasMany(DeliveryCharge::class);
    }

    public function getCityZones($cityId)
    {
        return Zone::where('city_id', $cityId)->get();
    }

    public function getZone($zoneId)
    {
        return Zone::where('id', $zoneId)->first();
    }

    public function getList($page, $limit, $start, $search, $request_data)
    {
        $query = $this::select(['*']);
        $query = $this->prepareSearchQuery($query, ['search_string' => $search['value']]);
        $_total = $query->count();

        if ($limit != '-1') {
            $query->limit($limit)->offset($start);
        }

        $zones = $query->get();
        $final_data = [];
        $i = 0;

        foreach ($zones as $zone) {
            $index = (++$i) + $start;
            $row = [
                'checkbox' => '<div class="checkbox"> <input type="checkbox" class="select-row" value="' . $zone->id . '" id="' . $zone->id . '"> <label for="' . $zone->id . '"></label></div>',
                'sl' => $index,
                'id' => $zone->id,
                'name' => $zone->name,
                'city_name' => $zone->city->name,
                'division_name' => $zone->city->division->name,
                'status' => $zone->status == 1
                    ? '<span class="badge badge-light-success" style="color:green;">Active</span>'
                    : '<span class="badge badge-light-danger" style="color:red;">Inactive</span>',
                'created_at' => Carbon::parse($zone->created_at)->diffForHumans(),
                'action' => '
                    <button type="button" class="btn btn-sm btn-danger delete-zone" data-id="' . $zone->id . '">
                        Delete
                    </button>
                ',
            ];
            $final_data[] = $row;
        }

        return [
            'data' => $final_data,
            'disableOrdering' => false,
            'draw' => intval($page),
            'input' => $request_data,
            'queries' => $query->toSql(),
            'recordsTotal' => $_total,
            'recordsFiltered' => $_total,
        ];
    }
}
