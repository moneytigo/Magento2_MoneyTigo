define(
  [
    'uiComponent',
    'Magento_Checkout/js/model/payment/renderer-list'
  ],
  function (
    Component,
    rendererList
  ) {
    'use strict';
    rendererList.push({
      type: 'moneytigo',
      component: 'Ipsinternationnal_MoneyTigo/js/view/payment/method-renderer/moneytigo-method'
    });
    return Component.extend({});
  }
);
