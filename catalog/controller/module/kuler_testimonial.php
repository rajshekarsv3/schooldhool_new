<?php
class ControllerModuleKulerTestimonial extends Controller
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

		$this->language->load('module/kuler_testimonial');
		$this->language->load('information/contact');

		if (empty($settings['testimonials_per_view']))
		{
			$settings['testimonials_per_view'] = 1;
		}

		$display_order = array();

		foreach($settings['testimonials'] as $key => $value)
		{
			$display_order[$key] = isset($value['display_order']) ? $value['display_order'] : 0;
		}

		array_multisort($display_order, SORT_ASC, $settings['testimonials']);

		$this->data['settings'] = $settings;

		$this->data['module']   = $module++;

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/kuler_testimonial.tpl'))
		{
			$this->template = $this->config->get('config_template') . '/template/module/kuler_testimonial.tpl';
		}
		else
		{
			$this->template = 'default/template/module/kuler_testimonial.tpl';
		}

		$this->render();
	}
}