# International Phone Number Field

[![Version](https://img.shields.io/packagist/v/innoweb/silverstripe-international-phone-number-field.svg?style=flat-square)](https://packagist.org/packages/innoweb/silverstripe-international-phone-number-field)
[![License](https://img.shields.io/packagist/l/innoweb/silverstripe-international-phone-number-field.svg?style=flat-square)](license.md)

## Introduction

Adds a database and form field for international phone numbers using [Google's libphonenumber](https://github.com/googlei18n/libphonenumber) and the [intl-tel-input plugin](https://github.com/jackocnr/intl-tel-input).

IP Geo Location services supported:
* [ipstack.com](https://ipstack.com)
* [ipinfo.io](https://ipinfo.io)
* [ipgeolocation](https://ipgeolocation.io/)
* [ipapi](https://freegeoip.app/) (default)

## Requirements

 * Silverstripe ^5
 * [libphonenumber port for PHP ^8](https://github.com/giggsey/libphonenumber-for-php)
 
 Note: this version is compatible with SilverStripe 5. For SilverStripe 4, please see the [4 release line](https://github.com/xini/silverstripe-international-phone-number-field/tree/4).

## Installation

Install the module using composer:
```
composer require innoweb/silverstripe-international-phone-number-field dev-master
```
Then run dev/build.

## Usage

### Database field

This module provides a database field to be used for data objects:

```
private static $db = [
	...
	'PhoneNumber' => 'Phone',
	...
];
```

This stores the phone number in the database as a varchar. 

In the CMS the data type `Phone` renders as a `InternationalPhoneNumberField`. 

In templates, the following formatting functions are available:

* `$PhoneNumber.International`: Returns the phone number in international format, e.g. "+41 44 668 1800"
* `$PhoneNumber.National`: Returns the phone number in national format, e.g. "044 668 1800"
* `$PhoneNumber.E164`: Returns the phone number in international format, but with no formatting applied, e.g. "+41446681800"
* `$PhoneNumber.URL` or `$PhoneNumber.RFC3966`: Returns the phone number in international format, but with all spaces and other separating symbols replaced with a hyphen, and with any phone number extension appended with ";ext=". It also will have a prefix of "tel:" added, e.g. "tel:+41-44-668-1800".

### Form field

The `InternationalPhoneNumberField` can be used for any Varchar field storing a phone number. 

## Configuration

To set the field to use the user's current location as default and customise the field, you can configure the following options for the `InternationalPhoneNumberField` class:

* `geolocation_service`: Uses IP location to determine the current users's country code. This can be `'ipstack'`, `'ipinfo'`, `'ipgeolocation'`, `'ipapi'` or `false`. Defaults to `false`.
* `geolocation_api_key`: API key for [ipstack.com](https://ipstack.com), [ipinfo.io](https://ipinfo.io) or [ipgeolocation](https://ipgeolocation.io/). Defaults to `false`.
* `geolocation_protocol`: Protocol to be used to connecto to geolocation service. Defaults to `'https'`.
* `initial_country`: Country code for initially shown country in the phone number field. Defaults to `'auto'`, in which case the location is determined using geolocation if that's set up.
* `load_default_from_user_agent`: Enable loading the default country from the user agent's timezone settings instead of a geo ip service. This is only used if `geolocation_service` is `false` and `initial_country` is `auto`. Defaults to `true`.
* `only_countries`: Array of country codes available for selection. Defaults to `false`, all countries are listed.
* `preferred_countries`: Array of country codes pushed to the top of the dropdown list. Defaults to `false`, all countries are listed alphabetically.
* `excluded_countries`: Array of country codes to be excluded from the dropdown lost. Defaults to `false`, all countries are listed.

## License

BSD 3-Clause License, see [License](license.md)
