<?php

class SparkassenInternetkasseMpprozessModuleFrontController extends ModuleFrontController
{
	public $display_column_left = false;

	public function initContent()
	{
		if (empty($this->context->cart))
			Tools::redirect('index.php');

		parent::initContent();
		$this->setTemplate('mpreturn.tpl');
		$SparkassenInternetkasse = new SparkassenInternetkasse();	
		$this->payModule = new SparkassenInternetkasseModel();

		$this->mpprozessAction();
		
		
		
	}
	
	public function mpprozessAction(){
		$context = Context::getContext();
		
		$url = $context->cookie->urlMP;

		$mp_amount = $context->cookie->Amount;
		
		$arrayamount = explode('.', $mp_amount);

		if(strlen($arrayamount[1]) < 2){
			$mp_amount = $mp_amount.'0';
		}

		$mp_amount = str_replace(',','',$mp_amount);
		$mp_amount = str_replace('.','',$mp_amount);
		
		$chars = 'ab7cd5fghjklmn4prstvw6xz2ae9iou';
		
		for ($p = 0; $p < 6; $p++)
		{
		$result .= ($p%2) ? $chars[mt_rand(19, 23)] : $chars[mt_rand(0, 18)];
		}
		
		$params=array(
				'command'=> 'authorization',
				'payment_options' => 'creditcard',
				'orderid'=>$result.'_'.$context->cookie->orderId,
				'amount'=>$mp_amount,
				'currency' => $this->payModule->getCurrency(),
				'basketnr' => $context->cookie->basketId,
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
			
			$rmsg = explode('+',$array['rmsg']);
			foreach($rmsg as $rmsg)
			{
				$rmsg_c .= $rmsg." ";
			}
			
			$array_oid = explode ( '_', $array['orderid'] );
			$oid = $array_oid[1];
		
			#$array['posherr'] = 0;
			if ($array['posherr'] == 0){
				
				#echo $this->payModule->processOnOk($oid,substr($mp_amount,0,-2).','.substr($mp_amount,-2) ,$this->payModule->getCurrency());
				
				$url = $this->payModule->processOnOk($oid,substr($mp_amount,0,-2).','.substr($mp_amount,-2) ,$this->payModule->getCurrency());
				Tools::redirect($url);
			}else{
				$url = $this->payModule->processOnError($oid,utf8_encode($rmsg_c));
				Tools::redirect($url);
		
			}
				
		}else{
			echo 'Leider steht dieser Service zur Zeit nicht zu Verfügung!<br>Bitte versuchen Sie es zu einem späteren Zeitpunkt nochmals';
		}

	}
}