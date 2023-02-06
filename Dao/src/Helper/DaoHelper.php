<?php
namespace Phalconeer\Dao\Helper;

class DaoHelper
{
    /**
     * Constant for read-write connection type
     */
    const CONNECTION_TYPE_READ_WRITE = 'rw';

    /**
     * Constant for read-only connection type
     */
    const CONNECTION_TYPE_READ_ONLY = 'ro';
    
    /**
     * Constant for normal insert mode
     */
    const INSERT_MODE_NORMAL = 0;

    /**
     * Constant for ignore insert mode
     */
    const INSERT_MODE_IGNORE = 1;

    /**
     * Constant for update insert mode
     */
    const INSERT_MODE_UPDATE = 2;
}