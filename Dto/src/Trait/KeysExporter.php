<?php
namespace Phalconeer\Dto\Trait;

use Phalconeer\Dto as This;

trait KeysExporter
{
    public function getKeys() : array
    {
        return This\Transformer\KeysExporter::getKeys($this);
    }
}