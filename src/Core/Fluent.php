<?php

namespace App\ExposeApi\Core;

use App\ExposeApi\Core\Contracts\Arrayable;
use App\ExposeApi\Core\Contracts\Jsonable;
use Countable;
use JsonSerializable;

class Fluent implements Jsonable, JsonSerializable, Arrayable, Countable
{
    public $attributes = [];

    /**
     * @param array $attributes
     *
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if (is_string($key)) {
                $this->attributes[$key] = $value;
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function toArray()
    {
        return $this->attributes;
    }

    /**
     * @param $item
     * @param $value
     */
    public function append($item, $value)
    {
        $this->attributes[$item] = $value;
    }

    /**
     * @param  $key
     *
     * @return mixed
     */
    public function key($key)
    {
        if (!array_key_exists($key, $this->attributes)) {
            return;
        }

        return $this->attributes[$key];
    }

    /**
     * Count elements of an object.
     *
     * @link   http://php.net/manual/en/countable.count.php
     *
     * @return int The custom count as an integer.
     *             </p>
     *             <p>
     *             The return value is cast to an integer.
     *
     * @since  5.1.0
     */
    public function count()
    {
        return count($this->attributes);
    }
}
