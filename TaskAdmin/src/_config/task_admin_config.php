<?php
use Phalconeer\Task as This;

return [
    This\Factory::MODULE_NAME      => [
        'cleanErroredTasksAfter'        => 180, //3 *60
        'retryCount'                    => 5,
    ]
];