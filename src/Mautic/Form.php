<?php

declare(strict_types=1);

namespace Escopecz\MauticFormSubmit\Mautic;

use Escopecz\MauticFormSubmit\Mautic;
use Escopecz\MauticFormSubmit\HttpHeader;

class Form
{

    public function __construct(
        protected Mautic $mautic,
        protected int    $id
    ) {
    }

    /**
     * Submit the $data array to the Mautic form, using the optional $curlOpts
     * array to override curl settings
     * Returns array containing info about the request, response and cookie
     */
    public function submit(array $data, array $curlOpts = []): array
    {
        $originalCookie = $this->mautic->getCookie()->getSuperGlobalCookie();
        $request = $this->prepareRequest($data);

        $ch = curl_init($request['url']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request['query']);

        if (isset($request['header'])) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $request['header']);
        }

        if (isset($request['referer'])) {
            curl_setopt($ch, CURLOPT_REFERER, $request['referer']);
        }

        if (isset($request['cookie'])) {
            curl_setopt($ch, CURLOPT_COOKIEFILE, $this->mautic->getCookie()->createCookieFile());
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, $this->mautic->getConfig()->getCurlVerbose());
        curl_setopt($ch, CURLOPT_HEADER, 1);

        foreach ($curlOpts as $key => $value) {
            curl_setopt($ch, $key, $value);
        }

        $result = curl_exec($ch);
        $response = $this->prepareResponse($result);
        $response['info'] = curl_getinfo($ch);
        curl_close($ch);

        $contact = $this->mautic->getContact();
        $httpHeader = new HttpHeader($response['header']);
        $sessionId = $httpHeader->getCookieValue(Cookie::MAUTIC_SESSION_ID);
        $deviceId = $httpHeader->getCookieValue(Cookie::MAUTIC_DEVICE_ID);
        $contactId = $httpHeader->getCookieValue($sessionId);

        if ($sessionId) {
            $contact->setSessionId($sessionId);
        }

        if ($deviceId) {
            $contact->setDeviceId($deviceId);
        }

        if ($contactId) {
            $contact->setId((int)$contactId);
        }

        return [
            'original_cookie' => $originalCookie,
            'new_cookie' => $this->mautic->getCookie()->toArray(),
            'request' => $request,
            'response' => $response,
        ];
    }

    /**
     * Prepares data for CURL request based on provided form data, $_COOKIE and $_SERVER
     */
    public function prepareRequest(array $data): array
    {
        $contact = $this->mautic->getContact();
        $request = ['header' => []];

        $data['formId'] = $this->id;

        // return has to be part of the form data array so Mautic would accept the submission
        if (!isset($data['return'])) {
            $data['return'] = '';
        }

        $request['url'] = $this->getUrl();
        $request['data'] = ['mauticform' => $data];

        if ($contactId = $contact->getId()) {
            $request['data']['mtc_id'] = $contactId;
        }

        if ($contactIp = $contact->getIp()) {
            $request['header'][] = "X-Forwarded-For: $contactIp";
            $request['header'][] = "Client-Ip: $contactIp";
        }

        if ($sessionId = $contact->getSessionId()) {
            $request['header'][] = "Cookie: mautic_session_id=$sessionId";
            $request['header'][] = "Cookie: mautic_device_id=$sessionId";
        }

        if (isset($_SERVER['HTTP_REFERER'])) {
            $request['referer'] = $_SERVER["HTTP_REFERER"];
        }

        $request['query'] = http_build_query($request['data']);

        return $request;
    }

    /**
     * Process the result and split into headers and content
     */
    public function prepareResponse(string|bool $result): array
    {
        $response = ['header' => null, 'content' => null];
        $d = "\r\n\r\n"; // Headers and content delimiter

        if (is_string($result) && str_contains($result, $d)) {
            [$header, $content] = explode($d, $result, 2);
            if (stripos($header, '100 Continue') !== false && str_contains($content, $d)) {
                [$header, $content] = explode($d, $content, 2);
            }
            $response['header'] = $header;
            $response['content'] = htmlentities($content);
        }

        return $response;
    }

    /**
     * Builds the form URL
     */
    public function getUrl(): string
    {
        return sprintf('%s/form/submit?formId=%d', $this->mautic->getBaseUrl(), $this->id);
    }

    public function getId(): int
    {
        return $this->id;
    }
}
