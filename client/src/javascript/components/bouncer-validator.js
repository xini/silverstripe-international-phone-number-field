import { isValidPhoneNumber } from 'libphonenumber-js';

;(function () {
	'use strict';

	function addValidator() {
		window.bouncerValidators = window.bouncerValidators || {};

		window.bouncerValidators.internationalPhoneNumber = {
			validator: function(field) {
				if (field.classList.contains('InternationalPhoneNumberField')) {
					var wrapper = field.closest('.middleColumn');
					if (typeof(wrapper) !== 'undefined' && wrapper !== null) {
						var hiddenFieldName = field.getAttribute('name').slice(0, -4);
						var hiddenField = wrapper.querySelector('input[name="' + hiddenFieldName + '"]');
						if (typeof(hiddenField) !== 'undefined' && hiddenField !== null) {
							if (hiddenField.value.trim() && !isValidPhoneNumber(hiddenField.value)) {
								// return true if field is NOT valid
								return true;
							}
						}
					}
				}
				// return false if field is valid!
				return false;
			},
			message: 'Please enter a valid phone number'
		};

	}

	if (document.readyState === "loading") {  // Loading hasn't finished yet
		document.addEventListener("DOMContentLoaded", addValidator);
	} else {  // `DOMContentLoaded` has already fired
		addValidator();
	}

}());
