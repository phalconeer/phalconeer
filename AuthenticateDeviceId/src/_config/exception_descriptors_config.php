<?php
use Phalconeer\AuthenticateDeviceId as This;

return [
    'exceptionDescriptors'          => [
        This\Helper\ExceptionHelper::CREATE_MEMBER_TOKEN__CREDENTIALS_NOT_FOUND
            => ['statusCode' => 401, 'message' => 'Authentication required'],
        This\Helper\ExceptionHelper::CREATE_MEMBER_TOKEN__APPLICATION_NOT_LINKED
            => ['statusCode' => 401, 'message' => 'Authentication required'],
    ]
];