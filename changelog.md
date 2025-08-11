# Changelog

All notable changes to this project will be documented in this file.

This project adheres to [Semantic Versioning](http://semver.org/).

## [5.4.0]

* remove support for defunct freegeoip
* add support for ipapi.co
* add support for loading default country code from user agent time zone settings

## [5.3.4]

* fix initialisation and validation for jQuery

## [5.3.3]

* fix frontend build

## [5.3.2]

* fix namespace and workaround for missing userforms module

## [5.3.1]

* fix error if UserForms module is not installed

## [5.3.0]

* add inputmode=tel attribute
* add editable form field for Userforms module

## [5.2.0]

* update frontend dependencies
* fix backend field validation

## [5.1.1]

* fix frontend build

## [5.1.0]

* update frontend dependencies to gulp 5

## [5.0.0]

* add Silverstripe 5 compatibility

## [4.3.4]

* PHP 8.1 compatibility: ensure field has value before validation attempt

## [4.3.3]

* update frontend dependencies

## [4.3.2]

* remove support for Abstract Geolocation API

## [4.3.1]

* update frontend dependencies

## [4.3.0]

* add js field validation compatible with https://github.com/xini/silverstripe-form-validation

## [4.2.1]

* fix submission of data if phone number is not correct or empty

## [4.2.0]

* switch to national mode for better usability, use hidden field to submit complete phone number to the server

## [4.1.0]

* add support for Abstract, ipgeolocation and freegeoip
* update frontend dependencies

## [4.0.2]

* fix db field for empty phone number

## [4.0.1]

* update frontend dependencies

## [4.0.0]

* namespace changes
* add DBPhone database field
* fix form field for usage in CMS

## [3.0.4]

* Fix utils.js and allow setting initial country from back-end
* fix ipstack API URL for js call

## [3.0.3]

* make all paths relative to fix issue with configurable resources path

## [3.0.2]

* fix PHP validation
* improve validation error message 
* ugrade frontend build to gulp 4

## [3.0.1]

* fix ipstack API URL 

## [3.0.0]

* Upgrade to SilverStripe 4.x
* Change License from MIT to BSD 3-Clause
* Add support for ipstack geo location
* remove jQuery dependency

## [2.0.2]

* fix uppercase country codes in config

## [2.0.1]

* fix loading of empty default config values

## [2.0.0]

* make javascript configurable in SS config. Removed default preferredCountries, to be configured.
* update js field selector to be compatible with BootstrapForms module
* open update libphonenumber-for-php limitation
* add changelog, code-of-conduct, contributing guide

## [1.1.0]

* add token config for ipinfo web service to allow https connection to ipinfo.io

## [1.0.2]

* fix validation if field is empty

## [1.0.1]

* update license information


## [1.0.0]

Initial release
