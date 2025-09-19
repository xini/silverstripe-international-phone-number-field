<?php

namespace Innoweb\InternationalPhoneNumberField\Forms\Extensions;

use SilverStripe\Core\Extension;
use SilverStripe\View\Requirements;

class ValidationExtension extends Extension
{
    // needs to be run on base FormField class, otherwise it's not going to be loaded on time
    public function addCustomValidatorScripts()
    {
        Requirements::javascript(
            'innoweb/silverstripe-international-phone-number-field: client/dist/javascript/intl-phone-number-field-validation.js',
            ['defer' => true]
        );
    }
}
