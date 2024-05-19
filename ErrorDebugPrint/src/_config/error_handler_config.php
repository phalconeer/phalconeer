<?php
use Phalconeer\ErrorDebugPrint as This;

return [
    'errorHandler'        => [
        'handlers'              => [
            This\Factory::MODULE_NAME       => []
        ],
    ],
];