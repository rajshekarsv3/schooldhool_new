<?php
class ModelTextplodeTextplode extends Model {

	public function getTemplateFromId($id){
		return $this->db->query("SELECT * FROM `" . DB_PREFIX . "textplode_templates` WHERE template_id=" . $this->db->escape($id) . " LIMIT 1");
	}

	public function getTemplateFromStatusName($name){
		$templateId = $this->db->query("SELECT * FROM `" . DB_PREFIX . "setting` WHERE `key`='textplode_template_" . $this->db->escape($name) . "'");
		if($templateId->num_rows == 0)
			return null;
		return $this->db->query("SELECT * FROM `" . DB_PREFIX . "textplode_templates` WHERE `template_id`=" . $templateId->row['value'])->row;
	}

	public function getStatusNameFromId($id){
		return strtolower($this->db->query("SELECT `name` FROM `" . DB_PREFIX . "order_status` WHERE order_status_id=" . $this->db->escape($id))->row['name']);
	}

	public function getAdminNumber(){
		return $this->db->query("SELECT * FROM `" . DB_PREFIX . "setting` WHERE `key`='textplode_admin_number'")->row['value'];
	}

	// Returns whether or not a "status hook" is active to determine whether or not to send message on change
	public function isActive($status){
		$result = $this->db->query("SELECT * FROM `" . DB_PREFIX . "setting` WHERE `key`='textplode_active_" . $status . "'");
		if(isset($result->row['value'])){
			if($result->row['value'] == "on"){
				return true;
			}
		}
		return false;
	}

	public function sendMessage($to, $message, $from = 'OPENCART'){
		$this->load->model('setting/setting');
		if($this->config->get('textplode_status') == 1){
			if($to == '' || $message == '' || $from == ''){
				$this->logError('', 'Missing or Invalid parameters');
			}

			if($this->config->get('textplode_from_name') != ''){
				$from = urlencode($this->config->get('textplode_from_name'));
			}

			$params = array(
				'action' => 'send',
				'recipients' => urlencode($to),
				'from' => $this->config->get('textplode_from_name'),
				'message' => urlencode($message),
				'apikey' => urlencode($this->config->get('textplode_apikey'))
			);

			$querystring = '';
			foreach($params as $param => $value){
				$querystring .= '&' . $param . '=' . $value;
			}
			$curl = curl_init('https://www.textplode.com/apirequest.php');
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, substr($querystring, 1));
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_TIMEOUT, 30);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			$response = json_decode(curl_exec($curl), true);
			curl_close($curl);

			if($response['return_code'] == 'FAILURE'){
				$this->logError('', $response['message']);
			}else{
				$this->logError('','Message successfully sent - Cost: ' . $response['credits_used'] . ' credits');
			}

			return $response['return_code'];
		}else{
			$this->logError('', 'Message tried to send, but extension is disabled');
		}
	}

	public function setSmsNotifications($customer_id, $enabled){
		if($enabled){
			$exists = $this->db->query("SELECT * FROM `" . DB_PREFIX . "textplode_sms_notifications` WHERE `customer_id`= " . $customer_id);
			if($exists->num_rows == 0){
				$this->db->query("INSERT INTO `" . DB_PREFIX . "textplode_sms_notifications` VALUES (" . $customer_id . ")");
			}
		}else{
			$this->db->query("DELETE FROM `" . DB_PREFIX . "textplode_sms_notifications` WHERE `customer_id`=" . $customer_id);
		}
	}

	public function getOptInOut(){
		$this->load->model('setting/setting');
		return $this->config->get('textplode_opt_in_out');
	}

	public function logError($function, $error){
		$timestamp = date('Y-m-d H:i:s') . ': ';
		$function = ($function == '') ? '' : '[' . $function . '()] ';
		file_put_contents(DIR_SYSTEM . 'logs/textplode.log', $timestamp . $function . $error . "\r\n", FILE_APPEND);
	}

}

?>