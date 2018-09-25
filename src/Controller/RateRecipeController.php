<?php

namespace App\ExposeApi\Controller;


use App\ExposeApi\Controller\Request\Recipe\RateRequest;
use App\ExposeApi\Recipe\Recipe;
use App\ExposeApi\Recipe\RecipeTemplate;
use Klein\App;
use Klein\Request;

class RateRecipeController extends BaseController
{


    public function rate(Request $request, App $app)
    {
        try {

            $rateRequest = new RateRequest($request);
            $rateRequest->validate();

            if ($rateRequest->failed()) {
                return json_encode($rateRequest->errors());
            }

            $result = $rateRequest->getRequestData();
            $data['id'] = $result['id'];
            $data['rate'] = $result['rate'];

            return Recipe::rate(
                $data, function (RecipeTemplate $item) {
                    $item->id();
                    $item->rate();
                }
            );

        } catch (\Throwable $exception) {
            return json_encode(
                [
                "code" => $exception->getCode(),
                "errors" => true,
                'message' => $exception->getMessage(),
                ]
            );

        }
    }

}