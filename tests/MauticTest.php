<?php

namespace Escopecz\MauticFormSubmit\Test;

use Escopecz\MauticFormSubmit\Mautic;
use Escopecz\MauticFormSubmit\Mautic\Form;

class MauticTest extends \PHPUnit_Framework_TestCase
{
    private $baseUrl = 'https://mymautic.com';

    function test_get_base_url()
    {
        $mautic = new Mautic($this->baseUrl);
        $this->assertSame($this->baseUrl, $mautic->getBaseUrl());
    }

    function test_get_form()
    {
        $mautic = new Mautic($this->baseUrl);
        $formId = 7;
        $form = $mautic->getForm($formId);

        $this->assertInstanceOf(Form::class, $form);
        $this->assertSame($formId, $form->getId());
    }
}
