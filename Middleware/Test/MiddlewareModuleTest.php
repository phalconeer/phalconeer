<?php
namespace Phalconeer\Module\Middleware\Test;

use Test;
use Phalconeer\Middleware;
use Phalconeer\Middleware\Test as This;

/**
 * Description of AccountingAccountBoTest
 *
 * @author Fulee
 */
class MiddlewareModuleTest extends Test\UnitTestCase
{
    public function testZeroLengthChain()
    {
        $tracker = new \ArrayObject();
        $target = 'done';
        $action = function() use ($tracker, $target) {
            $tracker->offsetSet(null, $target);
        };

        $chain = Middleware\Helper\MiddlewareHelper::createChain(
            new \SplStack(),
            $action
        );

        $chain();

        $this->assertEquals($target, $tracker->offsetGet(0), 'Last chain call was not executed');
    }

    public function testOneLengthChain()
    {
        $tracker = new \ArrayObject();
        $target = 'done';
        $middlewares = new \SplStack();
        $middlewares->offsetSet(null, new This\Mock\TrackerMiddlewareMock());

        $action = function($tracker) use ($target) {
            $tracker->offsetSet(null, $target);
        };

        $chain = Middleware\Helper\MiddlewareHelper::createChain(
            $middlewares,
            $action
        );

        $chain($tracker);

        $this->assertEquals(This\Mock\TrackerMiddlewareMock::TARGET, $tracker->offsetGet(0), 'Middleware was not executed in time');
        $this->assertEquals($target, $tracker->offsetGet(1), 'Last chain call was not executed');
    }

    public function testLongerChain()
    {
        $tracker = new \ArrayObject();
        $target = 'done';
        $middlewares = new \SplStack();
        $middlewares->offsetSet(null, new This\Mock\TrackerMiddlewareMock());
        $middlewares->offsetSet(null, new This\Mock\TrackerDifferentMiddlewareMock());
        $middlewares->offsetSet(null, new This\Mock\TrackerDifferentMiddlewareMock());
        $middlewares->offsetSet(null, new This\Mock\TrackerMiddlewareMock());
        $middlewares->offsetSet(null, new This\Mock\TrackerDifferentMiddlewareMock());

        $action = function($tracker) use ($target) {
            $tracker->offsetSet(null, $target);
        };

        $chain = Middleware\Helper\MiddlewareHelper::createChain(
            $middlewares,
            $action
        );

        $chain($tracker);

        $this->assertEquals(This\Mock\TrackerMiddlewareMock::TARGET, $tracker->offsetGet(0), 'Middleware was not executed in time');
        $this->assertEquals(This\Mock\TrackerDifferentMiddlewareMock::TARGET, $tracker->offsetGet(1), 'Middleware was not executed in time');
        $this->assertEquals(This\Mock\TrackerDifferentMiddlewareMock::TARGET, $tracker->offsetGet(2), 'Middleware was not executed in time');
        $this->assertEquals(This\Mock\TrackerMiddlewareMock::TARGET, $tracker->offsetGet(3), 'Middleware was not executed in time');
        $this->assertEquals(This\Mock\TrackerDifferentMiddlewareMock::TARGET, $tracker->offsetGet(4), 'Middleware was not executed in time');
        $this->assertEquals($target, $tracker->offsetGet(5), 'Last chain call was not executed');

        $tracker2 = new \ArrayObject();
        $middlewares2 = Middleware\Helper\MiddlewareHelper::createMiddlewaresContainer([
            new This\Mock\TrackerMiddlewareMock(),
            new This\Mock\TrackerDifferentMiddlewareMock(),
            new This\Mock\TrackerDifferentMiddlewareMock(),
            new This\Mock\TrackerDifferentMiddlewareMock(),
        ]);

        $chain2 = Middleware\Helper\MiddlewareHelper::createChain(
            $middlewares2,
            $action
        );

        $chain2($tracker2);

        $this->assertEquals(This\Mock\TrackerMiddlewareMock::TARGET, $tracker2->offsetGet(0), 'Middleware was not executed in time');
        $this->assertEquals(This\Mock\TrackerDifferentMiddlewareMock::TARGET, $tracker2->offsetGet(1), 'Middleware was not executed in time');
        $this->assertEquals(This\Mock\TrackerDifferentMiddlewareMock::TARGET, $tracker2->offsetGet(2), 'Middleware was not executed in time');
        $this->assertEquals(This\Mock\TrackerDifferentMiddlewareMock::TARGET, $tracker2->offsetGet(3), 'Middleware was not executed in time');
        $this->assertEquals($target, $tracker2->offsetGet(4), 'Last chain call was not executed');

        $tracker3 = new \ArrayObject();
        $middlewares3 = Middleware\Helper\MiddlewareHelper::createMiddlewaresContainer([
            new This\Mock\TrackerDifferentMiddlewareMock(),
            new This\Mock\TrackerDifferentMiddlewareMock(),
        ]);
        $middlewares3->offsetSet(null, new This\Mock\TrackerMiddlewareMock());
        $middlewares3->offsetSet(null, new This\Mock\TrackerMiddlewareMock());
        $middlewares3->offsetSet(null, new This\Mock\TrackerMiddlewareMock());

        $chain3 = Middleware\Helper\MiddlewareHelper::createChain(
            $middlewares3,
            $action
        );

        $chain3($tracker3);

        $this->assertEquals(This\Mock\TrackerDifferentMiddlewareMock::TARGET, $tracker3->offsetGet(0), 'Middleware was not executed in time');
        $this->assertEquals(This\Mock\TrackerDifferentMiddlewareMock::TARGET, $tracker3->offsetGet(1), 'Middleware was not executed in time');
        $this->assertEquals(This\Mock\TrackerMiddlewareMock::TARGET, $tracker3->offsetGet(2), 'Middleware was not executed in time');
        $this->assertEquals(This\Mock\TrackerMiddlewareMock::TARGET, $tracker3->offsetGet(3), 'Middleware was not executed in time');
        $this->assertEquals(This\Mock\TrackerMiddlewareMock::TARGET, $tracker3->offsetGet(4), 'Middleware was not executed in time');
        $this->assertEquals($target, $tracker3->offsetGet(5), 'Last chain call was not executed');
    }

