<?php
namespace Phalconeer\Data;

interface PrimaryKeyInterface
{
    public function getPrimaryKeyValue() : array;
}