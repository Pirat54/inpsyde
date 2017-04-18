<?php

class SparkassenInternetkasseMpreturnModuleFrontController extends ModuleFrontController
{
	public $display_column_left = false;

	public function initContent()
	{
		if (empty($this->context->cart))
			Tools::redirect('index.php');

		parent::initContent();
		$this->setTemplate('mpreturn.tpl');
	
		$this->mpreturnAction();
		
		
		
	}
	
	public function mpreturnAction()
	{
		$context = Context::getContext();

		$url = $context->cookie->urlMP;
		#echo $url;
		
		$params=array(
		'command'=> 'getwalletdata',
		'wallet_checkout_resource_url' => $_GET['checkout_resource_url'],
		'wallet_mpstatus'=>$_GET['mpstatus'],
		'wallet_oauth_token'=>$_GET['oauth_token'],
		'wallet_oauth_verifier' => $_GET['oauth_verifier'],
		'walletref'=> $context->cookie->walletref,
		);
		
		$fields_string .= http_build_query($params);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERPWD, $context->cookie->userMP.':'.$context->cookie->secretMP);
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

			if ($array['posherr'] == 0){
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
				$context->cookie->__set('was_in_wallet','1');
				$context->cookie->__set('customers_email_address','');
	
				#shipping
				$customer_name = explode('+',$array['customer_deliveryaddr_lastname']);
	
				$context->cookie->__set('first_name_c',utf8_encode($customer_name[0]));
				$context->cookie->__set('last_name_c',utf8_encode($customer_name[1]));
	
				$customer_street = explode('+',$array['customer_deliveryaddr_street']);
				foreach($customer_street as $street)
				{
					$street_c .= utf8_encode($street).' ';
				}
				$context->cookie->__set('street_c',trim($street_c));
	
				$customer_zip = explode('+',$array ['customer_addr_zip']);
				foreach($customer_zip as $zip)
				{
					$zip_c .= $zip;
				}
				$context->cookie->__set('zip_c',$zip_c);
	
				$customer_city = explode('+',$array['customer_deliveryaddr_city']);
				foreach($customer_city as $city)
				{
					$city_c .= utf8_encode($city).' ';
				}
				$context->cookie->__set('city_c',trim($city_c));
	
				$customer_country = explode('+',$array['customer_deliveryaddr_country']);
				foreach($customer_country as $country)
				{
					$country_c .= $country;
				}
				$context->cookie->__set('country_c',$country_c);
	
				$customer_phone = explode('+',$array['customer_deliveryaddr_phone']);
				foreach($customer_phone as $phone)
				{
					$phone_c .= $phone;
				}
				$context->cookie->__set('phone_c',$phone_c);
	
				$customer_email = explode('+',$array['customer_email']);
				foreach($customer_email as $email)
				{
					$email_c .= $email;
				}
				
	
				#end shipping
	
				#billing
				$billing_name = explode('+',$array['customer_lastname']);
				$context->cookie->__set('first_name_b',utf8_encode($billing_name[0]));
				$context->cookie->__set('last_name_b',utf8_encode($billing_name[1]));
	
				$billing_street = explode('+',$array['customer_addr_street']);
				foreach($billing_street as $street)
				{
					$street_b .= utf8_encode($street).' ';
				}
				$context->cookie->__set('street_b',trim($street_b));
	
				$billing_zip = explode('+',$array ['customer_addr_zip']);
				foreach($billing_zip as $zip)
				{
					$zip_b .= $zip;
				}
				$context->cookie->__set('zip_b',$zip_b);
	
				$billing_city = explode('+',$array['customer_addr_city']);
				foreach($billing_city as $city)
				{
					$city_b .= utf8_encode($city).' ';
				}
				$context->cookie->__set('city_b',trim($city_b));
	
				$billing_country = explode('+',$array['customer_addr_country']);
				foreach($billing_country as $country)
				{
					$country_b .= $country;
				}
				$context->cookie->__set('country_b',$country_b);
	
				$billing_phone = explode('+',$array['customer_phone']);
				foreach($billing_phone as $phone)
				{
					$phone_b .= $phone;
				}
				$context->cookie->__set('phone_b',$phone_b);
	
				#end billing
	
				$context->cookie->__set('customers_email_address',$email_c);
				#$context->cookie->__set('customers_email_address','gs@gswebtogo.de');
			}

		}
		
		if($street_c == ' ' || $zip_c == ' ' || $city_c == ' ' || $street_c == '' || $zip_c == '' || $city_c == ''){
			Tools::redirect($context->link->getModuleLink('SparkassenInternetkasse', 'confirmation', array('status'=>'error'), Tools::usingSecureMode()));
		}
		$id_customer = $this->getCustomerIdByEmail($context->cookie->customers_email_address);
		
		if($id_customer == ''){
			$customer = $this->setCustomerInformation($context->cookie->customers_email_address);
			$customer->add();
			#$id_customer = $customer->id;
			$address = self::setCustomerAddress($customer);
			$address->add();
			$address = self::setShippingAddress($customer);
			$address->add();
			$id_product = (int)Tools::getValue('id_product');
			$product_quantity = (int)Tools::getValue('quantity');
			$id_product_attribute = Tools::getValue('id_p_attr');
				
			if (($id_product > 0) && $id_product_attribute !== false && ($product_quantity > 0))
			{
				setContextData($cart);
			
				$cart->context->cookie->id_cart = (int)$cart->context->cart->id;
					
				$cart->context->cart->updateQty((int)$product_quantity, (int)$id_product, (int)$id_product_attribute);
				$cart->context->cart->update();
			}
			self::login($context->cookie->customers_email_address);
			
		}elseif ($id_customer = Customer::customerExists($context->cookie->customers_email_address, true)){
			$aid = self::checkShippingAddress($id_customer);
			if($aid == ''){
				$customer = new Customer($id_customer);
				$address = self::setShippingAddress($customer);
				$address->add();
			}
			
			
			
			$id_product = (int)Tools::getValue('id_product');
			$product_quantity = (int)Tools::getValue('quantity');
			$id_product_attribute = Tools::getValue('id_p_attr');
			
			if (($id_product > 0) && $id_product_attribute !== false && ($product_quantity > 0))
			{
				setContextData($cart);

					$cart->context->cookie->id_cart = (int)$cart->context->cart->id;
			
				$cart->context->cart->updateQty((int)$product_quantity, (int)$id_product, (int)$id_product_attribute);
				$cart->context->cart->update();
			}
			
			self::login($context->cookie->customers_email_address);
		}
		Tools::redirect('index.php?controller=order&step=1');
	}
	
	public static function getCustomerIdByEmail($email)
	{
		return Db::getInstance()->getValue('
			SELECT `id_customer`
			FROM `'._DB_PREFIX_.'customer`
			WHERE email = \''.pSQL($email).'\'');
	}
	
	function setCustomerInformation($email)
	{
		$context = Context::getContext();
		$customer = new Customer();
		$customer->email = $email;
		$customer->lastname = $context->cookie->last_name_b;
		$customer->firstname = $context->cookie->first_name_b;
		$customer->passwd = Tools::encrypt(Tools::passwdGen());
		return $customer;
	}
	
	public static function checkShippingAddress($id_customer)
	{
		$context = Context::getContext();
		return Db::getInstance()->getValue('
			SELECT `id_address`
			FROM `'._DB_PREFIX_.'address`
			WHERE id_customer = \''.pSQL($id_customer).'\' AND address1 = \''.pSQL($context->cookie->street_c).'\' AND postcode = \''.pSQL($context->cookie->zip_c).'\' AND city = \''.pSQL($context->cookie->city_c).'\'');
	}
	
	function setCustomerAddress($customer, $id = null)
	{
		$context = Context::getContext();
		$address = new Address($id);
		$address->id_country = Country::getByIso($context->cookie->country_b);
		if ($id == null)
		$address->alias = 'Masterpass_Address';
		$address->lastname = $context->cookie->last_name_b;
		$address->firstname = $context->cookie->first_name_b;
		$address->address1 = $context->cookie->street_b;
		$address->city = $context->cookie->city_b;
		$address->postcode = $context->cookie->zip_b;
		$address->phone = preg_replace('![^0-9]!', '', $context->cookie->phone_b);
		$address->id_customer = $customer->id;
		return $address;
	}
	
	function setShippingAddress($customer, $id = null)
	{
		$context = Context::getContext();
		$address = new Address($id);
		$address->id_country = Country::getByIso($context->cookie->country_c);
		if ($id == null)
		$address->alias = 'Masterpass_Address';
		$address->lastname = $context->cookie->last_name_c;
		$address->firstname = $context->cookie->first_name_c;
		$address->address1 = $context->cookie->street_c;
		$address->city = $context->cookie->city_c;
		$address->postcode = $context->cookie->zip_c;
		$address->phone = preg_replace('![^0-9]!', '', $context->cookie->phone_c);
		$address->id_customer = $customer->id;
		return $address;
	}
	
	public function login($email) {
		
		$customer = new \Customer();
		$authentication = $customer->getByEmail($email);
		if (!$authentication) //user doesn't exist
			return false;
		
		$ctx = \Context::getContext();
		$ctx->cookie->id_compare = isset($ctx->cookie->id_compare) ? $ctx->cookie->id_compare: \CompareProduct::getIdCompareByIdCustomer($customer->id);
		$ctx->cookie->id_customer = (int)($customer->id);
		$ctx->cookie->customer_lastname = $customer->lastname;
		$ctx->cookie->customer_firstname = $customer->firstname;
		$ctx->cookie->logged = 1;
		$customer->logged = 1;
		$ctx->cookie->is_guest = $customer->isGuest();
		$ctx->cookie->passwd = $customer->passwd;
		$ctx->cookie->email = $customer->email;
		// Add customer to the context
		$ctx->customer = $customer;
		$id_cart = (int)\Cart::lastNoneOrderedCart($ctx->customer->id);
		if ($id_cart) {
			$ctx->cart = new \Cart($id_cart);
		} else {
			$ctx->cart = new \Cart();
			$ctx->cart->id_currency = \Currency::getDefaultCurrency()->id; //mandatory field
		}
		$ctx->cart->id_customer = (int)$customer->id;
		$ctx->cart->secure_key = $customer->secure_key;
		$ctx->cart->save();
		$ctx->cookie->id_cart = (int)$ctx->cart->id;
		\CartRule::autoRemoveFromCart($ctx);
		\CartRule::autoAddToCart($ctx);
		$ctx->cookie->write();
		return true;
	}
}
