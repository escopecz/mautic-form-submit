<?php

namespace Escopecz\MauticFormSubmit\Mautic;

use Escopecz\MauticFormSubmit\Mautic\Cookie;

/**
 * Mautic Contact
 */
class Contact
{
    /**
     * Mautic contact IP address
     *
     * @var string
     */
    protected $ip;

    /**
     * Mautic Cookie
     *
     * @var Cookie
     */
    protected $cookie;

    /**
     * Constructor
     *
     * @param Cookie $cookie
     */
    public function __construct(Cookie $cookie)
    {
        $this->cookie = $cookie;
        $this->ip = $this->getIpFromServer();
    }

    /**
     * Returns Contact ID
     *
     * @return int
     */
    public function getId()
    {
        return (int) $this->cookie->getContactId();
    }

    /**
     * Set Mautic Contact ID to global cookie
     *
     * @param int $contactId
     *
     * @return Contact
     */
    public function setId($contactId)
    {
        $this->cookie->setContactId($contactId);

        return $this;
    }

    /**
     * Returns Contact IP address
     *
     * @return string|null
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Sert Contact IP address
     *
     * @param string $ip
     *
     * @return Contact
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * Returns Mautic Contact Session ID
     *
     * @return string|null
     */
    public function getSessionId()
    {
        return $this->cookie->getSessionId();
    }

    /**
     * Set Mautic session ID to global cookie
     *
     * @param string $sessionId
     *
     * @return Contact
     */
    public function setSessionId($sessionId)
    {
        $this->cookie->setSessionId($sessionId);

        return $this;
    }

    /**
     * Guesses IP address from $_SERVER
     *
     * @return string
     */
    public function getIpFromServer()
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

        foreach ($ipHolders as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                if (strpos($ip, ',') !== false) {
                    // Multiple IPs are present so use the last IP which should be
                    // the most reliable IP that last connected to the proxy
                    $ips = explode(',', $ip);
                    array_walk($ips, create_function('&$val', '$val = trim($val);'));
                    $ip = end($ips);
                }
                $ip = trim($ip);
                break;
            }
        }

        return $ip;
    }
}
