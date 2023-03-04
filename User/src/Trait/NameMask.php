<?php
namespace Phalconeer\User\Helper;

use Phalconeer\User as This;

trait NameMask
{
    public function nameMask(string $property) : ?string
    {
        return This\Transformer\NameMask::nameMask($this->{$property});
    }
}