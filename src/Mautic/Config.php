<?php

namespace Escopecz\MauticFormSubmit\Mautic;

/**
 * Mautic Configuration
 */
class Config
{
    /**
     * Curl verbose logging option
     *
     * @var bool
     */
    protected $curlVerbose = 1; 
    
    /**
     * Constructor
     *
     * @param int $curlVerbose
     */
    public function __construct(int $curlVerbose)
    {
        $this->curlVerbose = $curlVerbose;
    }

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
