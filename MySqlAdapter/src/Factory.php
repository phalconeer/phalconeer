<?php
namespace Phalconeer\MySqlAdapter;

use Phalcon\Db\Adapter;
use Phalconeer\Bootstrap;
use Phalconeer\Config;
use Phalconeer\Dao;
use Phalconeer\MySqlAdapter as This;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'mysqlAdapter';
    
    protected static $connectionCache = [];

    protected static array $requiredModules = [
        Config\Factory::MODULE_NAME,
    ];

    /**
     * Configures the Bootstrap module
     * @return \Phalcon\Mvc\Dispatcher
     */
    protected function configure() {
        $config = $this->di->get(Config\Factory::MODULE_NAME)->get(static::MODULE_NAME);
        $connectionParameters = $this->di->get(Config\Factory::MODULE_NAME)->database->mysql;
        return function (
            $connectionType = null,
            $readOnly = true,
            $forceNew = false,
            $updateCache = true
        ) use ($connectionParameters, $config) {
            if (is_null($connectionType)) {
                $connectionType = $config->get('defaultConnection', 'default');
            }
            $cacheKey = $connectionType . '-' . (($readOnly) ? '1' : '0');
            if (!$connectionParameters->offsetExists($connectionType)) {
                throw new This\Exception\SqlConfigurationNotFoundException($connectionType);
            }
            if (!$forceNew
                && array_key_exists($cacheKey, self::$connectionCache)) {
                return self::$connectionCache[$cacheKey];
            }
            try {
                $rw = new Adapter\Pdo\MySql(
                    array_merge_recursive(
                        $connectionParameters->get($connectionType, Config\Helper\ConfigHelper::$dummyConfig)->toArray(),
                        $config->get('connectionOptions', Config\Helper\ConfigHelper::$dummyConfig)->toArray()
                    )
                );
                $roName = $connectionType . '_ro';
                $ro = $connectionParameters->offsetExists($roName)
                    ? new Adapter\Pdo\Mysql(
                    array_merge_recursive(
                        $connectionParameters->get($roName, Config\Helper\ConfigHelper::$dummyConfig)->toArray(),
                        $config->get('connectionOptions', Config\Helper\ConfigHelper::$dummyConfig)->toArray()
                    )
                )
                    : $rw;
                $connection = [
                    Dao\Helper\DaoHelper::CONNECTION_TYPE_READ_WRITE => ($readOnly) ? $ro : $rw,
                    Dao\Helper\DaoHelper::CONNECTION_TYPE_READ_ONLY => $ro
                ];
                if ($updateCache) {
                    self::$connectionCache[$cacheKey] = $connection;
                }
                return $connection;
            } catch (\PDOException $ex) {
                throw new This\Exception\InvalidSqConnectionParameterException($connectionType);
            }
        };
    }
}