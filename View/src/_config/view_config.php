<?php
use \Phalcon\Mvc\View\Engine\Php as PhtmlParser;

return [
    'view'  => [
        'viewsDir'      => '/set/me',
        'engines'       => [
            '.phtml' => PhtmlParser::class
        ]
    ]
];