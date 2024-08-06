<?php

declare(strict_types=1);

namespace Escopecz\MauticFormSubmit;

use Escopecz\MauticFormSubmit\Mautic\Form;
use Escopecz\MauticFormSubmit\Mautic\Contact;
use Escopecz\MauticFormSubmit\Mautic\Cookie as MauticCookie;
use Escopecz\MauticFormSubmit\Mautic\Config;

class Mautic
{

    protected string $baseUrl;

    protected Contact $contact;


    protected MauticCookie $cookie;


    protected Config $config;

    public function __construct(string $baseUrl, Config $config = null)
    {
        $this->baseUrl = rtrim(trim($baseUrl), '/');
        $this->cookie = new MauticCookie;
        $this->contact = new Contact($this->cookie);
        $this->config = $config ?: new Config;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function getForm(int $id): Form
    {
        return new Form($this, $id);
    }

    public function setContact(Contact $contact): static
    {
        $this->contact = $contact;

        return $this;
    }

    public function getContact(): Contact
    {
        return $this->contact;
    }

    public function getCookie(): MauticCookie
    {
        return $this->cookie;
    }

    public function getConfig(): Config
    {
        return $this->config;
    }
}
