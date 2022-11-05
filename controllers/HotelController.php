<?php

namespace app\controllers;

use app\exceptions\HttpUnprocessableEntity;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use app\models\HotelModel;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Exception\HttpNotFoundException;

class HotelController
{
    // Route: /hotels
    // TODO: Pagination
    public function getHotels(Request $request, Response $response, array $args): Response
    {
        $query_params = $request->getQueryParams(); 
        $filters = $this->parseHotelFilters($query_params);
        try {
            $hotel_model = new HotelModel();
            if (isset($query_params["name"])) { //filters base on the name
                $result = $hotel_model->getWhereNameLike($query_params["name"]);
                $response->getBody()->write(json_encode($result));
            }
            if (isset($query_params["price_range"])) { //filters based on the price range
                $result = $hotel_model->getWherePriceLike($query_params["price_range"]);
                $response->getBody()->write(json_encode($result));
            }
            if (isset($query_params["accessibility"])) { // filters based on the accessibility
                $result = $hotel_model->getWhereAccessibilityLike($query_params["accessibility"]);
                $response->getBody()->write(json_encode($result));
            }
            if (isset($query_params["charging_station"])) { // filters based on the charging station
                $result = $hotel_model->getWhereChargingStationLike($query_params["charging_station"]);
                $response->getBody()->write(json_encode($result));
            } else {
                $result = $hotel_model->getAllHotels($filters); // gets all the hotels
                $response->getBody()->write(json_encode($result));
            }
            return $response;
        } catch (\Throwable $th) {
            throw new HttpInternalServerErrorException($request, 'Something broke!', $th);
        }
    }

    // Route: /hotels/{hotal_id}
    public function getHotel(Request $request, Response $response, array $args): Response
    {
        $hotel_id = intval($args['hotel_id']);
        if ($hotel_id < 1) {
            throw new HttpUnprocessableEntity($request, 'Hotel ID is not valid!');
        }

        $result = [];
        try {
            $hotel_model = new RestaurantModel();
            $result = $hotel_model->getSingleHotel($hotel_id);
        } catch (\Throwable $th) {
            throw new HttpInternalServerErrorException($request, 'Something broke!', $th);
        }

        if (empty($result)) {
            throw new HttpNotFoundException($request, "Restaurant with ID $hotel_id not found!");
        }

        $response->getBody()->write(json_encode($result));
        return $response;
    }

    // Route: /hotels/{hotal_id}
    public function deleteHotel(Request $request, Response $response, array $args): Response
    {
        $hotel_id = intval($args['hotel_id']);
        if ($hotel_id < 1) {
            throw new HttpUnprocessableEntity($request, 'Hotel ID is not valid!');
        }

        $result = [];
        try {
            $hotel_model = new RestaurantModel();
            $result = $hotel_model->deleteHotel($hotel_id);
        } catch (\Throwable $th) {
            throw new HttpInternalServerErrorException($request, 'Something broke!', $th);
        }

        if (empty($result)) {
            throw new HttpNotFoundException($request, "Restaurant with ID $hotel_id not found!");
        }

        $response->getBody()->write(json_encode($result));
        return $response;
    }


   
    private function parseHotelFilters(array $query_params): array
    {
        $name = $query_params['name'] ?? false;
        $charging_station = $query_params['charging_station'] ?? false;
        $street = $query_params['street'] ?? false;
        $price_range = $query_params['price_range'] ?? false;
        $accessibility = $query_params['accessibility'] ?? false;

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
        if ($accessibility !== false) {
            $ret['accessibility'] = $accessibility;
        }
        if ($charging_station !== false) {
            $ret['charging_station'] = strtolower($charging_station) == 'true';
        }

        return $ret;
    }
}