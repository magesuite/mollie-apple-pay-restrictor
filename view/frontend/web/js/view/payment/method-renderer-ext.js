define(['Magento_Checkout/js/model/payment/renderer-list'], function (rendererList) {
    'use strict';

    return function(mollieMethodRenderer) {
        /**
         * Remove Mollie Apple Pay Method from method-renderer list when body has class mollie_methods_applepay_hidden
         */
        if (document.body.classList.contains('mollie_methods_applepay_hidden')) {
            rendererList.remove(function(method) {
                return method.type == 'mollie_methods_applepay';
            });
        }

        return mollieMethodRenderer.extend({})
    }
});
