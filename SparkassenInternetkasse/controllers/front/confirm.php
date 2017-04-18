<?php

class SparkassenInternetkasseConfirmModuleFrontController extends ModuleFrontController
{
	public $display_column_left = false;

	public function initContent()
	{
		if (!$this->context->customer->isLogged(true) || empty($this->context->cart))
			Tools::redirect('index.php');

		parent::initContent();
		
		$SparkassenInternetkasse = new SparkassenInternetkasse();
		
		$customer=$this->context->customer;
		$this->context = Context::getContext();
		$cart = $this->context->cart;
		$this->id_module = Tools::getValue('paymentmethod');
		$currency = new Currency((int)$this->context->cart->id_currency);
		$total=$this->context->cart->getOrderTotal(true);
		$payModule = new SparkassenInternetkasseModel();
		$form_action=$payModule->getPaymentGatewayURL();
		$form_data=$payModule->getPaymentGatewayData();
		$this->context->smarty->assign(array(
			'form_action' => $form_action,
			'form_data'=>$form_data,
				
			'total' => Tools::displayPrice($total, $currency),
		));
		
		$SparkassenInternetkasse->validateOrder($cart->id, Configuration::get('SPARKASSENINTERNETKASSE_ORDERSTATE_PENDING'), $total, $SparkassenInternetkasse->displayName, NULL, array(), (int)$currency->id, false, $customer->secure_key);
		$this->setTemplate('order-summary.tpl');
	
	}
}
