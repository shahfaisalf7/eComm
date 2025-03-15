<?php

namespace Modules\DeliveryCharge\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;


class BoxWeightCharge extends Model
{
    protected $fillable = ['id', 'weight', 'charge', 'status', 'crated_by', 'updated_by'];
    private function prepareSearchQuery($query, $request)
    {
        $search_keys = ['id', 'weight', 'charge', 'status'];
        foreach ($search_keys as $key) {
            if (isset($request[$key]) && !empty($request[$key])) {
                $query->whereIn($key, is_array(($request[$key])) ? $request[$key] : [$request[$key]]);
            }
        }
        if (isset($request['search_string']) && strlen($request['search_string']) > 0) {
            $query->where('charge', 'like', "%" . $request['search_string'] . "%")
                ->orWhere('weight', 'like', "%" . $request['search_string'] . "%");
        }
        return $query;
    }
    public function getList($page, $limit, $start, $search, $request_data)
    {
        $query = $this::select(['*']);
        $query = $this->prepareSearchQuery($query, ['search_string' => $search['value']]);
        $_total = $query->count();

        if ($limit != '-1') {
            $query->limit($limit)->offset($start);
        }

        $bw_chareges = $query->get();
        $final_data = [];
        $i = 0;

        foreach ($bw_chareges as $bw_charege) {
            $index = (++$i) + $start;
            $row = [
                'checkbox' => '<div class="checkbox"> <input type="checkbox" class="select-row" value="' . $bw_charege->id . '" id="' . $bw_charege->id . '"> <label for="' . $bw_charege->id . '"></label></div>',
                'sl' => $index,
                'id' => $bw_charege->id,
                'weight' => $bw_charege->weight,
                'charge' => $bw_charege->charge,
                'status' => $bw_charege->status == 1
                    ? '<span class="badge badge-light-success" style="color:green;">Active</span>'
                    : '<span class="badge badge-light-danger" style="color:red;">Inactive</span>',
                'created_at' => Carbon::parse($bw_charege->created_at)->diffForHumans(),
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
