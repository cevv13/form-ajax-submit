# Form-ajax-submit
Make ajax validation with Laravel Requests for forms with bootstrap

This work is an adaptation of: https://github.com/guicho0601/laravel-form-ajax-validation

See the [full documentation.](https://github.com/guicho0601/laravel-form-ajax-validation/wiki)


##Installation

### 1. Composer

Add to the composer of your project

```console
composer require cevv13/form-ajax-submit
```

Or edit your composer.json

```json
"require": {
    "cevv13/form-ajax-submit": "dev-master"
},
```

### 2. Add the ServiceProvider

Open the file config/app.php

```php
"providers": {
    ...
    'Cevv13\FormAjaxSubmit\FormAjaxSubmitServiceProvider',
    ...
},
```

### 3. Publish vendor resources

You need to publish the necessary views for create the scripts of jQuery

```console
$ php artisan vendor:publish --tag=formajaxsubmit
```

### Author
- Name:  Carlos Villarroel
- Email: cevv13@hotmail.com

### License

The form-ajax-submit library is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
