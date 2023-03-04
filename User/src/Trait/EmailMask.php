<?php
namespace Phalconeer\User\Helper;

use Phalconeer\User as This;

trait EmailMask
{
    public function emailMask(string $property) : ?string
    {
        return This\Transformer\EmailMask::emailMask($this->{$property});
    }
}