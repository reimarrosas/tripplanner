<?php

namespace app\controllers;

use app\exceptions\HttpUnprocessableEntityException;
use app\models\AttractionModel;
use app\models\HotelModel;
use app\models\RestaurantModel;
use app\models\TagModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpInternalServerErrorException;

class RecommendationController
{
    // Route: /recommendations
    /**
     * Handler for generating recommendations based on the passed comma-separated query param `q`
     */
    public function recommend(Request $request, Response $response, array $args): Response
    {
        $query_params = $request->getQueryParams()['q'] ?? false;
        if (!$query_params) {
            throw new HttpUnprocessableEntityException($request, 'Request must contain a query param `q`');
        }
        $filters = explode(',', $query_params);
        $recommendations = [
            'restaurants' => [],
            'hotels' => [],
            'attractions' => []
        ];
        try {
            $restaurant_model = new RestaurantModel();
            $recommendations['restaurants'] = $restaurant_model->getRestaurantRecommendations($filters);

            $hotel_model = new HotelModel();
            $recommendations['hotels'] = $hotel_model->getHotelRecommendations($filters);

            $attraction_model = new AttractionModel();
            $recommendations['attractions'] = $attraction_model->getAttractionRecommendations($filters);
        } catch (\Throwable $th) {
            throw new HttpInternalServerErrorException($request, 'Something broke!', $th);
        }

        $response->getBody()->write(json_encode($recommendations));
        return $response;
    }

    /**
     * Handler for fetching all the tags available to generate he recommendation
     */
    public function getRecommendationTags(Request $request, Response $response, array $args): Response
    {
        $tags = [];
        try {
            $tag_model = new TagModel();
            $tags = $tag_model->getTags();
        } catch (\Throwable $th) {
            throw new HttpInternalServerErrorException($request, 'Something broke!', $th);
        }

        $response->getBody()->write(json_encode($tags));
        return $response;
    }
}