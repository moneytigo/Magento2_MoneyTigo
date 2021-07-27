define(
  [
    'Magento_Checkout/js/view/payment/default'
  ],
  function (Component) {
    'use strict';
    return Component.extend({
      defaults: {
        template: 'Ipsinternationnal_MoneyTigo/payment/moneytigopnf'
      },
      getMailingAddress: function () {
        return window.checkoutConfig.payment.checkmo.mailingAddress;
      },
    });
  }
);
