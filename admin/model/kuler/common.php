<?php
function _t($text, $placeholder = '')
{
	return ModelKulerCommon::__($text, $placeholder);
}

class ModelKulerCommon extends Model
{
	static $VERSION = '2.0.5';

	public static $__ = array();

	public static function loadTexts(array $texts)
	{
		self::$__ = array_merge(self::$__, $texts);
	}

	public static function __($text, $placeholder = '')
	{
		if (isset(self::$__[$text]))
		{
			return self::$__[$text];
		}
		else
		{
			// TODO: alert lost texts

			return $placeholder ? $placeholder : $text;
		}
	}

	public static function getTexts()
	{
		return self::$__;
	}

	public function isDevelopment()
	{
		if (isset($_COOKIE['kdev']) && $_COOKIE['kdev'] == 1)
		{
			return true;
		}

		return false;
	}

	public function getVersion()
	{
		return self::$VERSION;
	}

	public function getStores()
	{
		$this->load->model('setting/store');

		$rows = $this->model_setting_store->getStores();

		$stores = array(
			'0' => $this->config->get('config_name') . _t('text_default')
		);

		foreach ($rows as $row)
		{
			$stores[$row['store_id']] = $row['name'];
		}

		return $stores;
	}

	public function insertCommonResources()
	{
		$this->document->addStyle('view/kuler/bootstrap/css/bootstrap.min.css');
		$this->document->addStyle('view/kuler/css/bootstrap-reset.css');
		$this->document->addStyle('view/kuler/asset/font-awesome/css/font-awesome.min.css');
		$this->document->addStyle('view/kuler/asset/bootstrap-switch/css/bootstrap-switch.min.css');
		$this->document->addStyle('view/kuler/asset/bootstrap-fileupload/bootstrap-fileupload.css');
		$this->document->addStyle('view/kuler/asset/bootstrap-colorpicker/css/colorpicker.css');
		$this->document->addStyle('view/kuler/css/style.css');

		$this->document->addScript('view/kuler/js/handlebars.js');
		$this->document->addScript('view/kuler/angular/angular.js');
		$this->document->addScript('view/kuler/angular/ui/ui-bootstrap-custom-0.10.0.js');
		$this->document->addScript('view/kuler/angular/ui/autocomplete.js');

		$this->document->addScript('view/kuler/angular/angular-cookies.min.js');
		$this->document->addScript('view/kuler/angular/file-upload/angular-file-upload-shim.min.js');
		$this->document->addScript('view/kuler/angular/file-upload/angular-file-upload.min.js');
		$this->document->addScript('view/kuler/angular/app.js');
		$this->document->addScript('view/kuler/angular/services.js');
		$this->document->addScript('view/kuler/angular/directives.js');
		$this->document->addScript('view/kuler/angular/filters.js');

		$this->document->addScript('view/kuler/asset/bootstrap-switch/js/bootstrap-switch.js');
		$this->document->addScript('view/kuler/asset/bootstrap-colorpicker/js/bootstrap-colorpicker.js');
		$this->document->addScript('view/kuler/ckeditor/ckeditor.js');
		$this->document->addScript('view/kuler/bootstrap/js/bootstrap.min.js');
	}

	public function getFrontBase()
	{
		return isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1')) ? HTTPS_CATALOG : HTTP_CATALOG;
	}

	public function getStoreBase($store_id)
	{
		if ($store_id == 0)
		{
			return $this->getFrontBase();
		}
		else
		{
			$this->load->model('setting/setting');

			$store = $this->model_setting_setting->getSetting('config', $store_id);

			return $store['config_url'];
		}
	}

	public function getLanguages()
	{
		$this->load->model('localisation/language');

		return $this->model_localisation_language->getLanguages();
	}

	public function getLayouts()
	{
		$this->load->model('design/layout');

		$layouts = $this->model_design_layout->getLayouts();

		$results = array(
			array(
				'layout_id' => '-1',
				'name' => _t('All Layouts')
			)
		);

		$results = array_merge($results, $layouts);

		return $results;
	}

	public function getLayoutOptions()
	{
		$layouts = $this->getLayouts();

		$results = array();
		foreach ($layouts as $layout)
		{
			$results[$layout['layout_id']] = $layout['name'];
		}

		return $results;
	}

	public function getPositions()
	{
		$this->load->model('kuler/cp');

		$theme_options = $this->model_kuler_cp->loadThemeOptions($this->config->get('config_template'));

		$extra_positions = isset($theme_options['positions']) ? $theme_options['positions'] : array();

		$results = array(
			'content_top' => _t('text_content_top', 'Content Top'),
			'content_bottom' => _t('text_content_bottom', 'Content Bottom'),
			'column_left' => _t('text_column_left', 'Column Left'),
			'column_right' => _t('text_column_right', 'Column Right'),
		);

		foreach ($extra_positions as $key => $value)
		{
			$results[$key] = $value;
		}

		return $results;
	}

