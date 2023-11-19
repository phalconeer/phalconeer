<?php
namespace Phalconeer\Dto\Trait;

use Phalconeer\Dto as This;

trait MapFieldExporter
{
    public function mapFieldAsKey(string $field) : self
    {
        return This\Transformer\MapFieldExporter::mapFieldAsKey(
            $this,
            $field
        );
    }
}