    public function testLongerChainInReverse()
    {
        $tracker = new \ArrayObject();
        $target = 'done';
        $middlewares = new \SplDoublyLinkedList();
        $middlewares->offsetSet(null, new This\Mock\TrackerMiddlewareMock());
        $middlewares->offsetSet(null, new This\Mock\TrackerDifferentMiddlewareMock());
        $middlewares->offsetSet(null, new This\Mock\TrackerDifferentMiddlewareMock());
        $middlewares->offsetSet(null, new This\Mock\TrackerMiddlewareMock());
        $middlewares->offsetSet(null, new This\Mock\TrackerDifferentMiddlewareMock());

        $action = function($tracker) use ($target) {
            $tracker->offsetSet(null, $target);
        };

        $chain = Middleware\Helper\MiddlewareHelper::createChain(
            $middlewares,
            $action
        );

        $chain($tracker);

        $this->assertEquals(This\Mock\TrackerDifferentMiddlewareMock::TARGET, $tracker->offsetGet(0), 'Middleware was not executed in time');
        $this->assertEquals(This\Mock\TrackerMiddlewareMock::TARGET, $tracker->offsetGet(1), 'Middleware was not executed in time');
        $this->assertEquals(This\Mock\TrackerDifferentMiddlewareMock::TARGET, $tracker->offsetGet(2), 'Middleware was not executed in time');
        $this->assertEquals(This\Mock\TrackerDifferentMiddlewareMock::TARGET, $tracker->offsetGet(3), 'Middleware was not executed in time');
        $this->assertEquals(This\Mock\TrackerMiddlewareMock::TARGET, $tracker->offsetGet(4), 'Middleware was not executed in time');
        $this->assertEquals($target, $tracker->offsetGet(5), 'Last chain call was not executed');

        $tracker2 = new \ArrayObject();
        $middlewares2 = Middleware\Helper\MiddlewareHelper::createMiddlewaresContainer([
            new This\Mock\TrackerMiddlewareMock(),
            new This\Mock\TrackerDifferentMiddlewareMock(),
            new This\Mock\TrackerDifferentMiddlewareMock(),
            new This\Mock\TrackerDifferentMiddlewareMock(),
        ], true);

        $chain2 = Middleware\Helper\MiddlewareHelper::createChain(
            $middlewares2,
            $action
        );

        $chain2($tracker2);

        $this->assertEquals(This\Mock\TrackerDifferentMiddlewareMock::TARGET, $tracker2->offsetGet(0), 'Middleware was not executed in time');
        $this->assertEquals(This\Mock\TrackerDifferentMiddlewareMock::TARGET, $tracker2->offsetGet(1), 'Middleware was not executed in time');
        $this->assertEquals(This\Mock\TrackerDifferentMiddlewareMock::TARGET, $tracker2->offsetGet(2), 'Middleware was not executed in time');
        $this->assertEquals(This\Mock\TrackerMiddlewareMock::TARGET, $tracker2->offsetGet(3), 'Middleware was not executed in time');
        $this->assertEquals($target, $tracker2->offsetGet(4), 'Last chain call was not executed');
    }

