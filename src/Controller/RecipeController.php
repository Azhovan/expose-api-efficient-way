<?php

namespace App\ExposeApi\Controller;

use App\ExposeApi\Controller\Request\Recipe\CreateRequest;
use App\ExposeApi\Controller\Request\Recipe\DeleteRequest;
use App\ExposeApi\Controller\Request\Recipe\UpdateRequest;
use App\ExposeApi\Recipe\Recipe;
use App\ExposeApi\Recipe\RecipeId;
use App\ExposeApi\Recipe\RecipeTemplate;
use Klein\App;
use Klein\Request;

class RecipeController extends BaseController
{
    /**
     * store a recipe in database.
     *
     * @param Request $request
     * @param App     $app
     *
     * @return mixed
     */
    public function store(Request $request, App $app)
    {
        try {
            $createRequest = new CreateRequest($request);
            $createRequest->validate();

            if ($createRequest->failed()) {
                return json_encode($createRequest->errors());
            }

            $data = $createRequest->getRequestData();
            $id = RecipeId::generate();

            $response = Recipe::create(
                $data, function (RecipeTemplate $item) use ($id) {
                    $item->id($id);
                    $item->name();
                    $item->prepTime();
                    $item->difficulty();
                    $item->vegetarian();
                }
            );

            return $response;
        } catch (\Throwable $exception) {
            return json_encode(
                [
                'code'    => $exception->getCode(),
                'errors'  => true,
                'message' => $exception->getMessage(),
                ]
            );
        }
    }

    /**
     * @param Request $request
     * @param App     $app
     *
     * @return \App\ExposeApi\Recipe\Builder|array
     */
    public function delete(Request $request, App $app)
    {
        try {
            $createRequest = new DeleteRequest($request);
            $createRequest->validate();

            if ($createRequest->failed()) {
                return json_encode($createRequest->errors());
            }

            $id = $request->param('id');

            return Recipe::delete(['id' => $id]);
        } catch (\Throwable $exception) {
            return json_encode(
                [
                'code'    => $exception->getCode(),
                'errors'  => true,
                'message' => $exception->getMessage(),
                ]
            );
        }
    }

    /**
     * @param Request $request
     * @param App     $app
     *
     * @return \App\ExposeApi\Recipe\Builder|array
     */
    public function update(Request $request, App $app)
    {
        try {
            $updateRequest = new UpdateRequest($request);
            $updateRequest->validate();

            if ($updateRequest->failed()) {
                return json_encode($updateRequest->errors());
            }

            $data = $updateRequest->getRequestData();
            $id = $data['id'];

            $response = Recipe::update(
                $data, function (RecipeTemplate $item) use ($id) {
                    $item->id($id);
                    $item->name();
                    $item->prepTime();
                    $item->difficulty();
                    $item->vegetarian();
                }
            );

            return $response;
        } catch (\Throwable $exception) {
            return json_encode(
                [
                'code'    => $exception->getCode(),
                'errors'  => true,
                'message' => $exception->getMessage(),
                ]
            );
        }
    }

    /**
     * @param Request $request
     * @param App     $app
     *
     * @return \App\ExposeApi\Recipe\Builder|array
     */
    public function index(Request $request, App $app)
    {
        try {
            $id = $request->param('id') ?? '*';

            return Recipe::get(['id' => $id]);
        } catch (\Throwable $exception) {
            return json_encode(
                [
                'code'    => $exception->getCode(),
                'errors'  => true,
                'message' => $exception->getMessage(),
                ]
            );
        }
    }
}