	public function createLink($route, array $params = array())
	{
		$params['token'] = $this->session->data['token'];

		return urldecode(str_replace('&amp;', '&', $this->url->link($route, http_build_query($params), 'SSL')));
	}

	public function decodeMultilingualText($text)
	{
		if (is_array($text))
		{
			foreach ($this->getLanguages() as $language)
			{
				if (!empty($text[$language['code']]))
				{
					$text[$language['code']] = html_entity_decode($text[$language['code']], ENT_QUOTES, 'UTF-8');
				}
			}
		}
		else
		{
			$text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
		}

		return $text;
	}

	public function getCategories(array $data = array())
	{
		$this->load->model('catalog/category');

		$category_ids = array();
		$store_category_ids = array();

		foreach ($data as &$value)
		{
			$value = urldecode($value);
		}

		if (version_compare(VERSION, '1.5.5', '>='))
		{
			$categories = $this->model_catalog_category->getCategories($data);
		}
		else
		{
			$categories = $this->model_catalog_category->getCategories(0);
		}

		// Filter category by name
		if (!empty($data['filter_name']))
		{
			$filter_name = utf8_strtolower(urldecode($data['filter_name']));
			$config_language_id = intval($this->config->get('config_language_id'));

			$query = $this->db->query("SELECT category_id FROM ". DB_PREFIX ."category_description WHERE LCASE(name) LIKE '$filter_name%' AND language_id = $config_language_id");

			foreach ($query->rows as $category2store)
			{
				$category_ids[$category2store['category_id']] = $category2store['category_id'];
			}
		}

		// Filter category by store
		if (isset($data['store_id']))
		{
			$query = $this->db->query("SELECT * FROM ". DB_PREFIX ."category_to_store WHERE store_id = " . intval($data['store_id']));

			foreach ($query->rows as $category2store)
			{
				$store_category_ids[$category2store['category_id']] = $category2store['category_id'];
			}
		}

		// Remove category that does not match filter
		$results = array();

		if ((empty($data['filter_name']) || $category_ids) && $store_category_ids)
		{
			for ($i = 0; $i < count($categories); $i++)
			{
				if ((empty($data['filter_name']) || in_array($categories[$i]['category_id'], $category_ids)) && in_array($categories[$i]['category_id'], $store_category_ids))
				{
					$results[] = $categories[$i];
				}
			}
		}

		return $results;
	}

	public function getCategoryOptions(array $data = array())
	{
		$categories = $this->getCategories($data);

		if (empty($data['all']))
		{
			$results = array();
		}
		else
		{
			$results = array(
				0 => _t('text_all_categories', '-- All Categories --')
			);
		}

		foreach ($categories as $category)
		{
			$results[$category['category_id']] = $category['name'];
		}

		return $results;
	}


	public function getProducts($conditions = array(), $fetch_options = array())
	{
		$join = '';
		$where = '';
		$limitClause = '';

		if (!empty($conditions['item_id']))
		{
			foreach ($conditions['item_id'] as &$item_id)
			{
				$item_id = intval($item_id);
			}

			$where .= " AND p.product_id IN (". implode(', ', $conditions['item_id']) .")";
		}

		if (!empty($conditions['filter_name']))
		{
			$conditions['filter_name'] = urldecode($conditions['filter_name']);

			$where .= " AND LCASE(pd.name) LIKE '" . $this->db->escape(utf8_strtolower($conditions['filter_name'])) . "%'";
		}

		if (!empty($conditions['store_id']))
		{
			$join = '
                INNER JOIN '. DB_PREFIX .'product_to_store ps
                    ON (p.product_id = ps.product_id AND ps.store_id = '. intval($conditions['store_id']) .')
            ';
		}

		if (isset($fetch_options['start']))
		{
			$limitClause .= 'LIMIT ' . intval($fetch_options['start']) . ',' . intval($fetch_options['limit']);
		}

		$query = $this->db->query("
            SELECT *
            FROM " . DB_PREFIX . "product p
            LEFT JOIN " . DB_PREFIX . "product_description pd
                ON (p.product_id = pd.product_id)
            ". $join ."
            WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
                ". $where ."
            ORDER BY pd.name ASC
            ". $limitClause ."
        ");

		return $query->rows;
	}

	/**
	 * Check where theme belongs KulerThemes or not
	 * @param $theme_id string
	 * @return boolean
	 */
	public function isKulerTheme($theme_id)
	{
		if (file_exists(DIR_CATALOG . 'view/theme/' . $theme_id . '/data/theme_options.php'))
		{
			return true;
		}

		return false;
	}
}