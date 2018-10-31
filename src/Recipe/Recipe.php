<?php

namespace App\ExposeApi\Recipe;

/**
 * Class Recipe.
 *
 *
 * @method static \App\ExposeApi\Recipe\Builder create (array $data, \Closure $callback)
 * @method static \App\ExposeApi\Recipe\Builder delete (array $id, \Closure $callback = null)
 * @method static \App\ExposeApi\Recipe\Builder update (array $data, \Closure $callback)
 * @method static \App\ExposeApi\Recipe\Builder get (array $id)
 * @method static \App\ExposeApi\Recipe\Builder rate (array $data, \Closure $callback)
 * @method static \App\ExposeApi\Recipe\Builder search (array $data, \Closure $callback)
 *
 * @see \App\ExposeApi\Recipe\Builder
 */
class Recipe extends AbstractRecipe
{
    /**
     * {@inheritdoc}
     *
     * @return Builder|mixed
     */
    public static function getRecipeAccessor()
    {
        return new Builder();
    }
}
