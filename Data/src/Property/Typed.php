<?php
namespace Phalconeer\Data\Property;

use Phalconeer\Data as This;

class Typed extends This\ImmutableData
{
    protected static array $properties = [
        'value'         => This\Property\Any::class,
        'type'          => This\Helper\ParseValueHelper::TYPE_STRING
    ];

    protected This\Property\Any $value;

    protected string $type;

    public function value()
    {
        return $this->value;
    }

    public function type() : string
    {
        return $this->type;
    }

    public function set($value, $type) : self
    {
        return $this->setValueByKey(
                'value',
                This\Helper\ParseValueHelper::parseValue($value, $type)
            )
            ->setValueByKey('type', $type);
    }

    public function setValue($value, $forceTypeDetection = false)
    {
        if ($forceTypeDetection
            || is_null($this->type)) {
            $type = This\Helper\ParseValueHelper::detectType($value);
        } else {
            $type = $this->type;
        }
        return $this->set($value, $type);
    }

    public static function get($value) : self
    {
        return (new self())->setValue($value);
    }

    /**
     * This insert is needed for Phalcon/Config::path on Phalcon 4 to not throw a segemntation fault when an invalid key is asked from this Object
     */
    public function has()
    {
        return false;
    }
}