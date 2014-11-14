<?php
class ControllerModuleKulerInstagramStream extends Controller
{
	public function index($settings)
	{
		static $module = 0;

		$this->language->load('module/kuler_instagram_stream');
		$this->language->load('information/contact');

		$this->data['settings'] = $settings;

		$this->data['module']   = $module++;

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/kuler_instagram_stream.tpl'))
		{
			$this->template = $this->config->get('config_template') . '/template/module/kuler_instagram_stream.tpl';
		}
		else
		{
			$this->template = 'default/template/module/kuler_instagram_stream.tpl';
		}

		$this->render();
	}
}