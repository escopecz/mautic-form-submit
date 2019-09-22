<?php

namespace Escopecz\MauticFormSubmit\Mautic;

/**
 * Configuration object for Mautic form submission.
 */
class Config
{
    /**
     * Curl verbose logging option
     *
     * @var bool
     */
    protected $curlVerbose = true;

    /**
     * Returns Curl verbose logging option
     *
     * @return bool
     */
    public function getCurlVerbose()
    {
        return $this->curlVerbose;
    }
    
    /**
     * Set Curl verbose logging option
     *
     * @param bool $curlVerbose
     *
     * @return Config
     */
    public function setCurlVerbose($curlVerbose)
    {
        $this->curlVerbose = $curlVerbose;

        return $this;
    }
}
