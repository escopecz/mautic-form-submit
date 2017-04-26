<?php

namespace Escopecz\MauticFormSubmit\Mautic;

/**
 * Mautic Contact
 */
class Contact
{
    /**
     * Mautic contact ID
     *
     * @var int
     */
    protected $id;

    /**
     * Mautic contact IP address
     *
     * @var string
     */
    protected $ip;

    /**
     * Mautic Session ID
     *
     * @var string
     */
    protected $sessionId;

    /**
     * Constructor
     *
     * @param int    $id will be taken from $_COOKIE if not provided
     * @param string $ip will be taken from $_SERVER if not provided
     * @param string $sessionId will be taken from $_COOKIE if not provided
     */
    public function __construct($id = null, $ip = null, $sessionId = null)
    {
        if ($id === null) {
            $id = $this->getIdFromCookie();
        }

        if ($ip === null) {
            $ip = $this->getIpFromServer();
        }

        if ($sessionId === null) {
            $sessionId = $this->getMauticSessionIdFromCookie();
        }

        $this->id = (int) $id;
        $this->ip = $ip;
        $this->sessionId = $sessionId;
    }

    /**
     * Returns Contact ID
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * Returns Mautic Contact Session DI
     *
     * @return string|null
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * Gets Contact ID from $_COOKIE
     *
     * @return int|null
     */
    public function getIdFromCookie()
    {
        if (isset($_COOKIE['mtc_id'])) {
            return (int) $_COOKIE['mtc_id'];
        } elseif (isset($_COOKIE['mautic_session_id'])) {
            $mauticSessionId = $_COOKIE['mautic_session_id'];
            if (isset($_COOKIE[$mauticSessionId])) {
                return (int) $_COOKIE[$mauticSessionId];
            }
        }

        return null;
    }

    /**
     * Returns Mautic session ID if it exists in the cookie
     *
     * @return string|null
     */
    public function getMauticSessionIdFromCookie()
    {
        if (isset($_COOKIE['mautic_session_id'])) {
            return $_COOKIE['mautic_session_id'];
        }

        if (isset($_COOKIE['mtc_sid'])) {
            return $_COOKIE['mtc_sid'];
        }

        return null;
    }

    /**
     * Set Mautic session ID to global cookie
     *
     * @param string $sessionId
     *
     * @return Contact
     */
    public function setSessionIdCookie($sessionId)
    {
        $this->sessionId = $sessionId;
        $_COOKIE['mautic_session_id'] = $sessionId;
        $_COOKIE['mtc_sid'] = $sessionId;

        return $this;
    }

    /**
     * Set Mautic Contact ID to global cookie
     *
     * @param string $contactId
     *
     * @return Contact
     */
    public function setIdCookie($contactId)
    {
        $this->id = $contactId;

        $_COOKIE['mtc_id'] = $contactId;

        if ($this->sessionId) {
            $_COOKIE[$this->sessionId] = $contactId;
        }

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
