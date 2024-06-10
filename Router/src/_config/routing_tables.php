<?php
use Phalconeer\Router as This;

return [
    This\Factory::MODULE_NAME => [
        'routingTables'         => [
            This\Helper\RouterHelper::getUniqueNamespace(__DIR__) => APPLICATION_SOURCE_PATH . '/_config/routing_table.php'
        ],
    ]
];
