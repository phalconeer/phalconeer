<?php
use Phalconeer\AuthenticateBearer as This;

return [
    'exceptionDescriptors'          => [
        This\Helper\ExceptionHelper::AUTHENTICATE__SESSION_NOT_FOUND
            => ['statusCode' => 401, 'message' => 'Authentication required'],
        This\Helper\ExceptionHelper::AUTHENTICATE__SESSION_EXPIRED
            => ['statusCode' => 401, 'message' => 'Authentication required'],
    ]
];