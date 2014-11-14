<?php
class ModelTextplodeTextplode extends Model {

	public function install() {

		// Create our own table for storing templates
		$query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "textplode_templates'");
		if($query->num_rows == 0){

			$this->db->query("CREATE TABLE `" . DB_PREFIX . "textplode_templates` ( `template_id` int(11) unsigned NOT NULL AUTO_INCREMENT, `template_name` varchar(255) NOT NULL DEFAULT '', `template_content` varchar(306) NOT NULL DEFAULT '', PRIMARY KEY (`template_id`) ) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;");

			$this->db->query("REPLACE INTO `" . DB_PREFIX . "textplode_templates` (`template_name`, `template_content`) VALUES ('Cancelled Order', 'Your order has been cancelled.'),('Processing', 'We are currently processing your order. We will notify you when your order has been shipped.'),('Shipped', 'Your order has been shipped and should reach you within X working days.');");

		}

		// Migrate from 0.1.2 to 0.1.3
		$query = $this->db->query("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '" . DB_PREFIX . "textplode_templates' and column_name = 'language_id'");
		if($query->num_rows == 0){
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "textplode_templates` ADD `language_id` int(11) DEFAULT 1 NOT NULL  AFTER `template_content`;");
		}

		// Create a table to store customers who wish to receive updates
		$query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "textplode_sms_notifications'");
		if($query->num_rows == 0){

			$this->db->query("CREATE TABLE `" . DB_PREFIX . "textplode_sms_notifications` ( `customer_id` int(11) unsigned NOT NULL, PRIMARY KEY (`customer_id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

		}

		$store_name = substr($this->db->query("SELECT value from `" . DB_PREFIX . "setting` WHERE `key`='config_name'")->row['value'], 0 , 11);

		// Insert default settings - This gets done when we save for the first time, but it'd be nice to have something prepopulated
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "setting` WHERE `key` = 'textplode_from_name'");
		if($query->num_rows == 0){
			$this->db->query("INSERT INTO `" . DB_PREFIX . "setting` (`store_id`, `group`, `key`, `value`, `serialized`) VALUES (0, 'textplode', 'textplode_from_name', '".$store_name."', 0), (0, 'textplode', 'textplode_status', '0', 0)");
		}

	}

	public function uninstall() {
		// Delete the tables from the database. Probably only cleanup that needs doing...
		$this->db->query("DROP TABLE `" . DB_PREFIX . "textplode_templates`");
		$this->db->query("DROP TABLE `" . DB_PREFIX . "textplode_sms_notifications`");
	}

	public function newTemplate($data){
		$this->db->query("INSERT INTO `" . DB_PREFIX . "textplode_templates` (`template_name`, `template_content`, `language_id`) VALUES ('" . $this->db->escape($data['template_name']) . "', '" . $this->db->escape($data['template_content']) . "', '" . $this->db->escape($data['language_id']) . "');");
	}

	public function editTemplate($id, $data){
		$this->db->query("UPDATE `" . DB_PREFIX . "textplode_templates` SET `template_name` = '" . $this->db->escape($data['template_name']) . "', `template_content` = '" . $this->db->escape($data['template_content']) . "', `language_id` = '" . $this->db->escape($data['language_id']) . "' WHERE template_id = " . $this->db->escape($id) . ";");
	}

	public function deleteTemplate($id){
		$this->db->query("DELETE FROM `" . DB_PREFIX . "textplode_templates` WHERE `template_id`=" . $this->db->escape($id) . ";");
	}

	public function getTemplateFromId($id){
		return $this->db->query("SELECT * FROM `" . DB_PREFIX . "textplode_templates` WHERE template_id=" . $this->db->escape($id) . " LIMIT 1");
	}

	public function getTemplateFromStatusName($name){
		$templateId = $this->db->query("SELECT * FROM `" . DB_PREFIX . "setting` WHERE `key`='textplode_template_" . str_replace(' ', '_', $name) . "_" . $this->language->get('code') . "'");
		if($templateId->num_rows == 0)
			return null;
		return $this->db->query("SELECT * FROM `" . DB_PREFIX . "textplode_templates` WHERE `template_id`=" . $templateId->row['value'])->row;
	}

	public function getTemplates(){
		return $this->db->query("SELECT * FROM `" . DB_PREFIX . "textplode_templates`");
	}

	public function getStatuses(){
		return $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_status` ORDER BY order_status_id ASC");
	}

	public function getStatusNameFromId($id){
		return strtolower($this->db->query("SELECT `name` FROM `" . DB_PREFIX . "order_status` WHERE order_status_id=" . $this->db->escape($id))->row['name']);
	}

	public function getAdminNumber(){
		return $this->db->query("SELECT * FROM `" . DB_PREFIX . "setting` WHERE `key`='textplode_admin_number'")->row['value'];
	}

	// Returns whether or not a "status hook" is active to determine whether or not to send message on change
	public function isActive($status){
		$result = $this->db->query("SELECT * FROM `" . DB_PREFIX . "setting` WHERE `key`='textplode_active_" . str_replace(' ', '_', $status) . "_" . $this->language->get('code') . "'");
		if(isset($result->row['value'])){
			if($result->row['value'] == "on"){
				return true;
			}
		}
		return false;
	}

	public function getErrorLog(){
		$this->language->load('module/textplode');
		if(file_exists(DIR_SYSTEM . '/logs/textplode.log')){
			$contents = file_get_contents(DIR_SYSTEM . 'logs/textplode.log');
			if($contents){
				return $contents;
			}else{
				return $this->language->get('error_log_empty');
			}
		}else{
			return $this->language->get('error_log_empty');
		}
	}

	public function clearErrorLog(){
		file_put_contents(DIR_SYSTEM . 'logs/textplode.log', '');
	}

	public function getCredits(){
		$key = $this->db->query("SELECT `value` FROM `" . DB_PREFIX . "setting` WHERE `key` = 'textplode_apikey'")->row;
		if(!$key || empty($key['value'])){
			return null;
		}

		$params = array(
			'action' => 'credits',
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
			$this->logError('getCredits', $response['errors']['message']);
			return null;
		}else{
			return $response['credits'] ;
		}
	}

	public function getGroups(){
		$key = $this->db->query("SELECT `value` FROM `" . DB_PREFIX . "setting` WHERE `key` = 'textplode_apikey'")->row;
		if(!$key || empty($key['value'])){
			return null;
		}

		$params = array(
			'action' => 'groups',
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
			$this->logError('getGroups', $response['errors']['message']);
			return null;
		}else{
			return $response['groups'] ;
		}
	}

	public function isValidNumber($number){
		return true;
	}

	public function sendMessage($to, $message, $from = 'TEXTPLODE'){
		if($to == '' || $message == '' || $from == ''){
			$this->logError('sendMessage', 'Missing or Invalid parameters');
		}

		$this->load->model('setting/setting');
		
/* Commented By Raj to add Bhash SMS Api
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
			if(isset($response['errors'][0]['message'])){
				$this->logError('sendMessage', $response['errors'][0]['message']);
			}
		}else{
			$this->logError('','Message successfully sent - Cost: ' . $response['credits_used'] . ' credits');
		}

		return $response['return_code'] ;
		return 'SUCCESS';*/
        $url = "http://bulksmsservice.co.in/api/sentsms.php?username=condemo&api_password=eyyyyy33mp&to=".$to."&message=".urlencode($message)."&sender=SCLCOM&priority=2";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,$url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $strPage = curl_exec($curl);
        $this->logError('',$strPage);
		
		return $strPage;
	}

	public function getEvents(){
		return $this->db->query("SELECT * FROM `" . DB_PREFIX . "setting` WHERE `key` LIKE 'textplode_active_%' or `key` LIKE 'textplode_template_%'")->rows;
	}

	public function hasApiKey(){
		$this->load->model('setting/setting');
		return ($this->config->get('textplode_apikey') != '');
	}

	public function getMobileUsers(){
		$mobile = count($this->db->query("SELECT * FROM `" . DB_PREFIX . "customer` WHERE telephone <> ''")->rows);
		$customers = count($this->db->query("SELECT * FROM `" . DB_PREFIX . "customer`")->rows);
		$enabled = count($this->db->query("SELECT * FROM `" . DB_PREFIX . "textplode_sms_notifications`")->rows);
		$percentage = ($customers > 0) ? ($mobile / $customers) * 100 : 0;
		return array('mobile' => $mobile, 'customers' => $customers, 'percentage' => $percentage, 'enabled' => $enabled);
	}

	public function getLanguages(){
		$languages = $this->db->query("SELECT * FROM `" . DB_PREFIX . "language` ORDER BY sort_order ASC")->rows;
		return $languages;
	}

	public function sync($group){
		$this->load->model('setting/setting');

		$customers = $this->db->query("SELECT firstname, lastname, telephone FROM `" . DB_PREFIX . "customer`")->rows;

		// echo ('<pre>' . print_r($customers, true) . '</pre>');

		foreach($customers as $customer => $value){
			$customers[$customer]['telephone'] = urlencode(str_replace(' ', '', trim($value['telephone'])));
			$customers[$customer]['firstname'] = urlencode(trim($value['firstname']));
			$customers[$customer]['lastname'] = urlencode(trim($value['lastname']));
		}

		$params = array(
			'action' => 'sync',
			'group' => urlencode($group),
			'contacts' => json_encode($customers),
			'apikey' => urlencode($this->config->get('textplode_apikey'))
		);

		if($group == -1){
			$params['group_name'] = $this->config->get('config_title') . ' Customers';
		}

		$querystring = '';
		foreach($params as $param => $value){
			$querystring .= '&' . $param . '=' . $value;
		}

		// echo 'https://www.textplode.com/apirequest.php?' . substr($querystring, 1);

		$curl = curl_init('https://www.textplode.com/apirequest.php');
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, substr($querystring, 1));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		// echo 'Request Sent<br/>';
		$response = json_decode(curl_exec($curl), true);
		// echo curl_exec($curl);
		// echo '<pre>'.print_r($response['contacts'], true).'</pre>';exit;

		while(curl_getinfo($curl, CURLINFO_HTTP_CODE) != 200){
			sleep(1);
		}

		if($response['return_code'] == 'FAILURE'){
			$this->logError('sync',$response['message']);
		}else{
			$this->logError('sync','Customers synced successfully');
		}
		curl_close($curl);
		return ($response['return_code'] == 'SUCCESS') ? true : $response['message'];
	}

	public function getSMS($customer_id){
		$query = $this->db->query("SELECT * FROM  `" . DB_PREFIX . "textplode_sms_notifications` WHERE `customer_id` = " . $customer_id);
		$this->load->model('setting/setting');
		if ($this->config->get('textplode_opt_in_out') == 0) return true;
		return ($query->num_rows == 1);
	}

	public function logError($function, $error){
		$timestamp = date('Y-m-d H:i:s') . ': ';
		$function = ($function == '') ? '' : '[' . $function . '()] ';
		file_put_contents(DIR_SYSTEM . 'logs/textplode.log', $timestamp . $function . $error . "\r\n", FILE_APPEND);
	}

}

?>