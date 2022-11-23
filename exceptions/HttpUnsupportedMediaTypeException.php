<?php

namespace app\exceptions;

use Slim\Exception\HttpSpecializedException;

class HttpUnsupportedMediaTypeException extends HttpSpecializedException
{
    protected $code = 415;
    protected $message = 'Unsupported Media Type';
    protected $title = '415 Unsupported Media Type';
    protected $description = 'Request payload format is unsupported.';
}