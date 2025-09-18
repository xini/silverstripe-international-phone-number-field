<?php

namespace Innoweb\InternationalPhoneNumberField\Validators;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use SilverStripe\Core\Validation\FieldValidation\StringFieldValidator;
use SilverStripe\Core\Validation\ValidationResult;

class InternationalPhoneNumberFieldValidator extends StringFieldValidator
{
    protected function validateValue(): ValidationResult
    {
        $result = parent::validateValue();
        if (!$result->isValid()) {
            return $result;
        }

        $isValid = true;
        $phoneUtil = PhoneNumberUtil::getInstance();
        if ($this->value === false) {
            $result->addFieldError(
                $this->name,
                _t('InternationalPhoneNumberField.VALIDATION', 'Please enter a valid phone number in international format, e.g. "+41 44 668 1800".'),
                'validation'
            );
            $isValid = false;
        } elseif ($this->value) {
            try {
                $numberProto = $phoneUtil->parse(trim($this->value), null);
                if (!$phoneUtil->isValidNumber($numberProto)) {
                    $result->addFieldError(
                        $this->name,
                        _t('InternationalPhoneNumberField.VALIDATION', 'Please enter a valid phone number in international format, e.g. "+41 44 668 1800".'),
                        'validation'
                    );
                    $isValid = false;
                }
            } catch (NumberParseException $e) {
                $result->addFieldError(
                    $this->name,
                    _t('InternationalPhoneNumberField.VALIDATION', 'Please enter a valid phone number in international format, e.g. "+41 44 668 1800".'),
                    'validation'
                );
                $isValid = false;
            }
        }
        return $result;
    }
}
