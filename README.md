# PHP library for submitting Mautic Form from 3rd party app

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/escopecz/mautic-form-submit/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/escopecz/mautic-form-submit/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/escopecz/mautic-form-submit/badges/build.png?b=master)](https://scrutinizer-ci.com/g/escopecz/mautic-form-submit/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/escopecz/mautic-form-submit/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/escopecz/mautic-form-submit/?branch=master)

Submitting a form can get handy if you want to process the data with your app, but you want to send them to Mautic too. Mautic can then run automated tasks triggered by the form submission. [Read more about it](https://medium.com/@jan_linhart/the-simplest-way-how-to-submit-a-form-data-to-mautic-1454d3afd005) in the original post.

Since the new Mautic versions prefer cookie tracking over IP tracking which makes more tedious to submit the form as the tracked contact, this library will take care of the cookie sending via CURL. It will also listen the cookie from the response and updates the contact cookie with the values from the submit response. This way if the contact ID changes because of contact merge, the contact will continue browsing under the new contact ID.

The automatic cookie handling requires that your form will be on a page tracked by the Mautic JS tracking which provides the Mautic contact cookie in the first place.

## Install

### Via Composer

```bash
composer require escopecz/mautic-form-submit
```

## Usage

```php
//basic form data
$email		= $_REQUEST['email'] ?? $_REQUEST['Email'] ?? $_REQUEST['E-mail'] ?? $_REQUEST['E-MAIL']; // mauticform_input_mkregistraciaslp_email
$name		  = $_REQUEST['name'] ?? $_REQUEST['Name']; // mauticform_input_mkregistraciaslp_name
$phone		= $_REQUEST['phone'] ?? $_REQUEST['Phone']; // mauticform_input_mkregistraciaslp_phone
$form_id	= $_REQUEST['form_id'] ?? $_REQUEST['Form_id']; // mauticform_input_mkregistraciaslp_form_id
if (is_null($form_id)) {$form_id  = 1;} // if form_id is empty then use the 1'st Mautic form
$form   = $mautic->getForm($form_id);

// Require Composer autoloader
require __DIR__.'/vendor/autoload.php';
// or  require __DIR__.'/../../vendor/autoload.php'; if you place it not in the root folder, but for example in the /docroot/webhook/ folder

// Define the namespace of the Mautic object
use Escopecz\MauticFormSubmit\Mautic;

// Define the namespace of the Mautic configuration object
use Escopecz\MauticFormSubmit\Mautic\Config;

// It's optional to declare the configuration object to change some default values.
// For example to disable Curl verbose logging.
$config = new Config;
$config->setCurlVerbose(true);

// Instantiate the Mautic object with the base URL where the Mautic runs
$mautic = new Mautic('https://mymautic.com');

// Create a new instance of the Form object with with form id passed from forms
$form   = $mautic->getForm($form_id);

// Submit provided data array to the form 342
$result = $form->submit(['COOKIES' => $_REQUEST['COOKIES'], 'email' => $email, 'phone' => $phone, 'f_name' => $name,]);
```

- The integer passed to the `getForm()` method must be ID of the Mautic form.
- The array passed to the `submit()` method must be associative array of `['mautic_field_alias' => 'the_value']`.

For working example see the `examples` dir.

## Run project
```
ddev start 
```
Project url: https://mautic-form-submit.ddev.site/

## Testing

```
composer test
composer cs
composer phpstan
```

### Current status

[Travis](https://travis-ci.org/escopecz/mautic-form-submit)
[Scrutinizer](https://scrutinizer-ci.com/g/escopecz/mautic-form-submit)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
