<?php

require_once dirname(__FILE__).'/SparkassenInternetkasseCore.php';
require_once dirname(__FILE__).'/simpleLogger.php';

require_once dirname(__FILE__).'/../../../classes/order/Order.php';
require_once(dirname(__FILE__).'/../SparkassenInternetkasse.php');
require_once(dirname(__FILE__).'/../../../config/config.inc.php');
require_once dirname(__FILE__).'/../../../classes/order/OrderHistory.php';
		


class SparkassenInternetkasseModel
{
	var $logger;
	var $prefix;
	var $total;

	function getShopName(){
		return 'Prestashop';
	}
	
	public function getShopVersion(){
		return _PS_VERSION_;
	}
	
	Function getModulVersion(){
	
		return '1.0.0';
	}
	
	public function isMasterpass(){
		return ($this->getValueforKey('mp')==true);
	}
	
	public function addressfromMasterpass(){
		return ($this->getValueforKey('master_pass_address')==true);
	}
	
	function getMasterpassbutton(){
		$locale = $this->getLocale();
		if($locale != 'de' && $locale != 'en'){
			$locale = 'en';
		}
		$button = 'https://www.mastercard.com/mc_us/wallet/img/'.$locale.'/DE/mcpp_wllt_btn_chk_160x037px.gif';
		return $button;
	}
	
	function getCartTotal(){
		return $this->getAmount();
	}
	
	#Masterpass
	function startMasterpass() {
	
		$mCore=new SparkassenInternetkasseCore();
		$this->prefix='MP';
		$this->context = Context::getContext();
		$this->context->cookie->__set('urlMP','https://'.$mCore->getMasterPassURL($this));
	
		$this->context->cookie->__set('userMP',$this->getMPInterfaceUser());
		$this->context->cookie->__set('secretMP',$this->getMPInterfaceSecret());
		$srcJS = $mCore->getMasterPassJS($this);
		$paramsMP = $mCore->getMasterPassInit($this,$this->getwalletreturnurl());
		$button = $this->getMasterpassbutton();
		$timestamp = time();
		$datum = date("Ymd",$timestamp);
		$uhrzeit = date("His",$timestamp);
		$mp_order_id = $this->getOrderid();
		$this->context->cookie->__set('mpoid',$mp_order_id);
		$paramsMP['orderid']=$mp_order_id;
		$this->context->cookie->__set('basketIdMP',$paramsMP['orderid']);
		$string = '<img style=" cursor:pointer;" src="'.$button.'" onClick="handleBuyWithMasterPass()">'.$srcJS.$this->initMasterpass($paramsMP);
		return $string;
	}
	
