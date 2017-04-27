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
require __DIR__.'/vendor/autoload.php';

$mautic = new \Escopecz\MauticFormSubmit\Mautic('https://mymautic.com');
$form = $mautic->getForm(342);
$result = $form->submit(
    [
        'f_email' => 'john@doe.email',
    ]
);
```

- The integer passed to the `getForm()` method must be ID of the Mautic form.
- The array passed to the `submit()` method must be associative array of `['mautic_field_alias' => 'the_value']`.

For working example see the `examples` dir.

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
