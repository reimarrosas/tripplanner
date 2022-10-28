<?php

namespace app\exceptions;

use Slim\Exception\HttpSpecializedException;

class HttpUnprocessableEntity extends HttpSpecializedException
{
    protected $code = 422;
    protected $message = 'Unprocessable Entity';
    protected $title = '422 Unprocessable Entity';
    protected $description = 'The request was well-formed but was unable to be followed due to semantic errors.';
}

