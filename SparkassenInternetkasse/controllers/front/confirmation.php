<?php

class SparkassenInternetkasseConfirmationModuleFrontController extends ModuleFrontController
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
		$currency = new Currency((int)$this->context->cart->id_currency);
		$total=$this->context->cart->getOrderTotal(true);
		$payModule = new SparkassenInternetkasseModel();
		
		$this->context->smarty->assign(array(
			'status' => Tools::getValue('status')
		));
		
		$this->setTemplate('payment-state.tpl');
	
	}
}
