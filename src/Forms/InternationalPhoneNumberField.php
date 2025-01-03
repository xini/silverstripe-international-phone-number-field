<?php

namespace Innoweb\InternationalPhoneNumberField\Forms;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Manifest\ModuleResourceLoader;
use SilverStripe\Forms\TextField;
use SilverStripe\View\Requirements;

class InternationalPhoneNumberField extends TextField
{
    /**
     * @config
     * @var String|false $geolocation_service IP location service to determine the current users's country code. This can be 'ipstack', 'ipinfo', 'ipgeolocation' or 'freegeoip'. Defaults to 'freegeoip'.
     */
    private static $geolocation_service = 'freegeoip';

    /**
     * @config
     * @var String|false $geolocation_api_key API key for ipstack.com or ipinfo.io.
     */
    private static $geolocation_api_key = false;

    /**
     * @config
     * @var String $geolocation_protocol Protocol to be used to connecto to geolocation service. Defaults to 'https'.
     */
    private static $geolocation_protocol = 'https';

    /**
     * @config
     * @var String $initial_country Country code for initially shown country in the phone number field, e.g. 'au'. Defaults to 'auto', in which case the location is determined using geolocation if that's set up.
     */
    private static $initial_country = 'auto';

    /**
     * @config
     * @var Array|false $only_countries Array of country codes available for selection. Defaults to false, all countries are listed.
     */
    private static $only_countries = false;

    /**
     * @config
     * @var Array|false $preferred_countries Array of country codes pushed to the top of the dropdown list. Defaults to false, all countries are listed alphabetically.
     */
    private static $preferred_countries = false;

    /**
     * @config
     * @var Array|false $excluded_countries Array of country codes to be excluded from the dropdown lost. Defaults to false, all countries are listed.
     */
    private static $excluded_countries = false;

    /**
     * @var string
     */
    protected $initialCountry;

    /**
     * Used to determine if the number given is in the correct format when validating
     *
     * @var mixed
     */
    protected $originalValue = null;

    public function __construct($name, $title = null, $value = '')
    {
        parent::__construct($name, $title, $value);
        $this->addExtraClass('InternationalPhoneNumberField');
    }

    /**
     * {@inheritdoc}
     */
    public function Type()
    {
        return 'tel text';
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        // load data for template
        $IPLocationAPIKey = Config::inst()->get(InternationalPhoneNumberField::class, 'geolocation_api_key');
        $IPLocationAPIURL = '';
        $IPLocationReplyKey = 'country';
        $IPLocationService = Config::inst()->get(InternationalPhoneNumberField::class, 'geolocation_service');
        if ($IPLocationService) {
            $protocol = Config::inst()->get(InternationalPhoneNumberField::class, 'geolocation_protocol') ?: 'https';
            if ($IPLocationService == 'ipstack' && $IPLocationAPIKey) {
                $IPLocationAPIURL = Controller::join_links($protocol.'://api.ipstack.com', 'check', '?access_key='.$IPLocationAPIKey);
                $IPLocationReplyKey = 'country_code';
            } else if ($IPLocationService == 'ipinfo' && $IPLocationAPIKey) {
                $IPLocationAPIURL = Controller::join_links($protocol.'://ipinfo.io', '?token='.$IPLocationAPIKey);
                $IPLocationReplyKey = 'country';
            } else if ($IPLocationService == 'ipgeolocation' && $IPLocationAPIKey) {
                $IPLocationAPIURL = Controller::join_links($protocol.'://api.ipgeolocation.io/ipgeo', '?apiKey='.$IPLocationAPIKey);
                $IPLocationReplyKey = 'country_code2';
            } else if ($IPLocationService == 'freegeoip') {
                $IPLocationAPIURL = Controller::join_links($protocol.'://freegeoip.app/json/');
                $IPLocationReplyKey = 'country_code';
            }
        }

        $initialCountry = $this->getInitialCountry();
        if (!$initialCountry) {
            $initialCountry = Config::inst()->get(InternationalPhoneNumberField::class, 'initial_country') ? strtolower(Config::inst()->get(InternationalPhoneNumberField::class, 'initial_country')) : "'auto'";
        }
        $onlyCountries = Config::inst()->get(InternationalPhoneNumberField::class, 'only_countries') ? strtolower(implode('-', Config::inst()->get(InternationalPhoneNumberField::class, 'only_countries'))) : '';
        $preferredCountries = Config::inst()->get(InternationalPhoneNumberField::class, 'preferred_countries') ? strtolower(implode('-', Config::inst()->get(InternationalPhoneNumberField::class, 'preferred_countries'))) : '';
        $excludedCountries = Config::inst()->get(InternationalPhoneNumberField::class, 'excluded_countries') ? strtolower(implode('-', Config::inst()->get(InternationalPhoneNumberField::class, 'excluded_countries'))) : '';

        return array_merge(
            parent::getAttributes(),
            array(
                'type' => 'tel',
                'inputmode' => 'tel',
                'data-apiurl' => $IPLocationAPIURL,
                'data-apireplykey' => $IPLocationReplyKey,
                'data-initialcountry' => $initialCountry,
                'data-onlycountries' => $onlyCountries,
                'data-preferredcountries' => $preferredCountries,
                'data-excludedcountries' => $excludedCountries,
                'data-utilsscripturl' => ModuleResourceLoader::resourceURL('innoweb/silverstripe-international-phone-number-field:client/dist/javascript/intl-phone-number-utils.js')
            )
        );
    }

