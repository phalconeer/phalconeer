<?php
namespace Phalconeer\Data;

use Phalconeer\Data as This;

interface TagableInterface extends This\CommonInterface
{
    public function addTag(string $tag) : This\TagableInterface;
}