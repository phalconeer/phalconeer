<?php
namespace Phalconeer\FormValidator\Data;

use Phalconeer\Data\Helper\ParseValueHelper as PVH;
use Phalconeer\FormValidator as This;

class Type extends This\Data\FieldCheck implements This\StrictableInterface
{
    use This\Trait\StrictableRule;

    protected static array $properties = [
        'value'         => PVH::TYPE_STRING
    ];

    protected ?string $form;

    protected ?bool $isCondition = false;

    public function form() : ?string
    {
        return $this->getValue('form');
    }

    public function isCondition() : ?bool
    {
        return $this->getValue('isCondition');
    }
}