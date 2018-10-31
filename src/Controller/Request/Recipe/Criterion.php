<?php

namespace App\ExposeApi\Controller\Request\Recipe;

trait Criterion
{
    /**
     * check validation rule, and fill errorBag.
     *
     * @param $attribute
     */
    protected function required($attribute)
    {
        if (!$this->inBody($attribute) && !$this->inParams($attribute)) {
            $this->errorBag[] = $this->messages()["{$attribute}.required"];
        }
    }

    /**
     * check validation rule, and fill errorBag.
     *
     * @param $min
     * @param $max
     * @param $attribute
     */
    protected function range($min, $max, $attribute)
    {
        $value = $this->getAttributeValue($attribute);
        if ($value > $max || $value < $min) {
            $this->errorBag[] = $this->messages()["{$attribute}.range"];
        }
    }

    /**
     * Determine if the attribute exists in body of the request.
     *
     * @param  $attribute
     *
     * @return bool
     */
    private function inBody($attribute): bool
    {
        return $this->returnFromBody($attribute);
    }

    /**
     * Determine if the attribute exists in requests parameters.
     *
     * @param  $attribute
     *
     * @return bool
     */
    private function inParams($attribute): bool
    {
        return $this->returnFromParams($attribute);
    }

    private function getAttributeValue($attribute)
    {
        if ($this->inBody($attribute)) {
            return $this->ReturnFromBody($attribute);
        } elseif ($this->inParams($attribute)) {
            return $this->returnFromParams($attribute);
        }
    }

    /**
     * @param  $attribute
     *
     * @return mixed|bool
     */
    private function returnFromBody($attribute)
    {
        $body = json_decode($this->getRequestInstance()->body(), true);
        if (!empty($body[$attribute]) || null !== $body[$attribute]) {
            return $body[$attribute];
        }

        return false;
    }

    /**
     * @param  $attribute
     *
     * @return mixed|bool
     */
    private function returnFromParams($attribute)
    {
        if (!empty($this->getRequestInstance()->param($attribute))
            && null !== $this->getRequestInstance()->param($attribute)
        ) {
            return $this->getRequestInstance()->param($attribute);
        }

        return false;
    }
}
