<?php
class ControllerModuleKulerContactForm extends Controller
{
	protected $common;

	public function __construct($registry)
	{
		parent::__construct($registry);

		$this->load->model('kuler/common');
		$this->common = $this->model_kuler_common;
	}

	public function index($settings)
	{
		if (!$this->common->isKulerTheme($this->config->get('config_template')))
		{
			return false;
		}

		static $module = 0;

		$this->language->load('module/kuler_contact_form');
		$this->language->load('information/contact');

		$this->data['entry_name'] = trim($this->language->get('entry_name'), ':');
		$this->data['entry_email'] = trim($this->language->get('entry_email'), ':');
		$this->data['entry_enquiry'] = trim($this->language->get('entry_enquiry'), ':');
		$this->data['button_send_message'] = $this->language->get('button_send_message');

		$this->data['settings'] = $settings;

		$this->data['module']   = $module++;
		$this->data['action_url'] = $this->url->link('module/kuler_contact_form/send');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/kuler_contact_form.tpl'))
		{
			$this->template = $this->config->get('config_template') . '/template/module/kuler_contact_form.tpl';
		}
		else
		{
			$this->template = 'default/template/module/kuler_contact_form.tpl';
		}

		$this->render();
	}

	public function send()
	{
		$this->language->load('information/contact');

		$errors = array();

		try
		{
			if ($this->request->server['REQUEST_METHOD'] != 'POST')
			{
				throw new Exception('Error');
			}

			if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 32))
			{
				$errors['name'] = $this->language->get('error_name');
			}

			if (!preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['email']))
			{
				$errors['email'] = $this->language->get('error_email');
			}

			if ((utf8_strlen($this->request->post['enquiry']) < 10) || (utf8_strlen($this->request->post['enquiry']) > 3000))
			{
				$errors['enquiry'] = $this->language->get('error_enquiry');
			}

			if (!empty($errors))
			{
				throw new Exception('field');
			}

			// Send
			$mail = new Mail();
			$mail->protocol = $this->config->get('config_mail_protocol');
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->hostname = $this->config->get('config_smtp_host');
			$mail->username = $this->config->get('config_smtp_username');
			$mail->password = $this->config->get('config_smtp_password');
			$mail->port = $this->config->get('config_smtp_port');
			$mail->timeout = $this->config->get('config_smtp_timeout');
			$mail->setTo($this->config->get('config_email'));
			$mail->setFrom($this->request->post['email']);
			$mail->setSender($this->request->post['name']);
			$mail->setSubject(html_entity_decode(sprintf($this->language->get('email_subject'), $this->request->post['name']), ENT_QUOTES, 'UTF-8'));
			$mail->setText(strip_tags(html_entity_decode($this->request->post['enquiry'], ENT_QUOTES, 'UTF-8')));
			$mail->send();

			$results = array(
				'status' => 1,
				'message' => $this->language->get('text_message')
			);
		}
		catch (Exception $e)
		{
			$results = array(
				'status' => 0,
				'message' => ''
			);

			if ($e->getMessage() == 'field')
			{
				$results['fields'] = $errors;
			}
		}

		$this->response->setOutput(json_encode($results));
	}
}