<?php

namespace app\controllers;

use app\exceptions\HttpUnprocessableEntityException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use app\models\AttractionModel;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Exception\HttpNotFoundException;
use GuzzleHttp\Client;
use app\config\APIKeys;

class AttractionController
{
    // Route: /attractions
    /**
     * This function gets all the attrations
     * This function filters the data based on price min, price max, charging stations and parking
     * This function supports pagination
     */
    public function getAttractions(Request $request, Response $response, array $args): Response
    {
        $query_params = $request->getQueryParams();
        $filters = $this->parseAttractionFilters($query_params);
        $page_num = $query_params['page'] ?? '1';
        $page_size = $query_params['page_size'] ?? '4';

        if(!ctype_digit($page_num) || intval($page_num) < 1) {
            throw new HttpUnprocessableEntityException($request, 'Page Number should be an integer > 0!');
        } else if (!ctype_digit($page_size) || intval($page_size) < 1) {
            throw new HttpUnprocessableEntityException($request, 'Page Size should be an integer > 0!');
        }

        try {
            $attraction_model = new AttractionModel();
            $result = $attraction_model->getAllAttractions($filters, $page_num, $page_size);
            $response->getBody()->write(json_encode($result));
            return $response;
        } catch (\Throwable $th) {
            throw new HttpInternalServerErrorException($request, 'Something broke!', $th);
        }
    }

