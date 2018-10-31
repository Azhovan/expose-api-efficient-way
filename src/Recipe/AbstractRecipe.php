<?php

namespace App\ExposeApi\Recipe;

abstract class AbstractRecipe
{
    /**
     * Get the recipe builder class instance.
     *
     * @return mixed
     *
     * @see \App\ExposeApi\Recipe\Builder
     */
    abstract public static function getRecipeAccessor();

    /**
     * Handle dynamic, static calls to the object.
     *
     * @param  $method
     * @param  $arguments
     *
     * @throws \RuntimeException
     *
     * @return mixed
     */
    public static function __callStatic($method, $arguments)
    {
        $instance = static::getRecipeAccessor();

        if (!$instance) {
            throw new \RuntimeException('Recipe builder class does not exist');
        }

        return $instance->$method(...$arguments);
    }
}
