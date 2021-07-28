## MoneyTigo Payment Module for Magento 2.3.x to 2.4.x 

Tested up to version 2.4.1 should not cause technical problems on versions above 2.4.x
This payment module allows you to accept credit card payments through MoneyTigo.com payment solution (https://www.moneytigo.com).


* Module version: 1.1.0
* Composer required : 2.0 or more

INSTALLATION AND ACTIVATION
===========================

### Installation with COMPOSER
#### This is the recommended installation because it allows quick updates, so prefer the installation with COMPOSER
> To install MoneyTigo payment module on Magento you just need to enter the following command: 
> 
> ```console
> composer require ipsinternationnal/module-moneytigo-magento2
> ```
> 
> depending on the case or your version of composing you may have to use a method with some ignorance
> 
> ```console
> composer require ipsinternationnal/module-moneytigo-magento2 --ignore-platform-reqs
> ```
### Manual installation via FTP
> If you want to perform a manual installation it is also possible in this case you just have to create the following directories in the app/code directory of your Magento > installation: 
> ```
> Main directory : Ipsinternationnal (please respect upper & lower case)
> A subdirectory : MoneyTigo (please respect upper & lower case)
> 
> This will give app/code/Ipsinternational/MoneyTigo/
> ```
> Then copy the entire archive into the **MoneyTigo** directory.
> 
> You will need to activate the module with the following commands: 
> ```console
> php bin/magento module:enable Ipsinternationnal_MoneyTigo --clear-static-content
> php bin/magento setup:upgrade
> ```

### Update installation with COMPOSER
> ##### Attention only works if you had installed the module the first time with COMPOSER.
> The update is simple, just enter the following command: 
> ```console
> composer update ipsinternationnal/module-moneytigo-magento2 --ignore-platform-reqs
> ```

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

