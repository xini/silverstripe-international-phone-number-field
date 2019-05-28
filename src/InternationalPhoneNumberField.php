<?php

namespace Innoweb\InternationalPhoneNumberField;

use SilverStripe\Control\Controller;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\TextField;
use SilverStripe\View\Requirements;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class InternationalPhoneNumberField extends TextField
{

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
        return array_merge(
            parent::getAttributes(),
            array(
                'type' => 'tel',
            )
        );
    }

    public function FieldHolder($properties = array())
    {

        // load requirements
        Requirements::css('innoweb/silverstripe-international-phone-number-field:client/dist/css/intl-phone-number-field.css');

        Requirements::javascript('innoweb/silverstripe-international-phone-number-field:client/dist/javascript/intl-phone-number-library.js');
        
        $IPLocationAPIKey = Config::inst()->get(InternationalPhoneNumberField::class, 'geolocation_api_key');
        $IPLocationAPIURL = '';
        $IPLocationReplyKey = 'country';
        if ($IPLocationAPIKey) {
            $IPLocationService = Config::inst()->get(InternationalPhoneNumberField::class, 'geolocation_service');
            $protocol = Config::inst()->get(InternationalPhoneNumberField::class, 'geolocation_protocol');
            if ($IPLocationService == 'ipstack') {
                $IPLocationAPIURL = Controller::join_links($protocol.'://api.ipstack.com', '?access_key='.$IPLocationAPIKey);
                $IPLocationReplyKey = 'country_code';
            } else if ($IPLocationService == 'ipinfo') {
                $IPLocationAPIURL = Controller::join_links($protocol.'://ipinfo.io', '?token='.$IPLocationAPIKey);
                $IPLocationReplyKey = 'country';
            }
        }
        
        $initialCountry = Config::inst()->get(InternationalPhoneNumberField::class, 'initial_country') ? strtolower(Config::inst()->get(InternationalPhoneNumberField::class, 'initial_country')) : "'auto'";
        $onlyCountries = Config::inst()->get(InternationalPhoneNumberField::class, 'only_countries') ? strtolower(str_replace('"', "'", json_encode(Config::inst()->get(InternationalPhoneNumberField::class, 'only_countries')))) : '[]';
        $preferredCountries = Config::inst()->get(InternationalPhoneNumberField::class, 'preferred_countries') ? strtolower(str_replace('"', "'", json_encode(Config::inst()->get(InternationalPhoneNumberField::class, 'preferred_countries')))) : '[]';
        $excludedCountries = Config::inst()->get(InternationalPhoneNumberField::class, 'excluded_countries') ? strtolower(str_replace('"', "'", json_encode(Config::inst()->get(InternationalPhoneNumberField::class, 'excluded_countries')))) : '[]';
        
        Requirements::javascriptTemplate(
            'innoweb/silverstripe-international-phone-number-field:client/dist/javascript/intl-phone-number-field.js',
            array(
                'APIURL' => $IPLocationAPIURL,
                'APIReplyKey' => $IPLocationReplyKey,
                'InitialCountry' => $initialCountry,
                'OnlyCountries' => $onlyCountries,
                'PreferredCountries' => $preferredCountries,
                'ExcludedCountries' => $excludedCountries,
            )
        );

        // call parent
        $html = parent::FieldHolder();
        return $html;
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
                $this->value = null;
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
                        _t('InternationalPhoneNumberField.VALIDATION', 'Please enter a valid phone number.'),
                        'validation'
                    );
                    return false;
                }
            }
        } catch (NumberParseException $e) {
            $validator->validationError(
                $this->name,
                _t('InternationalPhoneNumberField.ERROR', 'An error occurred.'),
                'validation'
            );
            return false;
        }
        return true;
    }

}
