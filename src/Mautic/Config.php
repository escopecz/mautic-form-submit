<?php

declare(strict_types=1);

namespace Escopecz\MauticFormSubmit\Mautic;

/**
 * Configuration object for Mautic form submission.
 */
class Config
{
    /**
     * Curl verbose logging option
     */
    protected bool $curlVerbose = true;

    /**
     * Returns Curl verbose logging option
     */
    public function getCurlVerbose(): bool
    {
        return $this->curlVerbose;
    }
    
    /**
     * Set Curl verbose logging option
     */
    public function setCurlVerbose(bool $curlVerbose): static
    {
        $this->curlVerbose = $curlVerbose;

        return $this;
    }
}
