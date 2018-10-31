<?php

namespace App\ExposeApi\Recipe\Core\Event;

use App\ExposeApi\Core\Contracts\Jsonable;
use App\ExposeApi\Recipe\Core\Traits\RedisTrait;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * find and get the Recipe's details.
 *
 * Class QueryRecipe
 */
final class RecipeQueried extends AbstractRecipeEvent implements Countable, IteratorAggregate, Jsonable
{
    use RedisTrait;

    /**
     * event handler.
     *
     * @throws \Exception
     *
     * @return string
     */
    public function handle()
    {
        $needle = $this->data->getFluent()->key('id');

        return $this->get($needle);
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

    /**
     * {@inheritdoc}
     * Count elements of an object.
     */
    public function count()
    {
        return $this->data->getFluent()->count();
    }
}