    public function setInitialCountry($countryCode)
    {
        $this->initialCountry = strtolower($countryCode);
        return $this;
    }

    public function getInitialCountry()
    {
        return $this->initialCountry;
    }

    /**
     * @param array $properties
     * @return string
     */
    public function Field($properties = [])
    {
        // load requirements
        Requirements::css('innoweb/silverstripe-international-phone-number-field:client/dist/css/intl-phone-number-field.css');
        Requirements::javascript('innoweb/silverstripe-international-phone-number-field:client/dist/javascript/intl-phone-number-field.js');

        return parent::Field($properties);
    }

    public function setSubmittedValue($value, $data = null)
    {
        // Save original value
        $this->originalValue = $value;

        // Null case
        if (strlen($value ?? '') === 0) {
            $this->value = null;
            return $this;
        }

        // Format number
        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            $numberProto = $phoneUtil->parse($value, null);
            if ($phoneUtil->isValidNumber($numberProto)) {
                $this->value = $phoneUtil->format($numberProto, PhoneNumberFormat::INTERNATIONAL);
            } else {
                $this->value = false;
            }
        } catch (NumberParseException $e) {
            $this->value = false;
        }
        return $this;
    }

    public function Value()
    {
        // Show invalid value back to user in case of error
        if ($this->value === null || $this->value === false) {
            return $this->originalValue;
        }
        return $this->value;
    }

    public function setValue($value, $data = null)
    {
        $this->originalValue = $value;

        if (strlen($value ?? '') === 0) {
            $this->value = null;
            return $this;
        }

        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            $numberProto = $phoneUtil->parse($value, null);
            if ($phoneUtil->isValidNumber($numberProto)) {
                $this->value = $phoneUtil->format($numberProto, PhoneNumberFormat::INTERNATIONAL);
            } else {
                $this->value = false;
            }
        } catch (NumberParseException $e) {
            $this->value = null;
        }
        return $this;
    }

    public function validate($validator)
    {
        $result = true;
        $phoneUtil = PhoneNumberUtil::getInstance();
        if ($this->value === false) {
            $validator->validationError(
                $this->name,
                _t('InternationalPhoneNumberField.VALIDATION', 'Please enter a valid phone number in international format, e.g. "+41 44 668 1800".'),
                'validation'
            );
            $result = false;
        } elseif ($this->value) {
            try {
                $numberProto = $phoneUtil->parse(trim($this->value), null);
                if (!$phoneUtil->isValidNumber($numberProto)) {
                    $validator->validationError(
                        $this->name,
                        _t('InternationalPhoneNumberField.VALIDATION', 'Please enter a valid phone number in international format, e.g. "+41 44 668 1800".'),
                        'validation'
                    );
                    $result = false;
                }
            } catch (NumberParseException $e) {
                $validator->validationError(
                    $this->name,
                    _t('InternationalPhoneNumberField.VALIDATION', 'Please enter a valid phone number in international format, e.g. "+41 44 668 1800".'),
                    'validation'
                );
                $result = false;
            }
        }
        return $this->extendValidationResult($result, $validator);
    }

    public function getSchemaValidation()
    {
        $rules = parent::getSchemaValidation();
        $rules['internationalPhone'] = true;
        return $rules;
    }
}
