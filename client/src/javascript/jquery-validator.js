;(function () {
	'use strict';

	function addValidator() {
		// add validator method
		if (window.jQuery) {  
			(function($) {
				if ($.validator) {
					$.validator.addMethod("internationalPhone", function(phone_number, element) {
						return this.optional(element)
							|| $(element).intlTelInput("isValidNumber");
					}, "Please enter a valid phone number.");
				}
			}(jQuery));
		}
	}

	if (document.readyState === "loading") {  // Loading hasn't finished yet
		document.addEventListener("DOMContentLoaded", addValidator);
	} else {  // `DOMContentLoaded` has already fired
		addValidator();
	}

}());