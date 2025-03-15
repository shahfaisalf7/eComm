<?php

namespace Modules\DeliveryCharge\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProductWeightCharge extends Model
{
    protected $fillable = ['weight', 'charge', 'status', 'created_by', 'updated_by'];
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
    public static function getWeightCharge($weight)
    {

        if ($weight > 0) {
            // Find the closest weight from the `product_weight_charges` table
            $productWeight = DB::table('product_weight_charges')
                ->where('weight', '>=', $weight)
                ->orderBy('weight', 'asc')
                ->first();

            if ($productWeight) {
                return $productWeight->charge;
            }

            // If no matching weight is found, get the charge for the largest weight
            $maxWeight = DB::table('product_weight_charges')
                ->orderBy('weight', 'desc')
                ->first();

            if ($maxWeight) {
                return $maxWeight->charge;
            }
        }

        // Return 0 if no weight match is found or weight is invalid
        return 0;
    }
    public function getList($page, $limit, $start, $search, $request_data)
    {
        $query = $this::select(['*']);
        $query = $this->prepareSearchQuery($query, ['search_string' => $search['value']]);
        $_total = $query->count();

        if ($limit != '-1') {
            $query->limit($limit)->offset($start);
        }

        $pw_chareges = $query->get();
        $final_data = [];
        $i = 0;

        foreach ($pw_chareges as $pw_charege) {
            $index = (++$i) + $start;
            $row = [
                'checkbox' => '<div class="checkbox"> <input type="checkbox" class="select-row" value="' . $pw_charege->id . '" id="' . $pw_charege->id . '"> <label for="' . $pw_charege->id . '"></label></div>',
                'sl' => $index,
                'id' => $pw_charege->id,
                'weight' => $pw_charege->weight,
                'charge' => $pw_charege->charge,
                'status' => $pw_charege->status == 1
                    ? '<span class="badge badge-light-success" style="color:green;">Active</span>'
                    : '<span class="badge badge-light-danger" style="color:red;">Inactive</span>',
                'created_at' => Carbon::parse($pw_charege->created_at)->diffForHumans(),
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
