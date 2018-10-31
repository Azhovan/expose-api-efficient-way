<?php

namespace App\ExposeApi\Recipe\Core\Event;

use App\ExposeApi\Core\Contracts\Jsonable;
use App\ExposeApi\Recipe\Core\Traits\RedisTrait;
use IteratorAggregate;
use Traversable;

final class RecipeCreated extends AbstractRecipeEvent implements IteratorAggregate, Jsonable
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
        $this->save($this->data->id, $this->toJson());

        return $this->getOrFail($this->data->id);
    }

    /**
     * {@inheritdoc}
     *
     * @return Traversable|void
     */
    public function getIterator()
    {
        return $this->data->getIterator();
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
        return $this->data->getFluent()->toJson($options);
    }
}
