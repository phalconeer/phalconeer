<?php
namespace Phalconeer\Data\Test;
use Test;
use Phalconeer\Data\Test as This;

class MutableDataTest extends Test\UnitTestCase
{
    public function testMutability()
    {
        $testData = new \ArrayObject([
            'dateTimeObject'    => new \DateTime('@0'),
            'nestedObject'      => new \ArrayObject([
                'stringProperty'    => 'This is a nested string',
            ]),
        ]);

        $immutable = new This\Mock\Test($testData);
        $mutable = new This\Mock\MutableTest($testData);

        $immutableObject = $immutable->nestedObject();
        $mutableObject = $mutable->nestedObject();

        $immutableObject = $immutableObject->setStringProperty('This is a CHANGED nested string');
        $mutableObject = $mutableObject->setStringProperty('This is a CHANGED nested string');

        $this->assertEquals(
            'This is a nested string',
            $immutable->nestedObject()->stringProperty(),
            'Immutablility is violated on nested string property'
        );

        $this->assertEquals(
            'This is a CHANGED nested string',
            $mutable->nestedObject()->stringProperty(),
            'Mutability is not working as expected'
        );
    }
}