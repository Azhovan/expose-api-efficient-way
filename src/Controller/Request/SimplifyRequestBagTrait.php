<?php

namespace App\ExposeApi\Controller\Request;

/**
 * this class help the controllers to get data with minimum effort from the request object
 * Class SimplifyRequestBagTrait
 *
 * @package App\ExposeApi\Controller\Request\Recipe
 */
trait SimplifyRequestBagTrait
{

    public function getRequestData()
    {
        $body = json_decode($this->getRequestInstance()->body(), true);
        $params = $this->getRequestInstance()->params();

        return array_merge($body, $params);
    }


}