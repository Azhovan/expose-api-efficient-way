<?php

namespace App\ExposeApi\Recipe;

use App\ExposeApi\Core\Fluent;
use ArrayIterator;

final class RecipeTemplate implements \IteratorAggregate
{
    /**
     * the name of recipe.
     *
     * @var string
     */
    public $name;

    /**
     * unique id for recipe.
     *
     * @var int
     */
    public $id;

    /**
     * the prepare time.
     *
     * @var string
     */
    public $prepTime;

    /**
     * level of difficulty.
     *
     * @var int
     */
    public $difficulty;

    /**
     * is recipe can be used by vegetarian or not.
     *
     * @var bool
     */
    public $vegetarian;

    /**
     * Representation of Recipe template data.
     *
     * @var Fluent
     */
    private $fluent;

    /**
     * Rate of recipe (1-5).
     *
     * @var
     */
    public $rate;

    /**
     * RecipeTemplate constructor.
     *
     * @param  $data
     * @param Fluent $fluent
     *
     * @throws \Exception
     */
    public function __construct(array $data, Fluent $fluent = null)
    {
        $this->fluent = $fluent ?? (new Fluent($data));
    }

    /**
     * Dynamic value assignments.
     *
     * @param  $name
     * @param  $args
     *
     * @return RecipeTemplate
     */
    public function __call($name, $args)
    {
        if (property_exists($this, $name)) {
            if (isset($this->fluent->toArray()[$name])) {
                $this->{$name} = $this->fluent->toArray()[$name];
            }
            if (!empty($args[0])) {
                $this->{$name} = $args[0];
                $this->fluent->append($name, $args[0]);
            }
        }

        if ($name == 'id' && !empty($this->id)) {
            $this->fluent->append('id', $this->id);
        }

        return $this;
    }

    /**
     * @return Fluent|array
     */
    public function getFluent()
    {
        return $this->fluent;
    }

    /**
     * {@inheritdoc}
     *
     * return Traversable object from RecipeTemplate class
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->getFluent()->toArray());
    }
}
