<?php
if (!defined('_PS_VERSION_'))
	exit;

require_once dirname(__FILE__).'/api/SparkassenInternetkasseModel.php';



class SparkassenInternetkasse extends PaymentModule
{
	public function __construct()
	{
		$this->name = 'SparkassenInternetkasse';
		$this->tab = 'payments_gateways';
		$this->version = '1.0.0';
		$this->author = '';
		
		
		parent::__construct();

		$this->displayName = 'Sparkassen-Internetkasse';
		$this->description = $this->l('Sparkassen-Internetkasse');
		$this->page = basename(__FILE__, '.php');


		/* For 1.4.3 and less compatibility */
		$updateConfig = array(
			'PS_OS_CHEQUE' => 1,
			'PS_OS_PAYMENT' => 2,
			'PS_OS_PREPARATION' => 3,
			'PS_OS_SHIPPING' => 4,
			'PS_OS_DELIVERED' => 5,
			'PS_OS_CANCELED' => 6,
			'PS_OS_REFUND' => 7,
			'PS_OS_ERROR' => 8,
			'PS_OS_OUTOFSTOCK' => 9,
			'PS_OS_BANKWIRE' => 10,
			'PS_OS_PAYPAL' => 11,
			'PS_OS_WS_PAYMENT' => 12);

		foreach ($updateConfig as $u => $v)
			if (!Configuration::get($u) || (int)Configuration::get($u) < 1)
			{
				if (defined('_'.$u.'_') && (int)constant('_'.$u.'_') > 0)
					Configuration::updateValue($u, constant('_'.$u.'_'));
				else
					Configuration::updateValue($u, $v);
			}

		/* Backward compatibility */
		require(_PS_MODULE_DIR_.$this->name.'/backward_compatibility/backward.php');

		$this->checkForUpdates();
	}

