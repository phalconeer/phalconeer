<?php
use Phalconeer\TaskAdmin as This;

return [
    This\Factory::MODULE_NAME      => [
        'cleanErroredTasksAfter'        => 1, //180, //3 *60
        'retryCount'                    => 5,
    ]
];