	function initMasterpass($params){
		$url = $this->context->cookie->urlMP;
		$fields_string .= http_build_query($params);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERPWD, $this->context->cookie->userMP.':'.$this->context->cookie->secretMP);
		curl_setopt($ch,CURLOPT_POST, count($params));
		curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		$data = curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if ($status == 200) {
			$a = explode('&', $data);
			foreach ($a as $result) {
				$b = explode('=', $result);
				$array[$b[0]] = rawurldecode($b[1]);
			}
			$this->context->cookie->__set('walletref',$array['walletref']);
			#return '<script type="text/javascript" language="Javascript">function handleBuyWithMasterPass() {'.$array['walletjs'].'}</script>';
			return '<div with="100"><script type="text/javascript" language="Javascript">'.$array['walletjs'].'</script>&nbsp;</div><script type="text/javascript" language="Javascript">function handleBuyWithMasterPass() {'.$array['walletjs'].'}</script>';
		}
	}
	
	function getwalletreturnurl(){
		return Tools::getHttpHost(true).__PS_BASE_URI__.$this->getLocale().'/module/SparkassenInternetkasse/mpreturn';
	}
	
	function getMPInterfaceUser(){
		$this->logDebug('master_pass_user:'.$this->getValueforKey('master_pass_user'));
		return $this->getValueforKey('master_pass_user');
	}
	
	function getMPInterfaceSecret(){
		$this->logDebug('master_pass_secret:'.$this->getValueforKey('master_pass_secret'));
		return $this->getValueforKey('master_pass_secret');
	}
		
	private function getValueforKey($key){
		return Configuration::get(strtoupper('SparkassenInternetkasse_'.$key));
	}
	
	//api cals
	function getPaymentGatewayURL(){
		$mCore=new SparkassenInternetkasseCore();
		return $mCore->getPaymentGatewayURL($this);
	}
	
	function getPaymentGatewayData(){
		$mCore=new SparkassenInternetkasseCore();
		return $mCore->getTransactionParams($this);			
	}
	
	function proccessGatewayNotification(){
		$mCore=new SparkassenInternetkasseCore();
		return $mCore->processPaymentGatewayNotification( $_REQUEST, $this);			
	}	

	function isLiveMode(){
		
			$this->getValueforKey('test_mode')==0;
	}
	
	function preparePaymentGatewayRequest(){
		$mCore=new SparkassenInternetkasseCore();
		return $mCore->preparePaymentGatewayRequest($this);
	}
	
	
	public function isIFrame(){
		return ($this->getValueforKey('iframe_mode')==true);
	}
	
	function getSSLmerchant(){
		return $this->getValueforKey('sslmerchant');
	}
	
	function getSecret(){
		if($this->getValueforKey('secret')=='')
			return '';
		return $this->getValueforKey('secret');
	}

	function getPrefix(){
		return Tools::getValue('paymentmethod');
	}

	function getLoggerLevel(){
		if($this->getValueforKey('debug_mode')=='')
			return 'NONE';
		else
			return strtoupper($this->getValueforKey('debug_mode'));
	}
	
	function getLoggerFileName(){
		return $this->getValueforKey('log_file_path');
	}


	/**
	 * Enter description here ...
	 */
	function getOrderid(){
			
		$context = Context::getContext();
		$cart = $context->cart;
		return $cart->id;
		
		//return (int)Tools::getValue('id_order');
	}
	
	function getCssurl(){
		if($this->getValueforKey('cssurl')=='')
			return '';
		return $this->getValueforKey('cssurl');
	}
	
	function getTransactiontype(){
		return $this->getValueforKey($this->prefix.'_transactiontype');
	}
	

	function getPaymentMethod(){
	$paymentMethod='';
		switch ($this->getPrefix())
		{
			case 'CC' :
				$paymentMethod='creditcard';
				break;
			case 'DD' :
				$paymentMethod='directdebit';
				break;
			case 'GP' :
				$paymentMethod='banktransfer';
				break;
			case 'PP':
				$paymentMethod='paypal';
				break;
			case 'MP':
				$paymentMethod='masterpass';
				break;
		}

		return $paymentMethod;
	}

	/**
	 * Enter description here ...
	 */
	function getLocale(){
		$context = Context::getContext();
		$iso_code = $context->language->iso_code;
		return $iso_code;
	}

	/**
	 * Enter description here ...
	 */
	function getBasketid(){
		///qqq get the basketid
		
		//
		return $this->getPrefix().'_'.$this->getOrderid();
	}

	private function getChallengeToken(){
		if($_REQUEST['basketid']){
			preg_match('/(.+)_(.+)/', $_POST['basketid'], $m);
			return $m[2];
		}

		return '';
	}

	private function formatAmount($amount){
		// set the amount
		$tstr = number_format($amount, 2, ',', '');
		$tstr = substr( $tstr, 0,strpos($tstr,',')+3);
		return $tstr;
	}

	function getAmount(){
		$context = Context::getContext();		
		return $this->formatAmount($context->cart->getOrderTotal(true));
	}

	function getAcceptcountries(){
		return $this->getValueforKey($this->prefix.'_acceptcountries');
	}

	function getPayment_options() {
		if($this->prefix=='GP'){
			return $this->getValueforKey('AGE_VERIFICATION');
		} else{
			return $this->getValueforKey($this->prefix.'_PAYMENTOPTIONS');
		}
	}


	function getRejectcountries(){
		return $this->getValueforKey($this->prefix.'_rejectcountries');
	}

	function getCustomer_addr_city(){
		$context = Context::getContext();
		$id_address= $context->cart->id_address_delivery;
		$address = new Address($id_address);
		return $address->city;
	}

	function getCustomer_addr_street(){
	
		$context = Context::getContext();
		$id_address= $context->cart->id_address_delivery;
		$address = new Address($id_address);
		return $address->address1.' '. $address->address2;
	}
	function getCustomer_addr_zip(){
		$context = Context::getContext();
		$id_address= $context->cart->id_address_delivery;
		$address = new Address($id_address);
		return $address->postcode;
	}
	function getCustomer_addr_number(){
		$context = Context::getContext();
		$id_address= $context->cart->id_address_delivery;
		$address = new Address($id_address);
		return $address->address2;
		
		return '';
	}

	function getDeliverycountry(){
		$context = Context::getContext();
		$id_address= $context->cart->id_address_delivery;
		$address = new Address($id_address);
		$country= new Country($address->id_country);
		return $country->iso_code;
			}


	/**
	 * Enter description here ...
	 */
	function getCurrency(){
		$context = Context::getContext();
		$currency = new Currency((int)$context->cart->id_currency);
		return $currency->iso_code;
	}
	/**
	 * Enter description here ...
	 */
	function getSessionid(){
		//QQQ
		return '';
	}


	/**
	 * Enter description here ...
	 */
	function getNotificationfailedurl(){
		return '';
	}
	
	function getNotifyUrl(){
		$SparkassenInternetkasse=new SparkassenInternetkasse();
		return Tools::getShopDomainSsl(true, true)._MODULE_DIR_.'SparkassenInternetkasse/notification.php';
	}
	
	
	function getLogger(){
		if(!is_object($this->logger)){
			include_once 'simpleLogger.php';
			$this->logger=new SimpleLogger($this->getLoggerFileName(),$this->getLoggerLevel());
			//$this->logger=new SimpleLogger('/tmp/xyz.log','DEBUG');
		}
		return $this->logger;
	}


	/**
	 * @param unknown $param
	 */
	function logDebug($param){
		$this->getLogger()->debug("".$param);
	}

	/**
	 * @param unknown $param
	 */
	function logTransaction($param){
		$this->getLogger()->info("".$param);
	}

	/**
	 * @param unknown $param
	 */
	function logError($param){
		$this->getLogger()->error("".$param);
	}


	/**
	 * @return Ambigous <mixed, NULL>
	 */
	function getForm_label_submit(){
		//QQQ
		return '';
	}

	/**
	 * Enter description here ...
	 * @return Ambigous <NULL, mixed>
	 */
	function getDeliverycountryrejectmessage(){
		//QQQ 
		return '';
	}

	/**
	 * Enter description here ...
	 * @return Ambigous <NULL, mixed>
	 */
	function getForm_merchantref(){
		return $this->getValueforKey('form_merchantref');
	}

	/**
	 * Enter description here ...
	 * @return Ambigous <NULL, mixed>
	 */
	function getForm_label_cancel(){
		//QQQ
		return '';
	}

	/**
	 * Enter description here ...
	 * @return Ambigous <NULL, mixed>
	 */
	function  getDeliverycountryaction(){
		return $this->getValueforKey('cc_deliverycountry_action');
	}


	/**
	 * Enter description here ...
	 * @return Ambigous <NULL, mixed>
	 */
	function getAutocapture(){
			return $this->getValueforKey($this->prefix.'_autocapture');
	}

	/**
	 * Enter description here ...
	 * @return Ambigous <NULL, mixed>
	 */
	function getCountryrejectmessage(){
		return '';
	}

	/**
	 * Enter description here ...
	 * @return Ambigous <NULL, mixed>
	 */
	function getForm_merchantname(){
		return $this->getValueforKey('form_merchantname');
	}


	public function getAccountnumber(){
		'';
	}

	public function getBankcode(){
		if($this->isLiveMode())
			return '';
		else{
			return '12345679';
		}
	}

	function getBic(){
		if($this->isLiveMode())
		return '';
		else
		return 'TESTDETT421';
	}

	function getIban(){
		return '';
	}

	function getMandateid() {
		return $this->getValueforKey('mandateprefix',true).'-'.$this->getOrderid();
	}
	function getMandatename() {
		return $this->getValueforKey('mandatename',true);
	}

	function getMandatesigned() {
		return date('Ymd');
	}

	function getSequencetype() {
		return $this->getValueforKey('sequencetype');
	}


	public function getLabel0(){
		if($this->getValueforKey('label0'))
			return $this->getValueforKey('label0');
		else
			return '';
	}
	public function getLabel1(){
		if($this->getValueforKey('label1'))
			return $this->getValueforKey('label1');
		else
			return '';
	}
	public function getLabel2(){
		if($this->getValueforKey('label2'))
			return $this->getValueforKey('label2');
		else
			return '';
	}
	public function getLabel3(){
		if($this->getValueforKey('label3'))
			return $this->getValueforKey('label3');
		else
			return '';
	}

	public function getLabel4(){
		if($this->getValueforKey('label4'))
			return $this->getValueforKey('label4');
		else
			return '';
	}

	public function getText0(){
		if($this->getValueforKey('text0'))
			return $this->getValueforKey('text0');
		else
			return '';
	}
	public function getText1(){
		if($this->getValueforKey('text1'))
			return $this->getValueforKey('text1');
		else
			return '';
	}
	public function getText2(){
		if($this->getValueforKey('text2'))
			return $this->getValueforKey('text2');
		else
			return '';
	}
	public function getText3(){
		if($this->getValueforKey('text3'))
			return $this->getValueforKey('text3');
		else
			return '';
	}
	public function getText4(){
		if($this->getValueforKey('text4'))
			return $this->getValueforKey('text4');
		else
			return '';
	}


	function setAdditionalParamsforPayPal(array &$params){

		
		$context = Context::getContext();
		$this->logDebug("processOnError()->start() ".' Params:'.$params);
		if (version_compare(_PS_VERSION_, '1.5', '<'))
			$shipping_cost_wt = $this->context->cart->getOrderShippingCost();
		else
			$shipping_cost_wt = $context->cart->getTotalShippingCost();

		$params['basket_shipping_costs']=$this->formatAmount(number_format($shipping_cost_wt,2));
		$this->product_list = $context->cart->getProducts();
			foreach ($this->product_list as $product)
		{
			$params['basketitem_number'.++$index] = (int)$product['id_product'];

			$params['basketitem_name'.$index] = $product['name'];

			if (isset($product['attributes']) && (empty($product['attributes']) === false))
				$params['basketitem_name'.$index] .= ' - '.$product['attributes'];

			$params['basketitem_desc'.$index] = Tools::substr(strip_tags($product['description_short']), 0, 50).'...';

			#$params['basketitem_amount'.$index] = Tools::ps_round($product['price_wt'], $this->decimals);
			$params['basketitem_qty'.$index] = $product['quantity'];
			
		}

		return $params;



	}

	function processOnError($id_cart,$msg){
		$context = Context::getContext();
		$order_id=Order::getOrderByCartId($id_cart);
		$this->logDebug("processOnError()->start() order_id: ".$order_id.' msg:'.$msg);
		
		//$msg=str_replace("'", '', $msg);
		$this->updateOrderState($order_id,8);
		return $context->link->getModuleLink('SparkassenInternetkasse', 'confirmation', array('status'=>'error'), Tools::usingSecureMode());
			
		//8_PS_OS_ERROR_;
	}

	function processOnCancel($id_cart){
		$order_id=Order::getOrderByCartId($id_cart);
		
		$context = Context::getContext();
		$this->logDebug("processOnCancel()->start() order_id:".$order_id);
		$this->updateOrderState($order_id,6);
		$SparkassenInternetkasse = new SparkassenInternetkasse();
		return $context->link->getModuleLink($SparkassenInternetkasse->name, 'confirmation', array('status'=>'cancel'), Tools::usingSecureMode());
	}
	
	function updateOrderState($id_order,$newState){
			$this->logDebug('updateOrderState: newState:'.$newState);
			$order=new Order($id_order);
			$history = new OrderHistory();
			$history->id_order = (int)$id_order;
			$history->changeIdOrderState($newState, $id_order,true);
			$history->addWithemail();
			$history->save();
	}
	
	function processOnOk($id_cart,$amount,$currency){
		$context = Context::getContext();
			
		$order_id=Order::getOrderByCartId($id_cart);
		$this->logDebug("processOnOk()->start() order_id:".$order_id." amount:".$amount.' currency:'.$currency);
		$validationmsg=$this->validateOrderAmount($order_id,$amount,$currency);
		
		if($validationmsg=='ok')
		{
			$this->updateOrderState($order_id,(int)Configuration::get('SPARKASSENINTERNETKASSE_ORDERSTATE_SUCCESS'));
			$SparkassenInternetkasse = new SparkassenInternetkasse();
			return $context->link->getModuleLink($SparkassenInternetkasse->name, 'confirmation', array('status'=>'ok'), Tools::usingSecureMode());
		}
		else{
			return $this->processOnError(order_id,$validationmsg);
		}
	}

	function validateOrderAmount(&$order_id,$amount,$currency){
		$this->logDebug("validate()->start() order_id: ".$order_id.' amount:'.$amount.' currency:'.$currency);
		$order=new Order($order_id);
		$context = Context::getContext();
		$tmpCurrency = new Currency((int)$order->id_currency);
		$orderCurrency=$tmpCurrency->iso_code;
		$orderAmmount=$this->formatAmount($order->total_paid);
		$this->logDebug("validate() found order amount:".$orderAmmount. 'order currency: '.$orderCurrency);
		if($orderAmmount==$amount && $orderCurrency==$currency)
			return 'ok';
		else if($orderAmmount!=$amount)
			return 'order amount is not ok. o-Amount:'.$orderAmmount.' Amount:'.$amount;
		else if($orderCurrency!=$currency)
			return 'order currency is not ok. o-Currency:'.$orderCurrency.' currency:'.$currency;
		
		return 'false';
	}
	
	
	
	public static function getShopDomainSsl($http = false, $entities = false)
	{
		if (method_exists('Tools', 'getShopDomainSsl'))
			return Tools::getShopDomainSsl($http, $entities);
		else
		{
			if (!($domain = Configuration::get('PS_SHOP_DOMAIN_SSL')))
				$domain = self::getHttpHost();
			if ($entities)
				$domain = htmlspecialchars($domain, ENT_COMPAT, 'UTF-8');
			if ($http)
				$domain = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').$domain;
			return $domain;
		}
	}
	


}
