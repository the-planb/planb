<?php

declare(strict_types=1);

namespace PlanB\Tests\Pattern;

use PHPUnit\Framework\TestCase;
use PlanB\Pattern\Traits\SingletonTrait;

class SingletonTest extends TestCase
{
    public function test_it_only_can_be_instantiated_once()
    {
        $singleton = Singleton::getInstance();

        $this->assertSame($singleton, Singleton::getInstance());
    }

    public function test_it_throws_an_exception_when_is_unserialized()
    {
        $singleton = Singleton::getInstance();
        $serialized = serialize($singleton);

        $this->expectExceptionMessage('Cannot unserialize a singleton.');
        unserialize($serialized);
    }
}

class Singleton
{
    use SingletonTrait;
}
