<?php
namespace Phalconeer\MySqlAdapter\Helper;

/**
 * Module exception code: 11
 */
class ExceptionHelper
{
    //SqlQueryHelper
    const MYSQL_ADAPTER__FIELDNAME_NOT_STRING                   = 110100001;
    const MYSQL_ADAPTER__FIELDNAME_CONTAINS_UNDERSCORE          = 110100002;
    const MYSQL_ADAPTER__BETWEEN_REQUIRES_TWO_PARAMETERS        = 110100003;
    const MYSQL_ADAPTER__OBJECT_PASSED_AS_VALUE                 = 110100004;
    const MYSQL_ADAPTER__PARAMETER_VALUE_NOT_SET                = 110100005;
    const MYSQL_ADAPTER__DATA_OBJECT_CAN_NOT_BE_PARSED          = 110100006;

    const MYSQL_ADAPTER__DAO_CAN_NOT_START_TRANSACTION          = 110200001;

    const MYSQL_EXECUTION_FAILED                                = 110300001;
}