<?php
namespace Phalconeer\Data;

interface StorableInterface
{
    public function isStored() : bool;
}