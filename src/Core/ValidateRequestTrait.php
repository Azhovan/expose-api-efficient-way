<?php

namespace App\ExposeApi\Core;

use App\ExposeApi\Controller\Request\Recipe\Criterion;

trait ValidateRequestTrait
{

    use Criterion;

    /**
     * Determine if the request passes the access check.
     *
     * @return bool
     */
    protected function isAuthorized()
    {
        if (method_exists($this, 'authorize')) {
            return $this->authorize();
        }

        return true;
    }


    /**
     * Validate the Request class.
     *
     * @return ValidateRequestTrait
     */
    public function validate()
    {

        if (!$this->isAuthorized()) {
            throw new \RuntimeException("Access denied", 401);
        }

        $rules = $this->rules();
        foreach ($rules as $attribute => $criteria) {
            $this->checkRule($attribute, $criteria);
        }

        return $this;
    }

    /**
     * Dynamically get the validation functions
     *
     * @param $attribute
     * @param $criteria
     */
    public function checkRule($attribute, $criteria)
    {

        foreach ($criteria as $criterion) {

            if (!is_array($criterion)) {
                $this->{$criterion}($attribute);

            } else if (is_array($criterion)) {
                $method = key($criterion);
                $args = current($criterion);
                array_push($args, $attribute);
                $this->$method(...$args);
            }
        }
    }

    /**
     * @return bool
     */
    public function failed()
    {
        if (count($this->errorBag) > 0) {
            return true;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function errors()
    {
        return $this->errorBag;
    }


}