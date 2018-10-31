<?php

namespace App\ExposeApi\Controller;

use App\ExposeApi\Controller\Request\Recipe\SearchRequest;
use App\ExposeApi\Recipe\Recipe;
use App\ExposeApi\Recipe\RecipeTemplate;
use Klein\Request;

class SearchController extends BaseController
{
    /**
     * Search based on name, prepTime, difficulty or vegetarian.
     *
     * @param Request $request
     *
     * @return \App\ExposeApi\Recipe\Builder|string
     */
    public function index(Request $request)
    {
        try {
            $searchRequest = new SearchRequest($request);
            $data = $searchRequest->getRequestData();

            return Recipe::search(
                $data, function (RecipeTemplate $item) {
                    $item->name();
                    $item->prepTime();
                    $item->difficulty();
                    $item->vegetarian();
                }
            );
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
