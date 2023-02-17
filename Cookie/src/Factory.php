<?php
namespace Phalconeer\Cookie;

use Phalcon\Http\Response\Cookies;
use Phalconeer\Bootstrap;
use Phalconeer\Config;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'cookies';
    
    protected static $requiredModules = [
        Config\Factory::MODULE_NAME,
    ];

    protected static $configFiles = [
        __DIR__ . '/_config/cookie_config.php'
    ];

    // $cookie  = new Cookie(
    //     'my-cookie',                   // name
    //     1234,                          // value
    //     time() + 86400,                // expires
    //     "/",                           // path
    //     true,                          // secure
    //     ".phalcon.io",                 // domain
    //     true,                          // httponly
    //     [                              // options
    //         "samesite" => "Strict",    // 
    //     ]                              // 
    // );

    protected function configure()
    {
        $config = $this->di->get(Config\Factory::MODULE_NAME)->get('coookies', Config\Helper\ConfigHelper::$dummyConfig);
        $cookies  = new Cookies();

        $cookies->useEncryption($config->get('useEncryption', true));

        return $cookies;
    }
}