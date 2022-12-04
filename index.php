<?php

use app\controllers\FoodController;
use app\controllers\RestaurantController;
use app\controllers\LocationController;
use app\controllers\HotelController;
use app\controllers\AttractionController;
use app\controllers\AuthController;
use app\controllers\CarRentalController;
use app\controllers\CarController;
use app\controllers\RecommendationController;

use app\exceptions\HttpNotAcceptableException;
use app\exceptions\HttpUnprocessableEntityException;
use app\exceptions\HttpUnsupportedMediaTypeException;

use app\config\APIKeys;

use Slim\Factory\AppFactory;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpUnauthorizedException;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;

require __DIR__ . '/vendor/autoload.php';

//--Step 1) Instantiate App.
$app = AppFactory::create();

//-- Step 2) Add routing middleware.
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

// Middleware that checks if Accept Header is Any or application/json,
// If not, throw exception
// Checks if Content-Type is blank or application/json,
// If not, throw exception
// Forces also the response to be Content-Type: application/json
$app->add(function (Request $req, RequestHandler $handler) {
    $accept = $req->getHeader('accept')[0];

    // IF accept header is not application/json and not any
    if (strpos($accept, 'application/json') === false && strpos($accept, '*/*') === false) {
        throw new HttpNotAcceptableException($req, "Cannot handle Accept Header: $accept");
    }

    $content_type = $req->getHeader('content-type')[0] ?? false;

    // If content-type is neither undefined nor application/json
    if ($content_type !== false && strpos($content_type, 'application/json') === false) {
        throw new HttpUnsupportedMediaTypeException($req, "Cannot handle Content-Type Header: $content_type");
    }

    $body = json_decode($req->getBody(), true);
    $method = $req->getMethod();

    // If Method is PUT or POST and the body is empty/invalid json
    if (in_array($method, ['PUT', 'POST']) && !$body) {
        throw new HttpUnprocessableEntityException($req, 'Request body must be a valid JSON object');
    }

    return $handler->handle($req);
});

/**
 * Authorization middleware
 * 
 * Checks if the token is valid and if the token claim is sufficient for the operation
 */
$app->add(function (Request $request, RequestHandler $handler) {
    $uri = $request->getUri();
    $method = $request->getMethod();

    if (strpos($uri, 'register') !== false || strpos($uri, 'login') !== false) {
        return $handler->handle($request);
    }

    $token = $request->getHeader('Authorization')[0] ?? '';
    $parsed_token = explode(' ', $token)[1] ?? '';

    if ($parsed_token === '') {
        throw new HttpUnprocessableEntityException($request, 'Malformed Authorization token!');
    }

    try {
        $decoded_token = (array) JWT::decode($parsed_token, new Key(APIKeys::SECRET, 'HS256'));
    } catch (ExpiredException | SignatureInvalidException $e) {
        throw new HttpUnauthorizedException($request, 'Token invalid!', $e);
    } catch (\Throwable $e) {
        throw new HttpUnprocessableEntityException($request, 'Cannot parse token!', $e);
    }

    if (in_array($method, ['POST', 'PUT', 'DELETE'])) {
        var_dump($decoded_token);
        $role = $decoded_token['role'] ?? '';
        if ($role != 'admin') {
            throw new HttpForbiddenException($request, 'Insufficient permission!');
        }
    }

    return $handler->handle($request);
});

// Middleware that sets Content-Type to JSON
$app->add(function (Request $request, RequestHandler $handler) {
    return $handler->handle($request)->withHeader('Content-Type', 'application/json;charset=utf-8');
});

//-- Step 3) Add error handling middleware.
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
// Force the error handler to Content-Type: application/json
$errorMiddleware->getDefaultErrorHandler()->forceContentType('application/json');

//-- Step 4)
// TODO: change the name of the sub directory here.
// You also need to change it in .htaccess
$app->setBasePath("/tripplanner");

//-- Step 5)
// TODO: And here we define app routes.

// Define app routes.
$app->get('/hello', function (Request $request, Response $response, $args) {
    $response->getBody()->write(json_encode(["message" => "Hello, World!"]));
    return $response;
});

