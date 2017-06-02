<?php

namespace Escopecz\MauticFormSubmit\Mautic;

use Escopecz\MauticFormSubmit\Mautic;

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
     * Submit the $data array to the Mautic form
     * Returns array containing info about the request, response and cookie
     *
     * @param  array  $data
     *
     * @return array
     */
    public function submit(array $data)
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
        // Un-comment these two lines if you need to bypass SSL certificate validation
        // from cURL when it calls  your site
        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        list($header, $content) = explode("\r\n\r\n", curl_exec($ch), 2);
        $response['header'] = $header;
        $response['content'] = htmlentities($content);
        $response['info'] = curl_getinfo($ch);
        curl_close($ch);

        $contact = $this->mautic->getContact();

        if ($sessionId = $this->getSessionIdFromHeader($response['header'])) {
            $contact->setSessionId($sessionId);
        }

        if ($contactId = $this->getContactIdFromHeader($response['header'], $sessionId)) {
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
     * Finds the session ID hash in the response header
     *
     * @param  string $headers
     *
     * @return string|null
     */
    public function getSessionIdFromHeader($headers)
    {
        if (!$headers) {
            return null;
        }

        preg_match("/mautic_session_id=(.+?);/", $headers, $matches);

        if (isset($matches[1])) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Finds the Mautic Contact ID hash in the response header
     *
     * @param  string $headers
     * @param  string $sessionId
     *
     * @return int|null
     */
    public function getContactIdFromHeader($headers, $sessionId)
    {
        if (!$headers || !$sessionId) {
            return null;
        }

        preg_match("/$sessionId=(.+?);/", $headers, $matches);

        if (isset($matches[1])) {
            return (int) $matches[1];
        }

        return null;
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
        }

        if ($sessionId = $contact->getSessionId()) {
            $request['header'][] = "Cookie: mautic_session_id=$sessionId";
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
