<?php
namespace Phalconeer\Exception\Helper;

class ExceptionHelper
{
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
