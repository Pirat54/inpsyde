<?php
include_once(dirname(__FILE__).'/../../config/config.inc.php');
include_once(_PS_MODULE_DIR_.'SparkassenInternetkasse/api/SparkassenInternetkasseModel.php');

$payModule = new SparkassenInternetkasseModel();
$status=$payModule->proccessGatewayNotification();
echo $status['redirecturl'];