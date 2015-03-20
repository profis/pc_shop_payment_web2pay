<?php

class PC_shop_web2pay_payment_method extends PC_shop_payment_method {
	
	protected function _load_lib() {
		include_once 'web2pay/WebToPay.php';
	}
	
	public function make_online_payment() {
		$this->_load_lib();
		//list($first_name, $last_name) = explode(' ', )
		
		$langs = array(
			'lt' => 'LIT', 
			'lv' => 'LAV',
			'ee' => 'EST',
			'ru' => 'RUS',
			'en' => 'ENG',
			'de' => 'GER',
			'pl' => 'POL'
		);
		$payment_params = array(
			'projectid'     => $this->_payment_data['login'],
			'sign_password' => $this->_payment_data['payment_key'],

			'orderid'       => $this->_order_data['id'],
			'amount'        => ($this->_order_data['total_price'] * 100),
			'currency'      => $this->_order_data['currency'],
			//'lang'          => ($languageCode == 'LT') ? 'LTU' : 'ENG',

			'accepturl'     => $this->_get_accept_url(),
			'cancelurl'     => $this->_get_cancel_url(),
			'callbackurl'   => $this->_get_callback_url(),
			//'payment'       => (isset($_POST['payment'])) ? $_POST['payment'] : '',

			//'p_firstname'   => $customer->firstname,
			//'p_lastname'    => $customer->lastname,
			'p_email'       => $this->_order_data['email'],
			//'p_street'      => $address->address1,
			//'p_city'        => $address->city,
			//'p_zip'         => $address->postcode,
			//'p_countrycode' => $country->iso_code,
			//'test'          => false,
		);
		if (isset($langs[$this->site->ln])) {
			$payment_params['lang'] = $langs[$this->site->ln];
		}
		if ($this->_payment_data['test']) {
			$payment_params['test'] = 1;
		}
		try {
			$request = WebToPay::redirectToPayment($payment_params, true);
		} catch (WebToPayException $e) {
			echo get_class($e).': '.$e->getMessage();
		}
	}
	
	public function callback() {
		$this->_load_lib();
		$success = false;
		try {
			$response = WebToPay::checkResponse($_REQUEST, array(
				'projectid'     => $this->_payment_data['login'],
				'sign_password' => $this->_payment_data['payment_key']
			));
			$this->_response = $response;
			if ($response['type'] !== 'macro') {
				throw new Exception('Only macro payment callbacks are accepted');
			}
			
			$payment_succesful = $this->_is_payment_successful();
			if ($payment_succesful) {
				$success = true;
				echo 'OK';
			}
		} catch (Exception $e) {
			//echo get_class($e) . ': ' . $e->getMessage();
			echo $e->getMessage();
		}
		return $success;
	}
	
	public function accept() {
		$this->_load_lib();
		$payment_succesful = false;
		try {
			$response = WebToPay::checkResponse($_REQUEST, array(
				'projectid'     => $this->_payment_data['login'],
				'sign_password' => $this->_payment_data['payment_key']
			));
			
			$this->_response = $response;
			
			if ($response['type'] !== 'macro') {
				throw new Exception('Only macro payment callbacks are accepted');
			}
			
			if ($response['status'] == 1) {
				$payment_succesful = $this->_is_payment_successful();
			}
			elseif($response['status'] == 2) {
				return self::STATUS_WAITING;
			}
			elseif($response['status'] == 0) {
				return self::STATUS_FAILED;
			}
			
		} catch (Exception $e) {
			$message = $e->getMessage();
			if ($message == self::_STATUS_IS_PAID) {
				return self::STATUS_ALREADY_PURCHASED;
			}
			else {
				$this->_error = get_class($e) . ': ' . $message;
				return self::STATUS_ERROR;
			}
		}
		if ($payment_succesful) {
			return self::STATUS_SUCCESS;
		}
		return $payment_succesful;
	}
	
	protected function _get_response_payment_status() {
		return $this->_response['status'] == 1;
	}
	
	protected function _get_response_order_id() {
		return $this->_response['orderid'];
	}
	
	protected function _get_response_test() {
		return $this->_response['test'] !== '0';
	}
	
	protected function _get_response_amount() {
		return $this->_response['amount'];
	}
	
	protected function _get_response_currency() {
		return $this->_response['currency'];
	}
	
}
