<?php

class SparkassenInternetkasseRedirectModuleFrontController extends ModuleFrontController
{
	public $display_column_left = false;

	public function initContent()
	{
		

		if (!$this->context->customer->isLogged(true) || empty($this->context->cart))
			Tools::redirect('index.php');

		if($_GET['paymentmethod'] == 'MP'){
			$url = Tools::getHttpHost(true).__PS_BASE_URI__.$iso_code.'module/SparkassenInternetkasse/mpinit';
			Tools::redirect($url);
		}
		
		parent::initContent();
		
		$SparkassenInternetkasse = new SparkassenInternetkasse();
		$payModule = new SparkassenInternetkasseModel();
		
		$this->context->cookie->__set('Amount',$this->context->cart->getOrderTotal(true));
		$this->context->cookie->__set('basketId',$payModule->getBasketid());
		$this->context->cookie->__set('orderId',$payModule->getOrderid());
		
		$customer=$this->context->customer;
		$this->context = Context::getContext();
		$cart = $this->context->cart;
		$this->id_module = Tools::getValue('paymentmethod');
		$currency = new Currency((int)$this->context->cart->id_currency);
		$total=$this->context->cart->getOrderTotal(true);	
		

		$orderStatus = new OrderState(intval(Configuration::get('SPARKASSENINTERNETKASSE_ORDERSTATE_PENDING')));
		$SparkassenInternetkasse->validateOrder($cart->id, Configuration::get('SPARKASSENINTERNETKASSE_ORDERSTATE_PENDING'), $total, $SparkassenInternetkasse->displayName, NULL, array(), (int)$currency->id, false, $customer->secure_key);
		
		if($_GET['cfmp'] == 1){
			$context = Context::getContext();
			$iso_code = $context->language->iso_code;
			$context->cookie->__set('first_name_c','');
			$context->cookie->__set('last_name_c','');
			$context->cookie->__set('street_c','');
			$context->cookie->__set('zip_c','');
			$context->cookie->__set('city_c','');
			$context->cookie->__set('country_c','');
			$context->cookie->__set('email_c','');
			$context->cookie->__set('phone_c','');
			$context->cookie->__set('first_name_b','');
			$context->cookie->__set('last_name_b','');
			$context->cookie->__set('street_b','');
			$context->cookie->__set('zip_b','');
			$context->cookie->__set('city_b','');
			$context->cookie->__set('country_b','');
			$context->cookie->__set('phone_c','');
			$context->cookie->__set('[creditc]','');
			$context->cookie->__set('[txn_card]','');
			$context->cookie->__set('[txn_expdat]','');
			$context->cookie->__set('was_in_wallet','0');
			$context->cookie->__set('customers_email_address','');
			$url = Tools::getHttpHost(true).__PS_BASE_URI__.$iso_code.'/module/SparkassenInternetkasse/mpprozess';
			Tools::redirect($url);
		}
		
		if($payModule->isIFrame()){

		$iframe_url=$payModule->preparePaymentGatewayRequest();

		$this->context->smarty->assign(array(
			'form_action_type' => 'iframe',
			'iframe_url' => $iframe_url,
			'total' => Tools::displayPrice($total, $currency),
		));
		}
		
		else{
		$form_action=$payModule->getPaymentGatewayURL();
		$form_data=$payModule->getPaymentGatewayData();
		$this->context->smarty->assign(array(
				'form_action_type' => 'redirect',
				'form_action' => $form_action,
				'form_data'=>$form_data,				
				'total' => Tools::displayPrice($total, $currency),
			));
		}
		$orderStatus = new OrderState(intval(Configuration::get('SPARKASSENINTERNETKASSE_ORDERSTATE_PENDING')));
		$SparkassenInternetkasse->validateOrder($cart->id, Configuration::get('SPARKASSENINTERNETKASSE_ORDERSTATE_PENDING'), $total, $SparkassenInternetkasse->displayName, NULL, array(), (int)$currency->id, false, $customer->secure_key);
		$this->setTemplate('payment-redirect.tpl');
	}
}
