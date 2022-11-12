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

    // Route: /restaurants
    public function createRestaurant(Request $request, Response $response, array $args): Response
    {
        $body = $request->getParsedBody();
        $error = $this->validateNewRestaurant($body);

        if (!empty($error)) {
            throw new HttpUnprocessableEntityException($request, $error);
        }

        $body['charging_station'] = filter_var($body['charging_station'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        $result = 0;
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

    // Route: /restaurants/{restaurant_id}
    public function updateRestaurant(Request $request, Response $response, array $args): Response
    {
        $id = $args['restaurant_id'] ?? false;
        $int_id = intval($id);
        if (!ctype_digit($id)) {
            throw new HttpUnprocessableEntityException($request, 'Restaurant ID invalid!');
        }

        $body = $request->getParsedBody();
        $validation = $this->validateUpdateRestaurant($body);

        if (!empty($validation)) {
            throw new HttpUnprocessableEntityException($request, $validation);
        }
        $body['charging_station'] = filter_var($body['charging_station'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        $body = $this->remapUpdateBody($body);
        $result = 0;
        try {
            $restaurant_model = new RestaurantModel();
            $result = $restaurant_model->updateSingleRestaurant($int_id, $body);
        } catch (\Throwable $th) {
            throw new HttpInternalServerErrorException($request, 'Something broke!', $th);
        }

        $message = null;
        if ($result !== 1) {
            $message = 'Restaurant update resulted in no change!';
        }

        $response->getBody()->write(json_encode(['message' => $message ?? "Restaurant $int_id update successful!"]));
        return $response;
    }

    // Route: /restaurants/{restaurant_id}
    public function deleteRestaurant(Request $request, Response $response, $args): Response
    {
        $id = $args['restaurant_id'] ?? false;
        $int_id = intval($id);
        if (!ctype_digit($id) || $int_id < 1) {
            throw new HttpUnprocessableEntityException($request, 'Restaurant ID invalid!');
        }

        $result = 0;
        try {
            $restaurant_model = new RestaurantModel();
            $result = $restaurant_model->deleteRestaurant($int_id);
        } catch (\Throwable $th) {
            throw new HttpInternalServerErrorException($request, 'Something broke!', $th);
        }

        if ($result !== 1) {
            throw new HttpNotFoundException($request, "Restaurant $int_id not found!");
        }

        $response->getBody()->write(json_encode(['message' => "Restaurant $int_id deletion successful!"]));
        return $response;
    }

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
        } else if (!array_key_exists('charging_station', $body) || !is_bool($body['charging_station'])) {
            $ret = '`charging_station` must be a boolean value';
        } else if (!array_key_exists('street', $body) || !is_string($body['street']) || empty($body['street'])) {
            $ret = '`street` must be a non-empty string';
        } else if (!array_key_exists('price_max', $body) || (!is_float($body['price_max']) && !is_int($body['price_max']))) {
            $ret = '`price_max` must either be a decimal or an integer value';
        }

        return $ret;
    }

    private function validateUpdateRestaurant(mixed $body): string
    {
        if (!is_array($body)) {
            return 'Request body must be a valid JSON object';
        }

        $location = $body['location_fk'] ?? false;
        $name = $body['name'] ?? false;
        $price_min = $body['price_min'] ?? false;
        $accessibility = $body['accessibility'] ?? false;
        $charging_station = array_key_exists('charging_station', $body);
        $street = $body['street'] ?? false;
        $price_max = $body['price_max'] ?? false;

        $ret = '';

        if ($location !== false && (!is_int($location) || $location < 1)) {
            $ret = '`location_fk` must be a value greater than 0';
        } else if ($name !== false && (!is_string($name) || empty($name))) {
            $ret = '`name` must a non-empty string';
        } else if ($price_min !== false && (!is_int($price_min) && !is_float($price_min))) {
            $ret = '`price_min` must either be a decimal or an integer value';
        } else if ($accessibility !== false && !in_array($accessibility, ['public', 'walking', 'car'])) {
            $ret = '`accessibility` must be a `car`, `public`, or `walking`';
        } else if ($charging_station !== false && !is_bool($charging_station)) {
            $ret = '`charging_station` must be a boolean value';
        } else if ($street !== false && (!is_string($street) || empty($street))) {
            $ret = '`street` must be a non-empty string';
        } else if ($price_max !== false && (!is_int($price_min) && !is_float($price_min))) {
            $ret = '`price_max` must either be a decimal or an integer value';
        }

        return $ret;
    }

    private function remapUpdateBody(array $body): array
    {
        return [
            'location_fk' => $body['location_fk'],
            'name' => $body['name'],
            'price_min' => $body['price_min'],
            'accessibility' => $body['accessibility'],
            'charging_station' => $body['charging_station'],
            'street' => $body['street'],
            'price_max' => $body['price_max']
        ];
    }
}
