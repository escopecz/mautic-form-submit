<?php

namespace Escopecz\MauticFormSubmit\Test\Mautic;

use Escopecz\MauticFormSubmit\Mautic;
use Escopecz\MauticFormSubmit\Mautic\Form;

class FormTest extends \PHPUnit_Framework_TestCase
{
    private $baseUrl = 'https://mymautic.com';

    private $header = 'HTTP/1.1 302 Found
Date: Wed, 26 Apr 2017 06:08:08 GMT
Server: Apache/2.4.25 (Unix) OpenSSL/0.9.8zh PHP/7.0.15
X-Powered-By: PHP/7.0.15
Set-Cookie: 9743595cf0a472cb3ec0272949ffe7e8=67ma3ug969qnk5so4u6982mua0; path=/; HttpOnly
Set-Cookie: mautic_session_id=0435e490c376144325baa1a278c483cf071f92bf; expires=Thu, 26-Apr-2018 06:08:09 GMT; Max-Age=31536000; path=/
Set-Cookie: 0435e490c376144325baa1a278c483cf071f92bf=4491; expires=Thu, 26-Apr-2018 06:08:09 GMT; Max-Age=31536000; path=/
Cache-Control: no-cache
Location: http://localhost/mautic-form-submit/examples/simple-email-form/
Content-Length: 496
Content-Type: text/html; charset=UTF-8';

    function test_get_id_int_standalone()
    {
        $mautic = new Mautic($this->baseUrl);
        $formId = 3434;
        $form = new Form($mautic, $formId);

        $this->assertSame($formId, $form->getId());
    }

    function test_get_id_int_in_mautic_object()
    {
        $mautic = new Mautic($this->baseUrl);
        $formId = 3434;
        $form = $mautic->getForm($formId);

        $this->assertSame($formId, $form->getId());
    }

    function test_prepare_request()
    {
        $mautic = new Mautic($this->baseUrl);
        $formId = 3434;
        $form = new Form($mautic, $formId);
        $data = [
            'email' => 'john@doe.email',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ];
        $request = $form->prepareRequest($data);

        $this->assertSame($this->baseUrl.'/form/submit?formId='.$formId, $request['url']);
        $this->assertSame($data['email'], $request['data']['mauticform']['email']);
        $this->assertSame($data['first_name'], $request['data']['mauticform']['first_name']);
        $this->assertSame($data['last_name'], $request['data']['mauticform']['last_name']);
        $this->assertSame($formId, $request['data']['mauticform']['formId']);
        $this->assertSame('', $request['data']['mauticform']['return']);
    }

    function test_get_url()
    {
        $mautic = new Mautic($this->baseUrl);
        $formId = 3434;
        $form = $mautic->getForm($formId);

        $this->assertSame($this->baseUrl.'/form/submit?formId='.$formId, $form->getUrl());
    }

    function test_get_session_id_from_header()
    {
        $mautic = new Mautic($this->baseUrl);
        $form = $mautic->getForm(3434);
        $sessionId = $form->getSessionIdFromHeader($this->header);

        $this->assertSame('0435e490c376144325baa1a278c483cf071f92bf', $sessionId);
        $this->assertSame(null, $form->getSessionIdFromHeader(''));
    }

    function test_get_contact_id_from_request()
    {
        $mautic = new Mautic($this->baseUrl);
        $form = $mautic->getForm(3434);
        $contactId = $form->getContactIdFromHeader($this->header, '0435e490c376144325baa1a278c483cf071f92bf');

        $this->assertSame(4491, $contactId);
        $this->assertSame(null, $form->getContactIdFromHeader('', ''));
    }
}
