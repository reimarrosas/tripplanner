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
        try {
            $location_model = new LocationModel();
            if (isset($query_params["name"])) { //filters base on the name
                $result = $location_model->getWhereCountryLike($query_params["country"]);
                $response->getBody()->write(json_encode($result));
            }
            if (isset($query_params["city"])) { //filters based on the price range
                $result = $hotel_model->getWhereCityLike($query_params["city"]);
                $response->getBody()->write(json_encode($result));
            } else {
                $result = $location_model->getAllLocations($filters); // gets all the hotels
                $response->getBody()->write(json_encode($result));
            }
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