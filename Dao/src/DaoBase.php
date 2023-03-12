<?php
namespace Phalconeer\Dao;

use Phalcon\Db;
use Phalcon\Support\Helper\Str;
use Phalconeer\Dao as This;

abstract class DaoBase
{
    /**
     * The called class's name without the namespace.
     */
    protected string $calledClassName;

    public function __construct(
        protected array $connections
    )
    {
        $this->setCalledClassName();
        $this->connections = $connections;
    }

    /**
     * Returns a database connection.
     */
    protected function getConnection(
        string $type = This\Helper\DaoHelper::CONNECTION_TYPE_READ_ONLY
    ) : Db\Adapter\Pdo\AbstractPdo
    {
        if (!array_key_exists($type, $this->connections)) {
            throw new This\Exception\UndefinedConnectionException(
                get_class($this) . ' - ' . $type,
                This\Helper\ExceptionHelper::DAO__CONNECTION_NOT_CONFIGURED
            );
        }
        return $this->connections[$type];
    }

    /**
     * Sets the calledClassName class variable
     */
    private function setCalledClassName(string $calledClassName = null)
    {
        if (is_null($calledClassName)) {
            $calledClassName = substr(static::class, strrpos(static::class, '\\') + 1);
        }
        $this->calledClassName = $calledClassName;
    }

    protected function getResultObject(
        bool | array $result,
        bool $camelize = true
    ) : ?\ArrayObject
    {
        if ($result === false
            || !is_array($result)) {
            return null;
        }
        if (!$camelize) {
            return new \ArrayObject($result);
        }
        $camelizer = new Str\Camelize();
        $camelizedResult = array_reduce(
            array_keys($result),
            function (array $aggregate, string $key) use ($camelizer, $result) {
                $aggregate[lcfirst($camelizer($key))] = $result[$key];
                return $aggregate;
            },
            []
        );

        return new \ArrayObject($camelizedResult);
    }

    protected function getResultObjectSet(
        bool | array $result,
        bool $camelize = true
    ) : ?\ArrayObject
    {
        if ($result === false
            || !is_array($result)) {
            return null;
        }

        return new \ArrayObject(
            array_map(
                function ($resultItem) use ($camelize) {
                    return $this->getResultObject($resultItem, $camelize);
                },
                $result
            )
        );
    }
}
