<?php
use Test\UnitTestCase;
use Phalconeer\Data;
use Phalconeer\Data\Test;

/**
 * Class UnitTest
 */
class CompareValueHelperTest extends UnitTestCase
{
    public function testCompareSimpleValues()
    {
        $base = 'test';
        $check = 'test';
        $this->assertEquals(
            true,
            Data\Helper\CompareValueHelper::hasSameData($base, $check),
            'Compare value failed for simple equal strings'
        );

        $check2 = 'test2';
        $this->assertEquals(
            false,
            Data\Helper\CompareValueHelper::hasSameData($base, $check2),
            'Compare value failed for simple not equal strings'
        );

        $base = 123;
        $check = 123;
        $this->assertEquals(
            true,
            Data\Helper\CompareValueHelper::hasSameData($base, $check),
            'Compare value failed for simple equal integers'
        );

        $check2 = 456;
        $this->assertEquals(
            false,
            Data\Helper\CompareValueHelper::hasSameData($base, $check2),
            'Compare value failed for simple not equal integers'
        );

        $base = 123.45;
        $check = 123.45;
        $this->assertEquals(
            true,
            Data\Helper\CompareValueHelper::hasSameData($base, $check),
            'Compare value failed for simple equal floats'
        );

        $check2 = 456.45;
        $this->assertEquals(
            false,
            Data\Helper\CompareValueHelper::hasSameData($base, $check2),
            'Compare value failed for simple not equal floats'
        );

        $base = true;
        $check = true;
        $this->assertEquals(
            true,
            Data\Helper\CompareValueHelper::hasSameData($base, $check),
            'Compare value failed for simple equal bools'
        );

        $check2 = false;
        $this->assertEquals(
            false,
            Data\Helper\CompareValueHelper::hasSameData($base, $check2),
            'Compare value failed for simple not equal bools'
        );

        $base = null;
        $check = null;
        $this->assertEquals(
            true,
            Data\Helper\CompareValueHelper::hasSameData($base, $check),
            'Compare value failed for simple equal nulls'
        );

        $check2 = 'null';
        $this->assertEquals(
            false,
            Data\Helper\CompareValueHelper::hasSameData($base, $check2),
            'Compare value failed for simple not null'
        );
    }

    public function testCompareArrayValues()
    {
        $base = ['test', 12];
        $check = ['test', 12];
        $this->assertEquals(
            true,
            Data\Helper\CompareValueHelper::hasSameData($base, $check),
            'Compare value failed for simple equal arrays'
        );

        $check2 = ['test', 11];
        $this->assertEquals(
            false,
            Data\Helper\CompareValueHelper::hasSameData($base, $check2),
            'Compare value failed for simple not equal arrays'
        );
    }

    public function testCompareArrayObjectValues()
    {
        $base = new \ArrayObject(['test', 12]);
        $check = new \ArrayObject(['test', 12]);
        $this->assertEquals(
            true,
            Data\Helper\CompareValueHelper::hasSameData($base, $check),
            'Compare value failed for simple equal arrayObjects'
        );

        $check2 =new \ArrayObject( ['test', 11]);
        $this->assertEquals(
            false,
            Data\Helper\CompareValueHelper::hasSameData($base, $check2),
            'Compare value failed for simple not equal arrayObjects'
        );
    }

    public function testCompareDateTimeValues()
    {
        $base = new \DateTime('2000-01-01 12:00:00');
        $check = new \DateTime('2000-01-01 12:00:00');
        $this->assertEquals(
            true,
            Data\Helper\CompareValueHelper::hasSameData($base, $check),
            'Compare value failed for simple equal datetimes'
        );

        $check2 = new \DateTime('2000-01-01 12:00:01');
        $this->assertEquals(
            false,
            Data\Helper\CompareValueHelper::hasSameData($base, $check2),
            'Compare value failed for simple not equal datetimes'
        );
    }

    public function testCompareDataValues()
    {
        $base = new Test\Mock\Test(new \ArrayObject([
            'stringProperty'    => 'This is a string',
            'intProperty'       => 10,
            'floatProperty'     => 1.2342342,
            'boolProperty'      => true,
            'arrayProperty'     => ['a', 'b'],
            'callableProperty'  => function () {},
            'arrayObject'       => new ArrayObject(),
            'dateTimeObject'    => new DateTime('@0'),
            'nestedObject'      => new Test\Mock\Test(),
            'undocumented'      => '123'
        ]));
        $check = new Test\Mock\Test(new \ArrayObject([
            'stringProperty'    => 'This is a string',
            'intProperty'       => 10,
            'floatProperty'     => 1.2342342,
            'boolProperty'      => true,
            'arrayProperty'     => ['a', 'b'],
            'callableProperty'  => function () {},
            'arrayObject'       => new ArrayObject(),
            'dateTimeObject'    => new DateTime('@0'),
            'nestedObject'      => new Test\Mock\Test(),
            'undocumented'      => '123'
        ]));
        $this->assertEquals(
            true,
            Data\Helper\CompareValueHelper::hasSameData($base, $check),
            'Compare value failed for simple data Objects'
        );

        $check2 = new Test\Mock\Test();
        $this->assertEquals(
            false,
            Data\Helper\CompareValueHelper::hasSameData($base, $check2),
            'Compare value failed for simple not equal data objects'
        );
    }
}