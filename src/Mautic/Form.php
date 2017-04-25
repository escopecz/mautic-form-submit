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
     * @var int
     */
	protected $formId;

    /**
     * Constructor
     * 
     * @param Mautic $mautic
     * @param int    $formId
     */
    public function __construct(Mautic $mautic, $formId)
    {
    	$this->mautic = $mautic;
    	$this->formId = (int) $formId;
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

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        list($header, $content) = explode("\r\n\r\n", curl_exec($ch), 2);
        $response['header'] = $header;
        $response['content'] = htmlentities($content);
        $response['info'] = curl_getinfo($ch);
        curl_close($ch);

        return [
        	'$_COOKIE' => $_COOKIE,
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
    protected function prepareRequest(array $data)
    {
        $contact = $this->mautic->getContact();
        $contactId = $contact->getId();
        $contactIp = $contact->getIp();
        $request = [];

        $data['formId'] = $this->formId;

        // return has to be part of the form data array so Mautic would accept the submission
        if (!isset($data['return'])) {
            $data['return'] = '';
        }

        $request['url'] = $this->getFormUrl();
        $request['data'] = ['mauticform' => $data];

        if ($contactId) {
            $request['data']['mtc_id'] = $contactId;
        }

        if ($contactIp) {
            $request['header'] = ["X-Forwarded-For: $contactIp"];
        }

        if ($_SERVER['HTTP_REFERER']) {
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
    protected function getFormUrl()
    {
    	return sprintf('%s/form/submit?formId=%d', $this->mautic->getBaseUrl(), $this->formId);
    }
}
