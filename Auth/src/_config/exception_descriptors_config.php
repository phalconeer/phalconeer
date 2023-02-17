<?php
use Phalconeer\Auth as This;

return [
    'exceptionDescriptors'          => [
        This\Helper\ExceptionHelper::AUTHENTICATION__GENERIC_FAILURE
            => ['statusCode' => 401, 'message' => 'Authentication required'],
        
        This\Helper\ExceptionHelper::AUTHENTICATION__LOGIN_HANDLER_NOT_CALLABLE
            => ['statusCode' => 400, 'message' => 'Invalid login handler passed'],
        This\Helper\ExceptionHelper::AUTHENTICATION__LOGOUT_HANDLER_NOT_CALLABLE
            => ['statusCode' => 400, 'message' => 'Invalid logout handler passed'],
    ]
];