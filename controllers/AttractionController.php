<?php

namespace app\controllers;

use app\exceptions\HttpUnprocessableEntity;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use app\models\AttractionModel;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Exception\HttpNotFoundException;

class AttractionController
{
    // Route: /attractions
    // TODO: Pagination
    public function getAttractions(Request $request, Response $response, array $args): Response
    {
        $query_params = $request->getQueryParams(); 
        $filters = $this->parseAttractionFilters($query_params);
        try {
            $attraction_model = new AttractionModel();
            if (isset($query_params["name"])) { //
                $result = $attraction_model->getWhereNameLike($query_params["name"]);
                $response->getBody()->write(json_encode($result));
            }
            if (isset($query_params["price_range"])) { //
                $result = $hotel_model->getWherePriceRangeLike($query_params["price_range"]);
                $response->getBody()->write(json_encode($result));
            }
            if (isset($query_params["parking"])) { //
                $result = $hotel_model->getWhereParkingLike($query_params["parking"]);
                $response->getBody()->write(json_encode($result));
            }
            if (isset($query_params["charging_station"])) { //
                $result = $hotel_model->getWhereChargingStationLike($query_params["charging_station"]);
                $response->getBody()->write(json_encode($result));
            } else {
                $result = $attraction_model->getAllAttractions($filters); //
                $response->getBody()->write(json_encode($result));
            }
            return $response;
        } catch (\Throwable $th) {
            throw new HttpInternalServerErrorException($request, 'Something broke!', $th);
        }
    }

    // Route: /attractions/{attraction_id}
    public function getAttraction(Request $request, Response $response, array $args): Response
    {
        $attraction_id = intval($args['attraction_id']);
        if ($attraction_id < 1) {
            throw new HttpUnprocessableEntity($request, 'Attraction ID is not valid!');
        }

        $result = [];
        try {
            $attraction_model = new AttractionModel();
            $result =  $attraction_model->getSingleAttraction($attraction_id);
        } catch (\Throwable $th) {
            throw new HttpInternalServerErrorException($request, 'Something broke!', $th);
        }

        if (empty($result)) {
            throw new HttpNotFoundException($request, "Attraction with ID $attraction_id not found!");
        }

        $response->getBody()->write(json_encode($result));
        return $response;
    }

    // Route: /locations/{location_id}
    /**public function deleteLocation(Request $request, Response $response, array $args): Response
    {
        $location_id = intval($args['location_id']);
        if ($location_id < 1) {
            throw new HttpUnprocessableEntity($request, 'Location ID is not valid!');
        }

        $result = [];
        try {
            $location_model = new LocationModel();
            $result =  $location_model->deleteLocation($location_id);
        } catch (\Throwable $th) {
            throw new HttpInternalServerErrorException($request, 'Something broke!', $th);
        }

        if (empty($result)) {
            throw new HttpNotFoundException($request, "Location with ID $location_id not found!");
        }

        $response->getBody()->write(json_encode($result));
        return $response;
    }*/
   
    private function parseAttractionFilters(array $query_params): array
    {
        $name = $query_params['name'] ?? false;
        $charging_station = $query_params['charging_station'] ?? false;
        $street = $query_params['street'] ?? false;
        $price_range = $query_params['price_range'] ?? false;
        $parking = $query_params['parking'] ?? false;

        $ret = [];
        if ($name !== false) {
            $ret['name'] = "%$name%";
        }
        if ($street !== false) {
            $ret['street'] = "%$street%";
        }
        if ($price_range !== false) {
            $ret['price_range'] = $price_range;
        }
        if ($parking !== false) {
            $ret['parking'] = $parking;
        }
        if ($charging_station !== false) {
            $ret['charging_station'] = strtolower($charging_station) == 'true';
        }

        return $ret;
    }
}