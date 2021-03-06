<?php

// Composer autoloading
require __DIR__.'/../../vendor/autoload.php';

use Escopecz\MauticFormSubmit\Mautic;
use Escopecz\MauticFormSubmit\Mautic\Config;

class Controller
{
    public function __construct()
    {
        foreach ($_POST as $key => $val) {
            $_SESSION[$key] = $val;
        }

	if (isset($_POST['email_label']) && isset($_POST[$_POST['email_label']]) && isset($_POST['mautic_base_url']) && isset($_POST['form_id'])) {
		
	    // It's optional to create a Config DTO object and pass it to the Mautic object.
	    // For example to set Curl verbose logging to true.
	    $config = new Config;
	    $config->setCurlVerbose(true);

            $mautic = new Mautic($_POST['mautic_base_url'], $config);
            $form = $mautic->getForm($_POST['form_id']);

            $info = $form->submit(
                [
                    $_POST['email_label'] => $_POST[$_POST['email_label']],
                ]
            );

            $_SESSION['info'] = $info;
        }

        header(rtrim('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], '/controller.php'));
        die();
    }
}

session_start();
new Controller;
