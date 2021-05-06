;(function () {
	'use strict';

	function initInternationalPhoneField() {
		
		// init phone fields
		var fields = document.querySelectorAll('input.InternationalPhoneNumberField');

		Array.prototype.forEach.call(fields, function (field) {
			
			// define geo lookup function
			var geoLookup = null;
	        var initialCountry = field.getAttribute('data-initialcountry');
	        if (
	            field.getAttribute('data-apiurl') && field.getAttribute('data-apiurl').length > 0
	            && (typeof initialCountry === 'undefined' || initialCountry === 'auto')
	        ) {
				geoLookup = function(callback) {
					var xhr = new XMLHttpRequest();
					xhr.open('GET', field.getAttribute('data-apiurl'));
					xhr.setRequestHeader("Accept", "application/json");
					xhr.onload = function() {
					    if (xhr.status === 200) {
					    	var json = JSON.parse(xhr.responseText);
					    	var countryCode = (json && json[field.getAttribute('data-apireplykey')]) ? json[field.getAttribute('data-apireplykey')] : "";
							callback(countryCode);
					    }
					};
					xhr.send();
				};
			}
		
			window.intlTelInput(field, {
				geoIpLookup: geoLookup,
				initialCountry: initialCountry,
				nationalMode: false,
				onlyCountries: field.getAttribute('data-onlycountries') ? field.getAttribute('data-onlycountries').split('-') : [],
				preferredCountries: field.getAttribute('data-preferredcountries') ? field.getAttribute('data-preferredcountries').split('-') : [],
				excludeCountries: field.getAttribute('data-excludedcountries') ? field.getAttribute('data-excludedcountries').split('-') : [],
				utilsScript: field.getAttribute('data-utilsscripturl'),
			});
		});
	}

	if (document.readyState === "loading") {  // Loading hasn't finished yet
		document.addEventListener("DOMContentLoaded", initInternationalPhoneField);
	} else {  // `DOMContentLoaded` has already fired
		initInternationalPhoneField();
	}
	
	if (window.jQuery && window.jQuery.fn.entwine) { 
		jQuery.entwine("InternationalPhoneNumberField", function ($) {
		    $(":input.InternationalPhoneNumberField").entwine({
		        onmatch: function () {
		            initInternationalPhoneField();
		        }
		    });
		});
	}

}());
