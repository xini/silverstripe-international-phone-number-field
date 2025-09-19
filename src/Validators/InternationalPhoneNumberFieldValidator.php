<?php

namespace Innoweb\InternationalPhoneNumberField\Validators;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use Override;
use SilverStripe\Core\Validation\FieldValidation\StringFieldValidator;
use SilverStripe\Core\Validation\ValidationResult;

class InternationalPhoneNumberFieldValidator extends StringFieldValidator
{
    #[Override]
    protected function validateValue(): ValidationResult
    {
        $result = ValidationResult::create();

        if ($this->value === false) {
            $result->addFieldError(
                $this->name,
                _t('InternationalPhoneNumberField.VALIDATION', 'Please enter a valid phone number in international format, e.g. "+41 44 668 1800".'),
                'validation'
            );
        } elseif ($this->value) {
            try {
                $phoneUtil = PhoneNumberUtil::getInstance();
                $numberProto = $phoneUtil->parse(trim((string) $this->value), null);
                if (!$phoneUtil->isValidNumber($numberProto)) {
                    $result->addFieldError(
                        $this->name,
                        _t('InternationalPhoneNumberField.VALIDATION', 'Please enter a valid phone number in international format, e.g. "+41 44 668 1800".'),
                        'validation'
                    );
                }
            } catch (NumberParseException) {
                $result->addFieldError(
                    $this->name,
                    _t('InternationalPhoneNumberField.VALIDATION', 'Please enter a valid phone number in international format, e.g. "+41 44 668 1800".'),
                    'validation'
                );
            }
        }

        return $result;
    }
}
