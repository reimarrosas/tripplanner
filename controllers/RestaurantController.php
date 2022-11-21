<?php

namespace app\controllers;
use app\config\APIKeys;
use app\exceptions\HttpUnprocessableEntityException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use app\models\RestaurantModel;
use GuzzleHttp\Client;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Exception\HttpNotFoundException;

class RestaurantController
{
    // Route: /restaurants
    public function getRestaurants(Request $request, Response $response, array $args): Response
    {
        $query_params = $request->getQueryParams();
        $filters = $this->parseRestaurantFilters($query_params);
        $page_num = $query_params['page'] ?? '1';
        $page_size = $query_params['page_size'] ?? '4';
        
        if (!ctype_digit($page_num) || intval($page_num) < 1) {
            throw new HttpUnprocessableEntityException($request, 'Page Number should be an integer > 0!');
        } else if (!ctype_digit($page_size) || intval($page_size) < 1) {
            throw new HttpUnprocessableEntityException($request, 'Page Size should be an integer > 0!');
        }

        try {
            $restaurant_model = new RestaurantModel();
            $result = $restaurant_model->getAllRestaurants($filters, $page_num, $page_size);
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

        if (!is_array($body)) {
            throw new HttpUnprocessableEntityException($request, 'Request body must be a valid JSON object.');
        }

        $count = count($body);

        if ($count == 0) {
            throw new HttpUnprocessableEntityException($request, 'Request body must not be empty.');
        }

        for ($i = 0; $i < $count; ++$i) {
            $restaurant = $body[$i] ?? false;
            $validation = $this->validateRestaurant($restaurant);

            if (!empty($validation)) {
                throw new HttpUnprocessableEntityException($request, "$validation with restaurant on index $i.");
            }

            $body[$i] = $this->remapBody($body[$i]);
        }

        try {
            $restaurant_model = new RestaurantModel();
            $restaurant_model->createMultipleRestaurant($body);
        } catch (\Throwable $th) {
            throw new HttpInternalServerErrorException($request, 'Something broke!', $th);
        }

        $response->getBody()->write(json_encode(['message' => 'Restaurant/s creation successful!']));
        return $response->withStatus(201);
    }

    // Route: /restaurants
    public function updateRestaurant(Request $request, Response $response, array $args): Response
    {
        $body = $request->getParsedBody();

        if (!is_array($body)) {
            throw new HttpUnprocessableEntityException($request, 'Request body must be a valid JSON object.');
        }

        $count = count($body);

        if ($count == 0) {
            throw new HttpUnprocessableEntityException($request, 'Request body must not be empty.');
        }

        for ($i = 0; $i < $count; ++$i) {
            $restaurant = $body[$i] ?? false;

            $validation = '';

            $restaurant_id = $restaurant['restaurant_id'] ?? false;
            if ($restaurant_id === false || !is_int($restaurant_id) || $restaurant_id < 1) {
                $validation = 'Restaurant ID must be an integer > 0';
            } else {
                $validation = $this->validateRestaurant($restaurant);
            }

            if (!empty($validation)) {
                throw new HttpUnprocessableEntityException($request, "$validation with restaurant on index $i.");
            }

            $body[$i] = $this->remapBody($body[$i]);
            $body[$i]['restaurant_id'] = $restaurant_id;
        }

        try {
            $restaurant_model = new RestaurantModel();
            $restaurant_model->updateMultipleRestaurant($body);
        } catch (HttpNotFoundException $ex) {
            throw $ex;
        } catch (\Throwable $th) {
            throw new HttpInternalServerErrorException($request, 'Something broke!', $th);
        }

        $response->getBody()->write(json_encode(['message' => "Restaurant update(s) successful!"]));
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
            $ret['price_max'] = floatval($price_max);
        }
        if ($price_min !== false) {
            $ret['price_min'] = floatval($price_min);
        }
        if ($accessibility !== false) {
            $ret['accessibility'] = $accessibility;
        }
        if ($charging_station !== false) {
            $ret['charging_station'] = strtolower($charging_station) == 'true';
        }

        return $ret;
    }

    private function validateRestaurant(mixed $body): string
    {
        if (!is_array($body)) {
            return 'Request body must be a valid JSON object';
        }

        $ret = '';

        if (empty($body)) {
            $ret = 'Request must contain location_fk, name, price_min, accessibility, charging_station, street, and price_max';
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

    private function remapBody(array $body): array
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

    // Route: /restaurants/{restaurant_id}/reviews
    public function getReviews(Request $request, Response $response, array $args): Response
    {
        $restaurant_id = $args['restaurant_id'] ?? '';
        $int_restaurant_id = intval($restaurant_id);

        if (!ctype_digit($restaurant_id) || $int_restaurant_id < 1) {
            throw new HttpUnprocessableEntityException($request, '`restaurant_id` must be an integer > 0');
        }

        $restaurant = [];
        try {
            $restaurant_model = new RestaurantModel();
            $restaurant = $restaurant_model->getSingleRestaurant($int_restaurant_id);
        } catch (\Throwable $th) {
            throw new HttpInternalServerErrorException($request, 'Something broke!', $th);
        }

        if (empty($restaurant)) {
            throw new HttpNotFoundException($request, "Restaurant $restaurant_id not found!");
        }

        $client = new Client(['base_uri' => 'https://api.yelp.com/v3/businesses/']);

        $api_key = APIKeys::REVIEWS;
        $term = $restaurant['name'];
        $location = $restaurant['street'];
       
        $reviews = [];
        try {
            $headers = [
                'Authorization' => 'Bearer ' . $api_key       
            ];
            $res = $client->get("search?term=$term&location=$location", ['headers' => $headers]);
            $body = json_decode($res->getBody());
            $id = $body->businesses[0]->id;
            $res = $client->get("$id/reviews", ['headers' => $headers]);
            $reviews = json_decode($res->getBody());
        } catch (\Throwable $th) {
            throw new HttpInternalServerErrorException($request, $th);
        }

        $response_body = [
            'term' => $term,
            'location' => $location,
            'reviews' => $reviews
        ];
        $response->getBody()->write(json_encode($response_body));
        return $response;
    }
}
