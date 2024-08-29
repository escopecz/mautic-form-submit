<?php

declare(strict_types=1);

namespace Escopecz\MauticFormSubmit\Mautic;

class Contact
{
    /**
     * Mautic contact IP address
     */
    protected string $ip;

    /**
     * Constructor
     */
    public function __construct(
        protected Cookie $cookie
    ) {
        $this->ip = $this->getIpFromServer();
    }

    /**
     * Returns Contact ID
     */
    public function getId(): int
    {
        return (int) $this->cookie->getContactId();
    }

    /**
     * Set Mautic Contact ID to global cookie
     */
    public function setId(int $contactId): static
    {
        $this->cookie->setContactId($contactId);

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }

    public function getSessionId(): ?string
    {
        return $this->cookie->getSessionId();
    }


    public function setSessionId(string $sessionId): static
    {
        $this->cookie->setSessionId($sessionId);

        return $this;
    }

    public function setDeviceId(string $deviceId): static
    {
        $this->cookie->setDeviceId($deviceId);

        return $this;
    }

    /**
     * Guesses IP address from $_SERVER
     */
    public function getIpFromServer(): string
    {
        $ip = '';
        $ipHolders = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($ipHolders as $ipHolder) {
            if (!empty($_SERVER[$ipHolder])) {
                $ip = $_SERVER[$ipHolder];
                if (str_contains((string) $ip, ',')) {
                    // Multiple IPs are present so use the last IP which should be
                    // the most reliable IP that last connected to the proxy
                    $ips = explode(',', (string) $ip);
                    $ips = array_map('trim', $ips);
                    $ip = end($ips);
                }
                $ip = trim((string) $ip);
                break;
            }
        }

        return $ip;
    }
}
