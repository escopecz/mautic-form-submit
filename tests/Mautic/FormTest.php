<?php

declare(strict_types=1);

namespace Escopecz\MauticFormSubmit\Test\Mautic;

use Escopecz\MauticFormSubmit\Mautic;
use Escopecz\MauticFormSubmit\Mautic\Form;
use PHPUnit\Framework\TestCase;


class FormTest extends TestCase
{
    private string $baseUrl = 'https://mymautic.com';

    function test_get_id_int_standalone(): void
    {
        $mautic = new Mautic($this->baseUrl);
        $formId = 3434;
        $form = new Form($mautic, $formId);

        $this->assertSame($formId, $form->getId());
    }

    function test_get_id_int_in_mautic_object(): void
    {
        $mautic = new Mautic($this->baseUrl);
        $formId = 3434;
        $form = $mautic->getForm($formId);

        $this->assertSame($formId, $form->getId());
    }

    function test_prepare_request(): void
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

    /**
     * @dataProvider response_result_provider
     */
    function test_prepare_response($result, $expectedHeader, $expectedContentType): void
    {
        $mautic = new Mautic($this->baseUrl);
        $formId = 3434;
        $form = $mautic->getForm($formId);

        $response = $form->prepareResponse($result);

        $this->assertSame($expectedHeader, $response['header']);
        switch ($expectedContentType) {
            case 'string':
                $this->assertIsString($response['content']);
                break;
            case 'null':
                $this->assertNull($response['content']);
                break;
            default:
                throw new InvalidArgumentException("Nieznany typ: $expectedContentType");
        }
    }

    static function response_result_provider(): array
    {
        $continue = "HTTP/1.1 100 Continue";
        $header = "HTTP/1.1 302 Found\r
Date: Wed, 17 Apr 2019 11:41:44 GMT\r
Content-Type: text/html; charset=UTF-8\r
Location: ...";
        $content = '<html></html>';

        $d = "\r\n\r\n"; // Delimiter between headers and content

        return [
            // Normal response: headers + content
            [$header . $d . $content, $header, 'string'],

            // Continue response: 100 Continue + headers + content
            [$continue . $d . $header . $d . $content, $header, 'string'],

            // cURL returning false because of failure to execute request
            [false, null, 'null'],
        ];
    }

    function test_get_url(): void
    {
        $mautic = new Mautic($this->baseUrl);
        $formId = 3434;
        $form = $mautic->getForm($formId);

        $this->assertSame($this->baseUrl.'/form/submit?formId='.$formId, $form->getUrl());
    }
}