<?php
namespace Phalconeer\Dao;

use Phalcon\Db;
use Phalconeer\Dao as This;

abstract class DaoBase
{
    /**
     * The database connections.
     */
    protected array $connections = [
        This\Helper\DaoHelper::CONNECTION_TYPE_READ_ONLY => null
    ];

    /**
     * The called class's name without the namespace.
     */
    protected string $calledClassName;

    /**
     * Returns a database connection.
     */
    protected function getConnection(
        string $type = This\Helper\DaoHelper::CONNECTION_TYPE_READ_ONLY
    ) : Db\Adapter\Pdo\AbstractPdo
    {
        if (!array_key_exists($type, $this->connections)) {
            throw new This\Exception\UndefinedConnectionException(
                $type,
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

    public function __construct(array $connections)
    {
        $this->setCalledClassName();
        $this->connections = $connections;
    }
}
