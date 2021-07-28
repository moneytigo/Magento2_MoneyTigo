## MoneyTigo Payment Module for Magento 2.3.x to 2.4.x 

Tested up to version 2.4.1 should not cause technical problems on versions above 2.4.x
This payment module allows you to accept credit card payments through MoneyTigo.com payment solution (https://www.moneytigo.com).


* Module version: 1.1.0

INSTALLATION AND ACTIVATION
===========================

To install MoneyTigo payment module on Magento you just need to enter the following command: 

```
composer require ipsinternationnal/module-moneytigo-magento2
```

MODULE SETTINGS
===============
To connect the module to your MoneyTigo merchant account you must:

## 1. Retrieving your API credentials
  * Add your website to your MoneyTigo account
  * Retrieve the API key (Merchant Key)
  * Generated the SECRET key and retrieved it
## 2. Configure Magento plugins (MoneyTigo)
  * Click on "Stores" > "Configuration"
  * Then on the "Sales" tab > "Payment methods".
  * Just fill in the sections corresponding to MoneyTigo (MerchantKey & SecretKey)

TEST MODE
==========

(**Note:** To test transactions, don't forget to switch your website (in your MoneyTigo interface, in test/demo mode) and switch it to production when your tests are finished.

If you use the test mode you must use the following virtual credit cards:
* **Payment approved** : Card n° 4000 0000 0000 0002 , Expiry 12/22 , Cvv 123
* **Payment declined** : Card n° 4000 0000 0000 0036 , Expiry 12/22, Cvv 123
* **(Virtual cards do not work in production mode)**

### Don't forget to disable the test mode when you are ready to accept real transactions. 

