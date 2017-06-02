<?php

namespace Escopecz\MauticFormSubmit\Test\Mautic;

use Escopecz\MauticFormSubmit\Mautic;
use Escopecz\MauticFormSubmit\Mautic\Contact;
use Escopecz\MauticFormSubmit\Mautic\Cookie;

class ContactTest extends \PHPUnit_Framework_TestCase
{
    private $baseUrl = 'https://mymautic.com';

    /**
     * @runInSeparateProcess
     */
    function test_get_contact_from_mautic()
    {
        $mautic = new Mautic($this->baseUrl);
        $contact = $mautic->getContact();

        $this->assertInstanceOf(Contact::class, $contact);
        $this->assertSame(0, $contact->getId());
        $this->assertSame('', $contact->getIp());
    }

    /**
     * @runInSeparateProcess
     */
    function test_set_get_id()
    {
        $contactId = 452;
        $mautic = new Mautic($this->baseUrl);
        $contact = $mautic->getContact();
        $contact->setId($contactId);

        $this->assertSame($contactId, $contact->getId());
    }

    function test_set_get_ip()
    {
        $ip = '345.2.2.2';
        $mautic = new Mautic($this->baseUrl);
        $contact = $mautic->getContact();
        $contact->setIp($ip);

        $this->assertSame($ip, $contact->getIp());
    }

    /**
     * @runInSeparateProcess
     */
    function test_get_id_from_mtc_id_cookie()
    {
        $contactId = 4344;
        $cookie = new Cookie;
        $cookie->setContactId($contactId);
        $contact = new Contact($cookie);

        $this->assertSame($contactId, $contact->getId());
        $cookie->clear(Cookie::MTC_ID);
    }

    /**
     * @runInSeparateProcess
     */
    function test_get_id_from_mautic_session_id_cookie()
    {
        $contactId = 4344;
        $sessionId = 'slk3jhkn3gkn23lkgn3lkgn';
        $cookie = new Cookie;
        $cookie->setSessionId($sessionId)
            ->setContactId($contactId);
        $contact = new Contact($cookie);

        $this->assertEquals($contactId, $contact->getId());
        $cookie->unsetSessionId()
            ->unsetContactId();
    }

    function test_get_ip_from_server()
    {
        $contactIp = '345.2.2.2';
        $_SERVER['REMOTE_ADDR'] = $contactIp;
        $contact = new Contact(new Cookie);

        $this->assertSame($contactIp, $contact->getIp());
        unset($_SERVER['REMOTE_ADDR']);
    }

    function test_get_ip_from_server_method()
    {
        $contact = new Contact(new Cookie);

        $this->assertSame('', $contact->getIpFromServer());

        $contactIp = '345.2.2.2';
        $_SERVER['REMOTE_ADDR'] = $contactIp;

        $this->assertSame($contactIp, $contact->getIpFromServer());
        unset($_SERVER['REMOTE_ADDR']);
    }

    function test_get_ip_from_server_method_when_multiple_ips()
    {
        $contact = new Contact(new Cookie);

        $this->assertSame('', $contact->getIpFromServer());

        $_SERVER['REMOTE_ADDR'] = '222.333.444.4., 555.666.777.7, 345.2.2.2';

        // The last IP from the list is the right one
        $this->assertSame('345.2.2.2', $contact->getIpFromServer());
        unset($_SERVER['REMOTE_ADDR']);
    }

    /**
     * @runInSeparateProcess
     */
    function test_set_session_id_to_cookie()
    {
        $cookie = new Cookie;
        $contact = new Contact($cookie);

        $this->assertSame(null, $contact->getSessionId());

        $sessionId = 'sadfasfd98fuasofuasd9f87asfo';
        $contact->setSessionId($sessionId);

        $this->assertSame($sessionId, $contact->getSessionId());
        $cookie->unsetSessionId();
    }

    /**
     * @runInSeparateProcess
     */
    function test_set_contact_id_to_cookie()
    {
        $cookie = new Cookie;
        $contact = new Contact($cookie);

        $this->assertSame(0, $contact->getId());

        $contactId = 2332;
        $contact->setId($contactId);

        $this->assertSame($contactId, $contact->getId());
        $this->assertEquals($contactId, $cookie->getContactId());
        $cookie->unsetContactId();
    }
}
