<?php
use Phalconeer\MySqlAdapter as This;

return [
    This\Factory::MODULE_NAME   => [
        'defaultConnection'       => 'default',
        'connectionOptions'     => [
            \PDO::ATTR_STRINGIFY_FETCHES     => false,
            \PDO::ATTR_ERRMODE               => \PDO::ERRMODE_EXCEPTION
        ]
    ]
];