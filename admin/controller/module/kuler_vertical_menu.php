<?php
/**
 * Class ControllerModuleKulerVerticalMenu
 * @property Url $url
 * @property ModelModuleKulerVerticalMenu $model
 */
class ControllerModuleKulerVerticalMenu extends Controller {
	private $error = array();

	private $model;

	/* @var ModelKulerCommon $common */
	protected $common;

	public function __construct($registry)
	{
		parent::__construct($registry);

		$this->load->model('kuler/common');
		$this->common = $this->model_kuler_common;

		ModelKulerCommon::loadTexts($this->language->load('module/kuler_vertical_menu'));
	}

	public function index() {
        $this->load->model('module/kuler_vertical_menu');
        $this->load->model('localisation/language');
		$this->load->model('setting/setting');

		$this->model = $this->model_module_kuler_vertical_menu;

		$this->data['stores'] = $this->getStores();
		$this->data['selected_store_id'] = $this->getSelectedStore();
        
		$this->getLanguages();
		$this->getPathways();
		$this->getResources();

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->saveAction();
		}

		$this->data['front_base'] = $this->common->getFrontBase();

        $token = $this->session->data['token'];
        $languages = $this->getDataLanguages();

		$this->getErrors();
		
		$this->data['action'] = $this->url->link('module/kuler_vertical_menu', 'token=' . $token, 'SSL');
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $token, 'SSL');

        // Default data
        $default_menu = array(
            'type' => 'category',
            'title' => '',
            'status' => 1,
            'sort_order' => '',

            // Category
            'show_sub_categories' => 1,
            'category_image_width' => 80,
            'category_image_height' => 80,
            'image' => 1,
            'image_position' => 'float-left',

            // Product
            'image_width' => '155',
            'image_height' => '155',
            'name' => 1,
            'price' => 1,
            'rating' => 1,
            'description' => 1,
            'description_text' => 100,
            'add' => 1,
            'wishlist' => 1,
            'compare' => 1,

            // Custom
	        'enable_hyperlink' => false,
            'link' => '',
            'new_tab' => 1,
	        'sub_new_tab' => 0
        );

        $default_product_row = array(
            'product_id' => '',
            'name' => ''
        );

        $default_category_row = array(
            'category_id' => '',
            'name' => ''
        );

        $default_custom_row = array(
            'link' => '',
            'new_tab' => 1
        );
        foreach ($languages as $language)
        {
            $default_custom_row['titles'][$language['language_id']] = '';
        }

		// Get menu
        $this->data['menus'] = array();
        if (isset($this->request->post['menus']))
        {
            $this->data['menus'] = $this->request->post['menus'];
        }
        else
        {
	        if ($kuler_vertical_menu = $this->model_setting_setting->getSetting('kuler_vertical_menu', $this->data['selected_store_id']))
	        {
		        if (isset($kuler_vertical_menu['kuler_vertical_menu']))
		        {
			        $this->data['menus'] = $kuler_vertical_menu['kuler_vertical_menu'];
		        }
	        }
        }

        $this->data['menus'] = $this->prepareModules($this->data['menus']);

        // Get menu status
        $this->data['menu_status'] = 0;
        if (isset($this->request->post['menu_status']))
        {
            $this->data['menu_status'] = $this->request->post['menu_status'];
        }
        else
        {
	        if ($kuler_vertical_menu = $this->model_setting_setting->getSetting('kuler_vertical_menu', $this->data['selected_store_id']))
	        {
		        if (isset($kuler_vertical_menu['kuler_vertical_menu_status']))
		        {
			        $this->data['menu_status'] = $kuler_vertical_menu['kuler_vertical_menu_status'];
		        }
	        }
        }

		$this->data['modules'] = array();

		if (isset($this->request->post['module']))
		{
			$this->data['modules'] = $this->request->post['module'];
		}
		else
		{
			if (($settings = $this->model_setting_setting->getSetting('kuler_vertical_menu', $this->data['selected_store_id'])) && !empty($settings) && !empty($settings['kuler_vertical_menu_module']))
			{
				$this->data['modules'] = $settings['kuler_vertical_menu_module'];
			}
		}

        $this->data['language_id'] = $this->config->get('config_language_id');

        $this->data['default_menu'] = $default_menu;
        $this->data['default_product_row'] = $default_product_row;
        $this->data['default_category_row'] = $default_category_row;
        $this->data['default_custom_row'] = $default_custom_row;

		$this->data['default_module'] = $this->getDefaultModule();

        $this->data['token'] = $token;
        $this->data['languages'] = $languages;

		$this->data['layouts']      = $this->common->getLayouts();
		$this->data['positions']    = $this->common->getPositions();

		$this->data['category_autocomplete_url'] = $this->helperLink('module/kuler_vertical_menu/autocompleteCategory', array('token' => $this->session->data['token']));
		$this->data['product_autocomplete_url'] = $this->helperLink('module/kuler_vertical_menu/product_autocomplete', array('token' => $this->session->data['token']));

		$this->template = 'module/kuler_vertical_menu.phtml';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	public function autocompleteCategory()
	{
		if (isset($this->request->get['filter_name']))
		{
			$this->response->setOutput(json_encode(array()));
		}

		$this->load->model('module/kuler_vertical_menu');

		$this->model = $this->model_module_kuler_vertical_menu;

		$filter_name = $this->request->get['filter_name'];

		$categories = $this->model->getCategories(array(
			'filter_name'   => $filter_name,
			'store_id'      => $this->request->get['store_id']
		));

		$json = array();
		foreach ($categories as $category)
		{
			$json[] = array(
				'category_id'   => $category['category_id'],
				'name'          => strip_tags(html_entity_decode($category['name'], ENT_QUOTES, 'UTF-8'))
			);
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->setOutput(json_encode($json));
	}


	public function product_autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name']))
		{
			$this->load->model('module/kuler_vertical_menu');

			$filter_name = $this->request->get['filter_name'];

			$data = array(
				'filter_name'  => $filter_name,
				'store_id'     => $this->request->get['store_id'],
			);

			$results = $this->model_module_kuler_vertical_menu->getProducts(array(
					'filter_name' => $filter_name,
					'store_id' => $this->request->get['store_id']
				), array(
					'start' => 0,
					'limit' => 20
				)
			);

			foreach ($results as $result)
			{
				$json[] = array(
					'product_id' => $result['product_id'],
					'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$this->response->setOutput(json_encode($json));
	}

    private function prepareModules($modules)
    {
        if (is_array($modules))
        {
	        $front_base = $this->common->getFrontBase();

            foreach ($modules as &$module)
            {
                $module['title'] = $this->translate($module['title']);
                $module['main_title'] = $module['title'][$this->config->get('config_language_id')];

	            $module['link'] = htmlspecialchars_decode($module['link']);

	            if (!empty($module['title_icon']))
	            {
		            $module['title_icon_thumb'] = $front_base . $module['title_icon'];
	            }

	            if ($module['type'] == 'custom')
                {
	                if (isset($module['links']) && is_array($module['links']))
                    {
                        foreach ($module['links'] as &$link)
                        {
                            $link['titles'] = $this->translate($link['titles']);
	                        $link['link'] = htmlspecialchars_decode($link['link']);
                        }
                    }
                }
                else if ($module['type'] == 'html')
                {
                    $module['htmls'] = $this->translate($module['htmls']);
                }
            }
        }

        return $modules;
    }
	
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'module/kuler_vertical_menu'))
        {
			$this->error['warning'] = $this->language->get('error_permission');
		}

        if (!empty($this->request->post['menus']))
        {
            foreach ($this->request->post['menus'] as $menu_index => $menu)
            {
                if ($menu['type'] == 'product' && (empty($menu['image_width']) || empty($menu['image_height'])))
                {
                    $this->error['error_product_dimension'] = array(
                        $menu_index => $this->language->get('error_product_dimension')
                    );

                    break;
                }
            }
        }

		if (!$this->error)
        {
			return true;
		}
        else
        {
			return false;
		}	
	}
		
	protected function saveAction() {
        $this->load->model('setting/setting');

		$setting = array(
			'kuler_vertical_menu' => !empty($this->request->post['menus']) ? $this->request->post['menus'] : array(),
			'kuler_vertical_menu_module' => !empty($this->request->post['module']) ? $this->request->post['module'] : array()
		);

		$this->model_setting_setting->editSetting('kuler_vertical_menu',  $setting, $this->request->post['store_id']);

		$this->session->data['success'] = $this->language->get('text_success');
        
        if(isset($this->request->post['op']) && $this->request->post['op'] == 'close') {
            $this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
        } else {
            $this->redirect($this->url->link('module/kuler_vertical_menu', 'token=' . $this->session->data['token'] . '&store_id=' . $this->request->post['store_id'], 'SSL'));
        }
	}

	protected function getDefaultModule()
	{
		return array(
			'layout_id' => -1,
			'position' => 'content_top',
			'sort_order' => 10,
			'status' => 1
		);
	}

	private function getStores()
	{
		$this->load->model('setting/store');

		// Get stores
		$rows = $this->model_setting_store->getStores();

		$stores = array(
			0 => $this->config->get('config_name') . $this->language->get('text_default')
		);

		foreach ($rows as $row)
		{
			$stores[$row['store_id']] = $row['name'];
		}

		return $stores;
	}

	/**
	 * Get selected store id from post or get
	 */
	private function getSelectedStore()
	{
		$selected_store_id = 0;
		if (isset($this->request->post['store_id']))
		{
			$selected_store_id = $this->request->post['store_id'];
		}
		else if (isset($this->request->get['store_id']))
		{
			$selected_store_id = $this->request->get['store_id'];
		}

		return $selected_store_id;
	}

	protected function getResources() {
        $this->document->addStyle('view/kulercore/css/kulercore.css');
        $this->document->addStyle('view/kulercore/css/kuler_vertical_menu.css');
        $this->document->addScript('view/kulercore/js/handlebars.js');
        $this->document->addScript('view/javascript/ckeditor/ckeditor.js');
	}
	
	protected function getLayouts() {
		$this->load->model('design/layout');
		$result = $this->model_design_layout->getLayouts();
		return $result;
	}
	
	protected function getLanguages() {
        $__ = $this->language->load('module/kuler_vertical_menu');
		$this->data = array_merge($this->data, $__);
        $this->data['__'] = $__;
		$this->document->setTitle($this->data['heading_module']);

        // Load system language
        $texts = array(
            // Buttons
            'button_save',
            'button_cancel',
            'button_close',
        );

        foreach ($texts as $text)
        {
            $this->data[$text] = $this->language->get($text);
        }
	}
	
	protected function getErrors() {
		if (isset($this->error['warning']))
        {
			$this->data['error_warning'] = $this->error['warning'];
		}
        else
        {
			$this->data['error_warning'] = '';
		}

        $this->data['error_product_image'] = isset($this->error['error_product_dimension']) ? $this->error['error_product_dimension'] : array();
	}
	
	protected function getPathways() {
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
       		'text'      => $this->language->get('heading_module'),
			'href'      => $this->url->link('module/kuler_vertical_menu', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
	}

    protected function getDataLanguages()
    {
        $this->load->model('localisation/language');
        $languages = $this->model_localisation_language->getLanguages();
        $config_language = $this->config->get('config_language');

        $results = array();
	    if (isset($config_language))
	    {
		    $default_language = $languages[$config_language];
		    unset($languages[$config_language]);

		    $results[$config_language] = $default_language;
	    }

        $results = array_merge($results, $languages);

        return $results;
    }

    private function translate($texts)
    {
        $languages = $this->getDataLanguages();

        if (is_string($texts))
        {
            $text = $texts;
            $texts = array();

            foreach ($languages as $language)
            {
                $texts[$language['language_id']] = $text;
            }
        }
        else if (is_array($texts))
        {
            $first = current($texts);

            foreach ($languages as $language)
            {
                if (is_string($first))
                {
                    if (empty($texts[$language['language_id']]))
                    {
                        $texts[$language['language_id']] = $first;
                    }
                }
                else if (is_array($first))
                {
                    if (!isset($texts[$language['language_id']]))
                    {
                        $texts[$language['language_id']] = array();
                    }

                    foreach ($first as $key => $val)
                    {
                        if (empty($texts[$language['language_id']][$key]))
                        {
                            $texts[$language['language_id']][$key] = $val;
                        }
                    }
                }
            }
        }

        return $texts;
    }

	private function helperLink($route, array $params = array())
	{
		$params['token'] = $this->data['token'];

		return str_replace('&amp;', '&', $this->url->link($route, http_build_query($params), 'SSL'));
	}
}
?>