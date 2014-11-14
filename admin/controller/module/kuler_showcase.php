<?php
class ControllerModuleKulerShowcase extends Controller
{
	/* @var ModelKulerCommon $common */
	protected $common;

	public function __construct($registry)
	{
		parent::__construct($registry);

		$this->load->model('kuler/common');
		$this->common = $this->model_kuler_common;

		ModelKulerCommon::loadTexts($this->language->load('module/kuler_showcase'));
	}

	public function index()
	{
		$this->load->model('setting/setting');

		$this->data['store_id']             = isset($this->request->get['store_id']) ? $this->request->get['store_id']: 0;
		$this->data['token']                = $this->session->data['token'];
		$this->data['extension_code']       = 'kuler_showcase';
		$this->data['default_module']       = $this->getDefaultModule();
		$this->data['config_language']      = $this->config->get('config_language');

		$this->data['stores']               = $this->common->getStores();
		$this->data['languages']            = $this->common->getLanguages();
		$this->data['layouts']              = $this->common->getLayoutOptions();
		$this->data['positions']            = $this->common->getPositions();

		$this->data['front_base'] = $this->common->getFrontBase();

		$this->data['category_options'] = $this->common->getCategoryOptions(array(
			'all' => true,
			'store_id' => $this->data['store_id']
		));

		$data = $this->model_setting_setting->getSetting('kuler_showcase', $this->data['store_id']);

		if (!is_array($data))
		{
			$data = array();
		}

		$this->data['modules']      = isset($data['kuler_showcase_module']) ? $data['kuler_showcase_module'] : array();

		// Prepare modules
		foreach ($this->data['modules'] as &$module)
		{
			if (!empty($module['showcases']))
			{
				foreach ($module['showcases'] as &$showcase)
				{
					if (!empty($showcase['items']))
					{
						foreach ($showcase['items'] as &$item)
						{
							if ($item['type'] == 'html')
							{
								$item['html_content'] = $this->common->decodeMultilingualText($item['html_content']);
							}
						}
					}
				}
			}
		}

		$this->data['messages']     = ModelKulerCommon::getTexts();

		$this->data['action_url']   = $this->common->createLink('module/kuler_showcase/save');
		$this->data['cancel_url']   = $this->common->createLink('extension/module');
		$this->data['store_url']    = $this->common->createLink('module/kuler_showcase');

		// Breadcrumbs
		$this->data['breadcrumbs'] = array();

		$this->data['breadcrumbs'][] = array(
			'text'      => _t('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
		);

		$this->data['breadcrumbs'][] = array(
			'text'      => _t('text_modules'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$this->data['breadcrumbs'][] = array(
			'text'      => _t('heading_module'),
			'href'      => $this->url->link('module/kuler_showcase', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

        $this->document->setTitle(_t('heading_module'));

		$this->common->insertCommonResources();

		$this->document->addStyle('view/kuler/css/showcase.css');
		$this->document->addScript($this->common->createLink('module/kuler_showcase/jsMessages'));
		$this->document->addScript('view/kuler/js/showcase.js');

		$this->template = 'module/kuler_showcase.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	public function save()
	{
		try
		{
			if ($this->request->server['REQUEST_METHOD'] != 'POST')
			{
				throw new Exception(_t('error_permission'));
			}

			$this->validate();

			$data = array(
				'kuler_showcase_module' => isset($this->request->post['modules']) ? $this->request->post['modules']: array()
			);

			$this->load->model('setting/setting');

			$this->model_setting_setting->editSetting('kuler_showcase', $data, $this->request->post['store_id']);

			$result = array(
				'status' => 1,
				'message' => _t('text_success')
			);
		}
		catch (Exception $e)
		{
			$result = array(
				'status' => 0,
				'message' => $e->getMessage()
			);
		}

		$this->response->setOutput(json_encode($result));
	}

	public function jsMessages()
	{
		$js = 'var _tMessages = ' . json_encode(ModelKulerCommon::getTexts());

		$this->response->setOutput($js);
	}

	protected function getDefaultModule()
	{
		return array(
			'layout_id'               => '1',
			'position'                => 'content_top',
			'sort_order'              => '3',
			'show_title'              => '1',
			'status'                  => '1'
		);
	}

	protected function getDefaultShowcase() {
		return array(
			'display_order' => 1
		);
	}

	protected function validate()
	{
		if (!$this->user->hasPermission('modify', 'module/kuler_showcase'))
		{
			throw new Exception(_t('error_permission'));
		}
	}
}