<?php

namespace app\controllers;

use app\exceptions\HttpUnprocessableEntityException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use app\models\CarModel;
use Exception;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Exception\HttpNotFoundException;

class CarController
{
    // Route: /carrentals/{car_rental_id}/cars
    // This function fetches all the cars from a specific car rental 
    // This function also supports pagination
    public function getCars(Request $request, Response $response, array $args): Response
    {
        $car_rental_id = $args['car_rental_id'] ?? '';
        $int_car_rental_id = intval($car_rental_id);
        if (!ctype_digit($car_rental_id) || $car_rental_id < 1) {
            throw new HttpUnprocessableEntityException($request, 'Car Rental ID must be an integer > 0');
        }

        $query_params = $request->getQueryParams();
        $page_num = $query_params['page'] ?? '1';
        $page_size = $query_params['page_size'] ?? '4';

        if(!ctype_digit($page_num) || intval($page_num) < 1) {
            throw new HttpUnprocessableEntityException($request, 'Page Number should be an integer > 0!');
        } else if (!ctype_digit($page_size) || intval($page_size) < 1) {
            throw new HttpUnprocessableEntityException($request, 'Page Size should be an integer > 0!');
        }

        $result = [];
        try {
            $car_model = new CarModel();
            $result = $car_model->getAllCars($int_car_rental_id, $page_num, $page_size);
        } catch (\Throwable $th) {
            throw new HttpInternalServerErrorException($request, 'Something broke!', $th);
        }

        $response->getBody()->write(json_encode($result));
        return $response;
    }

    // Route: /carrentals/{car_rental_id}/cars/{car_id}
    // This function fetches a specific car from a specific car rental 
    public function getCar(Request $request, Response $response, array $args): Response
    {
        $car_rental_id = $args['car_rental_id'] ?? '';
        $car_id = $args['car_id'] ?? '';
        $car_params = [
            'car_rental_id' => $car_rental_id,
            'int_car_rental_id' => intval($car_rental_id),
            'car_id' => $car_id,
            'int_car_id' => intval($car_id)
        ];
        if (!ctype_digit($car_rental_id) || $car_rental_id < 1) {
            throw new HttpUnprocessableEntityException($request, 'Car Rental ID must be an integer > 0');
        } else if (!ctype_digit($car_id) || $car_id < 1) {
            throw new HttpUnprocessableEntityException($request, 'Car ID must be an integer > 0');
        }

        $result = [];
        try {
            $car_model = new CarModel();
            $result = $car_model->getCarById($car_params['int_car_rental_id'], $car_params['int_car_id']);
        } catch (\Throwable $th) {
            throw new HttpInternalServerErrorException($request, 'Something broke!', $th);
        }

        if (empty($result)) {
            throw new HttpNotFoundException($request, "Car $car_id in Car Rental $car_rental_id not found!");
        }

        $response->getBody()->write(json_encode($result));
        return $response;
    }

    // Route: /carrentals/cars
    // This function creates a car based on the attributes specified by the user
    public function createCar(Request $request, Response $response, array $args): Response
    {
        $body = $request->getParsedBody();

        for ($index = 0; $index < count($body); $index++){
            $validation = $this->validateCar($body[$index]);
        }
        
        if (!empty($validation)) {
            throw new HttpUnprocessableEntityException($request, $validation);
        }

        $result = 0;

        try {
            $car_model = new CarModel();
            for ($index = 0; $index < count($body); $index++){
                $single_car = $body[$index];
                $car_rental_fk = $single_car["car_rental_fk"];
                $make = $single_car["make"];
                $model = $single_car["model"];
                $passenger = $single_car["passenger"];
                $year = $single_car["year"];
                $type = $single_car["type"];
                $price = $single_car["price"];
                $car_record = array("car_rental_fk" => $car_rental_fk, "make" => $make, "model" => $model, 
                "passenger" => $passenger, "year" => $year, "type" => $type, "price" => $price);
                $car_model->createCar($car_record);
            }
        
            
        } catch (\Throwable $th) {
            throw new HttpInternalServerErrorException($request, 'Something broke!', $th);
        }

        // if ($result !== 1) {
        //     throw new HttpInternalServerErrorException($request, 'Car creation unsuccessful!');
        // }

        $response->getBody()->write(json_encode(['message' => 'Car creation successful!']));
        return $response->withStatus(201);
    }

