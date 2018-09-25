<?php

/**
 *  -----------------------------------------
 *  Endpoint Registration
 *  -----------------------------------------
 *   inject the requests to targeted controller
 */

use App\ExposeApi\Controller\Response\Response;

$route->with(
    '/recipes', function () use ($route) {

        /**
         * GET recipe by id
         */
        $route->respond(
            'DELETE', '/[:id]', function ($request, $response, $service, $app) use ($route) {
                $result = $app->RecipeController->delete(
                    $request,
                    $app
                );
                return (new Response($result, $route->response()))->get();
            }
        );

        /**
         * POST specific recipe
         */
        $route->respond(
            'POST', '/?', function ($request, $response, $service, $app) use ($route) {
                $result = $app->RecipeController->store(
                    $request,
                    $app
                );

                return (new Response($result, $route->response()))->get();
            }
        );


        /**
         * Update specific recipe
         */
        $route->respond(
            array('PUT','PATCH'), '/[:id]/?', function ($request, $response, $service, $app) use ($route) {
                $result = $app->RecipeController->update(
                    $request,
                    $app
                );

                return (new Response($result, $route->response()))->get();
            }
        );


        /**
         * Get specific recipe or a List of them
         */
        $route->respond(
            'GET', '/?[:id]?', function ($request, $response, $service, $app) use ($route) {
                $result = $app->RecipeController->index(
                    $request,
                    $app
                );

                return (new Response($result, $route->response()))->get();
            }
        );

        /**
         * Rate recipe by id
         */
        $route->respond(
            'POST', '/[:id]/rating', function ($request, $response, $service, $app) use ($route) {
                $result = $app->RateRecipeController->rate(
                    $request,
                    $app
                );


                return (new Response($result, $route->response()))->get();
            }
        );



        /**
         * Search Specific key=value
         */
        $route->respond(
            'POST', '/search', function ($request, $response, $service, $app) use ($route) {
                $result = $app->SearchController->index(
                    $request,
                    $app
                );

                return (new Response($result, $route->response()))->get();
            }
        );

    }
);


return $route;