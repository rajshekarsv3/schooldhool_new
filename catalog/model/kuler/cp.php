<?php
class ModelKulerCp extends Model
{
	const TABLE_THEME = 'kcp_theme';
	const TABLE_SKIN = 'kcp_skin';
	const TABLE_SKIN_OPTION = 'kcp_skin_option';

	protected $skin_options = array();

	/**
	 * Get path of theme data
	 * @param $theme_id
	 * @return string
	 */
	public function getThemeDataPath($theme_id)
	{
		return DIR_APPLICATION . 'view/theme/' . $theme_id . '/data/';
	}

	/**
	 * Load theme options
	 * @param $theme_id
	 */
	public function loadThemeOptions($theme_id, $cache = true)
	{
		static $cache = array();

		// Get from cache if enabled
		if ($cache && isset($cache[$theme_id]))
		{
			return $cache[$theme_id];
		}

		// Get theme data path
		$theme_option_file = $this->getThemeDataPath($theme_id) . 'theme_options.php';

		// Check where the theme option file is exist or not
		if (!file_exists($theme_option_file))
		{
			throw new Exception(_t('error_theme_option_file_lost', 'The theme option does not exist!'));
		}

		// Get file contents and decode
		$theme_options = include($theme_option_file);

		// Cache
		$cache[$theme_id] = $theme_options;

		return $theme_options;;
	}

	/**
	 * Get all skins of theme by theme ID
	 * @param $theme_id string Theme ID
	 * @return array
	 */
	public function getSkinsByThemeId($theme_id)
	{
		$theme_id = $this->db->escape($theme_id);

		$query = $this->db->query("SELECT * FROM ". DB_PREFIX . self::TABLE_SKIN ." WHERE theme_id = '$theme_id'");

		return $query->rows;
	}

	public function getCurrentThemeId()
	{
		return $this->config->get('theme_id');
	}

	public function getCurrentThemeVersion()
	{
		static $version;

		if (!$version)
		{
			$theme_id = $this->db->escape($this->getCurrentThemeId());

			$query = $this->db->query("SELECT * FROM ". DB_PREFIX . self::TABLE_THEME ." WHERE theme_id = '$theme_id' LIMIT 0, 1");

			$version = $query->row['version'];
		}

		return $version;
	}

	public function getCurrentSkinId()
	{
		return $this->config->get('skin_id');
	}

	public function getCurrentRootSkinId()
	{
		static $root_skin_id;

		if (!$root_skin_id)
		{
			$theme_id = $this->db->escape($this->getCurrentThemeId());
			$skin_id = $this->db->escape($this->getCurrentSkinId());

			$query = $this->db->query("SELECT * FROM ". DB_PREFIX . self::TABLE_SKIN ." WHERE theme_id = '$theme_id' AND skin_id = '$skin_id' LIMIT 0, 1");

			$root_skin_id = $query->row['root_skin_id'];
		}

		return $root_skin_id;
	}

	public function getSkinOptions($theme_id, $skin_id, $from_cache = true)
	{
		$cache_id = $theme_id . '_' . $skin_id;

		if ($from_cache && !empty($this->skin_options[$cache_id]))
		{
			return $this->skin_options[$cache_id];
		}

		$theme_id = $this->db->escape($theme_id);
		$skin_id = $this->db->escape($skin_id);

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . self::TABLE_SKIN_OPTION . " WHERE theme_id = '$theme_id' AND skin_id = '$skin_id'");

		$options = array();
		foreach ($query->rows as $row)
		{
			$options[$row['option']] = json_decode($row['value'], true);
		}

		$this->skin_options[$cache_id] = $options;

