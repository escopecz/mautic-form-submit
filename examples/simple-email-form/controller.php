<?php

// Composer autoloading
require __DIR__.'/../../vendor/autoload.php';

use Escopecz\MauticFormSubmit\Mautic;

class Controller
{
	public function __construct()
	{
		foreach ($_POST as $key => $val) {
			$_SESSION[$key] = $val;
		}

		if (isset($_POST['f_email']) && isset($_POST['mautic_base_url']) && isset($_POST['form_id'])) {
			$mautic = new Mautic($_POST['mautic_base_url']);
			$form = $mautic->getForm($_POST['form_id']);

			$info = $form->submit(
				[
					'f_email' => $_POST['f_email'],
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
