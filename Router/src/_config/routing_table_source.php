<?php
use Phalconeer\Router as This;

return [
    This\Factory::MODULE_NAME => [
        'routingTables'         => [
            APPLICATION_SOURCE_PATH . '/_config/routing_table.php'
        ],
    ]
];
