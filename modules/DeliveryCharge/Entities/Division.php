<?php

namespace Modules\DeliveryCharge\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $fillable = ['name', 'description', 'status'];
    private function prepareSearchQuery($query, $request)
    {
        $search_keys = ['id', 'name', 'description', 'status'];
        foreach ($search_keys as $key) {
            if (isset($request[$key]) && !empty($request[$key])) {
                $query->whereIn($key, is_array(($request[$key])) ? $request[$key] : [$request[$key]]);
            }
        }
        if (isset($request['search_string']) && strlen($request['search_string']) > 0) {
            $query->where('name', 'like', "%" . $request['search_string'] . "%");
        }
        return $query;
    }

    // Define relationships if needed
    public function cities()
    {
        return $this->hasMany(City::class);
    }

    public function getDivisions()
    {
        return Division::get();
    }

    public function getList($page, $limit, $start, $search, $request_data)
    {
        $query = $this::select(['*']);
        $query = $this->prepareSearchQuery($query, ['search_string' => $search['value']]);
        $_total = $query->count();

        if ($limit != '-1') {
            $query->limit($limit)->offset($start);
        }

        $divisions = $query->get();
        $final_data = [];
        $i = 0;

        foreach ($divisions as $division) {
            $index = (++$i) + $start;
            $row = [
                'checkbox' => '<div class="checkbox"> <input type="checkbox" class="select-row" value="' . $division->id . '" id="' . $division->id . '"> <label for="' . $division->id . '"></label></div>',
                'sl' => $index,
                'id' => $division->id,
                'name' => $division->name,
                'description' => $division->description,
                'status' => $division->status == 1
                    ? '<span class="badge badge-light-success" style="color:green;">Active</span>'
                    : '<span class="badge badge-light-danger" style="color:red;">Inactive</span>',
                'created_at' => Carbon::parse($division->created_at)->diffForHumans(),
                'action' => '
                    <button type="button" class="btn btn-sm btn-danger delete-division" data-id="' . $division->id . '">
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
