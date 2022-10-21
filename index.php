<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
//var_dump($_SERVER["REQUEST_METHOD"]);
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';

//--Step 1) Instantiate App.
$app = AppFactory::create();

//-- Step 2) Add routing middleware.
$app->addRoutingMiddleware();

//-- Step 3) Add error handling middleware.
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

//-- Step 4)
// TODO: change the name of the sub directory here.
// You also need to change it in .htaccess
$app->setBasePath("/tripplanner");

//-- Step 5)
// TODO: And here we define app routes.

// Define app routes.
$app->get('/hello', function (Request $request, Response $response, $args) {    
    $response->getBody()->write("Hello!");
    return $response;
});

// Run the app.
$app->run();
