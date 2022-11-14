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
        $result = null;
        try {
            $hotel_model = new HotelModel();
            if (isset($query_params["name"])) { //filters base on the name
                $result = $hotel_model->getWhereNameLike($query_params["name"]);
            }
            /**if (isset($query_params["price_range"])) { //filters based on the price range
                $result = $hotel_model->getWherePriceLike($query_params["price_range"]);
            }*/
            if (isset($query_params["accessibility"])) { // filters based on the accessibility
                $result = $hotel_model->getWhereAccessibilityLike($query_params["accessibility"]);
            }
            if (isset($query_params["charging_station"])) { // filters based on the charging station
                $result = $hotel_model->getWhereChargingStationLike($query_params["charging_station"]);
            } else {
                $result = $hotel_model->getAllHotels($filters); // gets all the hotels
            }
            $response->getBody()->write(json_encode($result));
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
            $hotel_model = new HotelModel();
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
            $hotel_model = new HotelModel();
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

    function updateHotel(Request $request, Response $response, array $args) {
        $hotel_model = new HotelModel();
        $data = $request->getParsedBody();
        $arr = array(); // creating empty arrays
    
        for ($i = 0; $i < count($data); $i++) {
            $single_hotel = $data[$i];
            $hotel_id = $single_hotel["hotel_id"];
            $name = $single_hotel["name"];
            $charging_station = $single_hotel["charging_station"];
            $street = $single_hotel["Street"];
            $price_min = $single_hotel["price_min"];
            $price_max = $single_hotel["price_max"];
            $accessibility = $single_hotel["accessibility"];
            
            array_push($arr, "The resource for hotel id : ".$hotel_id. " has been modified");
            $hotel_model->updateHotel($hotel_id, $name, $charging_station, $street, $price_min, $price_max, $accessibility);
        }
    
        $response_data = json_encode($arr);
        $response->getBody()->write($response_data);
        return $response;
       // return $response;
    }
   
    function createHotel(Request $request, Response $response, array $args) {
        $hotel_model = new HotelModel();
        $parsed_data = $request->getParsedBody();
        $arr = array(); // creating empty arrays
        $data_string = "";
        $hotel_id = "";
        $name =  "";
        $charging_station =  "";
        $street =  "";
        $price_min =  "";
        $price_max =  "";
        $accessibility =  "";

        for ($i = 0; $i < count($parsed_data); $i++) {
            $single_hotel = $parsed_data[$i];

            $hotel_id = $single_hotel["hotel_id"];
            $name =  $single_hotel["name"];
            $charging_station = $single_hotel["charging_station"];
            $street =  $single_hotel["street"];
            $price_min =  $single_hotel["price_min"];
            $price_max =  $single_hotel["price_max"];
            $accessibility = $single_hotel["accessibility"];
            
            /**if (isset($location_id)) {
                // This will check if the id already exist in the table
                $id = $artist_model->check($artist_id);
                if ($id != null) {
                    // If matches are found
                    $response_data = makeCustomJSONError("resourceNotFound", "The Artist ID ".$artist_id." already exists, please enter another id");
                    $response->getBody()->write($response_data);
                    return $response->withStatus(HTTP_NOT_FOUND);
                }
            }*/
    
            $hotel_record = array("hotel_id" => $hotel_id, "name" => $name, "charging_station" => $charging_station, "price_min" => $price_min, "Street" => $street, "price_max" => $price_max, "accessibility" => $accessibility);
            array_push($arr, "hotel_id : ".$hotel_id. " is created");
            $hotel_model->createHotel($hotel_record);
        }
        //$response->getBody()->write($artist_id.$artistName);
        $response_data = json_encode($arr);
        $response->getBody()->write($response_data);
        return $response;
    }

   
    private function parseHotelFilters(array $query_params): array
    {
        $name = $query_params['name'] ?? false;
        $charging_station = $query_params['charging_station'] ?? false;
        $street = $query_params['street'] ?? false;
        $price_max = $query_params['price_max'] ?? false;
        $price_min = $query_params['price_min'] ?? false;
        $accessibility = $query_params['accessibility'] ?? false;

        $ret = [];
        if ($name !== false) {
            $ret['name'] = "%$name%";
        }
        if ($street !== false) {
            $ret['street'] = "%$street%";
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
}