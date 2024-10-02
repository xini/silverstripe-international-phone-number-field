<?php

namespace Innoweb\InternationalPhoneNumberField\ORM;

use Innoweb\InternationalPhoneNumberField\Forms\InternationalPhoneNumberField;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use SilverStripe\Core\Config\Config;
use SilverStripe\ORM\Connect\MySQLDatabase;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\FieldType\DBField;

class DBPhone extends DBField
{

    /**
     * Set the default value for "nullify empty"
     *
     * {@inheritDoc}
     */
    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);
    }

    /**
     * (non-PHPdoc)
     * @see DBField::requireField()
     */
    public function requireField()
    {
        $charset = Config::inst()->get(MySQLDatabase::class, 'charset');
        $collation = Config::inst()->get(MySQLDatabase::class, 'collation');

        $parts = [
            'datatype' => 'varchar',
            'precision' => 20,
            'character set' => $charset,
            'collate' => $collation,
            'arrayValue' => $this->arrayValue
        ];

        $values = [
            'type' => 'varchar',
            'parts' => $parts
        ];

        DB::require_field($this->tableName, $this->name, $values);
    }

    public function setValue($value, $record = null, $markChanged = true)
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            $numberProto = $phoneUtil->parse($value, null);
            if ($phoneUtil->isValidNumber($numberProto)) {
                $this->value = $phoneUtil->format($numberProto, PhoneNumberFormat::INTERNATIONAL);
            } else {
                $this->value = null;
            }
        } catch (NumberParseException $e) {
            $this->value = null;
        }
        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see core/model/fieldtypes/DBField#exists()
     */
    public function exists()
    {
        $value = $this->RAW();
        // All truthy values and non-empty strings exist ('0' but not (int)0)
        return $value || (is_string($value) && strlen($value));
    }

    public function scaffoldFormField($title = null, $params = null)
    {
        return InternationalPhoneNumberField::create($this->name, $title);
    }

    /**
     * Returns the phone number in international format, e.g. +41 44 668 1800
     *
     * @return string
     */
    public function International()
    {
        if (!$this->value) {
            return null;
        }
        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            $numberProto = $phoneUtil->parse($this->value, null);
            if ($phoneUtil->isValidNumber($numberProto)) {
                return $phoneUtil->format($numberProto, PhoneNumberFormat::INTERNATIONAL);
            }
        } catch (NumberParseException $e) {
            return $this->value;
        }
    }

    /**
     * Returns the phone number in national format, e.g. 044 668 1800
     *
     * @return string
     */
    public function National()
    {
        if (!$this->value) {
            return null;
        }
        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            $numberProto = $phoneUtil->parse($this->value, null);
            if ($phoneUtil->isValidNumber($numberProto)) {
                return $phoneUtil->format($numberProto, PhoneNumberFormat::NATIONAL);
            }
        } catch (NumberParseException $e) {
            return $this->value;
        }
    }

    /**
     * Returns the phone number in international format, but with no formatting applied, e.g. +41446681800
     *
     * @return string
     */
    public function E164()
    {
        if (!$this->value) {
            return null;
        }
        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            $numberProto = $phoneUtil->parse($this->value, null);
            if ($phoneUtil->isValidNumber($numberProto)) {
                return $phoneUtil->format($numberProto, PhoneNumberFormat::E164);
            }
        } catch (NumberParseException $e) {
            return $this->value;
        }
    }

    /**
     * Returns the phone number in international format, but with all spaces and other
     * separating symbols replaced with a hyphen, and with any phone number extension appended with
     * ";ext=". It also will have a prefix of "tel:" added, e.g. "tel:+41-44-668-1800".
     *
     * @return string
     */
    public function RFC3966()
    {
        if (!$this->value) {
            return null;
        }
        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            $numberProto = $phoneUtil->parse($this->value, null);
            if ($phoneUtil->isValidNumber($numberProto)) {
                return $phoneUtil->format($numberProto, PhoneNumberFormat::RFC3966);
            }
        } catch (NumberParseException $e) {
            return $this->value;
        }
    }

    /**
     * Returns the phone number formatted as RFC3966.
     *
     * @return string
     */
    public function URL()
    {
        return $this->RFC3966();
    }
}
