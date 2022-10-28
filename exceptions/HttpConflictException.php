<?php

namespace app\exceptions;

use Slim\Exception\HttpSpecializedException;

class HttpConflictException extends HttpSpecializedException
{
    protected $code = 409;
    protected $message = 'Conflict';
    protected $title = '409 Conflict';
    protected $description = 'Request conflicts with the current state of the server.';
}
