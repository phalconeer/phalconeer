<?php
namespace Phalconeer\Dao;

use Phalconeer\Dao as This;
use Phalconeer\Data;
use Phalconeer\Dto;

interface DaoWriteInterface
{
    public function save(
        Data\DataInterface & Dto\ArrayObjectExporterInterface $data,
        bool $forceInsert = false,
        string $insertMode = This\Helper\DaoHelper::INSERT_MODE_NORMAL
    ) : ?Dto\ImmutableDto;

    public function delete(
        array $whereConditions = []
    ) : bool;
}
