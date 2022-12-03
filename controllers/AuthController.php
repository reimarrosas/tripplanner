<?php

namespace app\controllers;

use app\config\APIKeys;
use app\exceptions\HttpConflictException;
use app\exceptions\HttpUnprocessableEntityException;
use app\models\UserModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpUnauthorizedException;

class AuthController
{
    // Route: /login
    /**
     * Login route for authentication
     * 
     * Handles the creation of the authentication token based on if the user gives valid credentials
     */
    public function login(Request $request, Response $response, array $args): Response
    {
        $body = $request->getParsedBody();
        $validation = $this->validateAuthBody($body);

        if (!empty($validation)) {
            throw new HttpUnprocessableEntityException($request, $validation);
        }

        try {
            $user_model = new UserModel();
            $user = $user_model->getUserByUsername($body['username']);
        } catch (\Throwable $th) {
            throw new HttpInternalServerErrorException($request, 'Something broke!', $th);
        }

        if (empty($user)) {
            throw new HttpNotFoundException($request, 'User not found!');
        }

        if (!password_verify($body['password'], $user['password'])) {
            throw new HttpUnauthorizedException($request, 'Password invalid!');
        }

        $jwt = $this->generateJWT([
            'user_id' => $user['user_id'],
            'username' => $user['username'],
            'role' => $user['permission']
        ]);

        $response->getBody()->write(json_encode(['message' => 'User successfully logged in!', 'token' => $jwt]));
        return $response;
    }

    // Route: /register
    /**
     * Register route for authentication
     * 
     * Handles the creation of user for use in logging in.
     * Can create a normal user or an admin user based on if an admin auth token is passed
     */
    public function register(Request $request, Response $response, array $args): Response
    {
        $body = $request->getParsedBody();
        $validation = $this->validateAuthBody($body);

        if (!empty($validation)) {
            throw new HttpUnprocessableEntityException($request, $validation);
        }

        try {
            $user_model = new UserModel();
            $user = $user_model->getUserByUsername($body['username']);
        } catch (\Throwable $th) {
            //throw $th;
        }

        if (!empty($user)) {
            throw new HttpConflictException($request, 'User already exists!');
        }

        $bearer = $request->getHeader('Authorization')[0] ?? '';
        $jwt_payload = $this->parseBearerToken($bearer);

        try {
            $user_model->createUser([
                'username' => $body['username'],
                'password' => password_hash($body['password'], PASSWORD_DEFAULT),
                'permission' => $jwt_payload['role']
            ]);
        } catch (\Throwable $th) {
            throw new HttpInternalServerErrorException($request, 'Something broke!', $th);
        }

        $response->getBody()->write(json_encode(['message' => 'User successfully created!']));
        return $response->withStatus(201);
    }

    private function validateAuthBody(mixed $body): string
    {
        if (!is_array($body)) {
            return 'Request body must be a valid JSON object.';
        }

        $username = $body['username'] ?? '';
        $password = $body['password'] ?? '';

        $validation = '';

        if (empty($username) || !is_string($username)) {
            $validation = 'Username must be a non-empty string';
        } else if (empty($password) || !is_string($password)) {
            $validation = 'Password must be a non-empty string';
        }

        return $validation;
    }

    private function parseBearerToken(string $bearer): array
    {
        $jwt = [];

        $token = explode(' ', $bearer)[1] ?? '';

        try {
            $jwt = (array) JWT::decode($token, new Key(APIKeys::SECRET, 'HS256'));
        } catch (\Throwable $th) {
            $jwt['role'] = 'user';
        }

        return $jwt;
    }

    private function generateJWT(array $user_info): string
    {
        $payload = array_merge([
            'iss' => 'localhost/tripplanner',
            'exp' => time() + 3 * 60 * 60
        ], $user_info);

        return JWT::encode($payload, APIKeys::SECRET, 'HS256');
    }
}
