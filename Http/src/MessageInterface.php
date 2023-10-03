<?php
namespace Phalconeer\Http;

interface MessageInterface
{
    public function bodyVariable(string $key);

    public function bodyVariables() : array;

    public function withBodyVariables(array $variables, bool $merge = false) : self;
}