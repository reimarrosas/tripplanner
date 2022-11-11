<?php

namespace app\controllers;

use app\exceptions\HttpUnprocessableEntity;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use app\models\LocationModel;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Exception\HttpNotFoundException;

class LocationController
{
    // Route: /locations
    // TODO: Pagination
    public function getLocations(Request $request, Response $response, array $args): Response
    {
        $query_params = $request->getQueryParams(); 
        $filters = $this->parseLocationFilters($query_params);
        $result = Null;
        try {
            $location_model = new LocationModel();
            if (isset($query_params["country"]) && isset($query_params["city"])) { //filters base on country and city
                $result = $location_model->getWhereCityAndCountryLike($query_params["country"], $query_params["city"]);
            }elseif (isset($query_params["city"])) { //filters based on city
                $result = $location_model->getWhereCityLike($query_params["city"]);
            }elseif (isset($query_params["country"])){ //filters based on country
                $result = $location_model->getWhereCountryLike($query_params["country"]);
            } else {
                $result = $location_model->getAllLocations($filters); // gets all the locations
            }
            $response->getBody()->write(json_encode($result));
            return $response;
        } catch (\Throwable $th) {
            throw new HttpInternalServerErrorException($request, 'Something broke!', $th);
        }
    }

    // Route: /locations/{location_id}
    public function getLocation(Request $request, Response $response, array $args): Response
    {
        $hotel_id = intval($args['location_id']);
        if ($hotel_id < 1) {
            throw new HttpUnprocessableEntity($request, 'Location ID is not valid!');
        }

        $result = [];
        try {
            $location_model = new LocationModel();
            $result =  $location_model->getSingleLocation($location_id);
        } catch (\Throwable $th) {
            throw new HttpInternalServerErrorException($request, 'Something broke!', $th);
        }

        if (empty($result)) {
            throw new HttpNotFoundException($request, "Location with ID $location_id not found!");
        }

        $response->getBody()->write(json_encode($result));
        return $response;
    }

    // Route: /locations/{location_id}
    public function deleteLocation(Request $request, Response $response, array $args): Response
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
    }

    function updateLocation(Request $request, Response $response, array $args) {
        $location_model = new LocationModel();
        $data = $request->getParsedBody();
        $arr = array(); // creating empty arrays
    
        for ($i = 0; $i < count($data); $i++) {
            $single_location = $data[$i];
            $location_id = $single_location["location_id"];
            $country = $single_location["country"];
            $city = $single_location["city"];
            
            /**if (isset($artist_id)) {
                // This will check if the id exists in the table
                $id = $artist_model->check($artist_id);
                if ($id == null) {
                    // If matches are found
                    $response_data = makeCustomJSONError("resourceNotFound", "The Artist ID ".$artist_id." doesn't exist, please enter another id");
                    $response->getBody()->write($response_data);
                    return $response->withStatus(HTTP_NOT_FOUND);
                }
            }*/
    
            array_push($arr, "The resource for location id : ".$location_id. " has been modified");
            echo"hello";
            $location_model->updateLocation2($city, $country, $location_id);
            echo"hello";
        }
    
        $response_data = json_encode($arr);
        $response->getBody()->write($response_data);
        return $response;
       // return $response;
    }
   
    function createLocation(Request $request, Response $response, array $args) {
        $location_model = new LocationModel();
        $parsed_data = $request->getParsedBody();
        $arr = array(); // creating empty arrays
       //var_dump($parsed_data); exit;
        $data_string = "";
        $location_id ="";
        $country = "";
        $city = "";

        for ($i = 0; $i < count($parsed_data); $i++) {
            $single_location = $parsed_data[$i];
    
            $location_id = $single_location["location_id"];
            $country = $single_location["country"];
            $city = $single_location["city"];
            
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
    
            $location_record = array("location_id" => $location_id, "country" => $country, "city" => $city);
            array_push($arr, "Location id : ".$location_id. " is created");
            $location_model->createLocation($location_record);
        }
        //$response->getBody()->write($artist_id.$artistName);
        $response_data = json_encode($arr);
        $response->getBody()->write($response_data);
        return $response;
    }
    
    private function parseLocationFilters(array $query_params): array
    {
        $country = $query_params['country'] ?? false;
        $city = $query_params['city'] ?? false;

        $ret = [];
        if ($country !== false) {
            $ret['country'] = "%$country%";
        }
        if ($city !== false) {
            $ret['city'] = "%$city%";
        }

        return $ret;
    }
}