$app->get('/', function (Request $request, Response $response, array $args) {
    $base_route = 'http://localhost/tripplanner';

    $response->getBody()->write(json_encode([
        'attractions' => "$base_route/attractions",
        'cars' => "$base_route/carrentals/cars",
        'carrentals' => "$base_route/carrentals",
        'food' => "$base_route/restaurants/food",
        'hotels' => "$base_route/hotels",
        'locations' => "$base_route/locations",
        'restaurants' => "$base_route/restaurants",
        'recommendations' => "$base_route/recommendations",
        'climate' => "$base_route/locations/climate",
        'attraction_reviews' => "$base_route/attractions/reviews",
        'hotel_reviews' => "$base_route/hotels/reviews",
        'restaurant_reviews' => "$base_route/restaurants/reviews",
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    return $response;
});

// ROUTES
// FORMAT: [{class_name}::class, '{method_name}']
// Restaurants routes
$app->get('/restaurants', [RestaurantController::class, 'getRestaurants']);
$app->get('/restaurants/{restaurant_id}', [RestaurantController::class, 'getRestaurant']);
$app->post('/restaurants', [RestaurantController::class, 'createRestaurant']);
$app->put('/restaurants', [RestaurantController::class, 'updateRestaurant']);
$app->delete('/restaurants/{restaurant_id}', [RestaurantController::class, 'deleteRestaurant']);
$app->get('/restaurants/{restaurant_id}/reviews', [RestaurantController::class, 'getReviews']);
// Food routes
$app->get('/restaurants/{restaurant_id}/food', [FoodController::class, 'getAllFood']);
$app->get('/restaurants/{restaurant_id}/food/{food_id}', [FoodController::class, 'getSingleFood']);
$app->post('/restaurants/{restaurant_id}/food', [FoodController::class, 'createFood']);
$app->put('/restaurants/{restaurant_id}/food', [FoodController::class, 'updateFood']);
$app->delete('/restaurants/{restaurant_id}/food/{food_id}', [FoodController::class, 'deleteFood']);
// Locations routes
$app->get('/locations', [LocationController::class, 'getLocations']);
$app->get('/locations/{location_id}', [LocationController::class, 'getLocation']);
$app->delete('/locations/{location_id}', [LocationController::class, 'deleteLocation']);
$app->post('/locations', [LocationController::class, 'createLocation']);
$app->put('/locations', [LocationController::class, 'updateLocation']);
$app->get('/locations/{location_id}/climate', [LocationController::class, 'getClimate']);
// Hotels routes
$app->get('/hotels', [HotelController::class, 'getHotels']);
$app->get('/hotels/{hotel_id}', [HotelController::class, 'gethotel']);
$app->delete('/hotels/{hotel_id}', [HotelController::class, 'deleteHotel']);
$app->post('/hotels', [HotelController::class, 'createHotel']);
$app->put('/hotels', [HotelController::class, 'updateHotel']);
$app->get('/hotels/{hotel_id}/reviews', [HotelController::class, 'getReviews']);
// Attractions routes
$app->get('/attractions', [AttractionController::class, 'getAttractions']);
$app->get('/attractions/{attraction_id}', [AttractionController::class, 'getAttraction']);
$app->delete('/attractions/{attraction_id}', [AttractionController::class, 'deleteAttraction']);
$app->post('/attractions', [AttractionController::class, 'createAttraction']);
$app->put('/attractions', [AttractionController::class, 'updateAttraction']);
$app->get('/attractions/{attraction_id}/reviews', [AttractionController::class, 'getReviews']);
// Car Rentals routes
$app->get('/carrentals', [CarRentalController::class, 'getCarRentals']);
$app->get('/carrentals/{car_rental_id}', [CarRentalController::class, 'getCarRental']);
$app->post('/carrentals', [CarRentalController::class, 'createCarRental']);
$app->put('/carrentals', [CarRentalController::class, 'updateCarRental']); 
$app->delete('/carrentals/{car_rental_id}', [CarRentalController::class, 'deleteCarRental']);
// Car routes
$app->get('/carrentals/{car_rental_id}/cars', [CarController::class, 'getCars']);
$app->get('/carrentals/{car_rental_id}/cars/{car_id}', [CarController::class, 'getCar']);
$app->post('/carrentals/cars', [CarController::class, 'createCar']);
$app->put('/carrentals/{car_rental_id}/cars', [CarController::class, 'updateCar']); 
$app->delete('/carrentals/{car_rental_id}/cars/{car_id}', [CarController::class, 'deleteCar']);
// Recommendations
$app->get('/recommendations', [RecommendationController::class, 'recommend']);
$app->get('/recommendations/tags', [RecommendationController::class, 'getRecommendationTags']);
// Auth
$app->post('/login', [AuthController::class, 'login']);
$app->post('/register', [AuthController::class, 'register']);

// Run the app.
$app->run();
