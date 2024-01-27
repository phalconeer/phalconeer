<?php
use Phalconeer\TaskRunner;

return [
    TaskRunner\Factory::MODULE_NAME     => [
        'retryCount'                    => 5,
        'busySleep'                     => 20000,
        'lazySleep'                     => 2000000,
    ]
];