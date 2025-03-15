<?php

namespace Modules\DeliveryCharge\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProductBoxWeightCharge extends Model
{
    protected $fillable = ['weight', 'charge', 'status'];

    public static function getBoxWeightCharge($weight)
    {

        if ($weight > 0) {
            // Find the closest weight from the `product_weight_charges` table
            $productBoxWeight = DB::table('box_weight_charges')
                ->where('weight', '>=', $weight)
                ->orderBy('weight', 'asc')
                ->first();

            if ($productBoxWeight) {
                return $productBoxWeight->charge;
            }

            // If no matching weight is found, get the charge for the largest weight
            $maxWeight = DB::table('box_weight_charges')
                ->orderBy('weight', 'desc')
                ->first();

            if ($maxWeight) {
                return $maxWeight->charge;
            }
        }

        // Return 0 if no weight match is found or weight is invalid
        return 0;
    }
}
