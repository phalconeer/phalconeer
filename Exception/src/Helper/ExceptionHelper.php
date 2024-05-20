<?php
namespace Phalconeer\Exception\Helper;

class ExceptionHelper
{
//  All 'general' class errors must be between 1001 and 1999. All classes must be between 20xx and 99xx. A new error
//  class must start from the next hundred plus one (e.g.: if the last class occupied 21xx, then the first error code in
//  the new class should be 2201).

    //0YXXXX - system
    //  - 00XXXX - invalid argument
    //  - 001XXX - route not found
    //  - 002XXX - file not found
    //  - 003XXX - class not found
    //  - 004XXX - module not found
    //1YXXXX - authentication
    //  - 10XXXX - token
    //  - 11XXXX - scope
    //  - 12XXXX - user
    //2YXXXX - validation
    //  - 20XXXX - request header
    //  - 21XXXX - request body validation

    //0YYXXX -  system
    //  - 001XXX - invalid argument
    //  - 001XXX - route not found
    //  - 002XXX - file not found
    //  - 003XXX - class not found
    //  - 004XXX - module not found
    //  - ...
    //1YYXXX - authentication
    //  - 101XXX - user (missing credentials, user id missmatch)
    //  - 102XXX - token (missing, invalid etc.)
    //  - 103XXX - scope (missing, invalid etc.)
    //  - ...
    //2YYXXX - validation
    //  - 201XXX - Generic request header errors (common layer)
    //  - 202XXX - Generic business validation (teams, competitions etc...)
    //  - 203XXX - Generic business validation (teams, competitions etc...)
    //  - 204XXX - Request body validation
    //  - 205XXX - Request validation, slot is empty, here can be played application specific errorCodes by extending this class
    //  - 206XXX - Request validation, slot is empty, here can be played application specific errorCodes by extending this class
    //  - ...
    //4YYXXX - payment
    //  - 401XXX - not enough credit/dolbar/etc...
    //  - ...

    // 10ZZYYXXXX - module exceptions

    const E_GENERAL__UNKNOWN_ERROR          = 1;
    const E_GENERAL__NOT_IMPLEMENTED        = 2;
    const E_GENERAL__UNSUPPORTED_MEDIA_TYPE = 3;
    const E_GENERAL__NOT_VALID_JSON         = 4;
    const E_GENERAL__TOO_MANY_REQUESTS      = 5;
    const E_GENERAL__NOT_FOUND              = 6;

    //Bootstrap
    const INVLIAD_CONFIG_FILE_CONTENT       = 100001;

    const CONFIG_FILE_NOT_FOUND             = 102001;

    const CLASS_NOT_FOUND                   = 103001;
    const AUTOLOADER_NOT_CONFIGURED         = 103002;
    const CLASS_FAILED_TO_INITIALIZE        = 103003;
    const FILE_NOT_FOUND                    = 103004;
    const INVALID_NAMESPACE                 = 103005;
    
    const MODULE_NOT_LOADED                 = 104001;


}
