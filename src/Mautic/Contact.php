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
     * Constructor
     *
     * @param int    $id will be taken from $_COOKIE if not provided
     * @param string $ip will be taken from $_SERVER if not provided
     */
    public function __construct($id = null, $ip = null)
    {
        if ($id === null) {
            $id = $this->getIdFromCookie();
        }

        if ($ip === null) {
            $ip = $this->getIpFromServer();
        }

        $this->id = (int) $id;
        $this->ip = $ip;
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
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
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
