<?php

namespace Escopecz\MauticFormSubmit\Test\Mautic;

use Escopecz\MauticFormSubmit\Mautic;
use Escopecz\MauticFormSubmit\Mautic\Form;

class MauticTest extends \PHPUnit_Framework_TestCase
{
    private $baseUrl = 'https://mymautic.com';

    function test_get_id()
    {
        $mautic = new Mautic($this->baseUrl);
        $formId = 3434;
        $form = new Form($mautic, $formId);

        $this->assertSame($formId, $form->getId());
    }
}
