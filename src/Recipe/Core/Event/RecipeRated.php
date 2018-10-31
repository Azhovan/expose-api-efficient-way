<?php

namespace App\ExposeApi\Recipe\Core\Event;

use App\ExposeApi\Core\Contracts\Jsonable;
use App\ExposeApi\Recipe\Core\Traits\RedisTrait;

final class RecipeRated extends AbstractRecipeEvent implements Jsonable
{
    use RedisTrait;

    /**
     * store rate of recipes with different identifier.
     *
     * @var string
     */
    private const POSTFIX = '-rate';

    /**
     * event handler
     * data will be PERSIST in redis.
     *
     * @throws \Exception
     *
     * @return string
     */
    public function handle()
    {
        $key = $this->data->getFluent()->key('id');

        return $this->append($key, $this->data->rate, self::POSTFIX);
    }

    /**
     * Convert the object to JSON.
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        return $this->data->getFluent()->toJson($options);
    }
}
