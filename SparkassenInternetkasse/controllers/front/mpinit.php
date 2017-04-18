<?php

class SparkassenInternetkasseMpinitModuleFrontController extends ModuleFrontController
{
	public $display_column_left = false;

	public function initContent()
	{
		parent::initContent();
		
		$SparkassenInternetkasse = new SparkassenInternetkasse();	
		$payModule = new SparkassenInternetkasseModel();

		$string = $payModule->startMasterpass();
		
		$this->context->smarty->assign(array(
			'string' => $string,
		));
		
		$this->setTemplate('mpinit.tpl');
	}
}
