<?php

declare(strict_types=1);

namespace Escopecz\MauticFormSubmit\Mautic;

use Escopecz\MauticFormSubmit\Cookie as StandardCookie;

/**
 * Helper class to get cookie properties specific to Mautic
 */
class Cookie extends StandardCookie
{
    /**
     * Holds Mautic session ID defined by PHP
     */
    public const string MAUTIC_DEVICE_ID = 'mautic_device_id';

    /**
     * Holds Mautic session ID defined by PHP
     */
    public const string MAUTIC_SESSION_ID = 'mautic_session_id';

    /**
     * Holds Mautic session ID defined by JS
     */
    public const string MTC_SID = 'mtc_sid';

    /**
     * Holds Mautic Contact ID defined by JS
     */
    public const string MTC_ID = 'mtc_id';

    /**
     * Get Mautic Contact ID from Cookie
     */
    public function getContactId(): ?int
    {
        if (($mtcId = $this->getInt(self::MTC_ID)) !== 0) {
            return $mtcId;
        } elseif ($mauticSessionId = $this->getSessionId()) {
            return $this->getInt($mauticSessionId);
        }

        return null;
    }

    /**
     * Set Mautic Contact ID cookies
     * Note: Call setMauticSessionId prior to this
     */
    public function setContactId(int $contactId): static
    {
        $this->set(self::MTC_ID, $contactId);

        if ($sessionId = $this->getSessionId()) {
            $this->set($sessionId, $contactId);
        }

        return $this;
    }

    /**
     * Unit Mautic Contact ID cookies
     */
    public function unsetContactId(): static
    {
        $this->clear(self::MTC_ID);

        if ($sessionId = $this->getSessionId()) {
            $this->clear($sessionId);
        }

        return $this;
    }

    /**
     * Returns Mautic session ID if it exists in the cookie
     */
    public function getSessionId(): ?string
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
     */
    public function setSessionId(string $sessionId): static
    {
        $this->set(self::MAUTIC_SESSION_ID, $sessionId);
        $this->set(self::MTC_SID, $sessionId);

        return $this;
    }

    /**
     * Set Mautic Device ID cookies
     */
    public function setDeviceId(string $deviceId): static
    {
        $this->set(self::MAUTIC_DEVICE_ID, $deviceId);

        return $this;
    }

    /**
     * Unset Mautic Session ID cookies
     */
    public function unsetSessionId(): static
    {
        $this->clear(self::MAUTIC_SESSION_ID);
        $this->clear(self::MTC_SID);

        return $this;
    }
}
