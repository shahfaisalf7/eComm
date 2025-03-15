<?php

namespace Modules\Coupon\Exceptions;

use Exception;

class CouponNotApplicableForOfferPriceException extends Exception
{
    public function __construct($message = 'Coupon not applicable for offer price')
    {
        parent::__construct($message);
    }
}
