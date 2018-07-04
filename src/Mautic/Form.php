<?php

namespace Escopecz\MauticFormSubmit\Mautic;

use Escopecz\MauticFormSubmit\Mautic;
use Escopecz\MauticFormSubmit\Mautic\Cookie;
use Escopecz\MauticFormSubmit\HttpHeader;

/**
 * Mautic form
 */
class Form
{
    /**
     * @var Mautic
     */
    protected $mautic;

    /**
     * Form ID
     *
     * @var int
     */
    protected $id;

    /**
     * Constructor
     *
     * @param Mautic $mautic
     * @param int    $id
     */
    public function __construct(Mautic $mautic, $id)
    {
        $this->mautic = $mautic;
        $this->id = $id;
    }

    /**
     * Submit the $data array to the Mautic form, using the optional $curlOpts
     * array to override curl settings
     * Returns array containing info about the request, response and cookie
     *
     * @param  array  $data
     * @param  array  $curlOpts
     *
     * @return array
     */
    public function submit(array $data, array $curlOpts = [])
    {
        $originalCookie = $this->mautic->getCookie()->getSuperGlobalCookie();
        $response = [];
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
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);

        foreach ($curlOpts as $key => $value) {
            curl_setopt($ch, $key, $value);
        }

        list($header, $content) = explode("\r\n\r\n", curl_exec($ch), 2);
        $response['header'] = $header;
        $response['content'] = htmlentities($content);
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
            $contact->setId($contactId);
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
     *
     * @param  array $data
     *
     * @return array
     */
    public function prepareRequest(array $data)
    {
        $contact = $this->mautic->getContact();
        $request = ['header'];

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
     * Builds the form URL
     *
     * @return string
     */
    public function getUrl()
    {
        return sprintf('%s/form/submit?formId=%d', $this->mautic->getBaseUrl(), $this->id);
    }

    /**
     * Returns the Form ID
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