    // Route: /attractions/{attraction_id}
    /**
     * This function gets data from a specific Attraction
     */
    public function getAttraction(Request $request, Response $response, array $args): Response
    {
        $attraction_id = intval($args['attraction_id']);
        if ($attraction_id < 1) {
            throw new HttpUnprocessableEntityException($request, 'Attraction ID is not valid!');
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

    // Route: /attractions/{attraction_id}
    /**
     * This function deletes a specific Attraction
     */
    public function deleteAttraction(Request $request, Response $response, array $args): Response
    {
        $attraction_id = intval($args['attraction_id']);
        if ($attraction_id < 1) {
            throw new HttpUnprocessableEntityException($request, 'Hotel ID is not valid!');
        }

        $result = [];
        try {
            $attraction_model = new AttractionModel();
            $result =  $attraction_model->deleteAttraction($attraction_id);
        } catch (\Throwable $th) {
            throw new HttpInternalServerErrorException($request, 'Something broke!', $th);
        }

        if (empty($result)) {
            throw new HttpNotFoundException($request, "Location with ID $attraction_id not found!");
        }

        $response->getBody()->write(json_encode($result));
        return $response;
    }

    // Route: /attractions
    /**
     * This function can update ine or multiple Attractions 
     */
    function updateAttraction(Request $request, Response $response, array $args) {
        $attraction_model = new AttractionModel();
        $data = $request->getParsedBody();
        $arr = array(); // creating empty arrays
    
        for ($i = 0; $i < count($data); $i++) {
            $single_attraction = $data[$i];
            if (empty($single_attraction))
            {
                throw new HttpUnprocessableEntityException($request, "Your data is empty");
            }
            $attraction_id = $single_attraction["attraction_id"];
            $name = $single_attraction["name"];
            $charging_station = $single_attraction["charging_station"];
            $street = $single_attraction["street"];
            $price_min = $single_attraction["price_min"];
            $price_max = $single_attraction["price_max"];
            $parking = $single_attraction["parking"];

            if (!is_string($name) || empty($name)) {
                throw new HttpUnprocessableEntityException($request, "Error in name input");
            }

            if ($parking == "1") {
                //
            } else if ($parking == "0") {
                //
            } else {
                throw new HttpUnprocessableEntityException($request, "Error in parking input");
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
    
            if ((!is_numeric($price_max)) || empty($price_max)) {
                throw new HttpUnprocessableEntityException($request, "Error in price maximun input");
            }
        }

        for ($i = 0; $i < count($data); $i++) {
            $single_attraction = $data[$i];
            $attraction_id = $single_attraction["attraction_id"];
            $name = $single_attraction["name"];
            $charging_station = $single_attraction["charging_station"];
            $street = $single_attraction["street"];
            $price_min = $single_attraction["price_min"];
            $price_max = $single_attraction["price_max"];
            $parking = $single_attraction["parking"];
            
            array_push($arr, "The resource for attraction id : ".$attraction_id. " has been modified");
            $attraction_model->updateAttraction($attraction_id, $name,  $charging_station, $street, $price_min, $price_max, $parking);
        }
    
        $response_data = json_encode($arr);
        $response->getBody()->write($response_data);
        return $response;
       // return $response;
    }
   
    // Route: /attractions
    /**
     * This function creates one or multiple Attrations
     */
    function createAttraction(Request $request, Response $response, array $args) {
        $attraction_model = new AttractionModel();
        $parsed_data = $request->getParsedBody();
        $arr = array(); // creating empty arrays
        $data_string = "";
        $name = "";
        $charging_station = "";
        $street = "";
        $price_min = "";
        $price_max = "";
        $parking = "";
        $attraction_id = "";
        $location_fk = "";

        for ($i = 0; $i < count($parsed_data); $i++) {
            $single_attraction = $parsed_data[$i];
            if (empty($single_attraction))
            {
                throw new HttpUnprocessableEntityException($request, "Your data is empty");
            }
            $attraction_id = $single_attraction["attraction_id"];
            $name = $single_attraction["name"];
            $charging_station = $single_attraction["charging_station"];
            $street = $single_attraction["street"];
            $price_min = $single_attraction["price_min"];
            $price_max = $single_attraction["price_max"];
            $parking = $single_attraction["parking"];
            $location_fk = $single_attraction["location_fk"];
            
            if (!is_string($name) || empty($name)) {
                throw new HttpUnprocessableEntityException($request, "Error in name input");
            }
            if ($parking == "1") {
                //
            } else if ($parking == "0") {
                //
            } else {
                throw new HttpUnprocessableEntityException($request, "Error in parking input");
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
    
            if ((!is_numeric($price_max)) || empty($price_max)) {
                throw new HttpUnprocessableEntityException($request, "Error in price maximun input");
            }
            
        }

        for ($i = 0; $i < count($parsed_data); $i++) {
            $single_attraction = $parsed_data[$i];
    
            $attraction_id = $single_attraction["attraction_id"];
            $name = $single_attraction["name"];
            $charging_station = $single_attraction["charging_station"];
            $street = $single_attraction["street"];
            $price_min = $single_attraction["price_min"];
            $price_max = $single_attraction["price_max"];
            $parking = $single_attraction["parking"];
            $location_fk = $single_attraction["location_fk"];
            
            $attraction_record = array("attraction_id" => $attraction_id, "name" => $name, "charging_station" => 
            $charging_station, "name" => $name, "street" => $street, "price_min" => $price_min, "price_max" => $price_max, 
            "parking" => $parking, "location_fk" => $location_fk);
            array_push($arr, "Attraction id : ".$attraction_id. " is created");
            $attraction_model->createAttraction($attraction_record);
        }

        //$response->getBody()->write($artist_id.$artistName);
        $response_data = json_encode($arr);
        $response->getBody()->write($response_data);
        return $response->withStatus(201);
    }

    // Route: /attractions/{attraction_id}/reviews
    /**
     * This function returns a specific Attraction and fetches its reviews
     */
    public function getReviews(Request $request, Response $response, array $args): Response
    {
        $attraction_id = $args['attraction_id'] ?? '';
        $int_attraction_id = intval($attraction_id);

        if (!ctype_digit($attraction_id) || $int_attraction_id < 1) {
            throw new HttpUnprocessableEntityException($request, '`attraction_id` must be an integer > 0');
        }

        try {
            $attraction_model = new AttractionModel();
            $attraction = $attraction_model->getAttractionLocation($int_attraction_id);
           // var_dump($attraction);
        } catch (\Throwable $th) {
            throw new HttpInternalServerErrorException($request, 'Something broke!', $th);
        }

        if (empty($attraction)) {
            throw new HttpNotFoundException($request, "Attraction $attraction_id not found!");
        }

        $client = new Client(['base_uri' => 'https://api.yelp.com/v3/businesses/']);

        $api_key = APIKeys::REVIEWS;
        $term = $attraction['name'];
        $location = $attraction['city'] . ',' . $attraction['country'];

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

    /**
     * This function parses the data returned from the query
     */
    private function parseAttractionFilters(array $query_params): array
    {
        $name = $query_params['name'] ?? false;
        $charging_station = $query_params['charging_station'] ?? false;
        $street = $query_params['street'] ?? false;
        $price_min = $query_params['price_min'] ?? false;
        $price_max = $query_params['price_max'] ?? false;
        $parking = $query_params['parking'] ?? false;
        $ret = [];

        if ($name !== false) {
            $ret['name'] = "%$name%";
        }
        if ($street !== false) {
            $ret['street'] = "%$street%";
        }
        if ($price_min !== false) {
            $ret['price_min'] = $price_min;
        }
        if ($price_max !== false) {
            $ret['price_max'] = $price_max;
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