# International Phone Number Field

[![Version](https://img.shields.io/packagist/v/xini/silverstripe-international-phone-number-field.svg?style=flat-square)](https://packagist.org/packages/xini/silverstripe-international-phone-number-field)
[![License](https://img.shields.io/packagist/l/xini/silverstripe-international-phone-number-field.svg?style=flat-square)](license.md)

## Introduction

Adds a form field for international phone numbers using [Google's libphonenumber] (https://github.com/googlei18n/libphonenumber) and the [jQuery intl-tel-input plugin] (https://github.com/jackocnr/intl-tel-input).

## Requirements

 * SilverStripe 3.1+, <4.
 * [libphonenumber port for PHP] (https://github.com/giggsey/libphonenumber-for-php)

## Installation

Install the module using composer:
```
composer require xini/silverstripe-international-phone-number-field dev-master
```
or download or git clone the module into a ‘international-phone-number-field’ directory in your webroot.

Then run dev/build.

## Configuration

In your project config you can configure the following options for the `InternationalPhoneNumberField` class:

* `initial_country`: country code for initially shown country in the phone number field. deafults to 'auto', in which case the location is determined using ipinfo.io.
* `ipinfo_access_token`: access token for [ipinfo.io] (http://ipinfo.io/) to determine location of user. If no token is given, free service is used (limited to 1,000 requests per day, no https!)
* `only_countries`: array of country codes available for selection. Defaults to all countries.
* `preferred_countries`: array of country codes pushed to the top of the dropdown list. Deafults to none, all countries are listed alphabetically.

## License

MIT License, see [License](license.md)
