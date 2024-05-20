<?php
use Phalconeer\ErrorLogToAdapter as This;

return [
    'errorHandler'        => [
        'handlers'              => [
            This\Factory::MODULE_NAME       => []
        ],
    ],
];