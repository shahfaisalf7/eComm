<?php

namespace Modules\DeliveryCharge\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class DeliveryCharge extends Model
{
    protected $fillable = ['city_id', 'charge', 'status'];
    private function prepareSearchQuery($query, $request)
    {
        $search_keys = ['id', 'city_id', 'charge', 'status'];
        foreach ($search_keys as $key) {
            if (isset($request[$key]) && !empty($request[$key])) {
                $query->whereIn($key, is_array(($request[$key])) ? $request[$key] : [$request[$key]]);
            }
        }
        if (isset($request['search_string']) && strlen($request['search_string']) > 0) {
            $query->where('charge', 'like', "%" . $request['search_string'] . "%");
        }
        return $query;
    }
    // Define relationships if needed
    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function getList($page, $limit, $start, $search, $request_data)
    {
        $query = $this::select(['*']);
        $query = $this->prepareSearchQuery($query, ['search_string' => $search['value']]);
        $_total = $query->count();

        if ($limit != '-1') {
            $query->limit($limit)->offset($start);
        }

        $d_chareges = $query->get();
        $final_data = [];
        $i = 0;

        foreach ($d_chareges as $d_charege) {
            $index = (++$i) + $start;
            $row = [
                'checkbox' => '<div class="checkbox"> <input type="checkbox" class="select-row" value="' . $d_charege->id . '" id="' . $d_charege->id . '"> <label for="' . $d_charege->id . '"></label></div>',
                'sl' => $index,
                'id' => $d_charege->id,
                'city_id' => $d_charege->city_id,
                'city_name' => $d_charege->city->name,
                'division_id' => $d_charege->city->division->id,
                'division_name' => $d_charege->city->division->name,
                'charge' => $d_charege->charge,
                'status' => $d_charege->status == 1
                    ? '<span class="badge badge-light-success" style="color:green;">Active</span>'
                    : '<span class="badge badge-light-danger" style="color:red;">Inactive</span>',
                'created_at' => Carbon::parse($d_charege->created_at)->diffForHumans(),
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
