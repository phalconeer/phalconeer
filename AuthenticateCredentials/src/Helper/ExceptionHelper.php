<?php
namespace Phalconeer\AuthenticateCredentials\Helper;

/**
 * Module exception code: 19
 */
class ExceptionHelper
{
    const CREATE_MEMBER_TOKEN__CREDENTIALS_NOT_FOUND        = 190100001;
    const CREATE_MEMBER_TOKEN__APPLICATION_NOT_LINKED       = 190100002;
    const CREATE_MEMBER_TOKEN__USER_NOT_FOUND               = 190100003;

    const CREATE_MEMBER_TOKEN__CREDENTIAL_NOT_UNIQUE        = 190200001;
    const CREATE_MEMBER_TOKEN__CREDENTIAL_EXISTS            = 190200002;

    const CREATE_MEMBER_TOKEN__DAO_NOT_SET                  = 190300001;
}