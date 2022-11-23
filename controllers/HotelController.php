<?php

namespace app\controllers;

use app\exceptions\HttpUnprocessableEntityException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use app\models\HotelModel;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Exception\HttpNotFoundException;
use GuzzleHttp\Client;
use app\config\APIKeys;


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
            $result = $hotel_model->getAllHotels($filters);
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
            throw new HttpUnprocessableEntityException($request, 'Hotel ID is not valid!');
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
            throw new HttpUnprocessableEntityException($request, 'Hotel ID is not valid!');
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

    // Route: /hotels
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
            $location_fk = $single_hotel["location_fk"];
            
            if (!is_string($name) || empty($name)) {
                throw new HttpUnprocessableEntityException($request, "Error in name input");
            }
            if ($charging_station == "1") {
                //
            } else if($charging_station == "0") {
                //
            } else {
                throw new HttpUnprocessableEntityException($request, "Error in charging input");
            }
            if (!is_string($street) || empty($street)) {
                throw new HttpUnprocessableEntityException($request, "Error in street input");
            }
            if ((!is_numeric($price_min)) || empty($price_min)) {
                throw new HttpUnprocessableEntityException($request, "Error in price mininum input");
            }
            if ($accessibility == "walking") {
                //
            } else if ($accessibility == "car") {
                //
            } else if ($accessibility == "public") {
                //
            } else {
                throw new HttpUnprocessableEntityException($request, "Error in accessibility input");
            }
            if ((!is_numeric($price_min)) || empty($price_max)) {
                throw new HttpUnprocessableEntityException($request, "Error in price maximun input");
            }
        }

        for ($i = 0; $i < count($data); $i++) {
            $single_hotel = $data[$i];
            $hotel_id = $single_hotel["hotel_id"];
            $name = $single_hotel["name"];
            $charging_station = $single_hotel["charging_station"];
            $street = $single_hotel["Street"];
            $price_min = $single_hotel["price_min"];
            $price_max = $single_hotel["price_max"];
            $accessibility = $single_hotel["accessibility"];
            $location_fk = $single_hotel["location_fk"];
            
            array_push($arr, "The resource for hotel id : ".$hotel_id. " has been modified");
            $hotel_model->updateHotel($hotel_id, $name, $charging_station, $street, $price_min, $price_max, $accessibility, $location_fk);
        }
    
        $response_data = json_encode($arr);
        $response->getBody()->write($response_data);
        return $response;
       // return $response;
    }
   
    // Route: /hotels
    function createHotel(Request $request, Response $response, array $args) {
        $hotel_model = new HotelModel();
        $parsed_data = $request->getParsedBody();
        $arr = array(); // creating empty arrays
        $data_string = "";
        $hotel_id = "";
        $hotel_fk = "";
        $name =  "";
        $charging_station =  "";
        $street =  "";
        $price_min =  "";
        $price_max =  "";
        $accessibility =  "";

        for ($i = 0; $i < count($parsed_data); $i++) {
            $single_hotel = $parsed_data[$i];
            $hotel_id = $single_hotel["hotel_id"];
            $location_fk = $single_hotel["location_fk"];
            $name =  $single_hotel["name"];
            $charging_station = $single_hotel["charging_station"];
            $street =  $single_hotel["Street"];
            $price_min =  $single_hotel["price_min"];
            $price_max =  $single_hotel["price_max"];
            $accessibility = $single_hotel["accessibility"];
            
            if (!is_string($name) || empty($name)) {
                throw new HttpUnprocessableEntityException($request, "Error in name input");
            }
            if ($charging_station == "1") {
                //
            } else if($charging_station == "0") {  
                //
            } else {
                throw new HttpUnprocessableEntityException($request, "Error in charging input");
            }
            if (!is_string($street) || empty($street)) {
                throw new HttpUnprocessableEntityException($request, "Error in street input");
            }
            if ((!is_numeric($price_min)) || empty($price_min)) {
                throw new HttpUnprocessableEntityException($request, "Error in price mininum input");
            }
            if (!is_string($accessibility)) {
                throw new HttpUnprocessableEntityException($request, "Error in accessibility input");
            } else if ($accessibility == "walking") {
                //
            } else if ($accessibility == "car") {
                //
            }  else if ($accessibility == "public") {
                //
            } else {
                throw new HttpUnprocessableEntityException($request, "Error in accessibility input");
            }
            if ((!is_numeric($price_min)) || empty($price_max)) {
                throw new HttpUnprocessableEntityException($request, "Error in price maximun input");
            }

        }

        for ($i = 0; $i < count($parsed_data); $i++) {
            $single_hotel = $parsed_data[$i];
            $hotel_id = $single_hotel["hotel_id"];
            $location_fk = $single_hotel["location_fk"];
            $name =  $single_hotel["name"];
            $charging_station = $single_hotel["charging_station"];
            $street =  $single_hotel["Street"];
            $price_min =  $single_hotel["price_min"];
            $price_max =  $single_hotel["price_max"];
            $accessibility = $single_hotel["accessibility"];
            
            $hotel_record = array("hotel_id" => $hotel_id, "name" => $name, "charging_station" => $charging_station, "price_min" => $price_min, "Street" => $street, "price_max" => $price_max, "accessibility" => $accessibility, "location_fk" => $location_fk);
            array_push($arr, "hotel_id : ".$hotel_id. " is created");
            $hotel_model->createHotel($hotel_record);
        }
        
        $response_data = json_encode($arr);
        $response->getBody()->write($response_data);
        return $response->withStatus(201);
    }

    // Route: /hotels/{hotel_id}/reviews
    public function getReviews(Request $request, Response $response, array $args): Response
    {
        $hotel_id = $args['hotel_id'] ?? '';
        $int_hotel_id = intval($hotel_id);

        if (!ctype_digit($hotel_id) || $int_hotel_id < 1) {
            throw new HttpUnprocessableEntityException($request, '`hotel_id` must be an integer > 0');
        }

        $restaurant = [];
        try {
            $hotel_model = new HotelModel();
            $hotel = $hotel_model->getSingleHotel($int_hotel_id);
           // var_dump($attraction);
        } catch (\Throwable $th) {
            throw new HttpInternalServerErrorException($request, 'Something broke!', $th);
        }

        if (empty($hotel)) {
            throw new HttpNotFoundException($request, "Hotel $hotel_id not found!");
        }

        $client = new Client(['base_uri' => 'https://api.yelp.com/v3/businesses/']);

        $api_key = APIKeys::REVIEWS;
        $term = $hotel['name'];
        $location = $hotel['Street'];

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