		return $options;
	}

	public function getSkinOption($theme_id, $skin_id, $property)
	{
		$cache_id = $theme_id . '_' . $skin_id;

		$value = isset($this->skin_options[$cache_id]) && isset($this->skin_options[$cache_id][$property]) ? $this->skin_options[$cache_id][$property] : null;

		if ($value !== null)
		{
			if ($value === 'true')
			{
				$value = true;
			}

			if ($value === 'false')
			{
				$value = false;
			}
		}

		return $value;
	}

	public function processFontFamily($font)
	{
		$fonts = $this->getGoogleFonts();

		$variants = array();
		$is_exist = false;
		foreach ($fonts['items'] as $font_item)
		{
			if ($font_item['family'] == $font)
			{
				$is_exist = true;

				if (!empty($font_item['variants']))
				{
					$variants = $font_item['variants'];
				}
			}
		}

		if (!$is_exist)
		{
			return false;
		}

		$font_family = preg_replace('/(\w)\s+(\w)/', '$1+$2', $font);

		if (!empty($variants))
		{
			$font_family .= ':' . implode(',', $variants);
		}

		return $font_family;
	}

	public function getGoogleFonts()
	{
		static $fonts;

		if (!$fonts)
		{
			$font_contents = file_get_contents(dirname(__FILE__) . '/' . 'fonts.json');

			$fonts = json_decode($font_contents, true);
		}

		return $fonts;
	}

	public function getRecursiveCategories()
	{
		$paths = isset($this->request->get['path']) ? $this->request->get['path'] : '';
		$paths = explode('_', $paths);

		$categories = $this->model_catalog_category->getCategories(0);

		$top_categories = array();

		foreach ($categories as $category) {
			if ($category['top']) {
				// Level 2
				$children_data = array();

				$children = $this->model_catalog_category->getCategories($category['category_id']);

				foreach ($children as $child) {
					$data = array(
						'filter_category_id'  => $child['category_id'],
						'filter_sub_category' => true
					);

					$product_total = $this->model_catalog_product->getTotalProducts($data);

					$children_data[] = array(
						'name'  => $child['name'] . ($this->config->get('config_product_count') ? ' (' . $product_total . ')' : ''),
						'href'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id']),
						'category_id' => $child['category_id'],
						'path'  => $category['category_id'] . '_' . $child['category_id'],
						'active'   => isset($paths[1]) && $paths[1] == $child['category_id'] ? true : false
					);
				}

				// Level 1
				$top_categories[] = array(
					'category_id' => $category['category_id'],
					'name'	 => $category['name'],
					'children' => $children_data,
					'column'   => $category['column'] ? $category['column'] : 1,
					'href'	 => $this->url->link('product/category', 'path=' . $category['category_id']),
					'active'   => isset($paths[0]) && $paths[0] == $category['category_id'] ? true : false
				);
			}
		}

		$categories = $top_categories;

		foreach ($categories as &$tcategory)
		{
			foreach ($tcategory['children'] as &$sub_category)
			{
				$sub_category['children'] = $this->_getRecursiveCategoriesAtLevel3($sub_category['category_id'], $sub_category['path'], 2, $paths);
			}
		}

		return $categories;
	}

	public function subscribeNewsletter(array $info)
	{
		$kuler = Kuler::getInstance();
		$mail_service = $kuler->getSkinOption('mail_service');

		$result = false;

		$contact_list = $kuler->getSkinOption('contact_list');

		if (empty($contact_list))
		{
			throw new Exception(_t('error_the_newsletter_system_is_not_configured_please_contact_to_site_admin'));
		}

		switch ($mail_service)
		{
			case 'mailchimp':
				require_once('mail_service/mailchimp.php');

				$api_key = $kuler->getSkinOption('mailchimp_api_key');

				if (empty($api_key))
				{
					throw new Exception(_t('error_the_newsletter_system_is_not_configured_please_contact_to_site_admin'));
				}

				$api = new MCAPI($api_key);

				$mail = $info['mail'];
				$merge = array(
					'FNAME' => 'Email',
					'LNAME' => ' :' . $mail,
				);

				$api->listSubscribe($contact_list, $mail, $merge);

				if ($api->errorCode)
				{
					$message = $api->errorMessage;

					if ($api->errorCode == 214)
					{
						$message = _t('error_your_email_is_already_subscribed_to_list');
					}

					throw new Exception($message);
				}

				$result = true;

				break;
			case 'icontact':
				require_once('mail_service/icontact.php');

				$key = $kuler->getSkinOption('icontact_app_key');
				$username = $kuler->getSkinOption('icontact_username');
				$password = $kuler->getSkinOption('icontact_password');

				if (empty($key) || empty($username) || empty($password))
				{
					throw new Exception(_t('error_the_newsletter_system_is_not_configured_please_contact_to_site_admin'));
				}

				iContactApi::getInstance()->setConfig(array(
					'appId' => $key,
					'apiUsername' => $username,
					'apiPassword' => $password,
				));

				$oiContact = iContactApi::getInstance();

				try
				{
					$result = $oiContact->addContact($info['mail']);
					$result = $oiContact->subscribeContactToList($result->contactId, $contact_list, 'normal');
				}
				catch (Exception $e)
				{
					throw new Exception($e->getMessage());
				}

				$result = true;

				break;
		}

		return $result;
	}

	protected function _getRecursiveCategoriesAtLevel3($category_id, $path, $depth, $paths)
	{
		if ($depth == 6)
		{
			return array();
		}

		$categories = $this->model_catalog_category->getCategories($category_id);

		$results = array();

		foreach ($categories as $category) {
			$data = array(
				'filter_category_id'  => $category['category_id'],
				'filter_sub_category' => true
			);

			$product_total = $this->model_catalog_product->getTotalProducts($data);

			$new_path = $path . '_' . $category['category_id'];

			$results[] = array(
				'name'  => $category['name'] . ($this->config->get('config_product_count') ? ' (' . $product_total . ')' : ''),
				'href'  => $this->url->link('product/category', 'path=' . $new_path),
				'active' => isset($paths[$depth]) && $paths[$depth] == $category['category_id'] ? true : false,
				'children' => $this->_getRecursiveCategoriesAtLevel3($category['category_id'], $new_path, $depth + 1, $paths),
			);
		}

		return $results;
	}
}