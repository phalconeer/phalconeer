<?php
use Phalconeer\Memcache as This;

return [
    This\Factory::MODULE_NAME           => [
        'default'           => [
            'defaultSerializer'         => 'Json',
            'lifetime'                  => 3600,
            'persistent'                => false,
        ]
    ]
];