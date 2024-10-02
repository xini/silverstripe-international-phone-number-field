import { isValidPhoneNumber } from 'libphonenumber-js';

;(function () {
    'use strict';

    const addValidator = function() {
        // add validator method
        if (window.jQuery) {
            (function($) {
                if ($.validator) {
                    $.validator.addMethod("internationalPhone", function(phone_number, element) {
                        if (phone_number.trim().length === 0 && this.optional(element)) {
                            return true;
                        }
                        return isValidPhoneNumber(element.value)
                    }, "Please enter a valid phone number.");
                }
            })(window.jQuery);
        }
    }

    if (document.readyState === "loading") { // Loading hasn't finished yet
        document.addEventListener("DOMContentLoaded", addValidator);
    } else { // `DOMContentLoaded` has already fired
        addValidator();
    }
}());
