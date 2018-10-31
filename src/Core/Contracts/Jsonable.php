<?php

namespace App\ExposeApi\Core\Contracts;

interface Jsonable
{
    /**
     * Convert the object to JSON.
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0);
}
