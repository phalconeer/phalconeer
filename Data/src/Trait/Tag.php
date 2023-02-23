<?php
namespace Phalconeer\Data\Trait;

trait Tag
{
    protected \ArrayObject $tags;

    public function addTag(string $tag) : self
    {
        if (!isset($this->tags)
            || !$this->tags instanceof \ArrayObject) {
            $this->tags = new \ArrayObject();
        }
        $this->tags->offsetSet(null, $tag);
        return $this;
    }

    public function removeTag(string $tag) : self
    {
        if (!isset($this->tags)
            || !$this->tags instanceof \ArrayObject) {
            return $this;
        }
        $iterator = $this->tags->getIterator();
        $target = new \ArrayObject();
        while ($iterator->valid()) {
            if ($iterator->current() != $tag) {
                $target->offsetSet(null, $iterator->current());
            }
            $iterator->next();
        }
        return $this->setValueByKey('tags', $target);
    }
}