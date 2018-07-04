<?php

namespace Escopecz\MauticFormSubmit;

/**
 * HTTP Header Helper
 */
class HttpHeader
{
    /**
     * Key-valye paries of headers
     *
     * @var array
     */
    private $headers = [];

    /**
     * Key-valye paries of cookies
     *
     * @var array
     */
    private $cookies = [];

    public function __construct($textHeaders)
    {
        $this->parse($textHeaders);
    }

    /**
     * @param string $key
     * 
     * @return string|null
     */
    public function getHeaderValue($key)
    {
        return isset($this->headers[$key]) ? $this->headers[$key] : null;
    }

    /**
     * @param string $key
     * 
     * @return string|null
     */
    public function getCookieValue($key)
    {
        return isset($this->cookies[$key]) ? $this->cookies[$key] : null;
    }

    /**
     * Parse text headers and fills in cookies and headers properites
     *
     * @param string $headers
     */
    private function parse($headers)
    {
        foreach (preg_split('/\r\n|\r|\n/', $headers) as $i => $line) {
            if ($i === 0) {
                $this->headers['http_code'] = $line;
            } else {
                list($key, $value) = explode(': ', $line);

                if ($key === 'Set-Cookie') {
                    list($textCookie) = explode(';', $value);
                    list($cookieKey, $cookieValue) = explode('=', $textCookie);

                    $this->cookies[$cookieKey] = $cookieValue;
                } else {
                    $this->headers[$key] = $value;
                }
            }
        }
    }
}
