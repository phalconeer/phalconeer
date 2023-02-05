<?php
use Phalcon\Filter;

return [
    'restRequest' => [
        'defaultPageSize'           => 20,
        'defaultOffset'             => 0,
        'defaultSanitizers'         => [
            Filter\Filter::FILTER_STRING,
        ]
    ]
];