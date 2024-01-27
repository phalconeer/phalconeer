<?php
namespace Phalconeer\TaskRegistry\Helper;

use Phalconeer\TaskRegistry as This;

class TaskRegistryHelper
{
    const TASK_UNIQUE_ID_LENGTH = 12;

    const STATUS_NEW = 'new';

    const STATUS_PROCESSING = 'processing';

    const STATUS_DONE = 'done';

    const STATUS_ERRORED = 'errored';

    const STATUS_FAILED = 'failed';

    const STATUS_CANCELLED = 'cancelled';

    public static function getServerDetails() : This\Data\TaskEnvironment
    {
        return This\Data\TaskEnvironment::fromArray([
            'productName'       => PRODUCT,
            'server'            => array_key_exists('SERVER_ADDR', $_SERVER)
                ? $_SERVER['SERVER_ADDR']
                : null //TODO: add CLI server addrress
        ]);
    }
}
