<?php

if (!defined('_PS_VERSION_'))
	die(header('HTTP/1.0 404 Not Found'));


class LoginMpUser extends ObjectModel {

	public $user_id;

	protected $table = _DB_PREFIX_.'customer';
	protected $identifier = 'id_customer';


	public function __construct($id = false, $id_lang = false) 
	{
		parent::__construct($id, $id_lang);
	}
	
	public function getFields()
	{
		parent::validateFields();
		$fields = array();
		foreach (array_keys($this->fieldsValidate) as $field)
			$fields[$field] = $this->$field;
		return $fields;
	}

	public static function getMasterpassLoginUsers($email_mp_user = false)
	{
		$sql = "
			SELECT `id_customer` 
			FROM `"._DB_PREFIX_."customer`
			WHERE 1
		";

		$sql .= " AND `email` = '".$email_mp_user."' ";

		$results = DB::getInstance()->executeS($sql);
		$logins = array();

		if ($results && count($results))
		{
			foreach ($results as $result)
				$logins[$result['id_customer']] = new LoginMpUser((int)$result['id_customer']);
		}

		return $logins;
	}

}


?>