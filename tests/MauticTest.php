<?php

declare(strict_types=1);

namespace Escopecz\MauticFormSubmit\Test;

use Escopecz\MauticFormSubmit\Mautic;
use Escopecz\MauticFormSubmit\Mautic\Form;
use Escopecz\MauticFormSubmit\Mautic\Contact;
use Escopecz\MauticFormSubmit\Mautic\Cookie;
use PHPUnit\Framework\TestCase;

class MauticTest extends TestCase
{
    private string $baseUrl = 'https://mymautic.com';

    function test_get_base_url(): void
    {
        $mautic = new Mautic($this->baseUrl);
        $this->assertSame($this->baseUrl, $mautic->getBaseUrl());
    }

    function test_get_form(): void
    {
        $mautic = new Mautic($this->baseUrl);
        $formId = 7;
        $form = $mautic->getForm($formId);

        $this->assertInstanceOf(Form::class, $form);
        $this->assertSame($formId, $form->getId());
    }

    /**
     * @runInSeparateProcess
     */
    function test_get_contact(): void
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
    function test_get_set_contact(): void
    {
        $mautic = new Mautic($this->baseUrl);
        $contactId = 4;
        $contactIp = '234.3.2.33';
        $contact = new Contact(new Cookie);
        $contact->setId($contactId)
            ->setIp($contactIp);
        $mautic->setContact($contact);
        $contactB = $mautic->getContact();

        $this->assertInstanceOf(Contact::class, $contactB);
        $this->assertSame($contact->getId(), $contactB->getId());
        $this->assertSame($contact->getIp(), $contactB->getIp());
        $this->assertSame($contactId, $contactB->getId());
        $this->assertSame($contactIp, $contactB->getIp());
    }
}
