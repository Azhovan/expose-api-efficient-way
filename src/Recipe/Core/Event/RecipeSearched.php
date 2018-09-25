<?php

namespace App\ExposeApi\Recipe\Core\Event;


use App\ExposeApi\Core\Contracts\Jsonable;
use App\ExposeApi\Recipe\Exception\ExposeApiInvalidArgument;
use App\ExposeApi\Recipe\Core\Traits\RedisTrait;
use Countable;
use IteratorAggregate;

/**
 * Search and get the Recipe's details
 *
 * Class QueryRecipe
 *
 * @package App\ExposeApi\Recipe\Event
 */
final class RecipeSearched extends AbstractRecipeEvent implements Countable, IteratorAggregate, Jsonable
{

    use RedisTrait;

    /**
     * event handler
     *  search with Iterator
     *
     * @return string
     * @throws \Exception
     */
    public function handle()
    {
        return $this->search($this->toJson());
    }


    /**
     * @inheritdoc
     * @return
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

    /**
     * @inheritdoc
     * Count elements of an object
     */
    public function count()
    {
        return $this->data->getFluent()->count();
    }

}