<?php

namespace BenTools\ETL\Tests\Event\EventDispatcher;

use BenTools\ETL\Event\ETLEvent;
use PHPUnit\Framework\TestCase;

use BenTools\ETL\Event\EventDispatcher\ETLEventDispatcher;

class ETLEventDispatcherTest extends TestCase
{
    /**
     * @var ETLEventDispatcher
     */
    private $eventDispatcher;

    public function setUp()
    {
        $this->eventDispatcher = new ETLEventDispatcher();
    }

    public function testTrigger()
    {
        $received = false;
        $this->eventDispatcher->addListener('foo', function () use (&$received) {
            $received = true;
        });
        
        // Test with another name to ensure the correct event is dispatched
        $this->eventDispatcher->trigger(new ETLEvent('bar'));
        $this->assertEquals(false, $received);
        
        // Test with the correct name
        $this->eventDispatcher->trigger(new ETLEvent('foo'));
        $this->assertEquals(true, $received);
    }

    public function testPropagation()
    {
        $value = null;
        $this->eventDispatcher->addListener('foo', function () use (&$value) {
            $value = 'bar';
        });
        $this->eventDispatcher->addListener('foo', function () use (&$value) {
            $value = 'baz';
        });
        
        $this->eventDispatcher->trigger(new ETLEvent('foo'));
        $this->assertEquals('baz', $value);
    }

    public function testStopPropagation()
    {
        $value = null;
        $this->eventDispatcher->addListener('foo', function (ETLEvent $event) use (&$value) {
            $value = 'bar';
            $event->stopPropagation();
        });
        $this->eventDispatcher->addListener('foo', function () use (&$value) {
            $value = 'baz';
        });
        
        $this->eventDispatcher->trigger(new ETLEvent('foo'));
        $this->assertEquals('bar', $value);
    }
}
