<?php

namespace Woody\Http\Message\Tests;

use PHPUnit\Framework\TestCase;
use Woody\Http\Message\CookieFactory;

/**
 * Class CookieFactory
 *
 * @package Woody\Http\Message\Tests
 */
class CookieFactoryTest extends TestCase
{

    public function testSingle()
    {
        $header = 'name=value; Expires=Wed, 21 Oct 2025 07:28:00 GMT';

        $factory = new CookieFactory();
        $cookie = $factory->create($header);

        $this->assertEquals(['name' => 'value'], $cookie->getValues());
        $this->assertEquals(1761118080, $cookie->getExpires());
        $this->assertNull($cookie->getPath());
        $this->assertNull($cookie->getDomain());
        $this->assertNull($cookie->getSecure());
        $this->assertNull($cookie->getHttpOnly());
        $this->assertNull($cookie->getSameSite());
    }

    public function testDouble()
    {
        $header = 'id=a3fWa; name=value; Expires=Wed, 21 Oct 2025 07:28:00 GMT';

        $factory = new CookieFactory();
        $cookie = $factory->create($header);

        $this->assertEquals(['id' => 'a3fWa', 'name' => 'value'], $cookie->getValues());
        $this->assertEquals(1761118080, $cookie->getExpires());
        $this->assertNull($cookie->getPath());
        $this->assertNull($cookie->getDomain());
        $this->assertNull($cookie->getSecure());
        $this->assertNull($cookie->getHttpOnly());
        $this->assertNull($cookie->getSameSite());
    }

    public function testMaxAge()
    {
        $header = 'id=a3fWa; name=value; Max-Age=100';

        $factory = new CookieFactory();
        $cookie = $factory->create($header);

        $this->assertEquals(['id' => 'a3fWa', 'name' => 'value'], $cookie->getValues());
        $this->assertEquals(time() + 100, $cookie->getExpires());
        $this->assertNull($cookie->getPath());
        $this->assertNull($cookie->getDomain());
        $this->assertNull($cookie->getSecure());
        $this->assertNull($cookie->getHttpOnly());
        $this->assertNull($cookie->getSameSite());
    }

    public function testFull()
    {
        $header = 'name=value; Expires=Wed, 21 Oct 2025 07:28:00 GMT; path=/folder/; domain=www.free.fr; Secure; HttpOnly; SameSite=Lax';

        $factory = new CookieFactory();
        $cookie = $factory->create($header);

        $this->assertEquals(['name' => 'value'], $cookie->getValues());
        $this->assertEquals(1761118080, $cookie->getExpires());
        $this->assertEquals('/folder/', $cookie->getPath());
        $this->assertEquals('www.free.fr', $cookie->getDomain());
        $this->assertEquals(true, $cookie->getSecure());
        $this->assertEquals(true, $cookie->getHttpOnly());
        $this->assertEquals('Lax', $cookie->getSameSite());
    }

    public function testInvalid()
    {
        $header = 'Max-Age=100';

        $factory = new CookieFactory();
        $cookie = $factory->create($header);

        $this->assertNull($cookie);
    }
}
