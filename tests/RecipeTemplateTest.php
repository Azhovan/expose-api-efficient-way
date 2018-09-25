<?php

namespace Tests;

use App\ExposeApi\Core\Fluent;
use App\ExposeApi\Recipe\RecipeTemplate;
use PHPUnit\Framework\TestCase;

class RecipeTemplateTest extends TestCase
{

    public function test_constructor_input_arguments_case1()
    {
        $this->expectException(\TypeError::class);
        new RecipeTemplate();
    }

    public function test_constructor_input_arguments_case2()
    {
        $this->expectException(\TypeError::class);
        new RecipeTemplate('this is string, and NOT array');
    }

    public function test_getFluent_function_return_instance_of_Fluent_class()
    {
        $rtObject = new RecipeTemplate([]);
        $this->assertTrue($rtObject->getFluent() instanceof Fluent);
    }

    public function test_dynamic_overloading()
    {
        $rtObject = new RecipeTemplate([
            "name" => "name-1",
            "id" => "id-1",
            "prepTime" => "21 minutes",
            "difficulty" => "HARD",
            "vegetarian" => false
        ]);

        $id = $rtObject->id();
        $name = $rtObject->name();
        $prepTime = $rtObject->prepTime();
        $difficulty = $rtObject->difficulty();
        $vegetarian = $rtObject->vegetarian();

        $this->assertSame("name-1", $rtObject->getFluent()->key('name'));
        $this->assertSame("id-1", $rtObject->getFluent()->key('id'));
        $this->assertSame("21 minutes", $rtObject->getFluent()->key('prepTime'));
        $this->assertSame("HARD", $rtObject->getFluent()->key('difficulty'));
        $this->assertSame(false, $rtObject->getFluent()->key('vegetarian'));
    }

    public function test_override_dynamic_overloading()
    {
        $rtObject = new RecipeTemplate([
            "name" => "name-1",
            "id" => "id-1",
            "prepTime" => "21 minutes",
            "difficulty" => "HARD",
            "vegetarian" => false
        ]);

        $id = $rtObject->id('new-id');
        $name = $rtObject->name('new-name');
        $prepTime = $rtObject->prepTime('new-preptime');
        $difficulty = $rtObject->difficulty('new-difficulty');
        $vegetarian = $rtObject->vegetarian(true);

        $this->assertSame("new-name", $rtObject->getFluent()->key('name'));
        $this->assertSame("new-id", $rtObject->getFluent()->key('id'));
        $this->assertSame("new-preptime", $rtObject->getFluent()->key('prepTime'));
        $this->assertSame("new-difficulty", $rtObject->getFluent()->key('difficulty'));
        $this->assertSame(true, $rtObject->getFluent()->key('vegetarian'));
    }


    public function test_getIterator_return_iterator_array_object()
    {
        $rtObject = new RecipeTemplate([
            "name" => "name-1",
            "id" => "id-1",
            "prepTime" => "21 minutes",
            "difficulty" => "HARD",
            "vegetarian" => false
        ]);

        $this->assertInstanceOf(\ArrayIterator::class, $rtObject->getIterator());
    }


    public function test_getIterator_values_are_the_same_with_fluent_object()
    {
        $rtObject = new RecipeTemplate([
            "name" => "name-1",
            "id" => "id-1",
            "prepTime" => "21 minutes",
            "difficulty" => "HARD",
            "vegetarian" => false
        ]);

        $iterator = $rtObject->getIterator();

        $this->assertSame($rtObject->getFluent()->toArray(), $iterator->getArrayCopy());
    }
}