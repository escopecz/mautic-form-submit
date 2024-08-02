<?php

declare(strict_types=1);

namespace Escopecz\MauticFormSubmit;

class HttpHeader
{
    private array $headers = [];

    private array $cookies = [];

    public function __construct($textHeaders)
    {
        $this->parse($textHeaders);
    }

    public function getHeaderValue(string $key): ?string
    {
        return $this->headers[$key] ?? null;
    }

    public function getCookieValue(?string $key): ?string
    {
        return $this->cookies[$key] ?? null;
    }

    /**
     * Parse text headers and fills in cookies and headers properites
     */
    private function parse(string $headers): void
    {
        foreach (preg_split('/\r\n|\r|\n/', $headers) as $i => $line) {
            if ($i === 0) {
                $this->headers['http_code'] = $line;
            } else {
                [$key, $value] = explode(': ', $line);

                if ($key === 'Set-Cookie') {
                    [$textCookie] = explode(';', $value);
                    [$cookieKey, $cookieValue] = explode('=', $textCookie);

                    $this->cookies[$cookieKey] = $cookieValue;
                } else {
                    $this->headers[$key] = $value;
                }
            }
        }
    }
}
