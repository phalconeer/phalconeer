<?php
namespace Phalconeer\FormValidator\Data;

use Phalconeer\Data;
use Phalconeer\Dto;

abstract class FieldCheck extends Dto\ImmutableDto
{
    use Dto\Trait\ArrayLoader,
        Data\Trait\ParseTypes;

    protected array|bool|int|string $value;

    protected ?string $exceptionCode;

    public function __invoke()
    {
        return $this->getValue('value');
    }

    public function value()
    {
        return $this->getValue('value');
    }

    public function exceptionCode() : ?string
    {
        return $this->getValue('exceptionCode');
    }
}