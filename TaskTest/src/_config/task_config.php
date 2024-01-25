<?php
use Phalconeer\Task;
use Phalconeer\TaskTest as This;

return [
    Task\Factory::MODULE_NAME        => [
        Task\Helper\TaskHelper::CONFIG_HANDLER       => [
            This\Factory::MODULE_NAME       => [
                'module'               => This\Factory::MODULE_NAME,
                'repeatInterval'        => 15, 
                'parameterClass'        => This\Data\TaskTestWithTraits::class,
            ]
        ]
    ]
];