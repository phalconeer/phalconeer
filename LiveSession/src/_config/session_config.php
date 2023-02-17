<?php
use Phalconeer\LiveSession as This;

return [
    This\Factory::MODULE_NAME           => [
        'sessionDuration'       => 3000, // 50 * 60 seconds
        'sessionIdLength'       => 24,
    ]
];