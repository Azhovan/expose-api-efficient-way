<?php

namespace App\ExposeApi\Recipe\Core\Event;

use App\ExposeApi\Recipe\Core\Traits\RedisTrait;

final class RecipeDeleted extends AbstractRecipeEvent
{
    use RedisTrait;

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

        return $this->deleteOrFail($key);
    }
}
