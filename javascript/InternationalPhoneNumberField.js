(function($) {
	$(document).ready(function() {

		// add validator method
		if ($.validator) {
			$.validator.addMethod("internationalPhone", function(phone_number, element) {
				return this.optional(element)
					|| $(element).intlTelInput("isValidNumber");
			}, "Please enter a valid phone number.");
		}

		// init phone field
		$("input.InternationalPhoneNumberField").intlTelInput({
			// allowExtensions: true,
			// autoFormat: false,
			// autoHideDialCode: false,
			// autoPlaceholder: false,
			// dropdownContainer: $("body"),
			//excludeCountries: ["us"],
			geoIpLookup: function(callback) {
				$.get('$Protocol://ipinfo.io$TokenParameter', function() {}, "jsonp").always(function(resp) {
					var countryCode = (resp && resp.country) ? resp.country : "";
					callback(countryCode);
				});
			 },
			initialCountry: "auto",
			nationalMode: false,
			// numberType: "MOBILE",
			// onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
			preferredCountries: ['au', 'nz'],
			utilsScript: "/international-phone-number-field/lib/intl-tel-input/lib/libphonenumber/build/utils.js"
		});

	});
}(jQuery));
