<?php

namespace Modules\DeliveryCharge\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = ['name', 'division_id', 'status'];
    private function prepareSearchQuery($query, $request)
    {
        $search_keys = ['id', 'name', 'division_id', 'status'];
        foreach ($search_keys as $key) {
            if (isset($request[$key]) && !empty($request[$key])) {
                $query->whereIn($key, is_array(($request[$key])) ? $request[$key] : [$request[$key]]);
            }
        }
        if (isset($request['search_string']) && strlen($request['search_string']) > 0) {
            $query->where('name', 'like', "%" . $request['search_string'] . "%");
            $query->orWhereHas('division', function ($q) use ($request) {
                $q->where('name', 'like', "%" . $request['search_string'] . "%");
            });
        }
        return $query;
    }
    // Define relationships if needed
    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function zones()
    {
        return $this->hasMany(Zone::class);
    }

    public function delivery_charge()
    {
        return $this->hasOne(DeliveryCharge::class, 'city_id');
    }

    public function getCities($divisionId)
    {
        return City::where('division_id', $divisionId)->get();
    }

    public function getList($page, $limit, $start, $search, $request_data)
    {
        $query = $this::select(['*']);
        $query = $this->prepareSearchQuery($query, ['search_string' => $search['value']]);
        $_total = $query->count();

        if ($limit != '-1') {
            $query->limit($limit)->offset($start);
        }

        $cities = $query->get();
        $final_data = [];
        $i = 0;

        foreach ($cities as $city) {
            $index = (++$i) + $start;
            $row = [
                'checkbox' => '<div class="checkbox"> <input type="checkbox" class="select-row" value="' . $city->id . '" id="' . $city->id . '"> <label for="' . $city->id . '"></label></div>',
                'sl' => $index,
                'id' => $city->id,
                'name' => $city->name,
                'division_name' => $city->division->name,
                'status' => $city->status == 1
                    ? '<span class="badge badge-light-success" style="color:green;">Active</span>'
                    : '<span class="badge badge-light-danger" style="color:red;">Inactive</span>',
                'created_at' => Carbon::parse($city->created_at)->diffForHumans(),
                'action' => '
                    <button type="button" class="btn btn-sm btn-danger delete-city" data-id="' . $city->id . '">
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
