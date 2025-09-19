<?php

namespace Innoweb\InternationalPhoneNumberField\ORM;

use Innoweb\InternationalPhoneNumberField\Forms\InternationalPhoneNumberField;
use Innoweb\InternationalPhoneNumberField\Validators\InternationalPhoneNumberFieldValidator;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Override;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\FormField;
use SilverStripe\Forms\TextField;
use SilverStripe\Model\ModelData;
use SilverStripe\ORM\Connect\MySQLDatabase;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\FieldType\DBVarchar;

class DBPhone extends DBVarchar
{
    private static array $field_validators = [
        InternationalPhoneNumberFieldValidator::class,
    ];

    private static $max_chars = 20;

    /**
     * Set the default value for "nullify empty"
     *
     * {@inheritDoc}
     */
    public function __construct($name = null, $options = [])
    {
        $size = $this->config()->get('max_chars');
        parent::__construct($name, $size, $options);
    }

    /**
     * (non-PHPdoc)
     * @see DBField::requireField()
     */
    #[Override]
    public function requireField(): void
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

    #[Override]
    public function setValue(mixed $value, null|array|ModelData $record = null, bool $markChanged = true): static
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            $numberProto = $phoneUtil->parse($value, null);
            if ($phoneUtil->isValidNumber($numberProto)) {
                $this->value = $phoneUtil->format($numberProto, PhoneNumberFormat::INTERNATIONAL);
            } else {
                $this->value = null;
            }
        } catch (NumberParseException) {
            $this->value = null;
        }
        
        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see core/model/fieldtypes/DBField#exists()
     */
    #[Override]
    public function exists(): bool
    {
        $value = $this->RAW();
        // All truthy values and non-empty strings exist ('0' but not (int)0)
        return $value || (is_string($value) && strlen($value));
    }

    #[Override]
    public function scaffoldFormField(?string $title = null, array $params = []): ?FormField
    {
        return InternationalPhoneNumberField::create($this->name, $title);
    }

    #[Override]
    public function scaffoldSearchField(?string $title = null): ?FormField
    {
        return TextField::create($this->getName(), $title);
    }

    /**
     * Returns the phone number in international format, e.g. +41 44 668 1800
     *
     * @return string
     */
    public function International(): ?string
    {
        return $this->getFormattedValue(PhoneNumberFormat::INTERNATIONAL);
    }

    /**
     * Returns the phone number in national format, e.g. 044 668 1800
     *
     * @return string
     */
    public function National(): ?string
    {
        return $this->getFormattedValue(PhoneNumberFormat::NATIONAL);
    }

    /**
     * Returns the phone number in international format, but with no formatting applied, e.g. +41446681800
     *
     * @return string
     */
    public function E164(): ?string
    {
        return $this->getFormattedValue(PhoneNumberFormat::E164);
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
        return $this->getFormattedValue(PhoneNumberFormat::RFC3966);
    }

    /**
     * Returns the phone number formatted as RFC3966.
     *
     * @return string
     */
    #[Override]
    public function URL(): string
    {
        $value = $this->RFC3966();
        return empty($value) ? 'tel:' : $value;
    }

    protected function getFormattedValue(int|PhoneNumberFormat $format): ?string
    {
        $value = $this->getValue();
        if (empty($value)) {
            return null;
        }
        
        try {
            $phoneUtil = PhoneNumberUtil::getInstance();
            $numberProto = $phoneUtil->parse($value);
            if ($phoneUtil->isValidNumber($numberProto)) {
                return $phoneUtil->format($numberProto, $format);
            }
        } catch (NumberParseException) {
            return $this->value;
        }
        
        return null;
    }
}
