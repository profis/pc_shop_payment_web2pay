<?php
function pc_shop_payment_web2pay_install($controller) {
	global $core, $logger;
	$logger->debug('pc_shop_payment_web2pay_install()');
	
	$payment_option_model = new PC_shop_payment_option_model();
	$payment_option_model->absorb_debug_settings($logger);
	$payment_option_model->insert(array('code' => 'web2pay'), array(
		'lt' => array(
			'name' => 'Per mokėjimai.lt sistemą'
		),
		'en' => array(
			'name' => 'Using mokėjimai.lt system'
		),
		'ru' => array(
			'name' => 'Используя систему mokėjimai.lt'
		)
	), array('ignore' => true));
	
	//$core->Set_config('web2pay_signature', '', 'pc_shop_payment_web2pay');
	//$core->Set_config('web2pay_project_id', '', 'pc_shop_payment_web2pay');
	//$core->Set_config('web2pay_test', '', 'pc_shop_payment_web2pay');
	
	return true;
}

function pc_shop_payment_web2pay_uninstall($controller) {
	global $core, $logger;
	$logger->debug('pc_shop_payment_web2pay_uninstall()');
	
	$payment_option_model = new PC_shop_payment_option_model();
	$payment_option_model->absorb_debug_settings($logger);
	$payment_option_model->delete(array('where' => array('code' => 'web2pay')));
	
	return true;
}