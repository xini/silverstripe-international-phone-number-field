import ct from 'countries-and-timezones';

;(function () {
    'use strict';

    const loadCountryFromBrowserTimeZone = function() {
        if (typeof Intl !== 'undefined'
            && typeof Intl.DateTimeFormat === 'function'
            && typeof Intl.DateTimeFormat().resolvedOptions === 'function'
            && Intl.DateTimeFormat().resolvedOptions().timeZone
        ) {
            const userTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            const userCountry = ct.getCountryForTimezone(userTimeZone);
            if (typeof userCountry !== 'undefined'
                && userCountry !== null
                && !Array.isArray(userCountry)
                && Object.prototype.toString.call(userCountry) === '[object Object]'
            ) {
                return userCountry.id;
            }
        }
        return false;
    }

    window.loadCountryFromBrowserTimeZone = loadCountryFromBrowserTimeZone;

}());
