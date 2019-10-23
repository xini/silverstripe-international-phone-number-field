;(function () {
	'use strict';

	function initInternationalPhoneField() {
		// init phone fields
		var fields = document.querySelectorAll('input.InternationalPhoneNumberField');

		// define geo lookup function
		var geoLookup = null;
        var initialCountry = "$InitialCountry";
        if (
            '$APIURL'.length > 0
            && (typeof initialCountry === 'undefined' || initialCountry === 'auto')
        ) {
			geoLookup = function(callback) {
				var xhr = new XMLHttpRequest();
				xhr.open('GET', '$APIURL');
				xhr.setRequestHeader("Accept", "application/json");
				xhr.onload = function() {
				    if (xhr.status === 200) {
				    	var json = JSON.parse(xhr.responseText);
				    	var countryCode = (json && json['$APIReplyKey']) ? json['$APIReplyKey'] : "";
						callback(countryCode);
				    }
				};
				xhr.send();
			};
		}

		Array.prototype.forEach.call(fields, function (field) {
			window.intlTelInput(field, {
				geoIpLookup: geoLookup,
                initialCountry: initialCountry,
				nationalMode: false,
				onlyCountries: $OnlyCountries,
				preferredCountries: $PreferredCountries,
				excludeCountries: $ExcludedCountries,
				utilsScript: "$UtilsScriptURL"
			});
		});

	}

	if (document.readyState === "loading") {  // Loading hasn't finished yet
		document.addEventListener("DOMContentLoaded", initInternationalPhoneField);
	} else {  // `DOMContentLoaded` has already fired
		initInternationalPhoneField();
	}

}());
