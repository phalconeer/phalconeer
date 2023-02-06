<?php
namespace Phalconeer\Dao;

use Phalconeer\Dao as This;
use Phalconeer\Data;

interface DaoWriteInterface
{
    public function save(
        Data\DataInterface $data,
        $forceInsert = false,
        $insertMode = This\Helper\DaoHelper::INSERT_MODE_NORMAL
    ) : ?Data\DataInterface;

    public function delete(
        array $whereConditions = []
    ) : bool;
}
