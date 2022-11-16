<?php

namespace app\controllers;

use app\exceptions\HttpUnprocessableEntityException;
use app\models\FoodModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Exception\HttpNotFoundException;

class FoodController
{
    // Route: /restaurants/{restaurant_id}/food
    public function getAllFood(Request $request, Response $response, $args): Response
    {
        $restaurant_id = $args['restaurant_id'] ?? '';
        $int_restaurant_id = intval($restaurant_id);
        if (!ctype_digit($restaurant_id) || $restaurant_id < 1) {
            throw new HttpUnprocessableEntityException($request, 'Restaurant ID must be an integer > 0');
        }

        $result = [];
        try {
            $food_model = new FoodModel();
            $result = $food_model->getAllFood($int_restaurant_id);
        } catch (\Throwable $th) {
            throw new HttpInternalServerErrorException($request, 'Something broke!', $th);
        }

        $response->getBody()->write(json_encode($result));
        return $response;
    }

    // Route: /restaurants/{restaurant_id}/food/{food_id}
    public function getSingleFood(Request $request, Response $response, $args): Response
    {
        $restaurant_id = $args['restaurant_id'] ?? '';
        $food_id = $args['food_id'] ?? '';
        $food_params = [
            'restaurant_id' => $restaurant_id,
            'int_restaurant_id' => intval($restaurant_id),
            'food_id' => $food_id,
            'int_food_id' => intval($food_id)
        ];
        $validation = $this->validateFoodParams($food_params);

        if (!empty($validation)) {
            throw new HttpUnprocessableEntityException($request, $validation);
        }

        $result = [];
        try {
            $food_model = new FoodModel();
            $result = $food_model->getSingleFood($food_params['int_restaurant_id'], $food_params['int_food_id']);
        } catch (\Throwable $th) {
            throw new HttpInternalServerErrorException($request, 'Something broke!', $th);
        }

        if (empty($result)) {
            throw new HttpNotFoundException($request, "Food $food_id in Restaurant $restaurant_id not found!");
        }

        $response->getBody()->write(json_encode($result));
        return $response;
    }

    // Route: /restaurants/{restaurant_id}
    public function createFood(Request $request, Response $response, $args): Response
    {
        $body = $request->getParsedBody();
        $body['restaurant_fk'] = intval($args['restaurant_id'] ?? '');
        $validation = $this->validateFood($body);

        if (!empty($validation)) {
            throw new HttpUnprocessableEntityException($request, $validation);
        }

        $food = $this->remapBody($body);

        $result = 0;
        try {
            $food_model = new FoodModel();
            $result = $food_model->createSingleFood($food);
        } catch (\Throwable $th) {
            throw new HttpInternalServerErrorException($request, 'Something broke!', $th);
        }

        if ($result !== 1) {
            throw new HttpInternalServerErrorException($request, 'Restaurant creation unsuccessful!');
        }

        $response->getBody()->write(json_encode(['message' => 'Restaurant creation successful!']));
        return $response;
    }

    // Route: /restaurants/{restaurant_id}/food/{food_id}
    public function updateFood(Request $request, Response $response, $args): Response
    {
        $restaurant_id = $args['restaurant_id'] ?? '';
        $food_id = $args['food_id'] ?? '';
        $food_params = [
            'restaurant_id' => $restaurant_id,
            'int_restaurant_id' => intval($restaurant_id),
            'food_id' => $food_id,
            'int_food_id' => intval($food_id)
        ];
        $validation = $this->validateFoodParams($food_params);

        if (!empty($validation)) {
            throw new HttpUnprocessableEntityException($request, $validation);
        }

        $body = $request->getParsedBody();
        $validation = $this->validateFood($body);

        if (!empty($validation)) {
            throw new HttpUnprocessableEntityException($request, $validation);
        }
        $food = $this->remapBody($body);
        $food['food_id'] = $food_params['int_food_id'];

        try {
            $food_model = new FoodModel();

            if (empty($food_model->getSingleFood($food_params['int_restaurant_id'], $food_params['int_food_id']))) {
                throw new HttpNotFoundException($request, "Food $food_id in Restaurant $restaurant_id not found!");
            }

            $food_model->updateSingleFood($food);
        } catch(HttpNotFoundException $ex) {
            throw $ex;
        } catch (\Throwable $th) {
            throw new HttpInternalServerErrorException($request, 'Something broke!', $th);
        }

        $response->getBody()->write(json_encode(['message' => "Food $food_id update successful!"]));
        return $response;
    }

    // Route: /restaurants/{restaurant_id}/food/{food_id}
    public function deleteFood(Request $request, Response $response, $args): Response
    {
        $restaurant_id = $args['restaurant_id'] ?? '';
        $food_id = $args['food_id'] ?? '';
        $food_params = [
            'restaurant_id' => $restaurant_id,
            'int_restaurant_id' => intval($restaurant_id),
            'food_id' => $food_id,
            'int_food_id' => intval($food_id)
        ];
        $validation = $this->validateFoodParams($food_params);

        if (!empty($validation)) {
            throw new HttpUnprocessableEntityException($request, $validation);
        }

        $result = 0;
        try {
            $food_model = new FoodModel();
            $result = $food_model->deleteFood($food_params['int_restaurant_id'], $food_params['int_food_id']);
        } catch (\Throwable $th) {
            throw new HttpInternalServerErrorException($request, 'Something broke!', $th);
        }

        if ($result !== 1) {
            throw new HttpNotFoundException($request, "Food $food_id in Restaurant $restaurant_id not found!");
        }

        $response->getBody()->write(json_encode(['message' => "Food $food_id deletion successful!"]));
        return $response;
    }

    private function validateFoodParams(array $food_params): string
    {
        $ret = '';

        if (!ctype_digit($food_params['restaurant_id']) || $food_params['int_restaurant_id'] < 1) {
            $ret = '`restaurant_id` must be an integer > 0';
        } else if (!ctype_digit($food_params['food_id']) || $food_params['int_food_id'] < 1) {
            $ret = '`food_id` must be an integer > 0';
        }

        return $ret;
    }

    private function validateFood(mixed $food): string
    {
        if (!is_array($food)) {
            return 'Request must be a valid JSON object';
        }

        $ret = '';

        if (empty($food)) {
            $ret = 'Request must contain restaurant_fk, type, name, and price';
        } else if (!array_key_exists('restaurant_fk', $food) || !is_int($food['restaurant_fk']) || $food['restaurant_fk'] < 1) {
            $ret = '`restaurant_fk` must be an integer > 0';
        } else if (!array_key_exists('type', $food) || !is_string($food['type']) || empty($food['type'])) {
            $ret = '`type` must be a non-empty string';
        } else if (!array_key_exists('name', $food) || !is_string($food['name']) || empty($food['name'])) {
            $ret = '`name` must be a non-empty string';
        } else if (!array_key_exists('price', $food) || (!is_int($food['price']) && !is_float($food['price'])) || $food['price'] < 0) {
            $ret = '`price` must be either a decimal or an integer value';
        }

        return $ret;
    }

    private function remapBody(array $food): array
    {
        return [
            'restaurant_fk' => $food['restaurant_fk'],
            'type' => $food['type'],
            'name' => $food['name'],
            'price' => $food['price']
        ];
    }
}