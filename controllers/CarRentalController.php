<?php

namespace app\controllers;

use app\exceptions\HttpUnprocessableEntityException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use app\models\CarRentalModel;
use Exception;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Exception\HttpNotFoundException;

class CarRentalController
{
    // Route: /carrentals
    // TODO: Pagination
    public function getCarRentals(Request $request, Response $response, array $args): Response
    {
        $query_params = $request->getQueryParams();
        $filters = $this->parseCarRentalFilters($query_params);
        try {
            $carrental_model = new CarRentalModel();
            $result = $carrental_model->getAllCarRentals($filters);
            $response->getBody()->write(json_encode($result));
            return $response;
        } catch (\Throwable $th) {
            throw new HttpInternalServerErrorException($request, 'Something broke!', $th);
        }
    }

    // Route: /carrentals/{car_rental_id}
    public function getCarRental(Request $request, Response $response, array $args): Response
    {
        $car_rental_id = intval($args['car_rental_id']);
        if ($car_rental_id < 1) {
            throw new HttpUnprocessableEntityException($request, 'Car Rental ID is not valid!');
        }

        $result = [];
        try {
            $carrental_model = new CarRentalModel();
            $result = $carrental_model->getCarRentalById($car_rental_id);
        } catch (\Throwable $th) {
            throw new HttpInternalServerErrorException($request, 'Something broke!', $th);
        }

        if (empty($result)) {
            throw new HttpNotFoundException($request, "Car Rental with ID $car_rental_id not found!");
        }

        $response->getBody()->write(json_encode($result));
        return $response;
    }

    // Route: /carrentals
    public function createCarRental(Request $request, Response $response, array $args): Response
    {
        $body = $request->getParsedBody();

        for ($index = 0; $index < count($body); $index++){
            $validation = $this->validateCarRental($body[$index]);
        }
        
        if (!empty($validation)) {
            throw new HttpUnprocessableEntityException($request, $validation);
        }

        $result = 0;

        try {
            $carrental_model = new CarRentalModel();
            for ($index = 0; $index < count($body); $index++){
                $single_carrental = $body[$index];
                $location_fk = $single_carrental["location_fk"];
                $price_min = $single_carrental["price_min"];
                $rental_duration = $single_carrental["rental_duration"];
                $street = $single_carrental["street"];
                $price_max = $single_carrental["price_max"];
                $carrental_record = array("location_fk" => $location_fk, "price_min" => $price_min, "rental_duration" => $rental_duration, 
                "street" => $street, "price_max" => $price_max);
                $carrental_model->createCarRental($carrental_record);
            }
        
            
        } catch (\Throwable $th) {
            throw new HttpInternalServerErrorException($request, 'Something broke!', $th);
        }

        if ($result !== 1) {
            throw new HttpInternalServerErrorException($request, 'Car Rental creation unsuccessful!');
        }

        $response->getBody()->write(json_encode(['message' => 'Car Rental creation successful!']));
        return $response->withStatus(201);
    }

    // Route: /carrentals/{car_rental_id}
    public function updateCarRental(Request $request, Response $response, array $args): Response
    {
        $id = $args['car_rental_id'] ?? false;
        $int_id = intval($id);
        if (!ctype_digit($id)) {
            throw new HttpUnprocessableEntityException($request, 'Car Rental ID invalid!');
        }

        $body = $request->getParsedBody()[0];
        $validation = $this->validateCarRental($body);

        if (!empty($validation)) {
            throw new HttpUnprocessableEntityException($request, $validation);
        }

        $body = $this->remapBody($body);
        try {
            $carrental_model = new CarRentalModel();

            if (empty($carrental_model->getCarRentalById($int_id))) {
                throw new HttpNotFoundException($request, "Car Rental $int_id does not exist!");
            }

            $carrental_model->updateCarRental($int_id, $body);
        } catch (HttpNotFoundException $ex) {
            throw $ex;
        } catch (\Throwable $th) {
            throw new HttpInternalServerErrorException($request, 'Something broke!', $th);
        }

        $response->getBody()->write(json_encode(['message' => "Car Rental $int_id update successful!"]));
        return $response;
    }

    // Route: /carrentals/{car_rental_id}
    public function deleteCarRental(Request $request, Response $response, $args): Response
    {
        $id = $args['car_rental_id'] ?? false;
        $int_id = intval($id);
        if (!ctype_digit($id) || $int_id < 1) {
            throw new HttpUnprocessableEntityException($request, 'Car Rental ID invalid!');
        }

        $result = 0;
        try {
            $carrental_model = new CarRentalModel();
            $result = $carrental_model->deleteCarRental($int_id);
        } catch (\Throwable $th) {
            throw new HttpInternalServerErrorException($request, 'Something broke!', $th);
        }

        if ($result !== 1) {
            throw new HttpNotFoundException($request, "Car Rental $int_id not found!");
        }

        $response->getBody()->write(json_encode(['message' => "Car Rental $int_id deletion successful!"]));
        return $response;
    }

    private function parseCarRentalFilters(array $query_params): array
    {
        $price_max = $query_params['price_max'] ?? false;
        $price_min = $query_params['price_min'] ?? false;

        $ret = [];
        if ($price_max !== false) {
            $ret['price_max'] = $price_max;
        }
        if ($price_min !== false) {
            $ret['price_min'] = $price_min;
        }

        return $ret;
    }

    private function validateCarRental(mixed $body): string
    {
        if (!is_array($body)) {
            return 'Request body must be a valid JSON object';
        }

        $ret = '';
       
        if (empty($body)) {
            $ret = 'Request must contain location_fk, price_min, rental_duration, street, and price_max';
        } else if (!array_key_exists('location_fk', $body) || !is_int($body['location_fk']) || $body['location_fk'] < 1) {
            $ret = '`location_fk` must be a value greater than 0';
        }  else if (!array_key_exists('price_min', $body) || (!is_float($body['price_min']) && !is_int($body['price_min']))) {
            $ret = '`price_min` must either be a decimal or an integer value';
        }  else if (!array_key_exists('rental_duration', $body) || (!is_string($body['rental_duration']) || empty($body['rental_duration']))) {
            $ret = '`rental_duration` must be a string value';
        }  else if (!array_key_exists('street', $body) || !is_string($body['street']) || empty($body['street'])) {
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
            'price_min' => $body['price_min'],
            'rental_duration' => $body['rental_duration'],
            'street' => $body['street'],
            'price_max' => $body['price_max']
        ];
    }

    private function validateDate($date, $format = 'Y-m-d'){
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
}
