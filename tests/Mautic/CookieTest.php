<?php

namespace Escopecz\MauticFormSubmit\Test\Mautic;

use Escopecz\MauticFormSubmit\Mautic\Cookie;

class CookieTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @runInSeparateProcess
     */
    function test_set_get_unset_contact_id()
    {
        $cookie = new Cookie;

        $this->assertEquals(null, $cookie->getContactId());

        $contactId = 4344;
        $cookie->setContactId($contactId);

        $this->assertEquals($contactId, $cookie->getContactId());
        $cookie->unsetContactId();

        $this->assertEquals(null, $cookie->getContactId());

    }

    /**
     * @runInSeparateProcess
     */
    function test_set_get_unset_session_id()
    {
        $cookie = new Cookie;

        $this->assertSame(null, $cookie->getSessionId());

        $sid = 'kjsfk3j2jnfl2kj3rl2kj';
        $cookie->set(Cookie::MAUTIC_SESSION_ID, $sid);

        $this->assertSame($sid, $cookie->getSessionId());
        $cookie->clear(Cookie::MAUTIC_SESSION_ID);

        $this->assertSame(null, $cookie->getSessionId());

        $cookie->set(Cookie::MTC_SID, $sid);
        $this->assertSame($sid, $cookie->getSessionId());

        $cookie->clear(Cookie::MTC_SID);
        $this->assertSame(null, $cookie->getSessionId());
    }

    /**
     * @runInSeparateProcess
     */
    function test_set_get_unset_session_id2()
    {
        $cookie = new Cookie;

        $this->assertSame(null, $cookie->getSessionId());

        $sid = 'kjsfk3j2jnfl2kj3rl2kj';
        $cookie->setSessionId($sid);

        $this->assertSame($sid, $cookie->getSessionId());

        $cookie->unsetSessionId();

        $this->assertSame(null, $cookie->getSessionId());

        $cookie->set(Cookie::MTC_SID, $sid);

        $this->assertSame($sid, $cookie->getSessionId());

        $cookie->unsetSessionId();

        $this->assertSame(null, $cookie->getSessionId());
    }
}