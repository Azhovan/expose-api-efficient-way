<?php

namespace App\ExposeApi\Controller\Request\Recipe;

use App\ExposeApi\Controller\Request\AbstractRequest;
use Klein\Request;

/**
 * Filter the request before proceed with the request in controller
 *
 * @package App\ExposeApi\Controller\Request
 */
class RateRequest extends AbstractRequest
{

    public function __construct(Request $request)
    {
        $this->requestInstance = $request;
    }


    /**
     * Get the validation rules
     * these rules will be applied to request
     *
     * @return array
     */
    protected function rules()
    {
        return [
            "id" => ["required"],
            "rate" => ["required", ["range" => [1, 5]]]
        ];
    }

    /**
     * Determine if the user is authorized or not
     * if false returned , user is not able to access to resource
     *
     * @return bool
     */
    protected function authorize()
    {
        return true;
    }

    /**
     * Get the error messages for
     *   the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            "id.required" => "Recipe id does not exist",
            "rate.required" => "rate value is required",
            "rate.range" => "wrong rate range. it should be between 1-5"
        ];
    }


}