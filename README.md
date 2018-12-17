# International Phone Number Field

[![Version](https://img.shields.io/packagist/v/innoweb/silverstripe-international-phone-number-field.svg?style=flat-square)](https://packagist.org/packages/innoweb/silverstripe-international-phone-number-field)
[![License](https://img.shields.io/packagist/l/innoweb/silverstripe-international-phone-number-field.svg?style=flat-square)](license.md)

## Introduction

Adds a form field for international phone numbers using [Google's libphonenumber] (https://github.com/googlei18n/libphonenumber) and the [jQuery intl-tel-input plugin] (https://github.com/jackocnr/intl-tel-input).

## Requirements

 * SilverStripe ^4
 * [libphonenumber port for PHP ^8.9] (https://github.com/giggsey/libphonenumber-for-php)

## Installation

Install the module using composer:
```
composer require innoweb/silverstripe-international-phone-number-field dev-master
```
Then run dev/build.

## Configuration

In your project config you can configure the following options for the `InternationalPhoneNumberField` class:

* `geolocation_service`: Uses IP location to determine the current users's country code. This can be either `'ipstack'` or `'ipinfo'`. Defaults to `false`.
* `geolocation_api_key`: API key for [ipstack.com] (https://ipstack.com) or [ipinfo.io] (https://ipinfo.io).
* `geolocation_protocol`: Protocol to be used to connecto to geolocation service. Defaults to `'https'`.
* `initial_country`: country code for initially shown country in the phone number field. deafults to `'auto'`, in which case the location is determined using geolocation if that's set up.
* `only_countries`: array of country codes available for selection. Defaults to all countries.
* `preferred_countries`: array of country codes pushed to the top of the dropdown list. Deafults to none, all countries are listed alphabetically.
* `excluded_countries`: array of country codes to be excluded from the dropdown lost. Deafults to none.

## License

BSD 3-Clause License, see [License](license.md)