    // Route: /carrentals/{car_rental_id}/cars/{car_id}
    // This function updates the attributes of an existing car by the attributes specified by the user in a specific car rental
    public function updateCar(Request $request, Response $response, array $args): Response
    {
        $id = $args['car_id'] ?? false;
        $int_id = intval($id);
        $car_rental_id = $args['car_rental_id'] ?? false;
        $int_car_rental_id = intval($car_rental_id);
        if (!ctype_digit($id)) {
            throw new HttpUnprocessableEntityException($request, 'Car ID invalid!');
        } else if (!ctype_digit($car_rental_id)) {
            throw new HttpUnprocessableEntityException($request, 'Car Rental ID invalid!');
        }

        $body = $request->getParsedBody()[0];
        $validation = $this->validateCar($body);

        if (!empty($validation)) {
            throw new HttpUnprocessableEntityException($request, $validation);
        }

        $body = $this->remapBody($body);
        try {
            $car_model = new CarModel();

            if (empty($car_model->getCarById($int_car_rental_id, $int_id))) {
                throw new HttpNotFoundException($request, "Car $int_id does not exist!");
            }

            $car_model->updateCar($int_id, $body);
        } catch (HttpNotFoundException $ex) {
            throw $ex;
        } catch (\Throwable $th) {
            throw new HttpInternalServerErrorException($request, 'Something broke!', $th);
        }

        $response->getBody()->write(json_encode(['message' => "Car $int_id update successful!"]));
        return $response;
    }

    // Route: /carrentals/{car_rental_id}/car/{car_id}
    // This function deletes a specific car from a specific car rental
    public function deleteCar(Request $request, Response $response, $args): Response
    {
        $id = $args['car_rental_id'] ?? false;
        $int_id = intval($id);
        $car_id = $args['car_id'] ?? false;
        $int_car_id = intval($car_id);
        if (!ctype_digit($id) || $int_id < 1) {
            throw new HttpUnprocessableEntityException($request, 'Car Rental ID invalid!');
        } else if (!ctype_digit($car_id) || $int_car_id < 1) {
            throw new HttpUnprocessableEntityException($request, 'Car ID invalid!');
        }

        $result = 0;
        try {
            $car_model = new CarModel();
            $result = $car_model->deleteCar($int_car_id, $int_id);
        } catch (\Throwable $th) {
            throw new HttpInternalServerErrorException($request, 'Something broke!', $th);
        }

        if ($result !== 1) {
            throw new HttpNotFoundException($request, "Car $int_car_id not found!");
        }

        $response->getBody()->write(json_encode(['message' => "Car $int_car_id deletion successful!"]));
        return $response;
    }

    // This function validates the car attributes specified by a user 
    private function validateCar(mixed $body): string
    {
        if (!is_array($body)) {
            return 'Request body must be a valid JSON object';
        }

        $ret = '';
       
        if (empty($body)) {
            $ret = 'Request must contain car_rental_id, make, model, passenger, year, type, and price';
        } else if (!array_key_exists('car_rental_fk', $body) || !is_int($body['car_rental_fk']) || $body['car_rental_fk'] < 1) {
            $ret = '`car_rental_fk` must be a value greater than 0';
        } else if (!array_key_exists('make', $body) || !is_string($body['make']) || empty($body['make'])) {
            $ret = '`make` must be a non-empty string';
        } else if (!array_key_exists('model', $body) || !is_string($body['model']) || empty($body['model'])) {
            $ret = '`model` must be a non-empty string';
        } else if (!array_key_exists('passenger', $body) || (!is_float($body['passenger']) && !is_int($body['passenger']))) {
            $ret = '`passenger` must either be an integer value';
        } else if (!array_key_exists('year', $body) || (!is_float($body['year']) && !is_int($body['year']))) {
            $ret = '`year` must either be an integer value';
        }  else if (!array_key_exists('type', $body) || !is_string($body['type']) || empty($body['type'])) {
            $ret = '`type` must be a non-empty string';
        } else if (!array_key_exists('price', $body) || (!is_float($body['price']) && !is_int($body['price']))) {
            $ret = '`price` must either be a decimal or an integer value';
        }
        
        return $ret;
    }

    // This function associates the request body's car attributes to their corresponding variables in this file
    private function remapBody(array $body): array
    {
        return [
            'car_rental_fk' => $body['car_rental_fk'],
            'make' => $body['make'],
            'model' => $body['model'],
            'passenger' => $body['passenger'],
            'year' => $body['year'],
            'type' => $body['type'],
            'price' => $body['price']
        ];
    }
}
