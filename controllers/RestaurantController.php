<?php

namespace app\controllers;

use app\exceptions\HttpUnprocessableEntity;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use app\models\RestaurantModel;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Exception\HttpNotFoundException;

class RestaurantController
{
    // Route: /restaurants
    // TODO: Pagination
    public function getRestaurants(Request $request, Response $response, array $args): Response
    {
        $query_params = $request->getQueryParams();
        $filters = $this->parseRestaurantFilters($query_params);
        try {
            $restaurant_model = new RestaurantModel();
            $result = $restaurant_model->getAllRestaurants($filters);
            $response->getBody()->write(json_encode($result));
            return $response;
        } catch (\Throwable $th) {
            throw new HttpInternalServerErrorException($request, 'Something broke!', $th);
        }
    }

    // Route: /restaurants/{restaurant_id}
    public function getRestaurant(Request $request, Response $response, array $args): Response
    {
        $restaurant_id = intval($args['restaurant_id']);
        if ($restaurant_id < 1) {
            throw new HttpUnprocessableEntity($request, 'Restaurant ID is not valid!');
        }

        $result = [];
        try {
            $restaurant_model = new RestaurantModel();
            $result = $restaurant_model->getSingleRestaurant($restaurant_id);
        } catch (\Throwable $th) {
            throw new HttpInternalServerErrorException($request, 'Something broke!', $th);
        }

        if (empty($result)) {
            throw new HttpNotFoundException($request, "Restaurant with ID $restaurant_id not found!");
        }

        $response->getBody()->write(json_encode($result));
        return $response;
    }

    // TODO: createRestaurant

    // TODO: updateRestaurant

    // TODO: deleteRestaurant

    private function parseRestaurantFilters(array $query_params): array
    {
        $name = $query_params['name'] ?? false;
        $price = $query_params['price'] ?? false;
        $accessibility = $query_params['accessibility'] ?? false;
        $charging_station = $query_params['charging_station'] ?? false;

        $ret = [];
        if ($name !== false) {
            $ret['name'] = "%$name%";
        }
        if ($price !== false) {
            $ret['price'] = $price;
        }
        if ($accessibility !== false) {
            $ret['accessibility'] = $accessibility;
        }
        if ($charging_station !== false) {
            $ret['charging_station'] = strtolower($charging_station) == 'true';
        }

        return $ret;
    }
}
