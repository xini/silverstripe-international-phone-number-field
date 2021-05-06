<?php

namespace Innoweb\InternationalPhoneNumberField\Forms;

use SilverStripe\Control\Controller;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Manifest\ModuleResourceLoader;
use SilverStripe\Forms\TextField;
use SilverStripe\View\Requirements;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class InternationalPhoneNumberField extends TextField
{
    /**
     * @config
     * @var String|false $geolocation_service IP location service to determine the current users's country code. This can be either 'ipstack' or 'ipinfo'. Defaults to 'false'.
     */
    private static $geolocation_service = false;

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
        if ($IPLocationAPIKey) {
            $IPLocationService = Config::inst()->get(InternationalPhoneNumberField::class, 'geolocation_service');
            $protocol = Config::inst()->get(InternationalPhoneNumberField::class, 'geolocation_protocol');
            if ($IPLocationService == 'ipstack') {
                $IPLocationAPIURL = Controller::join_links($protocol.'://api.ipstack.com', 'check', '?access_key='.$IPLocationAPIKey);
                $IPLocationReplyKey = 'country_code';
            } else if ($IPLocationService == 'ipinfo') {
                $IPLocationAPIURL = Controller::join_links($protocol.'://ipinfo.io', '?token='.$IPLocationAPIKey);
                $IPLocationReplyKey = 'country';
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

    public function setValue($value, $data = null)
    {
        if(empty($value)) {
            $this->value = null;
        } else {
            $phoneUtil = PhoneNumberUtil::getInstance();
            try {
                $numberProto = $phoneUtil->parse($value, null);
                if ($phoneUtil->isValidNumber($numberProto)) {
                    $this->value = $phoneUtil->format($numberProto, PhoneNumberFormat::INTERNATIONAL);
                }
            } catch (NumberParseException $e) {
                $this->value = $value;
            }
        }
        return $this;
    }

    public function validate($validator)
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            $this->value = trim($this->value);
            if ($this->value) {
                $numberProto = $phoneUtil->parse($this->value, null);
                if ($phoneUtil->isValidNumber($numberProto)) {
                    $this->value = $phoneUtil->format($numberProto, PhoneNumberFormat::INTERNATIONAL);
                } else {
                    $validator->validationError(
                        $this->name,
                        _t('InternationalPhoneNumberField.VALIDATION', 'Please enter a valid phone number in international format, e.g. "+41 44 668 1800".'),
                        'validation'
                    );
                    return false;
                }
            }
        } catch (NumberParseException $e) {
            $validator->validationError(
                $this->name,
                _t('InternationalPhoneNumberField.VALIDATION', 'Please enter a valid phone number in international format, e.g. "+41 44 668 1800".'),
                'validation'
            );
            return false;
        }
        return true;
    }

    public function getSchemaValidation()
    {
        $rules = parent::getSchemaValidation();
        $rules['internationalPhone'] = true;
        return $rules;
    }

}
