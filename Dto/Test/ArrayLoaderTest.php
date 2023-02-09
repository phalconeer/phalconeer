<?php
namespace Phalconeer\Dto\Test;

use Test;
use Phalconeer\Dto\Test as This;

class ArrayLoaderTest extends Test\UnitTestCase
{
    public function testPropertyValues()
    {
        $testData = [
            'stringProperty'    => 'This is a string',
            'intProperty'       => 10,
            'floatProperty'     => 1.2342342,
            'boolProperty'      => true,
            'arrayProperty'     => ['a', 'b'],
            'callableProperty'  => function () {},
            'arrayObject'       => new \ArrayObject(['--', '!!']),
            'dateTimeObject'    => new \DateTime('@0'),
            'nestedObject'      => This\Mock\TestArrayLoader::fromArray([
                'stringProperty'    => 'This is a nested string',
                'intProperty'       => 99,
                'floatProperty'     => 0.000003,
                'boolProperty'      => false,
                'arrayProperty'     => new \ArrayObject([3, 4]),
            ]),
            'undocumented'      => '123'
        ];
        $dto = This\Mock\TestArrayLoader::fromArray($testData);

        $this->assertEquals(
            gettype($dto->stringProperty()),
            'string',
            'String property returned as string'
        );
        $this->assertEquals(
            $testData['stringProperty'],
            $dto->stringProperty(),
            'String property returned correctly'
        );

        $this->assertEquals(
            'integer',
            gettype($dto->intProperty()),
            'Int property returned as int'
        );
        $this->assertEquals(
            $testData['intProperty'],
            $dto->intProperty(),
            'Int property returned correctly'
        );

        $this->assertEquals(
            'double',
            gettype($dto->floatProperty()),
            'Double property returned as double'
        );
        $this->assertEquals(
            $testData['floatProperty'],
            $dto->floatProperty(),
            'Float property returned correctly'
        );

        $this->assertEquals(
            'boolean',
            gettype($dto->boolProperty()),
            'Bool property returned as bool'
        );
        $this->assertEquals(
            $testData['boolProperty'],
            $dto->boolProperty(),
            'Bool property returned correctly'
        );
        
        $this->assertTrue(
            is_array($dto->arrayProperty()),
            'Array property returned as array'
        );
        $this->assertEquals(
            $testData['arrayProperty'],
            $dto->arrayProperty(),
            'Array property returned correctly'
        );

        $this->assertTrue(
            is_callable($dto->callableProperty()),
            'Callable property returned as callable'
        );
        $this->assertEquals(
            $testData['callableProperty'],
            $dto->callableProperty(),
            'Callable property returned correctly'
        );

        $this->assertEquals(
            This\Mock\TestArrayLoader::class,
            get_class($dto->nestedObject()),
            'Nested object property returned with correct class'
        );
        $this->assertEquals(
            $testData['nestedObject'],
            $dto->nestedObject(),
            'Nested object property returned correctly'
        );

        $this->assertEquals(
            \ArrayObject::class,
            get_class($dto->arrayObject()),
            'Array object property returned with correct class'
        );
        $this->assertEquals(
            $testData['arrayObject'],
            $dto->arrayObject(),
            'Array object property returned correctly'
        );

        $this->assertEquals(
            \DateTime::class,
            get_class($dto->dateTimeObject()),
            'DateTime object property returned with correct class'
        );
        $this->assertEquals(
            $testData['dateTimeObject'],
            $dto->dateTimeObject(),
            'DateTime object property returned correctly'
        );

        $this->assertEquals(
            null,
            $dto->undocumented(),
            'Undocumented property returned correctly'
        );
    }
}