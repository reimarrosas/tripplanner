<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
//var_dump($_SERVER["REQUEST_METHOD"]);
use Slim\Factory\AppFactory;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

require __DIR__ . '/vendor/autoload.php';

//--Step 1) Instantiate App.
$app = AppFactory::create();

//-- Step 2) Add routing middleware.
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

// Run the app.
$app->run();
