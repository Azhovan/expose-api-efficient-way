<?php

namespace App\ExposeApi\Recipe\Core\Event;

use App\ExposeApi\Core\Contracts\Jsonable;
use App\ExposeApi\Recipe\Exception\ExposeApiInvalidArgument;
use App\ExposeApi\Recipe\Core\Traits\RedisTrait;
use IteratorAggregate;
use Traversable;

final class RecipeUpdated extends AbstractRecipeEvent implements IteratorAggregate, Jsonable
{

    use RedisTrait;

    /**
     * @var string
     */
    private const RECIPE_NOT_EXISTS = 0;

    /**
     * event handler
     * data will be PERSIST in redis
     *
     * @return string
     * @throws \Exception
     */
    public function handle()
    {
        if ($this->has($this->data->id)) {

            $this->save($this->data->id, $this->toJson());

            return $this->getOrFail($this->data->id);
        }

        throw new ExposeApiInvalidArgument("key not found", 404);

    }

    /**
     * @inheritdoc
     * @return     Traversable|void
     */
    public function getIterator()
    {
        return $this->data->getIterator();
    }

    /**
     * @inheritdoc
     *
     * @param  int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return $this->data->getFluent()->toJson($options);
    }

}