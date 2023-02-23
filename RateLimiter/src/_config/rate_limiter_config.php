<?php
use Phalconeer\RateLimiter as This;
return [
    This\Factory::MODULE_NAME       => [
        'limit'                 => 5,
        'interval'              => 60,
    ]
];