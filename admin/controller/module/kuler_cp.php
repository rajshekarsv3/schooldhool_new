<?php

/**
 * Class ControllerModuleKulerCp
 * @property Language $language
 * @property Document $document
 * @property Response $response
 */
class ControllerModuleKulerCp extends Controller
{
	/* @var ModelKulerCommon $common */
	protected $common;

	/**
	 * @var ModelKulerCp $model
	 */
	protected $model;

	public function __construct($registry)
	{
		parent::__construct($registry);

		$this->load->model('kuler/common');
		$this->common = $this->model_kuler_common;

		ModelKulerCommon::loadTexts($this->language->load('module/kuler_cp'));

		$this->load->model('kuler/cp');
		$this->model = $this->model_kuler_cp;
	}

	public function install()
	{
		$this->model->createTables();
	}

	public function uninstall()
	{
		$this->model->dropTables();
	}

	public function index()
	{
		$this->common->insertCommonResources();

		// Actions
		$this->data['module_url']               = $this->common->createLink('module/kuler_cp');
		$this->data['support_url']              = 'http://support.kulerthemes.com'; // TODO: Take a look at this
		$this->data['store_url']                = $this->common->createLink('module/kuler_cp/store');
		$this->data['skin_url']                 = $this->common->createLink('module/kuler_cp/skin');
		$this->data['save_url']                 = $this->common->createLink('module/kuler_cp/save');
		$this->data['save_skin_as_url']         = $this->common->createLink('module/kuler_cp/saveSkinAs');
		$this->data['remove_skin_url']          = $this->common->createLink('module/kuler_cp/removeSkin');
		$this->data['export_skins_url']         = $this->common->createLink('module/kuler_cp/exportSkins');
		$this->data['import_skins_url']         = $this->common->createLink('module/kuler_cp/importSkins');
		$this->data['style_panel_url']          = $this->common->createLink('module/kuler_cp/stylePanel');
		$this->data['newsletter_lists_url']     = $this->common->createLink('module/kuler_cp/newsletterLists');
		$this->data['module_url']               = $this->common->createLink('extension/module');

		$this->data['selected_store_id']                 = isset($this->request->get['store_id']) ? $this->request->get['store_id'] : 0;

		$this->data['fonts'] = $this->model->getFonts();

		$this->data['front_base'] = $this->common->getFrontBase();

		$this->data['stores'] = $this->common->getStores();
		$this->data['languages'] = $this->common->getLanguages();
		$this->data['default_language'] = $this->config->get('config_language_id');

		$this->data['token'] = $this->session->data['token'];

		$this->document->setTitle(_t('heading_module'));

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
			'href'      => $this->url->link('module/kuler_cp', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$this->document->addStyle('view/kuler/css/cp.css');
		$this->document->addScript($this->common->createLink('module/kuler_cp/jsMessages'));
		$this->document->addScript('view/kuler/js/cp.js');

		$this->template = 'module/kuler_cp/index.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	public function stylePanel()
	{
		$store_id = $this->request->get['store_id'];
		$skin_id = $this->request->get['skin_id'];

		$theme_id = $this->model->getThemeId($store_id);

		$theme_options = $this->model->loadThemeOptions($theme_id);

		$this->data['group_styles']     = $theme_options['styles'];
		$this->data['style_template']   = $this->model->loadThemeStyleTemplate($theme_id);

		$this->template = 'module/kuler_cp/style_panel.tpl';
		$this->response->setOutput($this->render());
	}

	/**
	 * Return current store info
	 * @param int $store_id Store Id of current store
	 * @return array
	 */
	public function store()
	{
		if ($this->common->isDevelopment())
		{
			sleep(1);
		}

		// Get store id from GET
		$store_id = $this->request->get['store_id'];

		// Get current theme from setting by this store
		$theme_id = $this->model->getThemeId($store_id);

		try
		{
			$config_theme_id = $this->model->getConfigTheme($store_id);

			// No Theme installed, let's install it
			if (!$theme_id)
			{
				$this->model->installTheme($store_id, $config_theme_id);
				$theme_id = $config_theme_id;
			}

			// Other theme is set
			if ($theme_id != $config_theme_id)
			{
				$this->model->installTheme($store_id, $config_theme_id);
				$theme_id = $config_theme_id;
			}

			// Load theme option and check theme version
			$theme_options = $this->model->loadThemeOptions($theme_id);
			$theme_version = $this->model->getThemeVersion($store_id);

			if ($theme_options['version'] != $theme_version)
			{
				$this->model->upgradeTheme($theme_id);
				$theme_updated = true;
			}

			// Get skins by the current theme
			$skin_rows = $this->model->getSkinsByThemeId($theme_id);

			// Prepare skins
			$skins = array();
			foreach ($skin_rows as $skin_row)
			{
				$can_remove = $skin_row['skin_id'] != $skin_row['root_skin_id'];

				$skins[$skin_row['skin_id']] = array(
					'name'          => $skin_row['name'],
					'can_remove'    => $can_remove,
					'group'         => $can_remove ? _t('text_custom_skins', 'Custom Skins') : _t('text_default_skins', 'Default Skins')
				);
			}

			$skin_id = $this->model->getSkinId($store_id);

			// Get skin options by theme id and skin id
			$skins_options = $this->model->getSkinOptions($theme_id, $skin_id);

			// Fix OPM-188
			if (!empty($skins_options['custom_block_content']) && is_string($skins_options['custom_block_content'])) {
				$contents = array();

				foreach ($this->common->getLanguages() as $language) {
					$contents[$language['code']] = $skins_options['custom_block_content'];
				}

				$skins_options['custom_block_content'] = $contents;
			}

			$store_url = $this->common->getStoreBase($store_id);

			// Patterns
			$pattern_files = $this->model->loadThemePatterns($theme_id);
			$patterns = array();
			foreach ($pattern_files as $pattern_file)
			{
				$patterns[] = array(
					'image' => str_replace(array('https:', 'http:'), '', $store_url . $pattern_file),
					'path' => $pattern_file
				);
			}

			$result = array(
				'theme_name'            => $theme_options['name'],
				'theme_id'              => $theme_id,
				'theme_version'         => version_compare($theme_options['version'], $theme_version) ? $theme_options['version'] : $theme_version,
				'theme_updated'         => !empty($theme_updated),
				'theme_updated_message' => _t('text_success_theme_updated', sprintf('You have updated %s to version %s.', $theme_options['name'], $theme_options['version'])),
				'documentation_url'     => "http://docs.kulerthemes.com/{$theme_options['id']}",
				'demo_url'              => "http://demo.kulerthemes.com/{$theme_options['id']}",
				'preview_url'           => $store_url,
				'skins'                 => $skins,
				'style_template'        => $this->model->loadThemeStyleTemplate($theme_id),
				'source_patterns'       => $patterns,
				'excluded_options'      => $theme_options['excluded_options'],
				'store'                 => array(
					'skin_id' => $skin_id
				),
				'options'               => $skins_options
			);
		}
		catch (Exception $e)
		{
			$this->response->addHeader('HTTP/1.1 500 Error');

			$result = array(
				'error' => $e->getMessage()
			);
		}

		$this->response->setOutput(json_encode($result));
	}

	/**
	 * Return skin options
	 * @param int $store_id Store Id
	 * @param string $skin_id Skin Id of current skin
	 * @return array
	 */
	public function skin()
	{
		if ($this->common->isDevelopment())
		{
			sleep(1);
		}

		// Get store id and skin id from GET
		$store_id = $this->request->get['store_id'];
		$skin_id = $this->request->get['skin_id'];

		try
		{
			// Get theme id from setting
			$theme_id = $this->model->getThemeId($store_id);

			// Get skin options by theme id and skin id
			$skin_options = $this->model->getSkinOptions($theme_id, $skin_id);

			$result = array(
				'options' => $skin_options
			);
		}
		catch (Exception $e)
		{
			$this->response->addHeader('HTTP/1.1 500 Error');

			$result = array(
				'error' => $e->getMessage(),
				'code' => $e->getCode()
			);
		}

		$this->response->setOutput(json_encode($result));
	}

	/**
	 * Remove skin
	 * @param int $store_id Store id
	 * @param string $skin_id Skin Id of current skin
	 * @return array
	 */
	public function removeSkin()
	{
		if ($this->common->isDevelopment())
		{
			sleep(1);
		}

		try
		{
			$this->assertPostAndPermission();

			// Get store id and skin id from GET
			$store_id = $this->request->post['store_id'];
			$skin_id = $this->request->post['skin_id'];

			// Get theme id from setting by store id
			$theme_id = $this->model->getThemeId($store_id);

			// Remove skin by theme id and skin id
			$this->model->removeSkin($theme_id, $skin_id);

			// Select default skin for this store
			$theme_options = $this->model->loadThemeOptions($theme_id);
			$this->model->setSkinForStore($store_id, $theme_id, $theme_options['default_skin_id']);

			$result = array(
				'status' => 1,
				'message'   => _t('text_success_save', 'Success: You have modified module Kuler CP!')
			);

			$this->response->setOutput(json_encode($result));
		}
		catch (Exception $e)
		{
			$this->response->addHeader('HTTP/1.1 500 Error');

			$result = array(
				'error' => $e->getMessage()
			);
		}

		$this->response->setOutput(json_encode($result));
	}

	/**
	 * Save skin and options
	 * @param int $store_id Store Id
	 * @param string $skin_id Skin Id
	 * @param string $name name of new skin
	 * @param array $options options of skin
	 * @return array
	 */
	public function saveSkinAs()
	{
		if ($this->common->isDevelopment())
		{
			sleep(1);
		}

		try
		{
			$this->assertPostAndPermission();

			// Get inputs from GET
			$store_id = $this->request->post['store_id'];
			$skin_id = $this->request->post['skin_id'];
			$new_skin_name = $this->request->post['new_skin_name'];
			$skin_options = $this->request->post['options'];
			$skin_css = $this->request->post['css'];

			// Get theme id from config
			$theme_id = $this->model->getThemeId($store_id);

			// Normalize skin id from skin name
			$new_skin_id = $this->model->normalizeSkinId($new_skin_name);

			// TODO: Check for name conflict

			// Get source skin
			$source_skin = $this->model->getSkin($theme_id, $skin_id);

			// Save skin info
			$new_skin = array(
				'skin_id'       => $new_skin_id,
				'name'          => $new_skin_name,
				'theme_id'      => $theme_id,
				'root_skin_id'  => $source_skin['root_skin_id']
			);

			$this->model->insertSkin($new_skin);

			$this->model->setSkinForStore($store_id, $theme_id, $new_skin_id);

			// Save skin options into db
			$this->model->updateSkinOptions($theme_id, $new_skin_id, $skin_options);

			// Process skin CSS
			$skin_css = html_entity_decode($skin_css, ENT_COMPAT, "UTF-8");
			$skin_css = $this->model->convertRelative2AbsoluteUrlCss($skin_css);
			$this->model->saveSkinCss($theme_id, $new_skin_id, $skin_css);

			$result = array(
				'status'    => 1,
				'message'   => _t('text_success_save', 'Success: You have modified module Kuler CP!')
			);
		}
		catch (Exception $e)
		{
			$this->response->addHeader('HTTP/1.1 500 Error');

			$result = array(
				'error' => $e->getMessage()
			);
		}

		$this->response->setOutput(json_encode($result));
	}

	/**
	 * Save current skin
	 * @param int $store_id Store Id
	 * @param string $skin_id Skin Id
	 * @param array $options of skin
	 */
	public function save()
	{
		if ($this->common->isDevelopment())
		{
			sleep(1);
		}

		try
		{
			$this->assertPostAndPermission();

			// Get inputs from POST
			$store_id = $this->request->post['store_id'];
			$skin_id = $this->request->post['skin_id'];
			$skin_options = $this->request->post['options'];
			$skin_css = $this->request->post['css'];

			// Save skin option into db
			$theme_id = $this->model->getThemeId($store_id);

			$this->model->setSkinForStore($store_id, $theme_id, $skin_id);
			$this->model->updateSkinOptions($theme_id, $skin_id, $skin_options);

			// Process skin CSS
			$skin_css = html_entity_decode($skin_css, ENT_COMPAT, "UTF-8");
			$skin_css = $this->model->convertRelative2AbsoluteUrlCss($skin_css);
			$this->model->saveSkinCss($theme_id, $skin_id, $skin_css);

			$result = array(
				'status' => 1,
				'message' => _t('text_success_save', 'Success: You have modified module Kuler CP!')
			);
		}
		catch (Exception $e)
		{
			$this->response->addHeader('HTTP/1.1 500 Error');

			$result = array(
				'error' => $e->getMessage()
			);
		}

		$this->response->setOutput(json_encode($result));
	}

	/**
	 * Export skins to JSON file
	 */
	public function exportSkins()
	{
		try
		{
			$this->assertPermission();

			// Get all skins form db
			$skins = $this->model->getAllSkins();

			// Get all options from db
			$skin_options = $this->model->getAllSkinOptions();

			// Group options by skin
			foreach ($skins as $key => &$skin)
			{
				if (isset($skin_options[$key]))
				{
					$skin['options'] = $skin_options[$key];
				}
			}

			$this->response->addheader('Pragma: public');
			$this->response->addheader('Expires: 0');
			$this->response->addheader('Content-Description: File Transfer');
			$this->response->addheader('Content-Type: application/octet-stream');
			$this->response->addheader('Content-Disposition: attachment; filename=kuler_cp_backup_' . date('Y-m-d_H-i-s', time()) . '.json');
			$this->response->addheader('Content-Transfer-Encoding: binary');

			if (ob_get_level()) ob_end_clean();

			$this->response->setOutput(json_encode($skins));
		}
		catch (Exception $e)
		{
			// TODO: Improve error showing better
			throw $e;
		}
	}

	/**
	 * Import skins from JSON file
	 */
	public function importSkins()
	{
		try
		{
			$this->assertPermission();

			if (!is_uploaded_file($this->request->files['file']['tmp_name']))
			{
				throw new Exception(_t('error_import_file', 'Import file error!'));
			}

			$this->model->deleteAllSkinData();

			$this->model->importSkinsFromFile($this->request->files['file']['tmp_name']);

			// TODO: Set skin for store
			$stores = $this->common->getStores();
			foreach ($stores as $store_id => $name)
			{
				$theme_id = $this->model->getThemeId($store_id);

				if ($this->model->isKulerTheme($theme_id))
				{
					$theme_options = $this->model->loadThemeOptions($theme_id);
					$skin_id = $theme_options['default_skin_id'];

					$this->model->setSkinForStore($store_id, $theme_id, $skin_id);
				}
			}

			$result = array(
				'status'    => 1,
				'message'   => _t('success_import_skins', 'Success: You have imported skins from file.')
			);
		}
		catch (Exception $e)
		{
			$this->response->addHeader('HTTP/1.1 500 Error');

			$result = array(
				'error' => $e->getMessage()
			);
		}

		$this->response->setOutput(json_encode($result));
	}

	/**
	 * Upgrade theme
	 */
	public function upgrade()
	{

	}

	public function jsMessages()
	{
		$js = 'var _tMessages = ' . json_encode(ModelKulerCommon::getTexts());

		$this->response->setOutput($js);
	}

	public function newsletterLists()
	{
		$type = $this->request->get['mail_service'];
		$info = $this->request->get;

		try
		{
			$lists = $this->model->getNewsletterLists($type, $info);

			$results = array(
				'status' => 1,
				'lists' => $lists
			);
		}
		catch (Exception $e)
		{
			$this->response->addHeader('HTTP/1.1 500 Error');

			$results = array(
				'status' => 0,
				'error' => $e->getMessage()
			);
		}

		$this->response->setOutput(json_encode($results));
	}

	public function autocomplete() {
		$filter_name = !empty($this->request->get['filter_name']) ? $this->request->get['filter_name'] : '';
		$item_id = !empty($this->request->get['item_id']) ? $this->request->get['item_id'] : array();
		$item_type = !empty($this->request->get['item_type']) ? $this->request->get['item_type'] : 'product';

		$data = array(
			'filter_name'   => $filter_name,
			'item_id'       => $item_id,
			'store_id'      => $this->request->get['store_id'],
		);

		$results = $this->common->getProducts($data, array(
				'start' => 0,
				'limit' => 20
			)
		);

		$items = array();

		foreach ($results as $result)
		{
			$items[$result['product_id']] = array(
				'item_id' => $result['product_id'],
				'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
				'link' => $this->common->createLink('catalog/product/update', array('product_id' => $result['product_id']))
			);
		}

		$json = array();
		if (!empty($item_id))
		{
			foreach ($item_id as $item_id_val)
			{
				$json[] = $items[$item_id_val];
			}
		}
		else
		{
			$json = $items;
		}

		$this->response->setOutput(json_encode($json));
	}


	protected function assertPostAndPermission()
	{
		if ($this->request->server['REQUEST_METHOD'] != 'POST')
		{
			throw new Exception(_t('error_request_method', 'Error: The request method must be POST!'));
		}

		$this->assertPermission();
	}

	protected function assertPermission()
	{
		if (!$this->user->hasPermission('modify', 'module/kuler_cp'))
		{
			throw new Exception(_t('error_permission', 'Warning: You do not have permission to modify module Kuler CP!'));
		}
	}
}