    public function testInterfaceValidatoin()
    {
        $tracker = new \ArrayObject();
        $target = 'done';
        $middlewares = new \SplStack();
        $middlewares->offsetSet(null, new This\Mock\TrackerMiddlewareMock());

        $action = function($tracker) use ($target) {
            $tracker->offsetSet(null, $target);
        };

        $chain = Middleware\Helper\MiddlewareHelper::createChain(
            $middlewares,
            $action,
            This\Mock\TrackerInterface::class
        );

        $chain($tracker);

        $this->assertEquals(This\Mock\TrackerMiddlewareMock::TARGET, $tracker->offsetGet(0), 'Middleware was not executed in time');
        $this->assertEquals($target, $tracker->offsetGet(1), 'Last chain call was not executed');
    }

    public function testFailMiddlewareValidation()
    {
        $tracker = new \ArrayObject();
        $target = 'done';
        $middlewares = new \SplStack();
        $middlewares->offsetSet(null, function () {});

        $action = function($tracker) use ($target) {
            $tracker->offsetSet(null, $target);
        };

        $this->expectException(Middleware\Exception\InvalidMiddlewareException::class);
        $this->expectExceptionCode(Middleware\Helper\ExceptionHelper::MIDDLEWARE__INVALID_MIDDLEWARE_CLASS);
        $chain = Middleware\Helper\MiddlewareHelper::createChain(
            $middlewares,
            $action,
            This\Mock\TrackerInterface::class
        );
    }

    public function testFailInterfaceValidation()
    {
        $tracker = new \ArrayObject();
        $target = 'done';
        $middlewares = new \SplStack();
        $middlewares->offsetSet(null, new This\Mock\TrackerDifferentMiddlewareMock());

        $action = function($tracker) use ($target) {
            $tracker->offsetSet(null, $target);
        };

        $this->expectException(Middleware\Exception\InvalidHandlerException::class);
        $this->expectExceptionCode(Middleware\Helper\ExceptionHelper::MIDDLEWARE__INVALID_HANDLER_FUNCTION);
        $chain = Middleware\Helper\MiddlewareHelper::createChain(
            $middlewares,
            $action,
            This\Mock\TrackerInterface::class
        );
    }

    public function testTerminatedChain()
    {
        $tracker = new \ArrayObject();
        $target = 'done';
        $middlewares = new \SplStack();
        $middlewares->offsetSet(null, new This\Mock\TrackerMiddlewareMock());
        $middlewares->offsetSet(null, new This\Mock\TrackerDifferentMiddlewareMock());
        $middlewares->offsetSet(null, new This\Mock\TrackerDifferentMiddlewareMock());
        $middlewares->offsetSet(null, new This\Mock\TerminateMiddlewareMock());
        $middlewares->offsetSet(null, new This\Mock\TrackerMiddlewareMock());
        $middlewares->offsetSet(null, new This\Mock\TrackerDifferentMiddlewareMock());

        $action = function($tracker, $terminated = null) use ($target) {
            $tracker->offsetSet(null, $target);
        };

        $chain = Middleware\Helper\MiddlewareHelper::createChain(
            $middlewares,
            $action
        );

        $chain($tracker);

        $this->assertEquals(This\Mock\TrackerMiddlewareMock::TARGET, $tracker->offsetGet(0), 'Middleware was not executed in time');
        $this->assertEquals(This\Mock\TrackerDifferentMiddlewareMock::TARGET, $tracker->offsetGet(1), 'Middleware was not executed in time');
        $this->assertEquals(This\Mock\TrackerDifferentMiddlewareMock::TARGET, $tracker->offsetGet(2), 'Middleware was not executed in time');
        $this->assertEquals(This\Mock\TerminateMiddlewareMock::TARGET, $tracker->offsetGet(3), 'Middleware was not executed in time');
        $this->assertEquals($target, $tracker->offsetGet(4), 'Last chain call was not executed');
    }

}