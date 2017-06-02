<?php

namespace Escopecz\MauticFormSubmit\Mautic;

use Escopecz\MauticFormSubmit\Cookie as StandardCookie;

/**
 * Helper class to get cookie properties specific to Mautic
 */
class Cookie extends StandardCookie
{
    /**
     * Holds Mautic session ID defined by PHP
     *
     * @var string
     */
    const MAUTIC_SESSION_ID = 'mautic_session_id';

    /**
     * Holds Mautic session ID defined by JS
     *
     * @var string
     */
    const MTC_SID = 'mtc_sid';

    /**
     * Holds Mautic Contact ID defined by JS
     *
     * @var string
     */
    const MTC_ID = 'mtc_id';

    /**
     * Get Mautic Contact ID from Cookie
     *
     * @return int|null
     */
    public function getContactId()
    {
        if ($mtcId = $this->getInt(self::MTC_ID)) {
            return $mtcId;
        } elseif ($mauticSessionId = $this->getSessionId()) {
            return $this->getInt($mauticSessionId);
        }

        return null;
    }

    /**
     * Set Mautic Contact ID cookies
     * Note: Call setMauticSessionId prior to this
     *
     * @param int $contactId
     *
     * @return Cookie
     */
    public function setContactId($contactId)
    {
        $this->set(self::MTC_ID, $contactId);

        if ($sessionId = $this->getSessionId()) {
            $this->set($sessionId, $contactId);
        }

        return $this;
    }

    /**
     * Unet Mautic Contact ID cookies
     *
     * @return Cookie
     */
    public function unsetContactId()
    {
        $this->clear(self::MTC_ID);

        if ($sessionId = $this->getSessionId()) {
            $this->clear($sessionId);
        }

        return $this;
    }

    /**
     * Returns Mautic session ID if it exists in the cookie
     *
     * @return string|null
     */
    public function getSessionId()
    {
        if ($mauticSessionId = $this->get(self::MAUTIC_SESSION_ID)) {
            return $mauticSessionId;
        }

        if ($mauticSessionId = $this->get(self::MTC_SID)) {
            return $mauticSessionId;
        }

        return null;
    }

    /**
     * Set Mautic Session ID cookies
     *
     * @param string $sessionId
     *
     * @return Cookie
     */
    public function setSessionId($sessionId)
    {
        $this->set(self::MAUTIC_SESSION_ID, $sessionId);
        $this->set(self::MTC_SID, $sessionId);

        return $this;
    }

    /**
     * Unset Mautic Session ID cookies
     *
     * @return Cookie
     */
    public function unsetSessionId()
    {
        $this->clear(self::MAUTIC_SESSION_ID);
        $this->clear(self::MTC_SID);

        return $this;
    }
}
