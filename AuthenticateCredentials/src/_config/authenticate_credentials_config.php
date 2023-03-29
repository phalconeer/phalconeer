<?php
use Phalconeer\AuthenticateCredentials as This;


return [
    This\Factory::MODULE_NAME           => [
        'algorhytm'                 => PASSWORD_BCRYPT,
        'cost'                      => 14,
    ]
];