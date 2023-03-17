<?php
namespace Phalconeer\ExceptionListener\Helper;

/**
 * Exception helper class.
 *
 * All 'general' class errors must be between 1001 and 1999. All classes must be between 20xx and 99xx. A new error
 * class must start from the next hundred plus one (e.g.: if the last class occupied 21xx, then the first error code in
 * the new class should be 2201).
 *
 */
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


    const E_GENERAL__UNKNOWN_ERROR          = 1001;
    const E_GENERAL__NOT_IMPLEMENTED        = 1002;
    const E_GENERAL__UNSUPPORTED_MEDIA_TYPE = 1003;
    const E_GENERAL__NOT_VALID_JSON         = 1004;
    const E_GENERAL__TOO_MANY_REQUESTS      = 1005;
    const E_GENERAL__NOT_FOUND              = 1006;

    const E_AUTH__USER_ID_MISMATCH  = 1;
    const E_AUTH__UNAUTHORIZED      = 2;
    const E_AUTH__FORBIDDEN         = 3;
    const E_AUTH__NO_GRANT_TYPE     = 4;
    const E_AUTH__INVALID_SCOPE     = 5;

    const E_AUTH__INVALID_TOKEN     = 6;
    const E_AUTH__MISSING_SCOPE     = 7;
    const E_AUTH__MISSING_CREDENTIALS = 8;

    const E_ACTION__FORBIDDEN                 = 2201;
    const E_ACTION__FEATURE_UNAVAILABLE       = 2202;
    const E_ACTION__DAILY_LIMIT_REACHED       = 2204;
    const E_ACTION__ALREADY_PERFORMED         = 2205;
    const E_ACTION__TOO_MANY_REQUESTS         = 2206;

    const VALIDATE_HEADER__MISSING_AUTH = 2301;
    const VALIDATE_REQUEST__BAD_REQUEST = 2302;

    const VALIDATE_TEAM__COMPETITION_CLOSED          = 2401;
    const VALIDATE_TEAM__BUDGET_OVERFLOW             = 2402;
    const VALIDATE_TEAM__MISSING_CAPTAIN             = 2403;
    const VALIDATE_TEAM__TOO_MANY_CAPTAINS           = 2404;
    const VALIDATE_TEAM__INVALID_FORMATION         = 2405;
    const VALIDATE_TEAM__INVALID_PLAYER              = 2406;
    const VALIDATE_TEAM__INVALID_SUB_CAPTAIN         = 2407;
    const VALIDATE_TEAM__EMPTY_POSITION              = 2408;
    const VALIDATE_TEAM__BOOST_NOT_ALLOWED           = 2409;
    const VALIDATE_TEAM__COMPETITION_IS_FULL         = 2410;
    const VALIDATE_TEAM__TEAM_NOT_FOUND              = 2411;

    const VALIDATE_BOOST__MISSING_PROTECTED_PLAYER   = 2501;
    const VALIDATE_BOOST__TOO_MANY_PROTECTED_PLAYER  = 2502;
    const VALIDATE_BOOST__MISSING_ALTERNATE_CAPTAIN  = 2503;
    const VALIDATE_BOOST__TOO_MANY_ALTERNATE_CAPTAIN = 2504;
    const VALIDATE_BOOST__MISSING_SUB_CAPTAIN        = 2505;

    const PAYMENT_NOT_ALLOWED = 3001;

    const ACCOUNTING__INVALID_TRANSACTION_TYPE_FOR_DISPLAY_NAME = 3101;
    const ACCOUNTING__INSUFFICIENT_FUNDS = 3102;

    const SQL_HELPER_INVALID_PARAMETER              = 12;
}
