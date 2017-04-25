<?php

namespace Escopecz\MauticFormSubmit\Test;

use Escopecz\MauticFormSubmit\Mautic;

class MauticTest extends \PHPUnit_Framework_TestCase
{
    function test_get_base_url()
    {
        $baseUrl = 'https://mymautic.com';
        $mautic = new Mautic($baseUrl);
        $this->assertSame($baseUrl, $mautic->getBaseUrl());
    }
}
