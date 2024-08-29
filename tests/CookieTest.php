<?php

declare(strict_types=1);

namespace Escopecz\MauticFormSubmit\Test;

use Escopecz\MauticFormSubmit\Cookie;
use PHPUnit\Framework\TestCase;


class CookieTest extends TestCase
{
    /**
     * @runInSeparateProcess
     */
    function test_set_get_unset(): void
    {
        $cookie = new Cookie();
        $key = 'some_cookie';

        $this->assertNull($cookie->get($key));

        $val = 452423;
        $cookie->set($key, $val);

        $this->assertSame((string) $val, $cookie->get($key));
        $this->assertSame($val, $cookie->getInt($key));

        $cookie->clear($key);

        $this->assertSame(0, $cookie->getInt($key));
    }

    /**
     * @runInSeparateProcess
     */
    function test_super_global_cookie(): void
    {
        $cookie = new Cookie();

        $this->assertTrue(is_array($cookie->getSuperGlobalCookie()));
    }

    /**
     * @runInSeparateProcess
     */
    function test_to_array(): void
    {
        $cookie = new Cookie();
        $key = 'some_cookie';
        $val = 452423;
        $cookie->set($key, $val);

        $this->assertSame([$key => $val], $cookie->toArray());
    }

    function test_get_cookie_file(): void
    {
        $cookie = new Cookie;
        $file = $cookie->createCookieFile();

        $this->assertTrue(is_string($file));
        $this->assertTrue(file_exists($file));
        $this->assertTrue(is_writable($file));
        $this->assertTrue(unlink($file));
    }
}