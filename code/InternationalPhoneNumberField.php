<?php

class InternationalPhoneNumberField extends TextField {
	
	public function __construct($name, $title = null, $value = '') {
		parent::__construct($name, $title, $value);
		$this->addExtraClass('InternationalPhoneNumberField');
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function Type() {
		return 'tel text';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAttributes() {
		return array_merge(
			parent::getAttributes(),
			array(
				'type' => 'tel',
			)
		);
	}
	
	public function FieldHolder($properties = array()) {

		// load requirements
		Requirements::css('international-phone-number-field/lib/intl-tel-input/build/css/intlTelInput.css');
		Requirements::javascript(THIRDPARTY_DIR.'/jquery/jquery.min.js');
		Requirements::javascript(THIRDPARTY_DIR.'/jquery-validate/jquery.validate.min.js');
		Requirements::combine_files(
			'InternationalPhoneNumberField.js',
			array(
				'international-phone-number-field/lib/intl-tel-input/build/js/intlTelInput.min.js',
				'international-phone-number-field/javascript/init.js',
			)
		);
		
		// call parent
		$html = parent::FieldHolder();
		return $html;
	}
	
	public function setValue($val) {
		if(empty($val)) {
			$this->value = null;
		} else {
			$phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
			try {
				$numberProto = $phoneUtil->parse($val, null);
				if ($phoneUtil->isValidNumber($numberProto)) {
					$this->value = $phoneUtil->format($numberProto, \libphonenumber\PhoneNumberFormat::INTERNATIONAL);
				}
			} catch (\libphonenumber\NumberParseException $e) {
				$this->value = null;
			}
		}
		return $this;
	}
	
	
	public function validate($validator) {
		$phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
		try {
			$numberProto = $phoneUtil->parse($this->value, null);
			if ($phoneUtil->isValidNumber($numberProto)) {
				$this->value = $phoneUtil->format($numberProto, \libphonenumber\PhoneNumberFormat::INTERNATIONAL);
				return true;
			}
		} catch (\libphonenumber\NumberParseException $e) {
			$validator->validationError(
				$this->name,
				_t('InternationalPhoneNumberField.VALIDATIONERROR', 'An error occurred.'),
				'validation'
			);
			return false;
		}
		$validator->validationError(
			$this->name,
			_t('InternationalPhoneNumberField.VALIDATION', 'Please enter a valid phone number.'),
			'validation'
		);
		return false;
	}
}
