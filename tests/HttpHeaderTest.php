<?php

declare(strict_types=1);

namespace Escopecz\MauticFormSubmit\Test;

use Escopecz\MauticFormSubmit\HttpHeader;
use PHPUnit\Framework\TestCase;


class HttpHeaderTest extends TestCase
{
    private string $testTextHeader = 'HTTP/1.1 302 Found
Date: Wed, 04 Jul 2018 08:33:39 GMT
Server: Apache/2.4.33 (Unix) OpenSSL/1.0.2o PHP/7.1.16
X-Powered-By: PHP/7.1.16
Set-Cookie: 10b307a255ccdba6a2d32498b0c61978=5329avlkmibr4galpllvo1rmf7; path=/; HttpOnly
Set-Cookie: mautic_session_id=deleted; expires=Thu, 01-Jan-1970 00:00:01 GMT; Max-Age=0; path=/
Set-Cookie: mautic_device_id=6txmz3mu2dslmkhrera668e; expires=Thu, 04-Jul-2019 08:33:39 GMT; Max-Age=31536000; path=/
Set-Cookie: mtc_id=18061; path=/
Set-Cookie: mtc_sid=6txmz3mu2dslmkhrera668e; path=/
Set-Cookie: mautic_session_id=6txmz3mu2dslmkhrera668e; expires=Thu, 04-Jul-2019 08:33:39 GMT; Max-Age=31536000; path=/
Set-Cookie: 6txmz3mu2dslmkhrera668e=18061; expires=Thu, 04-Jul-2019 08:33:39 GMT; Max-Age=31536000; path=/
Cache-Control: no-cache
Location: http://localhost/mautic-form-submit/examples/simple-email-form/?mauticMessage=Hello%21
X-Debug-Token: 152d17
X-Debug-Token-Link: http://mautic.test/index_dev.php/_profiler/152d17
Content-Length: 588
Content-Type: text/html; charset=UTF-8';

    function test_get_base_url(): void
    {
        $httpHeader = new HttpHeader($this->testTextHeader);
        $this->assertEquals('6txmz3mu2dslmkhrera668e', $httpHeader->getCookieValue('mautic_session_id'));
        $this->assertEquals('18061', $httpHeader->getCookieValue('mtc_id'));
        $this->assertEquals('18061', $httpHeader->getCookieValue('6txmz3mu2dslmkhrera668e'));
        $this->assertEquals('Apache/2.4.33 (Unix) OpenSSL/1.0.2o PHP/7.1.16', $httpHeader->getHeaderValue('Server'));
    }
}
