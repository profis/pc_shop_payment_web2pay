<?php


function pc_shop_payment_web2pay_fields($params) {
	$params['fields'] = array(
		'web2pay' => array(
			'config::pc_shop_payment_web2pay::web2pay_project_id',
			'config::pc_shop_payment_web2pay::web2pay_signature',
			'config::pc_shop_payment_web2pay::web2pay_test'
		)
	);
}


$core->Register_hook('plugin/admin_crud/PC_shop_payment_options_admin_api/', 'pc_shop_automera_import_method');
