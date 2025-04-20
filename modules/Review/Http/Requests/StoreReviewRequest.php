<?php

namespace Modules\Review\Http\Requests;

use Modules\Core\Http\Requests\Request;
use Modules\Support\Rules\GoogleRecaptcha;

class StoreReviewRequest extends Request
{
    /**
     * Available attributes.
     *
     * @var string
     */
    protected $availableAttributes = 'review::attributes';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'rating' => 'required|numeric',
            'reviewer_name' => 'required',
            'comment' => 'required',
        ];

        // Apply reCAPTCHA only for non-API requests
        if (!$this->is('api/*')) {
            $rules['g-recaptcha-response'] = ['bail', 'sometimes', 'required', new GoogleRecaptcha()];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return array_merge(parent::messages(), [
            'g-recaptcha-response.required' => trans('support::recaptcha.validation.failed_to_verify'),
        ]);
    }
}
