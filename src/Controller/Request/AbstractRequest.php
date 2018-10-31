<?php

namespace App\ExposeApi\Controller\Request;

use App\ExposeApi\Core\Contracts\ValidateRequest;
use App\ExposeApi\Core\ValidateRequestTrait;
use Klein\Request;

/**
 * Form request validation abstraction.
 *
 * Class AbstractRequest
 */
abstract class AbstractRequest implements ValidateRequest
{
    use ValidateRequestTrait, SimplifyRequestBagTrait;
    /**
     * instance of request object.
     *
     * @var Request
     */
    protected $requestInstance;

    /**
     * hold all errors.
     *
     * @var array of errors
     */
    protected $errorBag = [];

    /**
     * return the object of Request Instance class.
     *
     * @return mixed
     */
    public function getRequestInstance()
    {
        return $this->requestInstance;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    abstract protected function rules();

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    abstract protected function authorize();

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    abstract public function messages();
}
