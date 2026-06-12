<?php

namespace Innoweb\InternationalPhoneNumberField\Forms\Extensions;

use Innoweb\InternationalPhoneNumberField\Forms\InternationalPhoneNumberField;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Extension;
use SilverStripe\View\Requirements;

class ObserveDynamicFieldsExtension extends Extension
{
    // apply this to a page controller to load the field requirements independently of a field being rendered with
    // the page, and to enable the observer initialising fields added after the initial page load, e.g. in popups.
    // alternatively, leave this extension off and add the scripts and the `window.DoObserveInnowebIntlPhoneFields`
    // flag to your templates manually.
    protected function onAfterInit()
    {
        if (Config::inst()->get(InternationalPhoneNumberField::class, 'geolocation_service') === false
            && Config::inst()->get(InternationalPhoneNumberField::class, 'initial_country') === 'auto'
            && Config::inst()->get(InternationalPhoneNumberField::class, 'load_default_from_user_agent') === true
        ) {
            Requirements::javascript('innoweb/silverstripe-international-phone-number-field:client/dist/javascript/intl-phone-number-field-default-from-browser.js');
        }

        Requirements::css('innoweb/silverstripe-international-phone-number-field:client/dist/css/intl-phone-number-field.css');
        Requirements::javascript('innoweb/silverstripe-international-phone-number-field:client/dist/javascript/intl-phone-number-field.js');

        Requirements::customScript(
            'window.DoObserveInnowebIntlPhoneFields = true;',
            'intl-phone-number-field-observe-dynamic-fields'
        );
    }
}
