<?php
namespace Phalconeer\Impression\Trait;

trait Accept
{
    /**
     * Accepted MIME types of the visitor.
     */
    protected ?string $accept;

    public function setAccept(string $accept) : self
    {
        return $this->setValueByKey('accept', $accept);
    }
}