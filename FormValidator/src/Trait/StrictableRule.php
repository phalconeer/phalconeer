<?php
namespace Phalconeer\FormValidator\Trait;

trait StrictableRule
{
    protected ?bool $isStrict;

    public function isStrict() : ?bool
    {
        return (isset($this->isStrict))
            ? $this->getValue('isStrict')
            : null;
    }
}