<?php


namespace Ipsinternationnal\MoneyTigo\Model\Payment;

class MoneyTigo extends \Magento\Payment\Model\Method\AbstractMethod
{

    protected $_code = "moneytigo";
    protected $_isOffline = true;

    public function isAvailable(
        \Magento\Quote\Api\Data\CartInterface $quote = null
    ) {
        return parent::isAvailable($quote);
    }
}