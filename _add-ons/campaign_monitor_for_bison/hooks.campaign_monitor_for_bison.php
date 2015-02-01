<?php

class Hooks_campaign_monitor_for_bison extends Hooks 
{
	private $order_details;

	/**
	 * Run when checkout is completed
	 *
	 * @return string
	 **/
	function bison__checkout_complete($order_details)
	{
		if (!function_exists('curl_init')) {
			$this->log->error('cURL is not installed.');
			return;
		}
		// $this->order_details = $order_details;

		$api_key = array_get($this->config, 'api_key');
		$list_id = array_get($this->config, 'list_id');

		$trigger_field = $this->config['trigger_field'];
		$trigger_value = $this->config['trigger_value'];

		if (array_get($order_details, $trigger_field) == $trigger_value) {

      $full_name = order_details['first_name'] . " " . order_details['last_name'];

			$data = json_encode(array(
				'EmailAddress' => order_details['email'],
				'Name'         => $full_name
			));

			$url = 'https://api.createsend.com/api/v3.1/subscribers/' . $list_id . '.json';
			$this->performRequest($url, $data, $api_key);

		}
	}

	private function performRequest($url, $data = null, $auth = null)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		if ($auth)
			curl_setopt($ch, CURLOPT_USERPWD, $auth);
		if ($data)
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
	}
}