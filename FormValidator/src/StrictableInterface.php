<?php
namespace Phalconeer\FormValidator;

interface StrictableInterface
{
    public function isStrict() : ?bool;
}