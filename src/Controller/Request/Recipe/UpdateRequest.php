<?php

namespace App\ExposeApi\Controller\Request\Recipe;

use App\ExposeApi\Controller\Request\AbstractRequest;
use Klein\Request;

/**
 * Filter the request before proceed with the request in controller.
 */
class UpdateRequest extends AbstractRequest
{
    public function __construct(Request $request)
    {
        $this->requestInstance = $request;
    }

    /**
     * Get the validation rules
     * these rules will be applied to request.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'id' => ['required'],
        ];
    }

    /**
     * Determine if the user is authorized or not
     * if false returned , user is not able to access to resource.
     *
     * @return bool
     */
    protected function authorize()
    {
        $headers = $this->getRequestInstance()->headers();

        return getAuth($headers);
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
            'id.required' => "Recipe's ID field is required",
        ];
    }
}
