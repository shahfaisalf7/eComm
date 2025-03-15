<?php

namespace Modules\Account\Http\Requests;

use Modules\Core\Http\Requests\Request;

class SaveAddressRequest extends Request
{
    /**
     * Available attributes.
     *
     * @var string
     */
    protected $availableAttributes = 'account::attributes.addresses';


    protected function prepareForValidation()
    {
        $this->merge([
            'country' => $this->input('country', 'BD'), // Set default if null
        ]);
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'full_name' => ['required'],
            // 'first_name' => ['required'],
            // 'last_name' => ['required'],
            'address_1' => ['required'],
            'city' => ['required'],
            // 'zip' => ['required'],
            // 'zone_id' => ['required'],
            'zone' => ['required'],
            'country' => ['required'],
            'state' => ['required'],
        ];
    }
}
