<?php

namespace app\controllers;

use app\exceptions\HttpUnprocessableEntityException;
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
            throw new HttpUnprocessableEntityException($request, 'Restaurant ID is not valid!');
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
    // Route: /restaurants
    public function createRestaurant(Request $request, Response $response, array $args): Response
    {
        $body = $request->getParsedBody();
        $error = $this->validateNewRestaurant($body);

        if (!empty($error)) {
            throw new HttpUnprocessableEntityException($request, $error);
        }

        $body['charging_station'] = filter_var($body['charging_station'], FILTER_VALIDATE_BOOLEAN);

        $result = 0;

        var_dump($body);
        try {
            $restaurant_model = new RestaurantModel();
            $result = $restaurant_model->createSingleRestaurant($body);
        } catch (\Throwable $th) {
            throw new HttpInternalServerErrorException($request, 'Something broke!', $th);
        }

        if ($result !== 1) {
            throw new HttpInternalServerErrorException($request, 'Restaurant creation unsuccessful!');
        }

        $response->getBody()->write(json_encode(['message' => 'Restaurant creation successful!']));
        return $response->withStatus(201);
    }

    // TODO: updateRestaurant

    // TODO: deleteRestaurant

    private function parseRestaurantFilters(array $query_params): array
    {
        $name = $query_params['name'] ?? false;
        $price_max = $query_params['price_max'] ?? false;
        $price_min = $query_params['price_min'] ?? false;
        $accessibility = $query_params['accessibility'] ?? false;
        $charging_station = $query_params['charging_station'] ?? false;

        $ret = [];
        if ($name !== false && !empty($name)) {
            $ret['name'] = "%$name%";
        }
        if ($price_max !== false) {
            $ret['price_max'] = $price_max;
        }
        if ($price_min !== false) {
            $ret['price_min'] = $price_min;
        }
        if ($accessibility !== false) {
            $ret['accessibility'] = $accessibility;
        }
        if ($charging_station !== false) {
            $ret['charging_station'] = strtolower($charging_station) == 'true';
        }

        return $ret;
    }

    private function validateNewRestaurant(mixed $body): string
    {
        $ret = '';
        if (!is_array($body)) {
            $ret = 'Request body must be a valid JSON object';
        } else if (empty($body)) {
            $ret = 'Request must contain location_fk, name, price_min, accessibility, charging_station, street, and price_max';
            return $ret;
        } else if (!array_key_exists('location_fk', $body) || !is_int($body['location_fk']) || $body['location_fk'] < 1) {
            $ret = '`location_fk` must be a value greater than 0';
        } else if (!array_key_exists('name', $body) || !is_string($body['name']) || empty($body['name'])) {
            $ret = '`name` must a non-empty string';
        } else if (!array_key_exists('price_min', $body) || (!is_float($body['price_max']) && !is_int($body['price_max']))) {
            $ret = '`price_min` must either be a decimal or an integer value';
        } else if (!array_key_exists('accessibility', $body) || !is_string($body['accessibility']) || !in_array($body['accessibility'], ['car', 'public', 'walking'])) {
            $ret = '`accessibility` must be a `car`, `public`, or `walking`';
        } else if (!array_key_exists('charging_station', $body) || filter_var($body['charging_station'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === null) {
            $ret = '`charging_station` must be either a truthy or falsy string';
        } else if (!array_key_exists('street', $body) || !is_string($body['street']) || empty($body['street'])) {
            $ret = '`street` must be a non-empty string';
        } else if (!array_key_exists('price_max', $body) || (!is_float($body['price_max']) && !is_int($body['price_max']))) {
            $ret = '`price_max` must either be a decimal or an integer value';
        }

        return $ret;
    }
}
