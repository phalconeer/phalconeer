<?php
namespace Phalconeer\Data\Trait;

trait Tag
{
    protected \ArrayObject $tags;

    public function addTag(string $tag) : self
    {
        $this->tags->offsetSet(null, $tag);
        return $this;
    }
}