<?php
namespace Phalconeer\Data\Test;
use Test;
use Phalconeer\Data;
use Phalconeer\Data\Test as This;

use Phalconeer\Exception\TypeMismatchException;

class ImmutableDataTest extends Test\UnitTestCase
{
    public function testPropertyValues()
    {
        $testData = new \ArrayObject([
            'stringProperty'    => 'This is a string',
            'intProperty'       => 10,
            'floatProperty'     => 1.2342342,
            'boolProperty'      => true,
            'arrayProperty'     => ['a', 'b'],
            'callableProperty'  => function () {},
            'arrayObject'       => new \ArrayObject(['--', '!!']),
            'dateTimeObject'    => new \DateTime('@0'),
            'nestedObject'      => new This\Mock\Test(new \ArrayObject([
                'stringProperty'    => 'This is a nested string',
                'intProperty'       => 99,
                'floatProperty'     => 0.000003,
                'boolProperty'      => false,
                'arrayProperty'     => [3, 4]
            ])),
            'undocumented'      => '123'
        ]);
        $dto = new This\Mock\Test($testData);
        $dtoPrototypeTrait = new This\Mock\TestPrototypeTrait($testData);
        $dtoParseTypesTrait = new This\Mock\TestParseTypesTrait($testData);

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
            gettype($dtoPrototypeTrait->stringProperty()),
            'string',
            'String property returned as string'
        );
        $this->assertEquals(
            $testData['stringProperty'],
            $dtoPrototypeTrait->stringProperty(),
            'String property returned correctly'
        );
        $this->assertEquals(
            gettype($dtoParseTypesTrait->stringProperty()),
            'string',
            'String property returned as string'
        );
        $this->assertEquals(
            $testData['stringProperty'],
            $dtoParseTypesTrait->stringProperty(),
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
            'integer',
            gettype($dtoPrototypeTrait->intProperty()),
            'Int property returned as int'
        );
        $this->assertEquals(
            $testData['intProperty'],
            $dtoPrototypeTrait->intProperty(),
            'Int property returned correctly'
        );
        $this->assertEquals(
            'integer',
            gettype($dtoParseTypesTrait->intProperty()),
            'Int property returned as int'
        );
        $this->assertEquals(
            $testData['intProperty'],
            $dtoParseTypesTrait->intProperty(),
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
            'double',
            gettype($dtoPrototypeTrait->floatProperty()),
            'Double property returned as double'
        );
        $this->assertEquals(
            $testData['floatProperty'],
            $dtoPrototypeTrait->floatProperty(),
            'Float property returned correctly'
        );
        $this->assertEquals(
            'double',
            gettype($dtoParseTypesTrait->floatProperty()),
            'Double property returned as double'
        );
        $this->assertEquals(
            $testData['floatProperty'],
            $dtoParseTypesTrait->floatProperty(),
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
        $this->assertEquals(
            'boolean',
            gettype($dtoPrototypeTrait->boolProperty()),
            'Bool property returned as bool'
        );
        $this->assertEquals(
            $testData['boolProperty'],
            $dtoPrototypeTrait->boolProperty(),
            'Bool property returned correctly'
        );
        $this->assertEquals(
            'boolean',
            gettype($dtoParseTypesTrait->boolProperty()),
            'Bool property returned as bool'
        );
        $this->assertEquals(
            $testData['boolProperty'],
            $dtoParseTypesTrait->boolProperty(),
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
            is_array($dtoPrototypeTrait->arrayProperty()),
            'Array property returned as array'
        );
        $this->assertEquals(
            $testData['arrayProperty'],
            $dtoPrototypeTrait->arrayProperty(),
            'Array property returned correctly'
        );
        $this->assertTrue(
            is_array($dtoParseTypesTrait->arrayProperty()),
            'Array property returned as array'
        );
        $this->assertEquals(
            $testData['arrayProperty'],
            $dtoParseTypesTrait->arrayProperty(),
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
        $this->assertTrue(
            is_callable($dtoPrototypeTrait->callableProperty()),
            'Array property returned as array'
        );
        $this->assertEquals(
            $testData['arrayProperty'],
            $dtoPrototypeTrait->arrayProperty(),
            'Callable property returned correctly'
        );
        $this->assertTrue(
            is_callable($dtoParseTypesTrait->callableProperty()),
            'Callable property returned as callable'
        );
        $this->assertEquals(
            $testData['callableProperty'],
            $dtoParseTypesTrait->callableProperty(),
            'Callable property returned correctly'
        );

        $this->assertEquals(
            This\Mock\Test::class,
            get_class($dto->nestedObject()),
            'Nested object property returned with correct class'
        );
        $this->assertEquals(
            $testData['nestedObject'],
            $dto->nestedObject(),
            'Nested object property returned correctly'
        );
        $this->assertEquals(
            This\Mock\Test::class,
            get_class($dtoPrototypeTrait->nestedObject()),
            'Nested object property returned with correct class'
        );
        $this->assertEquals(
            true,
            Data\Helper\CompareValueHelper::hasSameData(
                $testData['nestedObject'],
                $dtoPrototypeTrait->nestedObject()
            ),
            'Nested object property returned correctly'
        );
        $this->assertEquals(
            $testData['nestedObject'],
            $dtoPrototypeTrait->nestedObject(),
            'Nested object property returned correctly'
        );
        $this->assertEquals(
            This\Mock\Test::class,
            get_class($dtoParseTypesTrait->nestedObject()),
            'Nested object property returned with correct class'
        );
        $this->assertEquals(
            $testData['nestedObject'],
            $dtoParseTypesTrait->nestedObject(),
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
            \ArrayObject::class,
            get_class($dtoPrototypeTrait->arrayObject()),
            'Array object property returned with correct class'
        );
        $this->assertEquals(
            $testData['arrayObject'],
            $dtoPrototypeTrait->arrayObject(),
            'Array object property returned correctly'
        );
        $this->assertEquals(
            \ArrayObject::class,
            get_class($dtoParseTypesTrait->arrayObject()),
            'Array object property returned with correct class'
        );
        $this->assertEquals(
            $testData['arrayObject'],
            $dtoParseTypesTrait->arrayObject(),
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
            \DateTime::class,
            get_class($dtoPrototypeTrait->dateTimeObject()),
            'DateTime object property returned with correct class'
        );
        $this->assertEquals(
            true,
            Data\Helper\CompareValueHelper::hasSameData(
                $testData['dateTimeObject'],
                $dtoPrototypeTrait->dateTimeObject()
            ),
            'DateTime object property returned correctly'
        );
        $this->assertEquals(
            $testData['dateTimeObject'],
            $dtoPrototypeTrait->dateTimeObject(),
            'DateTime object property returned correctly'
        );
        $this->assertEquals(
            \DateTime::class,
            get_class($dtoParseTypesTrait->dateTimeObject()),
            'DateTime object property returned with correct class'
        );
        $this->assertEquals(
            $testData['dateTimeObject'],
            $dtoParseTypesTrait->dateTimeObject(),
            'DateTime object property returned correctly'
        );

        $this->assertEquals(
            null,
            $dto->undocumented(),
            'Undocumented property returned correctly'
        );
        $this->assertEquals(
            null,
            $dtoPrototypeTrait->undocumented(),
            'Undocumented property returned correctly'
        );
        $this->assertEquals(
            '123',
            $dtoParseTypesTrait->undocumented(),
            'Undocumented property returned correctly'
        );
    }

    public function testPropertiesCanReturnNull()
    {
        $dto = new This\Mock\Test();
        $dtoPrototypeTrait = new This\Mock\TestPrototypeTrait();

        $this->assertEquals(
            null,
            $dto->stringProperty(),
            'String can return null'
        );
        $this->assertEquals(
            null,
            $dtoPrototypeTrait->stringProperty(),
            'String can return null'
        );

        $this->assertEquals(
            null,
            $dto->intProperty(),
            'Int can return null'
        );
        $this->assertEquals(
            null,
            $dtoPrototypeTrait->intProperty(),
            'Int can return null'
        );

        $this->assertEquals(
            null,
            $dto->floatProperty(),
            'Float can return null'
        );
        $this->assertEquals(
            null,
            $dtoPrototypeTrait->floatProperty(),
            'Float can return null'
        );

        $this->assertEquals(
            null,
            $dto->boolProperty(),
            'Bool can return null'
        );
        $this->assertEquals(
            null,
            $dtoPrototypeTrait->boolProperty(),
            'Bool can return null'
        );

        $this->assertEquals(
            null,
            $dto->arrayProperty(),
            'Array can return null'
        );
        $this->assertEquals(
            null,
            $dtoPrototypeTrait->arrayProperty(),
            'Array can return null'
        );

        $this->assertEquals(
            null,
            $dto->arrayObject(),
            'Array object can return null'
        );
        $this->assertEquals(
            null,
            $dtoPrototypeTrait->arrayObject(),
            'Array object can return null'
        );

        $this->assertEquals(
            null,
            $dto->nestedObject(),
            'Nested object can return null'
        );
        $this->assertEquals(
            null,
            $dtoPrototypeTrait->nestedObject(),
            'Nested object can return null'
        );

        $this->assertEquals(
            null,
            $dto->dateTimeObject(),
            'Date time object can return null'
        );
        $this->assertEquals(
            null,
            $dtoPrototypeTrait->dateTimeObject(),
            'Date time object can return null'
        );

        $this->assertEquals(
            null,
            $dto->undocumented(),
            'Undocumented can return null'
        );
    }


    public function testReturnedObjectsAreImmutable()
    {
        $testData = [
            'nestedObject'      => new This\Mock\Test(),
            'arrayObject'       => new \ArrayObject(['--', '||']),
            'dateTimeObject'    => new \DateTime('@0')
        ];

        $dto = new This\Mock\Test(new \ArrayObject($testData));
        $dtoPrototypeTrait = new This\Mock\TestPrototypeTrait(new \ArrayObject($testData));
        $dtoParseTypesTrait = new This\Mock\TestParseTypesTrait(new \ArrayObject($testData));

        $arrayObject = $dto->arrayObject();
        $arrayObject->offsetSet(null, '!!');
        $dateTimeObject = $dto->dateTimeObject();
        $dateTimeObject->modify('+1day');

        $this->assertEquals(
            $testData['nestedObject'],
            $dto->nestedObject(),
            'Nested objects properties are preserved'
        );
        $this->assertNotSame(
            $testData['nestedObject'],
            $dto->nestedObject(),
            'Nested object returned is not the same object'
        );
        $this->assertEquals(
            $testData['nestedObject'],
            $dtoPrototypeTrait->nestedObject(),
            'Nested objects properties are preserved'
        );
        $this->assertEquals(
            true,
            Data\Helper\CompareValueHelper::hasSameData(
                $testData['nestedObject'],
                $dtoPrototypeTrait->nestedObject()
            ),
            'Nested objects properties are preserved'
        );
        $this->assertNotSame(
            $testData['nestedObject'],
            $dtoPrototypeTrait->nestedObject(),
            'Nested object returned is not the same object'
        );
        $this->assertEquals(
            $testData['nestedObject'],
            $dtoParseTypesTrait->nestedObject(),
            'Nested objects properties are preserved'
        );
        $this->assertNotSame(
            $testData['nestedObject'],
            $dtoParseTypesTrait->nestedObject(),
            'Nested object returned is not the same object'
        );

        $this->assertEquals(
            $testData['arrayObject'],
            $dto->arrayObject(),
            'Array objects properties are preserved'
        );
        $this->assertNotSame(
            $testData['arrayObject'],
            $dto->arrayObject(),
            'Array object returned is not the same object'
        );
        $this->assertEquals(
            $testData['arrayObject'],
            $dtoPrototypeTrait->arrayObject(),
            'Array objects properties are preserved'
        );
        $this->assertNotSame(
            $testData['arrayObject'],
            $dtoPrototypeTrait->arrayObject(),
            'Array object returned is not the same object'
        );
        $this->assertEquals(
            $testData['arrayObject'],
            $dtoParseTypesTrait->arrayObject(),
            'Array objects properties are preserved'
        );
        $this->assertNotSame(
            $testData['arrayObject'],
            $dtoParseTypesTrait->arrayObject(),
            'Array object returned is not the same object'
        );

        $this->assertEquals(
            $testData['dateTimeObject'],
            $dto->dateTimeObject(),
            'Date time objects properties are preserved'
        );
        $this->assertNotSame(
            $testData['dateTimeObject'],
            $dto->nestedObject(),
            'Date time object returned is not the same object'
        );
        $this->assertEquals(
            $testData['dateTimeObject'],
            $dtoPrototypeTrait->dateTimeObject(),
            'Date time objects properties are preserved'
        );
        $this->assertNotSame(
            $testData['dateTimeObject'],
            $dtoPrototypeTrait->nestedObject(),
            'Date time object returned is not the same object'
        );
        $this->assertEquals(
            $testData['dateTimeObject'],
            $dtoParseTypesTrait->dateTimeObject(),
            'Date time objects properties are preserved'
        );
        $this->assertNotSame(
            $testData['dateTimeObject'],
            $dtoParseTypesTrait->nestedObject(),
            'Date time object returned is not the same object'
        );
    }

    public function testInvalidStringValueThrowsException()
    {
        $this->expectException(TypeMismatchException::class);
        $dto = new This\Mock\Test(new \ArrayObject([
          'stringProperty'      => []
        ]));
    }

    public function testParsedInvalidStringValueThrowsException()
    {
        $this->expectException(TypeMismatchException::class);
        $dto = new This\Mock\TestParseTypesTrait(new \ArrayObject([
          'stringProperty'      => []
        ]));
    }

    public function testInvalidIntValueThrowsException()
    {
        $this->expectException(TypeMismatchException::class);
        $dto = new This\Mock\Test(new \ArrayObject([
          'intProperty'      => []
        ]));
    }

    public function testParsedInvalidIntValueThrowsException()
    {
        $this->expectException(TypeMismatchException::class);
        $dto = new This\Mock\TestParseTypesTrait(new \ArrayObject([
          'intProperty'      => []
        ]));
    }

    public function testInvalidFloatValueThrowsException()
    {
        $this->expectException(TypeMismatchException::class);
        $dto = new This\Mock\Test(new \ArrayObject([
          'floatProperty'      => []
        ]));
    }

    public function testParsedInvalidFloatValueThrowsException()
    {
        $this->expectException(TypeMismatchException::class);
        $dto = new This\Mock\TestParseTypesTrait(new \ArrayObject([
          'floatProperty'      => []
        ]));
    }

    public function testInvalidBoolValueThrowsException()
    {
        $this->expectException(TypeMismatchException::class);
        $dto = new This\Mock\Test(new \ArrayObject([
          'boolProperty'      => []
        ]));
    }

    public function testParsedInvalidBoolValueThrowsException()
    {
        $this->expectException(TypeMismatchException::class);
        $dto = new This\Mock\TestParseTypesTrait(new \ArrayObject([
          'boolProperty'      => []
        ]));
    }

    public function testInvalidNestedValueThrowsException()
    {
        $this->expectException(TypeMismatchException::class);
        $dto = new This\Mock\Test(new \ArrayObject([
          'nestedObject'     => new \DateTime()
        ]));
    }

    public function testParsedInvalidNestedValueThrowsException()
    {
        $this->expectException(TypeMismatchException::class);
        $dto = new This\Mock\TestParseTypesTrait(new \ArrayObject([
          'nestedObject'     => new \DateTime()
        ]));
    }

    public function testMerge()
    {
        $testData = [
            'stringProperty'    => 'This is a string',
            'intProperty'       => 10,
            'boolProperty'      => true,
            'arrayProperty'     => ['a', 'b'],
            'arrayObject'       => new \ArrayObject(['--', '!!']),
            'dateTimeObject'    => new \DateTime('@0'),
            'nestedObject'      => new This\Mock\Test(new \ArrayObject([
                'stringProperty'    => 'This is a nested string',
                'intProperty'       => 99,
                'floatProperty'     => 0.000003,
                'boolProperty'      => false,
                'arrayProperty'     => [3, 4]
            ])),
            'undocumented'      => '123'
        ];

        $dto = new This\Mock\Test(new \ArrayObject($testData));

        $arrayProperty = $dto->arrayProperty();
        $arrayProperty[] = 'c';

        $dateTimeProperty = $dto->dateTimeObject();
        $dateTimeProperty->modify('+1day');

        $change = new This\Mock\Test(new \ArrayObject([
            'stringProperty'    => 'Changed string',
            'floatProperty'     => 1.2342342,
            'arrayProperty'     => $arrayProperty,
            'arrayObject'       => new \ArrayObject(['**', '$$']),
            'dateTimeObject'    => $dateTimeProperty,
            'nestedObject'      => new This\Mock\Test(new \ArrayObject([
                'stringProperty'    => 'This is going to be the only property set',
            ])),
            'undocumented'      => null
        ]));

        $expectedOutput = new This\Mock\Test(new \ArrayObject([
            'stringProperty'    => 'This is a string',
            'intProperty'       => 10,
            'floatProperty'     => null,
            'boolProperty'      => true,
            'arrayProperty'     => ['a', 'b'],
            'callableProperty'  => null,
            'arrayObject'       => new \ArrayObject(['--', '!!']),
            'dateTimeObject'    => '1970-01-01T00:00:00+00:00',
            'nestedObject'      => new \ArrayObject([
                'stringProperty'    => 'This is a nested string',
                'intProperty'       => 99,
                'floatProperty'     => 0.000003,
                'boolProperty'      => false,
                'arrayProperty'     => [3, 4],
                'callableProperty'  => null,
                'arrayObject'       => null,
                'dateTimeObject'    => null,
                'nestedObject'      => null,
            ]),
        ]));

        $expectedOutputChanged = new This\Mock\Test(new \ArrayObject([
            'stringProperty'    => 'Changed string',
            'intProperty'       => 10,
            'boolProperty'      => true,
            'floatProperty'     => 1.2342342,
            'arrayProperty'     => ['a', 'b', 'c'],
            'callableProperty'  => null,
            'arrayObject'       => new \ArrayObject(['**', '$$']),
            'dateTimeObject'    => '1970-01-02T00:00:00+00:00',
            'nestedObject'      => new \ArrayObject([
                'stringProperty'    => 'This is going to be the only property set',
                'intProperty'       => null,
                'floatProperty'     => null,
                'boolProperty'      => null,
                'arrayProperty'     => null,
                'callableProperty'  => null,
                'arrayObject'       => null,
                'dateTimeObject'    => null,
                'nestedObject'      => null,
            ]),
        ]));

        $newDto = $dto->merge($change);

        $this->assertEquals(
            true,
            Data\Helper\CompareValueHelper::hasSameData(
                $expectedOutput,
                $dto,
            ),
            'Apply changes worked as expected'
        );

        $this->assertEquals(
            true,
            Data\Helper\CompareValueHelper::hasSameData(
                $expectedOutputChanged,
                $newDto
            ),
            'Apply changes worked as expected'
        );
    }
} 