	public function install()
	{
		if (!parent::install() || !$this->registerHook('payment') || !$this->registerHook('displayPaymentEU') || !$this->registerHook('paymentReturn') ||
				!$this->registerHook('shoppingCartExtra') || !$this->registerHook('backBeforePayment') || !$this->registerHook('rightColumn') ||
				!$this->registerHook('cancelProduct') || !$this->registerHook('productFooter') || !$this->registerHook('header') ||
				!$this->registerHook('adminOrder') || !$this->registerHook('backOfficeHeader') || !$this->registerHook('actionPSCleanerGetModulesTables'))
					return false;
		
		$inst=parent::install() &&
			$this->registerHook('orderConfirmation') &&
			$this->registerHook('payment') &&
			$this->registerHook('paymentReturn') &&
			$this->registerHook('displayHeader') &&
			$this->registerHook('backOfficeHeader');

			Configuration::updateValue('SPARKASSENINTERNETKASSE_TEST_MODE', 0);
			Configuration::updateValue('SPARKASSENINTERNETKASSE_IFRAME_MODE', 0);
			Configuration::updateValue('SPARKASSENINTERNETKASSE_HOLD_REVIEW_OS', _PS_OS_ERROR_);
			Configuration::updateValue('SPARKASSENINTERNETKASSE_CC',1);
			Configuration::updateValue('SPARKASSENINTERNETKASSE_GP',1);
			Configuration::updateValue('SPARKASSENINTERNETKASSE_DD',1);
			Configuration::updateValue('SPARKASSENINTERNETKASSE_PP',1);
			Configuration::updateValue('SPARKASSENINTERNETKASSE_MP',1);
			Configuration::updateValue('SPARKASSENINTERNETKASSE_DEBUG_MODE','off');
			
			$this->addNewOrderState();
			
		return $inst;
	}
	
	
		/**
	 * Add paymill order state
	 * @return boolean
	 */
	private function addNewOrderState()
	{
		if (!Configuration::get('SPARKASSENINTERNETKASSE_ORDERSTATE_PENDING'))
		{
			$new_orderstate = new OrderState();
			$new_orderstate->name = array();
			$new_orderstate->module_name = $this->name;
			$new_orderstate->send_email = false;
			$new_orderstate->color = '#73E650';
			$new_orderstate->hidden = false;
			$new_orderstate->delivery = false;
			$new_orderstate->logable = true;
			$new_orderstate->invoice = false;
			$new_orderstate->paid = false;
			foreach (Language::getLanguages() as $language)
			{
				if (Tools::strtolower($language['iso_code']) == 'de')
					$new_orderstate->name[$language['id_lang']] = 'Warten auf Zahlungseingang von SparkassenInternetkasse';
				else
					$new_orderstate->name[$language['id_lang']] = 'Waiting for payment via SparkassenInternetkasse';
			}

			if ($new_orderstate->add())
			{
				$_icon = dirname(__FILE__).'/img/orderstate_min.gif';
				$new_state_icon = dirname(__FILE__).'/../../img/os/'.(int)$new_orderstate->id.'.gif';
				copy($_icon, $new_state_icon);
			}

			Configuration::updateValue('SPARKASSENINTERNETKASSE_ORDERSTATE_PENDING', (int)$new_orderstate->id);
		}
		
		if (!Configuration::get('SPARKASSENINTERNETKASSE_ORDERSTATE_SUCCESS'))
		{
			$new_orderstate = new OrderState();
			$new_orderstate->name = array();
			$new_orderstate->module_name = $this->name;
			$new_orderstate->send_email = false;
			$new_orderstate->color = '#238C00';
			$new_orderstate->hidden = false;
			$new_orderstate->delivery = false;
			$new_orderstate->logable = true;
			$new_orderstate->invoice = false;
			$new_orderstate->paid = true;
			foreach (Language::getLanguages() as $language)
			{
				if (Tools::strtolower($language['iso_code']) == 'de')
					$new_orderstate->name[$language['id_lang']] = 'SparkassenInternetkasse Zahlungseingang erfolgreich ';
				else
					$new_orderstate->name[$language['id_lang']] = 'Successful payment via SparkassenInternetkasse ';
			}

			if ($new_orderstate->add())
			{
				$_icon = dirname(__FILE__).'/img/orderstate_min.gif';
				$new_state_icon = dirname(__FILE__).'/../../img/os/'.(int)$new_orderstate->id.'.gif';
				copy($_icon, $new_state_icon);
			}

			Configuration::updateValue('SPARKASSENINTERNETKASSE_ORDERSTATE_SUCCESS', (int)$new_orderstate->id);
		}

		return true;
	}
	
	
	private function addNewState(){
		
		// check if the order status is defined
		if (!defined('SPARKASSENINTERNETKASSE_STATUS_PENDING')) {
			// order status is not defined - check if, it exists in the table
		$rq = Db::getInstance()->getRow('
		SELECT `id_order_state` FROM `'._DB_PREFIX_.'order_state_lang`
		WHERE id_lang = \''.pSQL('1').'\' AND  name = \''.pSQL('SparkassenInternetkasseName pending').'\'');
		if ($rq && isset($rq['id_order_state']) && intval($rq['id_order_state']) > 0) {
			// order status exists in the table - define it.
			define('SPARKASSENINTERNETKASSE_STATUS_PENDING', $rq['id_order_state']);
		} else {
					// order status doesn't exist in the table
					// insert it into the table and then define it.
			Db::getInstance()->Execute('
			INSERT INTO `'._DB_PREFIX_.'order_state` (`unremovable`, `color`) VALUES(1, \'lightblue\')');
			$stateid = Db::getInstance()->Insert_ID();
			Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'order_state_lang` (`id_order_state`, `id_lang`, `name`)
			VALUES(' . intval($stateid) . ', 1, \'SparkassenInternetkasseName pending\')');
			define('SPARKASSENINTERNETKASSE_STATUS_PENDING', $stateid);
		}
		return true;
}
	}

	public function uninstall()
	{
		Configuration::deleteByName('SPARKASSENINTERNETKASSE_TEST_MODE');
		Configuration::deleteByName('SPARKASSENINTERNETKASSE_IFRAME_MODE');
		Configuration::deleteByName('SPARKASSENINTERNETKASSE_MASTERPASS_MODE');
		Configuration::deleteByName('SPARKASSENINTERNETKASSE_CC');
		Configuration::deleteByName('SPARKASSENINTERNETKASSE_GP');
		Configuration::deleteByName('SPARKASSENINTERNETKASSE_DD');
		Configuration::deleteByName('SPARKASSENINTERNETKASSE_PP');
		Configuration::deleteByName('SPARKASSENINTERNETKASSE_MP');
		Configuration::deleteByName('SPARKASSENINTERNETKASSE_HOLD_REVIEW_OS');
		Configuration::deleteByName('SPARKASSENINTERNETKASSE_SSLMERCHANT');
		Configuration::deleteByName('SPARKASSENINTERNETKASSE_SECRET');
		Configuration::deleteByName('SPARKASSENINTERNETKASSE_DEBUG_MODE');
		Configuration::deleteByName('SPARKASSENINTERNETKASSE_HOLD_REVIEW_OS');
		Configuration::deleteByName('SPARKASSENINTERNETKASSE_LOG_FILE_PATH');
		Configuration::deleteByName('SPARKASSENINTERNETKASSE_FORM_MERCHANTNAME');
		Configuration::deleteByName('SPARKASSENINTERNETKASSE_FORM_MERCHANTNAME');

		
		
		
		//GP
		Configuration::deleteByName('SPARKASSENINTERNETKASSE_LABEL0');
		Configuration::deleteByName('SPARKASSENINTERNETKASSE_LABEL1');
		Configuration::deleteByName('SPARKASSENINTERNETKASSE_LABEL2');
		Configuration::deleteByName('SPARKASSENINTERNETKASSE_LABEL3');
		Configuration::deleteByName('SPARKASSENINTERNETKASSE_LABEL4');
		                                            
		Configuration::deleteByName('SPARKASSENINTERNETKASSE_TEXT0');
		Configuration::deleteByName('SPARKASSENINTERNETKASSE_TEXT1');
		Configuration::deleteByName('SPARKASSENINTERNETKASSE_TEXT2');
		Configuration::deleteByName('SPARKASSENINTERNETKASSE_TEXT3');
		Configuration::deleteByName('SPARKASSENINTERNETKASSE_TEXT4');
		Configuration::deleteByName('SPARKASSENINTERNETKASSE_AGE_VERIFICATION');
		Configuration::deleteByName('SPARKASSENINTERNETKASSE_GP_TRANSACTIONTYPE');
		
		Configuration::deleteByName('SPARKASSENINTERNETKASSE_CC_PAYMENTOPTIONS');
		Configuration::deleteByName('SPARKASSENINTERNETKASSE_CC_AUTOCAPTURE');
	
		Configuration::deleteByName('SPARKASSENINTERNETKASSE_CC_ACCEPTCOUNTRIES');
		Configuration::deleteByName('SPARKASSENINTERNETKASSE_CC_DELIVERYCOUNTRY_ACTION');
		Configuration::deleteByName('SPARKASSENINTERNETKASSE_CC_REJECTCOUNTRIES');			
			
		Configuration::deleteByName('SPARKASSENINTERNETKASSE_DD_PAYMENTOPTIONS');
		Configuration::deleteByName('SPARKASSENINTERNETKASSE_DD_AUTOCAPTURE');
		Configuration::deleteByName('SPARKASSENINTERNETKASSE_DD_SEQUENCETYPE');
		Configuration::deleteByName('SPARKASSENINTERNETKASSE_DD_MANDATENAME');
		Configuration::deleteByName('SPARKASSENINTERNETKASSE_DD_MANDATEPREFIX');
		
		//MP
		Configuration::deleteByName('SPARKASSENINTERNETKASSE_MASTER_PASS_USER');
		Configuration::deleteByName('SPARKASSENINTERNETKASSE_MASTER_PASS_SECRET');
		Configuration::deleteByName('SPARKASSENINTERNETKASSE_MASTER_PASS_ADDRESS');
		
		return parent::uninstall();
	}

	public function hookOrderConfirmation($params)
	{
		if ($params['objOrder']->module != $this->name)
			return;

		if ($params['objOrder']->getCurrentState() != Configuration::get('PS_OS_ERROR'))
		{
			Configuration::updateValue('SPARKASSENINTERNETKASSE_CONFIGURATION_OK', true);
			$this->context->smarty->assign(array('status' => 'ok', 'id_order' => intval($params['objOrder']->id)));
		}
		else
			$this->context->smarty->assign('status', 'failed');

		return $this->display(__FILE__, 'views/templates/hook/orderconfirmation.tpl');
	}

	public function hookBackOfficeHeader()
	{
		$this->context->controller->addJQuery();
		if (version_compare(_PS_VERSION_, '1.5', '>='))
			$this->context->controller->addJqueryPlugin('fancybox');
		$this->context->controller->addCSS(__PS_BASE_URI__.'modules/SparkassenInternetkasse/css/SparkassenInternetkasse.css');
	}
	
	public function hookdisplayHeader()
	{
		if (!$this->active)
			return;

		$this->context->controller->addCSS(__PS_BASE_URI__.'modules/SparkassenInternetkasse/css/SparkassenInternetkasse.css');
	}
	
	public function getContent()
	{
		$html = '';

		if (Tools::isSubmit('submitModule'))
		{
			
			Configuration::updateValue('SPARKASSENINTERNETKASSE_SSLMERCHANT', Tools::getvalue('SparkassenInternetkasse_sslmerchant'));
			Configuration::updateValue('SPARKASSENINTERNETKASSE_SECRET', Tools::getvalue('SparkassenInternetkasse_secret'));
				
			Configuration::updateValue('SPARKASSENINTERNETKASSE_TEST_MODE', Tools::getvalue('SparkassenInternetkasse_test_mode'));
			Configuration::updateValue('SPARKASSENINTERNETKASSE_IFRAME_MODE', Tools::getvalue('SparkassenInternetkasse_iframe_mode'));
			Configuration::updateValue('SPARKASSENINTERNETKASSE_MASTERPASS_MODE', Tools::getvalue('SparkassenInternetkasse_masterpass_mode'));
			Configuration::updateValue('SPARKASSENINTERNETKASSE_CC', Tools::getvalue('SparkassenInternetkasse_cc'));
			Configuration::updateValue('SPARKASSENINTERNETKASSE_GP', Tools::getvalue('SparkassenInternetkasse_gp'));
			Configuration::updateValue('SPARKASSENINTERNETKASSE_DD', Tools::getvalue('SparkassenInternetkasse_dd'));
			Configuration::updateValue('SPARKASSENINTERNETKASSE_PP', Tools::getvalue('SparkassenInternetkasse_pp'));
			Configuration::updateValue('SPARKASSENINTERNETKASSE_MP', Tools::getvalue('SparkassenInternetkasse_mp'));
			Configuration::updateValue('SPARKASSENINTERNETKASSE_DEBUG_MODE', Tools::getvalue('SparkassenInternetkasse_debug_mode'));
			Configuration::updateValue('SPARKASSENINTERNETKASSE_HOLD_REVIEW_OS', (int)Tools::getvalue('SparkassenInternetkasse_hold_review_os'));
			Configuration::updateValue('SPARKASSENINTERNETKASSE_LOG_FILE_PATH', Tools::getvalue('SparkassenInternetkasse_log_file_path'));
			Configuration::updateValue('SPARKASSENINTERNETKASSE_FORM_MERCHANTNAME', Tools::getvalue('SparkassenInternetkasse_form_merchantname'));
			Configuration::updateValue('SPARKASSENINTERNETKASSE_FORM_MERCHANTNAME', Tools::getvalue('SparkassenInternetkasse_form_merchantname'));
				
				
			
			//GP
			Configuration::updateValue('SPARKASSENINTERNETKASSE_LABEL0', Tools::getvalue('SparkassenInternetkasse_label0'));
			Configuration::updateValue('SPARKASSENINTERNETKASSE_LABEL1', Tools::getvalue('SparkassenInternetkasse_label1'));
			Configuration::updateValue('SPARKASSENINTERNETKASSE_LABEL2', Tools::getvalue('SparkassenInternetkasse_label2'));
			Configuration::updateValue('SPARKASSENINTERNETKASSE_LABEL3', Tools::getvalue('SparkassenInternetkasse_label3'));
			Configuration::updateValue('SPARKASSENINTERNETKASSE_LABEL4', Tools::getvalue('SparkassenInternetkasse_label4'));
			
			Configuration::updateValue('SPARKASSENINTERNETKASSE_TEXT0', Tools::getvalue('SparkassenInternetkasse_text0'));
			Configuration::updateValue('SPARKASSENINTERNETKASSE_TEXT1', Tools::getvalue('SparkassenInternetkasse_text1'));
			Configuration::updateValue('SPARKASSENINTERNETKASSE_TEXT2', Tools::getvalue('SparkassenInternetkasse_text2'));
			Configuration::updateValue('SPARKASSENINTERNETKASSE_TEXT3', Tools::getvalue('SparkassenInternetkasse_text3'));
			Configuration::updateValue('SPARKASSENINTERNETKASSE_TEXT4', Tools::getvalue('SparkassenInternetkasse_text4'));
			Configuration::updateValue('SPARKASSENINTERNETKASSE_AGE_VERIFICATION', Tools::getvalue('SparkassenInternetkasse_age_verification'));
			Configuration::updateValue('SPARKASSENINTERNETKASSE_GP_TRANSACTIONTYPE', Tools::getvalue('SparkassenInternetkasse_gp_transactiontype'));
			//CC
			Configuration::updateValue('SPARKASSENINTERNETKASSE_CC_PAYMENTOPTIONS', Tools::getvalue('SparkassenInternetkasse_cc_paymentoptions'));
			Configuration::updateValue('SPARKASSENINTERNETKASSE_CC_AUTOCAPTURE', Tools::getvalue('SparkassenInternetkasse_cc_autocapture'));
			
			Configuration::updateValue('SPARKASSENINTERNETKASSE_CC_ACCEPTCOUNTRIES', Tools::getvalue('SparkassenInternetkasse_cc_acceptcountries'));
			Configuration::updateValue('SPARKASSENINTERNETKASSE_CC_DELIVERYCOUNTRY_ACTION', Tools::getvalue('SparkassenInternetkasse_cc_deliverycountry_action'));
			Configuration::updateValue('SPARKASSENINTERNETKASSE_CC_REJECTCOUNTRIES', Tools::getvalue('SparkassenInternetkasse_cc_rejectcountries'));
			//DD
			Configuration::updateValue('SPARKASSENINTERNETKASSE_DD_PAYMENTOPTIONS', Tools::getvalue('SparkassenInternetkasse_dd_paymentoptions'));
			Configuration::updateValue('SPARKASSENINTERNETKASSE_DD_AUTOCAPTURE', Tools::getvalue('SparkassenInternetkasse_dd_autocapture'));
			Configuration::updateValue('SPARKASSENINTERNETKASSE_DD_SEQUENCETYPE', Tools::getvalue('SparkassenInternetkasse_dd_sequencetype'));
			Configuration::updateValue('SPARKASSENINTERNETKASSE_DD_MANDATENAME', Tools::getvalue('SparkassenInternetkasse_dd_mandatename'));
			Configuration::updateValue('SPARKASSENINTERNETKASSE_DD_MANDATEPREFIX', Tools::getvalue('SparkassenInternetkasse_dd_mandateprefix'));
			//MP
			Configuration::updateValue('SPARKASSENINTERNETKASSE_MASTER_PASS_USER', Tools::getvalue('SparkassenInternetkasse_master_pass_user'));
			Configuration::updateValue('SPARKASSENINTERNETKASSE_MASTER_PASS_SECRET', Tools::getvalue('SparkassenInternetkasse_master_pass_secret'));
			Configuration::updateValue('SPARKASSENINTERNETKASSE_MASTER_PASS_ADDRESS', Tools::getvalue('SparkassenInternetkasse_address_from_masterpass'));
	
			
			$html .= $this->displayConfirmation($this->l('Configuration updated'));
		}

		// For "Hold for Review" order status
		//$currencies = Currency::getCurrencies(false, true);
		$order_states = OrderState::getOrderStates((int)$this->context->cookie->id_lang);

		$this->context->smarty->assign(array(
			//'currencies' => $currencies,
			'module_dir' => $this->_path,
			'order_states' => $order_states,

			'SPARKASSENINTERNETKASSE_SSLMERCHANT' => Configuration::get('SPARKASSENINTERNETKASSE_SSLMERCHANT'),
			'SPARKASSENINTERNETKASSE_SECRET' => Configuration::get('SPARKASSENINTERNETKASSE_SECRET'),
			'SPARKASSENINTERNETKASSE_HOLD_REVIEW_OS' => (int)Configuration::get('SPARKASSENINTERNETKASSE_HOLD_REVIEW_OS'),
			
			
			'SPARKASSENINTERNETKASSE_DD' => Configuration::get('SPARKASSENINTERNETKASSE_DD'),
			'SPARKASSENINTERNETKASSE_TEST_MODE' => (bool)Configuration::get('SPARKASSENINTERNETKASSE_TEST_MODE'),
			'SPARKASSENINTERNETKASSE_IFRAME_MODE' => (bool)Configuration::get('SPARKASSENINTERNETKASSE_IFRAME_MODE'),
			'SPARKASSENINTERNETKASSE_MASTERPASS_MODE' => (bool)Configuration::get('SPARKASSENINTERNETKASSE_MASTERPASS_MODE'),
			'SPARKASSENINTERNETKASSE_CC' => Configuration::get('SPARKASSENINTERNETKASSE_CC'),
			'SPARKASSENINTERNETKASSE_GP' => Configuration::get('SPARKASSENINTERNETKASSE_GP'),
			'SPARKASSENINTERNETKASSE_PP' => Configuration::get('SPARKASSENINTERNETKASSE_PP'),
			'SPARKASSENINTERNETKASSE_DD' => Configuration::get('SPARKASSENINTERNETKASSE_DD'),
			'SPARKASSENINTERNETKASSE_MP' => Configuration::get('SPARKASSENINTERNETKASSE_MP'),
			'SPARKASSENINTERNETKASSE_DEBUG_MODE' => Configuration::get('SPARKASSENINTERNETKASSE_DEBUG_MODE'),
			'SPARKASSENINTERNETKASSE_LOG_FILE_PATH' => Configuration::get('SPARKASSENINTERNETKASSE_LOG_FILE_PATH'),
			
			'SPARKASSENINTERNETKASSE_TEXT0' => Configuration::get('SPARKASSENINTERNETKASSE_TEXT0'),
			'SPARKASSENINTERNETKASSE_TEXT1' => Configuration::get('SPARKASSENINTERNETKASSE_TEXT1'),
			'SPARKASSENINTERNETKASSE_TEXT2' => Configuration::get('SPARKASSENINTERNETKASSE_TEXT2'),
			'SPARKASSENINTERNETKASSE_TEXT3' => Configuration::get('SPARKASSENINTERNETKASSE_TEXT3'),
			'SPARKASSENINTERNETKASSE_TEXT4' => Configuration::get('SPARKASSENINTERNETKASSE_TEXT4'),
	
			'SPARKASSENINTERNETKASSE_LABEL0' => Configuration::get('SPARKASSENINTERNETKASSE_LABEL0'),
			'SPARKASSENINTERNETKASSE_LABEL1' => Configuration::get('SPARKASSENINTERNETKASSE_LABEL1'),
			'SPARKASSENINTERNETKASSE_LABEL2' => Configuration::get('SPARKASSENINTERNETKASSE_LABEL2'),
			'SPARKASSENINTERNETKASSE_LABEL3' => Configuration::get('SPARKASSENINTERNETKASSE_LABEL3'),
			'SPARKASSENINTERNETKASSE_LABEL4' => Configuration::get('SPARKASSENINTERNETKASSE_LABEL4'),
			'SPARKASSENINTERNETKASSE_LABEL4' => Configuration::get('SPARKASSENINTERNETKASSE_LABEL4'),
			'SPARKASSENINTERNETKASSE_AGE_VERIFICATION' => Configuration::get('SPARKASSENINTERNETKASSE_AGE_VERIFICATION'),
	
			'SPARKASSENINTERNETKASSE_GP_TRANSACTIONTYPE' => Configuration::get('SPARKASSENINTERNETKASSE_GP_TRANSACTIONTYPE'),
			
			'SPARKASSENINTERNETKASSE_CC_PAYMENTOPTIONS' => Configuration::get('SPARKASSENINTERNETKASSE_CC_PAYMENTOPTIONS'),
			'SPARKASSENINTERNETKASSE_CC_ACCEPTCOUNTRIES' => Configuration::get('SPARKASSENINTERNETKASSE_CC_ACCEPTCOUNTRIES'),
			'SPARKASSENINTERNETKASSE_CC_DELIVERYCOUNTRY_ACTION' => Configuration::get('SPARKASSENINTERNETKASSE_CC_DELIVERYCOUNTRY_ACTION'),
			'SPARKASSENINTERNETKASSE_CC_REJECTCOUNTRIES' => Configuration::get('SPARKASSENINTERNETKASSE_CC_REJECTCOUNTRIES'),
			'SPARKASSENINTERNETKASSE_CC_AUTOCAPTURE' => Configuration::get('SPARKASSENINTERNETKASSE_CC_AUTOCAPTURE'),
		//DD		
			'SPARKASSENINTERNETKASSE_DD_PAYMENTOPTIONS' => Configuration::get('SPARKASSENINTERNETKASSE_DD_PAYMENTOPTIONS'),
			'SPARKASSENINTERNETKASSE_DD_AUTOCAPTURE' => Configuration::get('SPARKASSENINTERNETKASSE_DD_AUTOCAPTURE'),
			'SPARKASSENINTERNETKASSE_DD_TRANSACTIONTYPE' => Configuration::get('SPARKASSENINTERNETKASSE_DD_TRANSACTIONTYPE'),
			'SPARKASSENINTERNETKASSE_DD_SEQUENCETYPE' => Configuration::get('SPARKASSENINTERNETKASSE_DD_SEQUENCETYPE'),
			'SPARKASSENINTERNETKASSE_DD_MANDATENAME' => Configuration::get('SPARKASSENINTERNETKASSE_DD_MANDATENAME'),
			'SPARKASSENINTERNETKASSE_DD_MANDATEPREFIX' => Configuration::get('SPARKASSENINTERNETKASSE_DD_MANDATEPREFIX'),
		//MP
			'SPARKASSENINTERNETKASSE_MASTER_PASS_USER' => Configuration::get('SPARKASSENINTERNETKASSE_MASTER_PASS_USER'),
			'SPARKASSENINTERNETKASSE_MASTER_PASS_SECRET' => Configuration::get('SPARKASSENINTERNETKASSE_MASTER_PASS_SECRET'),
			'SPARKASSENINTERNETKASSE_MASTER_PASS_ADDRESS' => Configuration::get('SPARKASSENINTERNETKASSE_MASTER_PASS_ADDRESS'),
			

		));

		/* Determine which currencies are enabled on the store and supported by Authorize.net & list one credentials section per available currency */

				$configuration_id_name = 'SPARKASSENINTERNETKASSE_sslmerchant_';
 				$configuration_key_name = 'SparkassenInternetkasse_secret_';
				$this->context->smarty->assign($configuration_id_name, Configuration::get($configuration_id_name));
				$this->context->smarty->assign($configuration_key_name, Configuration::get($configuration_key_name));

		return $this->context->smarty->fetch(dirname(__FILE__).'/views/templates/admin/configuration.tpl');
	}
	
	public function hookshoppingCartExtra(){
		$helper = new SparkassenInternetkasseModel();
		if($helper->isMasterpass()){
			$mpiniturl = Tools::getHttpHost(true).__PS_BASE_URI__.$helper->getLocale().'/module/SparkassenInternetkasse/mpinit';
			$this->context->smarty->assign(array(
					'mp_logo' => $helper->getMasterpassbutton(),
					'mpiniturl' => $mpiniturl
			));
	
			return $this->fetchTemplate('mp_checkout_shortcut_button.tpl');
		}
	}
	
	public function fetchTemplate($name)
	{
		if (version_compare(_PS_VERSION_, '1.4', '<'))
			$this->context->smarty->currentTemplate = $name;
		elseif (version_compare(_PS_VERSION_, '1.5', '<'))
		{
			$views = 'views/templates/';
			if (@filemtime(dirname(__FILE__).'/'.$name))
				return $this->display(__FILE__, $name);
			elseif (@filemtime(dirname(__FILE__).'/'.$views.'hook/'.$name))
			return $this->display(__FILE__, $views.'hook/'.$name);
			elseif (@filemtime(dirname(__FILE__).'/'.$views.'front/'.$name))
			return $this->display(__FILE__, $views.'front/'.$name);
			elseif (@filemtime(dirname(__FILE__).'/'.$views.'admin/'.$name))
			return $this->display(__FILE__, $views.'admin/'.$name);
		}
	
		return $this->display(__FILE__, $name);
	}

	
		/**
	 * @return string
	 */
	public function hookdisplayPaymentReturn()
	{
		if (!$this->active)
			return;
		return $this->display(__FILE__, 'views/templates/hook/confirmation.tpl');
	}
	
	
	public function hookdisplayPayment($params)
	{
		if (!$this->active)
			return;
		
		$context = Context::getContext();
 		if($context->cookie->customers_email_address != ''){
 			$context->cookie->__set('customers_email_address','');
 			$iso_code = $context->language->iso_code;
 			$url = Tools::getHttpHost(true).__PS_BASE_URI__.$iso_code.'/module/SparkassenInternetkasse/redirect?cfmp=1';
 			Tools::redirect($url);
		}
		
		$this->context->smarty->assign('x_invoice_num', (int)$params['cart']->id);
		$this->context->smarty->assign('SPARKASSENINTERNETKASSE_CC', Configuration::get('SPARKASSENINTERNETKASSE_CC'));
		$this->context->smarty->assign('SPARKASSENINTERNETKASSE_DD', Configuration::get('SPARKASSENINTERNETKASSE_DD'));
		$this->context->smarty->assign('SPARKASSENINTERNETKASSE_GP', Configuration::get('SPARKASSENINTERNETKASSE_GP'));
		$this->context->smarty->assign('SPARKASSENINTERNETKASSE_PP', Configuration::get('SPARKASSENINTERNETKASSE_PP'));
		$this->context->smarty->assign('SPARKASSENINTERNETKASSE_MP', Configuration::get('SPARKASSENINTERNETKASSE_MP'));
		$this->context->smarty->assign('SPARKASSENINTERNETKASSE_IFRAME_MODE', Configuration::get('SPARKASSENINTERNETKASSE_IFRAME_MODE'));
		
		return $this->display(__FILE__, 'views/templates/hook/payment.tpl');
		
	}


	private function checkForUpdates()
	{
		// Used by PrestaShop 1.3 & 1.4
		if (version_compare(_PS_VERSION_, '1.5', '<') && self::isInstalled($this->name))
			foreach (array('1.4.8', '1.4.11') as $version)
			{
				$file = dirname(__FILE__).'/upgrade/install-'.$version.'.php';
				if (Configuration::get('MANDANT') < $version && file_exists($file))
				{
					include_once($file);
					call_user_func('upgrade_module_'.str_replace('.', '_', $version), $this);
				}
			}
	}
	

}
