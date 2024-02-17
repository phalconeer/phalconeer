<?php
namespace Phalconeer\Dto\Trait;

use Phalconeer\Dto as This;

trait MapFieldExporter
{
    public function mapFieldAsKey(string | array $field) : self
    {
        return This\Transformer\MapFieldExporter::mapFieldAsKey(
            $this,
            $field
        );
    }

    public function stripKeys() : self
    {
        $target = new self();
        $iterator = $this->getIterator();
        while ($iterator->valid()) {
            $target->offsetSet(null, $iterator->current());
            $iterator->next();
        }

        return $target;
    }
}