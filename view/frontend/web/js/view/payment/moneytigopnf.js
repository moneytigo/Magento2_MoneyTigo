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
      type: 'moneytigopnf',
      component: 'Ipsinternationnal_MoneyTigo/js/view/payment/method-renderer/moneytigopnf-method'
    });
    return Component.extend({});
  }
);
