<?php

require __DIR__ . '/../helpers/helpers.php';


$route = new \Klein\Klein();

$route->respond(
    function ($request, $response, $service, $app) use ($route) {

        $app->register(
            'RecipeController', function () {
                return new \App\ExposeApi\Controller\RecipeController();
            }
        );

        $app->register(
            'RateRecipeController', function () {
                return new \App\ExposeApi\Controller\RateRecipeController();
            }
        );

        $app->register(
            'SearchController', function () {
                return new \App\ExposeApi\Controller\SearchController();
            }
        );

    }
);