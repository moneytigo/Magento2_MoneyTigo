<?php

declare(strict_types=1);

namespace Ipsinternationnal\MoneyTigo\Controller\Index;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\CsrfAwareActionInterface; //Csrf added
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\InvalidRequestException; //Csrf added
use Magento\Framework\App\RequestInterface; //Csrf added
use Magento\Framework\Exception\RemoteServiceUnavailableException;
use Magento\Sales\Model\Order;

class Ipn extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface 
{
  

/**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Ipsinternationnal\MoneyTigo\Model\IpnFactory
     */
    protected $_ipnFactory;

    /**
     * @var OrderFactory
     */
    private $orderFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Paypal\Model\IpnFactory $ipnFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param OrderFactory $orderFactory
     */
	
	public function __construct(
    Context $context
  ) 
  {
 
    parent::__construct(
        $context
    );
  }
	
	
	
    /**
     * @inheritDoc
     */
    public function createCsrfValidationException(
        RequestInterface $request
    ): ?InvalidRequestException {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
	
	
  public function getTransactionMtg($data)
  {
	  $MerchantKey = $data['MerchantKey'];
	  $ShaKey = $data['SecretKey'];
	  $TransID = $data['TransactionID'];
	  $beforesign = $MerchantKey."!".$TransID."!".$ShaKey;
//Encode in base 64 + sign with SHA512 encryption
	  $sign = hash("sha512", base64_encode($beforesign."|".$ShaKey));

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://payment.moneytigo.com/transactions/?TransID='.$TransID.'&ApiKey='.$MerchantKey.'&SHA='.$sign.'');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$headers = array();
$headers[] = 'Accept: application/json';
$headers[] = 'Content-Type: application/json';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);
return $result;
  }
	
	public function generateComment($results=NULL)
	{
		
		$commentIs = "";
	  if($results['Transaction_Status']) {
	  $commentIs .= "<b>Status of the transaction</b><br>";
	  foreach($results['Transaction_Status'] as $key => $value)
	  {
	  $commentIs .= "".$key." : ".$value." <br>";  
	  }
	  
	  }
	  
	  if($results['Card']) {
		  $commentIs .= "<b>Payment card info</b><br>";

	  foreach($results['Card'] as $key => $value)
	  {
	  $commentIs .= "".$key." : ".$value." <br>";    
	  }
	  }
	  
	  if($results['Bank']) {
		    $commentIs .= "<b>MoneyTigo transaction reference</b><br>";
 	  foreach($results['Bank'] as $key => $value)
	  {
	  $commentIs .= "".$key." : ".$value." <br>";    
	  }
	  }
	  return $commentIs;
	}
	
  public function execute()
  {
	  if (!$this->getRequest()->isPost()) {
		    echo "No post received value";
			http_response_code(403);
            return;
      }
	  
	  // Get the data
    $post = filter_input_array(INPUT_POST);
	//$post = filter_input_array(INPUT_GET);
	 // Get order
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	 // Get helper config (Data from admin)
    $helper = $objectManager->create('Ipsinternationnal\MoneyTigo\Helper\Data');
 
	 
	   
	if(!$post["TransId"])
	{
	  echo "No TransID vars received from MoneyTigo";
	  http_response_code(401);
      return;	
	}
	$dataMoneyTigo = array("MerchantKey" => $helper->getConfigValue("payment/moneytigo/merchantkey"), "SecretKey" => $helper->getConfigValue("payment/moneytigo/secretkey"), "TransactionID" => $post["TransId"]);
	
	//RequestSent and store result 
	$results = json_decode($this->getTransactionMtg($dataMoneyTigo), true);
	
	//findOrder reference for this transaction
	$order = $objectManager->create('\Magento\Sales\Model\Order')->load($results['Merchant_Order_Id']);
	$order_data = $order->getData();
	
	//Stop if order not found for this transaction ID
	if(!$order_data)
	{
		http_response_code(400);
		echo "No order found for this transaction";
		
         return;
	}		
	//print_r($dataMoneyTigo);
	if($results['Transaction_Status']['State'] == 2)
	{
			//ORDER VALIDATION
			//Change Order State to PAID (processing)
			$order->setState(Order::STATE_PROCESSING)->setStatus(Order::STATE_PROCESSING);
			//Sent mail Order received
			$order->setCanSendNewEmailFlag(true);
			//Include amount paid
			$order->setTotalPaid((float)$results["Financial"]["Total_Paid"]);
			//Set amount du to 0
			$order->setTotalDue(0);
			//Valid order
			$order->save();
			
			
			//ORDER TRANSACTION History
		$order->addStatusHistoryComment($this->generateComment($results));	
		$order->save();

			//Mail sending
			
		$emailSender = $objectManager->create('\Magento\Sales\Model\Order\Email\Sender\OrderSender');
		$emailSender->send($order, true);
	    echo "This order was processed correctly. (Payment approved and order in PROCESSING status).";
	}
		  else 
	  {
		$order->setState(Order::STATE_CANCELED)->setStatus(Order::STATE_CANCELED);
        $order->save();
		//ORDER TRANSACTION History
		$order->addStatusHistoryComment($this->generateComment($results));	
		$order->save();
		
		echo "This order was processed correctly. (Payment canceled and order in CANCEL status).";
	  }


  }

}