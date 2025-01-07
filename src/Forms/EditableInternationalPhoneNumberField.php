<?php

namespace Innoweb\InternationalPhoneNumberField\Forms;

use SilverStripe\Forms\FormField;
use SilverStripe\UserForms\Model\EditableFormField;

if (!class_exists(EditableFormField::class)) {
    return;
}

/**
 * EditableInternationalPhoneNumberField
 *
 * Allow users to define a validating editable phone number field for a UserDefinedForm
 */
class EditableInternationalPhoneNumberField extends EditableFormField
{
    private static $singular_name = 'Phone Number Field';

    private static $plural_name = 'Phone Number Fields';

    private static $has_placeholder = true;

    private static $table_name = 'EditableInternationalPhoneNumberField';

    public function getSetsOwnError()
    {
        return true;
    }

    public function getFormField()
    {
        $field = InternationalPhoneNumberField::create($this->Name, $this->Title ?: false, $this->Default)
            ->setFieldHolderTemplate(EditableFormField::class . '_holder')
            ->setTemplate(EditableFormField::class);

        $this->doUpdateFormField($field);

        return $field;
    }

    /**
     * Updates a formfield with the additional metadata specified by this field
     *
     * @param FormField $field
     */
    protected function updateFormField($field)
    {
        parent::updateFormField($field);

        $field->setAttribute('data-rule-internationalPhone', true);
    }
}
