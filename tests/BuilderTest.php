<?php

namespace Tests;

use App\ExposeApi\Controller\Response\Response;
use App\ExposeApi\Recipe\Builder;
use App\ExposeApi\Recipe\RecipeId;
use App\ExposeApi\Recipe\RecipeTemplate;
use PHPUnit\Framework\TestCase;

/**
 * By making these tests all controller's functionality will be tested
 *
 * Class BuilderTest
 *
 * @package Tests
 */
class BuilderTest extends TestCase
{

    private const SUCCESS_ACTION = 1;
    private const ERROR_ACTION = 0;
    private const RATE_EXAMPLE = 2;

    private $recipeId;

    public function setUp()
    {
        parent::setUp();

        $this->recipeId = RecipeId::generate();
    }

    public function test_createTemplate_function_accept_array_as_argument()
    {
        $builder = new Builder();

        $this->expectException(\TypeError::class);

        $builder->createTemplate('');
        $builder->createTemplate(null);
    }

    public function test_createTemplate_function_return_RecipeTemplate_object()
    {
        $builder = new Builder();
        $this->assertInstanceOf(\App\ExposeApi\Recipe\RecipeTemplate::class, $builder->createTemplate([]));
    }


    public function test_create_function()
    {
        $builder = new Builder();
        $data = [
            "name" => "name-1",
            "prepTime" => "21 minutes",
            "difficulty" => "HARD",
            "vegetarian" => false
        ];
        $id = $this->recipeId;

        $result = $builder->create(
            $data, function (RecipeTemplate $item) use ($id) {
                $item->id($id);
                $item->name();
                $item->prepTime();
                $item->difficulty();
                $item->vegetarian();
            }
        );

        $expected = '{"name":"name-1","prepTime":"21 minutes","difficulty":"HARD","vegetarian":false,"id":"' . $id . '"}';

        $mockReponseObject = $this->createMock(\Klein\Response::class);
        $this->assertSame($expected, (new Response($result, $mockReponseObject))->get());

        return $this->recipeId;
    }

    /**
     * @depends test_create_function
     * @param   $recipeId
     */
    public function test_fetch_a_recipe_result($recipeId)
    {
        $builder = new Builder();
        $data = [
            "name" => "name-1",
            "prepTime" => "21 minutes",
            "difficulty" => "HARD",
            "vegetarian" => false
        ];
        $id = $this->recipeId;

        $result = $builder->create(
            $data, function (RecipeTemplate $item) use ($id) {
                $item->id($id);
                $item->name();
                $item->prepTime();
                $item->difficulty();
                $item->vegetarian();
            }
        );

        $expected = '{"name":"name-1","prepTime":"21 minutes","difficulty":"HARD","vegetarian":false,"id":"' . $id . '"}';
        $mockReponseObject = $this->createMock(\Klein\Response::class);

        $actual = $builder->get(['id' => $id]);
        $this->assertSame($expected, (new Response($actual, $mockReponseObject))->get());

        return $this->recipeId;
    }

    /**
     *
     * @depends test_create_function
     * @param   $recipeId
     * @return  mixed
     */
    public function test_update_function($recipeId)
    {
        $builder = new Builder();
        $data = [
            "name" => "name-2",
            "prepTime" => "prepTime-2",
            "difficulty" => "HARD-2",
            "vegetarian" => true
        ];

        $result = $builder->update(
            $data, function (RecipeTemplate $item) use ($recipeId) {
                $item->id($recipeId);
                $item->name();
                $item->prepTime();
                $item->difficulty();
                $item->vegetarian();
            }
        );

        $expected = '{"name":"name-2","prepTime":"prepTime-2","difficulty":"HARD-2","vegetarian":true,"id":"' . $recipeId . '"}';

        $mockReponseObject = $this->createMock(\Klein\Response::class);
        $this->assertSame($expected, (new Response($result, $mockReponseObject))->get());

        return $recipeId;

    }

    /**
     *
     * @depends test_update_function
     * @return  mixed
     */
    public function test_rate_recipe_function($recipeId)
    {
        $data = [];
        $data['id'] = $recipeId;
        $data['rate'] = self::RATE_EXAMPLE;

        $builder = new Builder();
        $result = $builder->rate(
            $data, function (RecipeTemplate $item) {
                $item->id();
                $item->rate();
            }
        );


        $expected = '{"rates":[' . self::RATE_EXAMPLE . ']}';
        $mockReponseObject = $this->createMock(\Klein\Response::class);
        $this->assertSame($expected, (new Response($result, $mockReponseObject))->get());
    }

    /**
     * @depends test_create_function
     * @param   $recipeId
     */
    public function test_delete_a_recipe($recipeId)
    {
        $builder = new Builder();

        $response = $builder->delete(['id' => $recipeId]);

        $this->assertSame(self::SUCCESS_ACTION, (int)$response);
    }


}