<?php
class ControllerModuleTextplode extends Controller {
	private $error = array();

	// For editing messaging templates
	public function edit(){
		$this->language->load('module/textplode');
		$this->load->model('textplode/textplode');

		$template = $this->model_textplode_textplode->getTemplateFromId($this->request->get['id'])->row;
		if(($this->request->server['REQUEST_METHOD'] == 'POST')){
			$this->model_textplode_textplode->editTemplate($this->request->get['id'], $this->request->post);
			$this->session->data['success'] = $this->language->get('success_template_edited');
			$this->session->data['textplode_tab'] = 'tab-templates';
			$this->redirect($this->url->link('module/textplode', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->language->load('module/textplode');

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
		);

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/textplode', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$this->document->setTitle($this->language->get('heading_title'));
		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_save_continue'] = $this->language->get('button_save_continue');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['entry_name'] = $template['template_name'];
		$this->data['entry_content'] = $template['template_content'];
		$this->data['entry_language'] = $template['language_id'];
		$this->data['template_name'] = $this->language->get('template_name');
		$this->data['template_content'] = $this->language->get('template_content');
		$this->data['action'] = $this->url->link('module/textplode/edit&id='.$this->request->get['id'], 'token=' . $this->session->data['token'], 'SSL');
		$this->data['cancel'] = $this->url->link('module/textplode', 'token=' . $this->session->data['token'], 'SSL');


		$this->load->model('textplode/textplode');
		$this->template = 'module/textplode-templates.tpl';

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	// Delete a message template
	public function delete(){
		$this->language->load('module/textplode');
		$this->load->model('textplode/textplode');
		$this->model_textplode_textplode->deleteTemplate($this->request->get['id']);
		$this->session->data['success'] = $this->language->get('success_template_deleted');
		$this->session->data['textplode_tab'] = 'tab-templates';
		$this->redirect($this->url->link('module/textplode', 'token=' . $this->session->data['token'], 'SSL'));
	}


	// Create a new message template
	public function create(){
		$this->language->load('module/textplode');
		$this->load->model('textplode/textplode');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_save_continue'] = $this->language->get('button_save_continue');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['template_name'] = $this->language->get('template_name');
		$this->data['template_content'] = $this->language->get('template_content');

		$this->data['action'] = $this->url->link('module/textplode/create', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['cancel'] = $this->url->link('module/textplode', 'token=' . $this->session->data['token'], 'SSL');

		if(($this->request->server['REQUEST_METHOD'] == 'POST')){
			$this->model_textplode_textplode->newTemplate($this->request->post);
			$this->session->data['success'] = $this->language->get('success_template_created');
			$this->session->data['textplode_tab'] = 'tab-templates';
			$this->redirect($this->url->link('module/textplode', 'token=' . $this->session->data['token'], 'SSL'));
		}

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
		);

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/textplode', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);


		$this->load->model('textplode/textplode');
		$this->template = 'module/textplode-templates.tpl';

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	// Determine whether message sent or not and display message
	public function send(){
		$this->language->load('module/textplode');
		$this->load->model('textplode/textplode');

		if(isset($this->request->get['success'])){
			if($this->request->get['success'] == 'SUCCESS'){
				$this->session->data['success'] = $this->language->get('success_message_sent');
				$this->model_textplode_textplode->logError('', 'Message successfully sent from admin interface');
			}else if($this->request->get['success'] == 'FAILURE'){
				if($this->request->get['error_code'] == 8){
					$this->session->data['error'] = 'Message could not be sent from admin interface due to insufficient credits';
					$this->model_textplode_textplode->logError('', 'Message could not be sent from admin interface due to insufficient credits');
				}else{
					$this->session->data['error'] = $this->language->get('error_message_not_sent');
					$this->model_textplode_textplode->logError('', 'Error ' . $this->request->get['error_code'] . ': Message could not be sent from admin interface due to an error');
				}
			}
		}

		$this->session->data['textplode_tab'] = 'tab-sendmessage';

		if(isset($this->request->get['number'])){
			$this->session->data['textplode_customer_number'] = $this->request->get['number'];
		}

		$this->redirect($this->url->link('module/textplode', 'token=' . $this->session->data['token'], 'SSL'));
	}

	// Default action to take. Show Textplode extension settings
	public function index() {
		$this->checkNeedInstall();
		$this->language->load('module/textplode');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');
		$this->load->model('textplode/textplode');
		// If we are dealing with a form submission
		if(($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()){
			$this->model_setting_setting->editSetting('textplode', $this->request->post);
			$this->cache->delete('product');
			$this->session->data['success'] = $this->language->get('text_success');
			// Allows us to use a Save & Continue button rather than returning to modules
			if($this->request->post['continue'] == '1'){
				$this->session->data['textplode_tab'] = $this->request->post['current_tab'];
				$this->redirect($this->url->link('module/textplode', 'token=' . $this->session->data['token'], 'SSL'));
			}else{
				$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
			}
		}

		// Languages
		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['heading_user'] = $this->language->get('heading_user');
		$this->data['heading_admin'] = $this->language->get('heading_admin');
		$this->data['heading_general'] = $this->language->get('heading_general');
		$this->data['heading_account'] = $this->language->get('heading_account');
		$this->data['heading_statistics'] = $this->language->get('heading_statistics');
		$this->data['table_heading_status'] = $this->language->get('table_heading_status');
		$this->data['table_heading_template'] = $this->language->get('table_heading_template');
		$this->data['table_heading_active'] = $this->language->get('table_heading_active');
		$this->data['tab_general'] = $this->language->get('tab_general');
		$this->data['tab_account'] = $this->language->get('tab_account');
		$this->data['tab_templates'] = $this->language->get('tab_templates');
		$this->data['tab_triggers'] = $this->language->get('tab_triggers');
		$this->data['tab_sendmessage'] = $this->language->get('tab_sendmessage');
		$this->data['tab_logs'] = $this->language->get('tab_logs');
		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_status'] = $this->language->get('text_status');
		$this->data['text_credits'] = $this->language->get('text_credits');
		$this->data['text_template_name'] = $this->language->get('text_template_name');
		$this->data['text_template_content'] = $this->language->get('text_template_content');
		$this->data['text_apikey'] = $this->language->get('text_apikey');
		$this->data['text_pending'] = $this->language->get('text_pending');
		$this->data['text_processing'] = $this->language->get('text_processing');
		$this->data['text_shipped'] = $this->language->get('text_shipped');
		$this->data['text_complete'] = $this->language->get('text_complete');
		$this->data['text_canceled'] = $this->language->get('text_canceled');
		$this->data['text_denied'] = $this->language->get('text_denied');
		$this->data['text_canceled_reversal'] = $this->language->get('text_canceled_reversal');
		$this->data['text_failed'] = $this->language->get('text_failed');
		$this->data['text_refunded'] = $this->language->get('text_refunded');
		$this->data['text_reversed'] = $this->language->get('text_reversed');
		$this->data['text_chargeback'] = $this->language->get('text_chargeback');
		$this->data['text_expired'] = $this->language->get('text_expired');
		$this->data['text_processed'] = $this->language->get('text_processed');
		$this->data['text_voided'] = $this->language->get('text_voided');
		$this->data['text_recipient'] = $this->language->get('text_recipient');
		$this->data['text_message'] = $this->language->get('text_message');
		$this->data['text_admin_number'] = $this->language->get('text_admin_number');
		$this->data['text_from_name'] = $this->language->get('text_from_name');
		$this->data['text_from_name_limit'] = $this->language->get('text_from_name_limit');
		$this->data['text_sync_group'] = $this->language->get('text_sync_group');
		$this->data['text_new_order'] = $this->language->get('text_new_order');
		$this->data['text_new_customer'] = $this->language->get('text_new_customer');
		$this->data['text_send'] = $this->language->get('text_send');
		$this->data['text_clear'] = $this->language->get('text_clear');
		$this->data['text_characters'] = $this->language->get('text_characters');
		$this->data['text_enabled_users'] = $this->language->get('text_enabled_users');
		$this->data['text_mobile_users'] = $this->language->get('text_mobile_users');
		$this->data['text_mobile_users_format'] = $this->language->get('text_mobile_users_format');
		$this->data['text_opt_in_out'] = $this->language->get('text_opt_in_out');
		$this->data['text_opt_in'] = $this->language->get('text_opt_in');
		$this->data['text_opt_out'] = $this->language->get('text_opt_out');
		$this->data['text_opt_disabled'] = $this->language->get('text_opt_disabled');
		$this->data['text_select'] = $this->language->get('text_select');
		$this->data['text_sync_first'] = $this->language->get('text_sync_first');
		$this->data['text_sync_now'] = $this->language->get('text_sync_now');
		$this->data['link_buy_more'] = $this->language->get('link_buy_more');
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_save_continue'] = $this->language->get('button_save_continue');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['text_phone'] = $this->language->get('text_phone');
		$this->data['text_single'] = $this->language->get('text_single');

		// Values returned from the model to be used in the view
		$this->data['entry_credits'] = $this->model_textplode_textplode->getCredits();
		$this->data['array_templates'] = $this->model_textplode_textplode->getTemplates()->rows;
		$this->data['array_statuses'] = $this->model_textplode_textplode->getStatuses()->rows;
		$this->data['array_languages'] = $this->model_textplode_textplode->getLanguages();
		$this->data['array_groups'] = $this->model_textplode_textplode->getGroups();
		$this->data['array_mobile_customers'] = $this->model_textplode_textplode->getMobileUsers();

		$this->data['error_log'] = $this->model_textplode_textplode->getErrorLog();

		$this->data['store_name'] = $this->config->get('config_title');
		$this->data['sync_group'] = $this->config->get('textplode_sync_group');
		$this->data['hasApiKey'] = $this->model_textplode_textplode->hasApiKey();

		// Display errors if we have any
		if (isset($this->session->data['error'])) {
			$this->data['error_warning'] = $this->session->data['error'];
			unset($this->session->data['error']);
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		if (isset($this->error['image'])) {
			$this->data['error_image'] = $this->error['image'];
		} else {
			$this->data['error_image'] = array();
		}

		// Breadcrumbs
		$this->data['breadcrumbs'] = array();

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
		);

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/textplode', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		// Form actions
		$this->data['action'] = $this->url->link('module/textplode', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');


		// Assign all view variables the correct values from the model or registry
		if (isset($this->request->post['textplode_status'])) {
			$this->data['textplode_status'] = $this->request->post['textplode_status'];
		} else {
			$this->data['textplode_status'] = $this->config->get('textplode_status');
		}

		if (isset($this->request->post['textplode_apikey'])) {
			$this->data['textplode_apikey'] = $this->request->post['textplode_apikey'];
		} else {
			$this->data['textplode_apikey'] = $this->config->get('textplode_apikey');
		}

		$events = $this->model_textplode_textplode->getEvents();

		foreach($events as $event){
			if (isset($this->request->post[$event['key']])) {
				$this->data['active_array'][$event['key']] = $this->request->post[$event['key']];
			} else {
				$this->data['active_array'][$event['key']] = $event['value'];
			}
		}

		if (isset($this->request->post['textplode_from_name'])) {
			$this->data['textplode_from_name'] = $this->request->post['textplode_from_name'];
		} else {
			$this->data['textplode_from_name'] = $this->config->get('textplode_from_name');
		}

		if (isset($this->request->post['textplode_admin_number'])) {
			$this->data['textplode_admin_number'] = $this->request->post['textplode_admin_number'];
		} else {
			$this->data['textplode_admin_number'] = $this->config->get('textplode_admin_number');
		}

		if (isset($this->request->post['textplode_sync_group'])) {
			$this->data['textplode_sync_group'] = $this->request->post['textplode_sync_group'];
		} else {
			$this->data['textplode_sync_group'] = $this->config->get('textplode_sync_group');
		}

		if (isset($this->request->post['textplode_opt_in_out'])) {
			$this->data['textplode_opt_in_out'] = $this->request->post['textplode_opt_in_out'];
		} else {
			$this->data['textplode_opt_in_out'] = $this->config->get('textplode_opt_in_out');
		}

		if(isset($this->session->data['textplode_tab'])){
			$this->data['textplode_tab'] = $this->session->data['textplode_tab'];
			unset($this->session->data['textplode_tab']);
		}else{
			$this->data['textplode_tab'] = '';
		}

		if(isset($this->session->data['textplode_customer_number'])){
			$this->data['textplode_customer_number'] = $this->session->data['textplode_customer_number'];
			unset($this->session->data['textplode_customer_number']);
		}else{
			$this->data['textplode_customer_number'] = '';
		}

		// Load template (view) and show it
		$this->template = 'module/textplode.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	public function sync(){
		$this->language->load('module/textplode');
		$this->load->model('textplode/textplode');
		$this->load->model('setting/setting');
		$this->config->set('textplode_sync_group', $this->request->get['group']);
		$ret = $this->model_textplode_textplode->sync($this->request->get['group']);
		if($ret === true){
			$this->session->data['success'] = $this->language->get('success_sync');
		}else{
			$this->session->data['error'] = $ret;
		}
		//sleep(5);
		$this->redirect($this->url->link('module/textplode', 'token=' . $this->session->data['token'], 'SSL'));
	}

	public function clearerrors(){
		$this->load->model('textplode/textplode');
		$this->model_textplode_textplode->clearErrorLog();
		$this->redirect($this->url->link('module/textplode', 'token=' . $this->session->data['token'], 'SSL'));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'module/textplode')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (isset($this->request->post['textplode_module'])) {
			foreach ($this->request->post['textplode_module'] as $key => $value) {
				if (!$value['image_width'] || !$value['image_height']) {
					$this->error['image'][$key] = $this->language->get('error_image');
				}
			}
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	public function checkNeedInstall(){
		$this->load->model('textplode/textplode');
		$this->model_textplode_textplode->install();
	}

	public function install() {
		$this->checkNeedInstall();
		$this->redirect($this->url->link('module/textplode', 'token=' . $this->session->data['token'], 'SSL'));
	}

	public function uninstall() {
		$this->load->model('textplode/textplode');
		$this->model_textplode_textplode->uninstall();
	}
}
?>