<?php

namespace Tests;

use App\ExposeApi\Recipe\Builder;
use App\ExposeApi\Recipe\Recipe;
use PHPUnit\Framework\TestCase;

class RecipeTest extends TestCase
{
    public function test_Recipe_class_must_implement_getRecipeAccessor_function()
    {
        $recipeObject = new Recipe();
        $this->assertTrue(method_exists($recipeObject, "getRecipeAccessor"));
    }

    public function test_getRecipeAccessor_function_should_return_instance_of_Builder_class()
    {
        $accessor = Recipe::getRecipeAccessor();
        $this->assertTrue($accessor instanceof Builder);

    }
}