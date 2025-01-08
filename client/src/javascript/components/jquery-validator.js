import { isValidPhoneNumber } from 'libphonenumber-js';

;(function () {
    'use strict';

    const addValidator = function() {
        // add validator method
        if (window.jQuery) {
            (function($) {
                if ($.validator) {
                    $.validator.addMethod("internationalPhone", function(phone_number, element) {
                        let hidden = $(element).closest('.field').find('input[type="hidden"]').first();
                        if (typeof hidden !== 'undefined' && hidden !== null) {
                            let value = hidden.val();
                            if (value.length === 0 && this.optional(element)) {
                                return true;
                            }
                            return isValidPhoneNumber(value);
                        } else {
                            console.error('internationalPhone: hidden field not found');
                        }
                        return false;
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
