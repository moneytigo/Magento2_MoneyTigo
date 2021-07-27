<?php
namespace Ipsinternationnal\MoneyTigo\Controller\Index;

use \Magento\Framework\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;
use \Magento\Framework\App\Action\Action;
use \Magento\Framework\Registry;

class Redirect extends Action
{
  
  protected $_coreRegistry;
  protected $_pageFactory;
  
  public function __construct(
    Context $context,
    PageFactory $resultPageFactory,
    Registry $resultRegistry) 
  {
    $this->_pageFactory = $resultPageFactory;
    $this->_coreRegistry = $resultRegistry;
    parent::__construct(
        $context
    );
  }
    
  
  public function execute()
  {

    // Get Order by id
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    $order = $objectManager->create('\Magento\Sales\Model\Order')->load(intval($_GET['id']));
    $order_data = $order->getData(); 
    if(!empty($order_data['customer_id'])){
      // Get Customer by id
      $customer = $objectManager->create('\Magento\Customer\Model\Customer')->load(intval($order_data['customer_id']));
      $customer_data = $customer->getData();
      // Get Address by id
      $address = $objectManager->create('\Magento\Customer\Model\Address')->load(intval($customer_data['default_shipping']));
      $address_data = $address->getData();
    }


    // Get helper config (Data from admin MoneyTigo)
    $helper = $objectManager->create('Ipsinternationnal\MoneyTigo\Helper\Data');

    // Get store config
    $store = $objectManager->get('\Magento\Framework\Locale\Resolver');
    $lang = explode("_", $store->getLocale());

    // Get BaseUrl
    $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
    $baseUrl = $storeManager->getStore()->getBaseUrl();


    // Create MoneyTigo inputs
    $moneytigo['amount']         = number_format($order_data['grand_total'], 2 , '.', '');
    $moneytigo['MerchantKey']           = $helper->getGeneralConfig('merchantkey');
    $moneytigo['RefOrder']   = $order->getIncrementId(); //correspondance avec increment id et moneytigo
    $moneytigo['urlIPN']          = $baseUrl . "ipsinternationnal_moneytigo/index/ipn/";
    if($_GET['Lease'] == 3){
      $moneytigo['Lease']          = "3";
    }

    // Check if customer has id (to recover address) and redirect after payment in order view 
    if(!empty($order_data['customer_id'])){
      $moneytigo['urlOK']     = $baseUrl . "sales/order/view/order_id/" . $_GET['id'];
	  $moneytigo['urlKO']     = $baseUrl . "sales/order/view/order_id/" . $_GET['id'];
      $moneytigo['Customer_Name']     = $order_data['customer_lastname'];
	  $moneytigo['Customer_FirstName']     = $order_data['customer_firstname'];
	  $moneytigo['Customer_Email']     = $order_data['customer_email'];
    }else{
	  $objectManagerCInfo = \Magento\Framework\App\ObjectManager::getInstance();
      $CustomerInformations = $objectManagerCInfo->create('Magento\Sales\Model\Order')->load($_GET['id']); // pass orderId
      $moneytigo['urlOK']     = $baseUrl . "ipsinternationnal_moneytigo/index/CustomerReturn?mtgo_id=" . $order->getIncrementId() ."&mtgo_lname=" . $order->getBillingAddress()->getLastname() . "";
	  $moneytigo['urlKO']     = $baseUrl . "ipsinternationnal_moneytigo/index/CustomerReturn?mtgo_id=" . $order->getIncrementId() ."&mtgo_lname=" . $order->getBillingAddress()->getLastname() . "";
      $moneytigo['Customer_Name']     = $CustomerInformations->getBillingAddress()->getLastname();
      $moneytigo['Customer_FirstName']     = $CustomerInformations->getBillingAddress()->getFirstname();
	  $moneytigo['Customer_Phone']     = $CustomerInformations->getBillingAddress()->getTelephone();
	  $moneytigo['Customer_Email']     = $order_data['customer_email'];
    }
    

    // Save data in register
    $this->_coreRegistry->register('data_moneytigo', json_encode($moneytigo));
    
    // Call layout.xml
    $resultPage = $this->_pageFactory->create();
    $resultPage->addHandle('moneytigo_index_redirect');
    $resultPage->getConfig()->getTitle()->prepend(__('Creating payment'));
    return $resultPage;
    
  }
}