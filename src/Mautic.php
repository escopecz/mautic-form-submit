<?php

namespace Escopecz\MauticFormSubmit;

use Escopecz\MauticFormSubmit\Mautic\Form;
use Escopecz\MauticFormSubmit\Mautic\Contact;

/**
 * Mautic representation
 */
class Mautic
{
    /**
     * Mautic base (root) URL
     *
     * @var string
     */
    protected $baseUrl;

    /**
     * Mautic Contact
     *
     * @var Contact
     */
    protected $contact;

    /**
     * Constructor
     *
     * @param string $baseUrl
     */
    public function __construct($baseUrl)
    {
        $this->baseUrl = rtrim(trim($baseUrl), '/');
        $this->contact = new Contact();
    }

    /**
     * Returns Mautic's base URL
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Returns new Mautic Form representation object
     *
     * @param  int $id
     *
     * @return Form
     */
    public function getForm($id)
    {
        return new Form($this, $id);
    }

    /**
     * Sets the Mautic Contact if you want to replace the default one
     *
     * @param Contact $contact
     *
     * @return Mautic
     */
    public function setContact(Contact $contact)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Returns Mautic Contact representation object
     *
     * @return Contact
     */
    public function getContact()
    {
        return $this->contact;
    }
}
