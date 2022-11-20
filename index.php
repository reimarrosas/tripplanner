<?php

use app\controllers\FoodController;
use app\controllers\RestaurantController;
use app\controllers\LocationController;
use app\controllers\HotelController;
use app\controllers\AttractionController;
use app\controllers\CarRentalController;
use app\controllers\CarController;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
//var_dump($_SERVER["REQUEST_METHOD"]);
use Slim\Factory\AppFactory;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use app\exceptions\HttpNotAcceptableException;

require __DIR__ . '/vendor/autoload.php';

//--Step 1) Instantiate App.
$app = AppFactory::create();

//-- Step 2) Add routing middleware.
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

// Middleware that checks if Accept Header is Any or application/json,
// If not, throw exception
// Forces also the response to be Content-Type: application/json
$app->add(function (Request $req, RequestHandler $handler) {
    $accept = $req->getHeader('accept')[0];

    // IF accept header is not application/json and not any
    if (strpos($accept, 'application/json') === false && strpos($accept, '*/*') === false) {
        throw new HttpNotAcceptableException($req, "Cannot handle Accept Header: $accept");
    }

    $res = $handler->handle($req);

    return $res->withAddedHeader('Content-Type', 'application/json');
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

// ROUTES
// FORMAT: [{class_name}::class, '{method_name}']
// Restaurants routes
$app->get('/restaurants', [RestaurantController::class, 'getRestaurants']);
$app->get('/restaurants/{restaurant_id}', [RestaurantController::class, 'getRestaurant']);
$app->post('/restaurants', [RestaurantController::class, 'createRestaurant']);
$app->put('/restaurants/{restaurant_id}', [RestaurantController::class, 'updateRestaurant']);
$app->delete('/restaurants/{restaurant_id}', [RestaurantController::class, 'deleteRestaurant']);
// Food routes
$app->get('/restaurants/{restaurant_id}/food', [FoodController::class, 'getAllFood']);
$app->get('/restaurants/{restaurant_id}/food/{food_id}', [FoodController::class, 'getSingleFood']);
$app->post('/restaurants/{restaurant_id}/food', [FoodController::class, 'createFood']);
$app->put('/restaurants/{restaurant_id}/food/{food_id}', [FoodController::class, 'updateFood']);
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
// Attractions routes
$app->get('/attractions', [AttractionController::class, 'getAttractions']);
$app->get('/attractions/{attraction_id}', [AttractionController::class, 'getAttraction']);
$app->delete('/attractions/{attraction_id}', [AttractionController::class, 'deleteAttraction']);
$app->post('/attractions', [AttractionController::class, 'createAttraction']);
$app->put('/attractions', [AttractionController::class, 'updateAttraction']);
// Car Rentals routes
$app->get('/carrentals', [CarRentalController::class, 'getCarRentals']);
$app->get('/carrentals/{car_rental_id}', [CarRentalController::class, 'getCarRental']);
$app->post('/carrentals', [CarRentalController::class, 'createCarRental']);
$app->put('/carrentals/{car_rental_id}', [CarRentalController::class, 'updateCarRental']);
$app->delete('/carrentals/{car_rental_id}', [CarRentalController::class, 'deleteCarRental']);
// Car routes
$app->get('/carrentals/{car_rental_id}/cars', [CarController::class, 'getCars']);
$app->get('/carrentals/{car_rental_id}/cars/{car_id}', [CarController::class, 'getCar']);
$app->post('/carrentals/cars', [CarController::class, 'createCar']);
$app->put('/carrentals/{car_rental_id}/cars/{car_id}', [CarController::class, 'updateCar']);
$app->delete('/carrentals/{car_rental_id}/cars/{car_id}', [CarController::class, 'deleteCar']);

// Run the app.
$app->run();
