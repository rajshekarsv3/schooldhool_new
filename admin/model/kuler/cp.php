<?php

/**
 * Class ModelKulerCp
 * @property Log $log
 * @property Db $db
 */
class ModelKulerCp extends Model
{
	const TABLE_THEME = 'kcp_theme';
	const TABLE_SKIN = 'kcp_skin';
	const TABLE_SKIN_OPTION = 'kcp_skin_option';

	/* @var ModelKulerCommon $common */
	protected $common;

	public function __construct($registry)
	{
		parent::__construct($registry);

		$this->load->model('kuler/common');
		$this->load->model('setting/setting');

		$this->common = $this->model_kuler_common;
	}

	public function createTables()
	{
		$sqls = array(
			'
			CREATE TABLE `'. DB_PREFIX .'kcp_skin` (
			  `theme_id` varchar(64) NOT NULL,
			  `skin_id` varchar(64) NOT NULL,
			  `name` varchar(64) NOT NULL,
			  `root_skin_id` varchar(64) NOT NULL,
			  PRIMARY KEY (`theme_id`,`skin_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			',
			'
			CREATE TABLE `'. DB_PREFIX .'kcp_skin_option` (
			  `option` varchar(64) NOT NULL,
			  `theme_id` varchar(64) NOT NULL,
			  `skin_id` varchar(64) NOT NULL,
			  `value` text NOT NULL,
			  PRIMARY KEY (`option`,`theme_id`,`skin_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;
			',
			'
			CREATE TABLE `'. DB_PREFIX .'kcp_theme` (
			  `theme_id` varchar(64) NOT NULL,
			  `version` varchar(10) NOT NULL,
			  PRIMARY KEY (`theme_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;
			'
		);

		foreach ($sqls as $sql)
		{
			$this->db->query($sql);
		}

		return true;
	}

	public function dropTables()
	{
		$sqls = array(
			'DROP TABLE IF EXISTS `'. DB_PREFIX .'kcp_skin`',
			'DROP TABLE IF EXISTS `'. DB_PREFIX .'kcp_skin_option`',
			'DROP TABLE IF EXISTS `'. DB_PREFIX .'kcp_theme`',
		);

		foreach ($sqls as $sql)
		{
			$this->db->query($sql);
		}

		return true;
	}

	public function isInstalledTheme($theme_id)
	{
		$theme_id = $this->db->escape($theme_id);

		$query = $this->db->query("SELECT theme_id FROM `". DB_PREFIX . self::TABLE_THEME ."` WHERE theme_id = '$theme_id' LIMIT 0, 1");

		return $query->num_rows > 0 ? true : false;
	}

	/**
	 * Install theme
	 * @param $theme_id string
	 * @param $store_id int
	 */
	public function installTheme($store_id, $theme_id)
	{
		$theme_options = $this->loadThemeOptions($theme_id);

		if (!$this->isInstalledTheme($theme_id))
		{
			$theme_id = $this->db->escape($theme_id);
			$version = $this->db->escape($theme_options['version']);

			$this->db->query("INSERT INTO `". DB_PREFIX . self::TABLE_THEME ."` VALUES('$theme_id', '$version')");

			$skin_file = $this->getThemeDataPath($theme_id) . 'skins.json';
			$this->importSkinsFromFile($skin_file);
		}

		// Choose skin for this store
		$skin_id = $theme_options['default_skin_id'];
		$this->setSkinForStore($store_id, $theme_id, $skin_id);
	}

	/**
	 * @param $theme_id string
	 */
	public function upgradeTheme($theme_id)
	{
		$theme_id = $this->db->escape($theme_id);

		// Get all skins and all skin options by theme id
		$old_skins = array();
		$query = $this->db->query("SELECT * FROM ". DB_PREFIX . self::TABLE_SKIN ." WHERE theme_id = '$theme_id'");
		$old_skins = $query->rows;

		$old_skin_options = array();
		$query = $this->db->query("SELECT * FROM ". DB_PREFIX . self::TABLE_SKIN_OPTION . " WHERE theme_id = '$theme_id'");
		$old_skin_options = $query->rows;

		// Remove them by theme id
		$this->db->query("DELETE FROM ". DB_PREFIX . self::TABLE_SKIN ." WHERE theme_id = '$theme_id'");
		$this->db->query("DELETE FROM ". DB_PREFIX . self::TABLE_SKIN_OPTION . " WHERE theme_id = '$theme_id'");

		// Insert from theme skin files
		$skin_file = $this->getThemeDataPath($theme_id) . 'skins.json';
		$this->importSkinsFromFile($skin_file);

		$skins = json_decode(file_get_contents($skin_file), true);

		// Copy lost option
		foreach ($old_skins as $skin_row)
		{
			$skin_row['skin_id'] = $this->db->escape($skin_row['skin_id']);
			$skin_row['name'] = $this->db->escape($skin_row['name']);
			$skin_row['root_skin_id'] = $this->db->escape($skin_row['root_skin_id']);

			if (empty($skins[$theme_id . '|' . $skin_row['skin_id']]))
			{
				$this->db->query("
					INSERT INTO ". DB_PREFIX . self::TABLE_SKIN ." VALUES ('$theme_id', '{$skin_row['skin_id']}', '{$skin_row['name']}', '{$skin_row['root_skin_id']}')
				");
			}
			else
			{
				$this->db->query("
					UPDATE `". DB_PREFIX . self::TABLE_SKIN ."`
					SET `root_skin_id` = '{$skin_row['root_skin_id']}', `name` = '{$skin_row['name']}'
					WHERE `theme_id` = '$theme_id'
						AND `skin_id` = '{$skin_row['skin_id']}'
				");
			}
		}

		foreach ($old_skin_options as $skin_option_row)
		{
			$skin_option_row['option'] = $this->db->escape($skin_option_row['option']);
			$skin_option_row['theme_id'] = $this->db->escape($skin_option_row['theme_id']);
			$skin_option_row['skin_id'] = $this->db->escape($skin_option_row['skin_id']);
			$skin_option_row['value'] = $this->db->escape($skin_option_row['value']);

			$skin_code = $theme_id . '|' . $skin_row['skin_id'];

			if (empty($skins[$skin_code]) || empty($skins[$skin_code]['options'][$skin_option_row['option']]))
			{
				$this->db->query("
					INSERT INTO ". DB_PREFIX . self::TABLE_SKIN_OPTION . "
					SET `option` = '{$skin_option_row['option']}'
						, `theme_id` = '{$skin_option_row['theme_id']}'
						, `skin_id` = '{$skin_option_row['skin_id']}'
						, `value` = '{$skin_option_row['value']}'
					ON DUPLICATE KEY UPDATE `value` = '{$skin_option_row['value']}'
				");
			}
			else
			{
				$this->db->query("
					UPDATE ". DB_PREFIX . self::TABLE_SKIN_OPTION . "
					SET `value` = '{$skin_option_row['value']}'
					WHERE `theme_id` = '{$skin_option_row['theme_id']}'
						AND `skin_id` = '{$skin_option_row['skin_id']}'
						AND `option` = '{$skin_option_row['option']}'
				");
			}
		}

		// Update theme version
		$theme_option = $this->loadThemeOptions($theme_id);
		$this->db->query("UPDATE `". DB_PREFIX . self::TABLE_THEME ."` SET version = '{$theme_option['version']}' WHERE theme_id = '$theme_id'");
	}

	/**
	 * Get config theme by store id
	 * @param $store_id int
	 * @return string
	 */
	public function getConfigTheme($store_id)
	{
		$settings = $this->model_setting_setting->getSetting('config', $store_id);

		return is_array($settings) && isset($settings['config_template']) ? $settings['config_template'] : null;
	}

	/**
	 * Get theme id
	 * @param $store_id int
	 * @return string
	 */
	public function getThemeVersion($store_id)
	{
		$theme_id = $this->db->escape($this->getThemeId($store_id));

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . self::TABLE_THEME . "` WHERE theme_id = '$theme_id'");

		return isset($query->row['version']) ? $query->row['version'] : null;
	}

	/**
	 * Get theme id
	 * @param $store_id int
	 * @return string
	 */
	public function getThemeId($store_id)
	{
		$settings = $this->model_setting_setting->getSetting('kuler_cp', $store_id);

		return is_array($settings) && isset($settings['theme_id']) ? $settings['theme_id'] : null;
	}

	/**
	 * Get skin id
	 * @param $store_id int
	 * @return string
	 */
	public function getSkinId($store_id)
	{
		$settings = $this->model_setting_setting->getSetting('kuler_cp', $store_id);

		return is_array($settings) && isset($settings['skin_id']) ? $settings['skin_id'] : null;
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

	/**
	 * Get path of theme data
	 * @param $theme_id
	 * @return string
	 */
	public function getThemeDataPath($theme_id)
	{
		return DIR_CATALOG . 'view/theme/' . $theme_id . '/data/';
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
			$e = new Exception(_t('error_theme_option_file_lost', 'Our theme is not set for this store! Please choose store which our theme is set.'), 4041);

			throw $e;
		}

		// Get file contents and decode
		$theme_options = include($theme_option_file);

		if (empty($theme_options['excluded_options']))
		{
			$theme_options['excluded_options'] = array();
		}

		// Cache
		$cache[$theme_id] = $theme_options;

		return $theme_options;
	}

	/**
	 * @param $theme_id int
	 */
	public function loadThemeStyleTemplate($theme_id)
	{
		$path = $this->getThemeDataPath($theme_id) . 'style.tpl';

		if (!file_exists($path))
		{
			throw new Exception(_t('error_theme_style_template_lost', 'The theme style template does not exist!'));
		}

		return file_get_contents($path);
	}

	/**
	 * @param $theme_id int
	 */
	public function loadThemePatterns($theme_id)
	{
		$pattern_dir = $this->getThemeDataPath($theme_id) . 'patterns';

		if (!is_dir($pattern_dir))
		{
			throw new Exception(_t('error_theme_pattern_lost', 'Error: The theme patterns are lost!'));
		}

		$files = glob($pattern_dir . '/*.*');
		$results = array();
		foreach ($files as $file)
		{
			$results[] = str_replace(DIR_CATALOG, 'catalog/', $file);
		}

		return $results;
	}

	/**
	 *
	 * @param $key
	 */
	public function getThemeOption($key)
	{

	}

	public function setSkinForStore($store_id, $theme_id, $skin_id)
	{
		$this->model_setting_setting->editSetting('kuler_cp', array(
			'theme_id' => $theme_id,
			'skin_id' => $skin_id
		), $store_id);
	}

	/**
	 * Get all skins
	 * @return array {theme_id}|{skin_id} => skin_data
	 */
	public function getAllSkins()
	{
		$query = $this->db->query('SELECT * FROM '. DB_PREFIX . self::TABLE_SKIN);

		$skins = array();
		foreach ($query->rows as $row)
		{
			$skins[$row['theme_id'] . '|' . $row['skin_id']] = array(
				'name'          => $row['name'],
				'theme_id'      => $row['theme_id'],
				'root_skin_id'  => $row['root_skin_id']
			);
		}

		return $skins;
	}

	/**
	 * Get all skin options grouped by theme_id and skin_id
	 * @return array
	 */
	public function getAllSkinOptions()
	{
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . self::TABLE_SKIN_OPTION);

		$options = array();
		foreach ($query->rows as $row)
		{
			$key = $row['theme_id'] . '|' . $row['skin_id'];

			if (!isset($options[$key]))
			{
				$options[$key] = array();
			}

			$options[$key][$row['option']] = json_decode($row['value']);
		}

		return $options;
	}

	/**
	 * Delete skin and skin options
	 */
	public function deleteAllSkinData()
	{
		$this->db->query("DELETE FROM " . DB_PREFIX . self::TABLE_SKIN);
		$this->db->query("DELETE FROM " . DB_PREFIX . self::TABLE_SKIN_OPTION);
	}

	/**
	 * @param array $skin
	 * @return bool
	 */
	public function insertSkin(array $skin)
	{
		$skin['skin_id']        = $this->db->escape($skin['skin_id']);
		$skin['name']           = $this->db->escape($skin['name']);
		$skin['theme_id']       = $this->db->escape($skin['theme_id']);

		$this->db->query("INSERT INTO ". DB_PREFIX . self::TABLE_SKIN ."(skin_id, theme_id, root_skin_id, name) VALUES('{$skin['skin_id']}', '{$skin['theme_id']}', '{$skin['root_skin_id']}', '{$skin['name']}')");

		return true;
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

	/**
	 * @param $theme_id
	 * @param $skin_id
	 * @return array
	 */
	public function getSkin($theme_id, $skin_id)
	{
		$theme_id = $this->db->escape($theme_id);
		$skin_id = $this->db->escape($skin_id);

		$query = $this->db->query("SELECT * FROM ". DB_PREFIX . self::TABLE_SKIN ." WHERE theme_id = '$theme_id' AND skin_id = '$skin_id' LIMIT 0, 1    ");

		return $query->row;
	}

	/**
	 * Get skin options by theme id and skin id
	 * @param $theme_id string
	 * @param $skin_id string
	 * @return array {option} => value
	 */
	public function getSkinOptions($theme_id, $skin_id)
	{
		$theme_id = $this->db->escape($theme_id);
		$skin_id = $this->db->escape($skin_id);

		$query = $this->db->query("SELECT * FROM ". DB_PREFIX . self::TABLE_SKIN_OPTION ." WHERE theme_id = '$theme_id' AND skin_id = '$skin_id'");

		$options = array();

		foreach ($query->rows as $row)
		{
			$options[$row['option']] = json_decode($row['value'], true);
		}

		return $options;
	}

	/**
	 * @param $theme_id
	 * @param $skin_id
	 * @param array $options
	 * @return bool
	 */
	public function updateSkinOptions($theme_id, $skin_id, array $options)
	{
		$theme_id   = $this->db->escape($theme_id);
		$skin_id    = $this->db->escape($skin_id);

		// Delete all skin options
		$this->db->query("DELETE FROM `". DB_PREFIX . self::TABLE_SKIN_OPTION ."` WHERE `theme_id` = '$theme_id' AND `skin_id` = '$skin_id'");

		$options = $this->decode($options);

		// Insert new option
		foreach ($options as $key => $value)
		{
			$key = $this->db->escape($key);
			$value = $this->db->escape(json_encode($value)); // TODO: Improve encoding better

			$this->db->query("INSERT INTO `". DB_PREFIX . self::TABLE_SKIN_OPTION ."`(`theme_id`, `skin_id`, `option`, `value`) VALUES ('$theme_id', '$skin_id', '$key', '$value')");
		}

		return true;
	}

	/**
	 * Remove skin
	 * @param $theme_id
	 * @param $skin_id
	 * @return boolean
	 */
	public function removeSkin($theme_id, $skin_id)
	{
		$theme_id = $this->db->escape($theme_id);
		$skin_id = $this->db->escape($skin_id);

		$this->db->query("DELETE FROM ". DB_PREFIX . self::TABLE_SKIN ." WHERE theme_id = '$theme_id' AND skin_id = '$skin_id'");
		$this->db->query("DELETE FROM ". DB_PREFIX . self::TABLE_SKIN_OPTION ." WHERE theme_id = '$theme_id' AND skin_id = '$skin_id'");

		return true;
	}

	/**
	 * Normalize skin id by skin name
	 * @param $skin_name string
	 * @return string
	 */
	public function normalizeSkinId($skin_name)
	{
		$skin_name = strtolower($skin_name);
		return preg_replace('/\s+/', '_', trim($skin_name));
	}

	/**
	 * Import skins from file
	 * @param $file string the path to skin file
	 * @return boolean
	 */
	public function importSkinsFromFile($file)
	{
		if (!file_exists($file))
		{
			throw new Exception(_t('error_skin_file_lost', 'The skin file does not exist!'));
		}

		$contents = file_get_contents($file);

		$data = json_decode($contents, true);

		// TODO: Check data

		foreach ($data as $key => $skin)
		{
			list ($theme_id, $skin_id) = explode('|', $key, 2);

			$skin_data = array(
				'skin_id'           => $skin_id,
				'theme_id'          => $theme_id,
				'root_skin_id'      => $skin['root_skin_id'],
				'name'              => $skin['name']
			);

			$this->insertSkin($skin_data);
			$this->updateSkinOptions($skin['theme_id'], $skin_id, $skin['options']);
		}

		return true;
	}

	public function getFonts()
	{
		$default_text = _t('text_style_system_fonts', 'System Fonts');

		$fonts = array(
			array(
				'family' => 'Georgia, serif',
				'group' => $default_text
			),
			array(
				'family' => 'Helvetica, sans-serif',
				'group' => $default_text
			)
		);

		// Read Google fonts
		$font_file = DIR_APPLICATION . 'view/kuler/fonts.json';
		if (!file_exists($font_file))
		{
			throw new Exception(_t('error_font_file_lost', 'The font file does not exist!'));
		}

		$contents = $this->readFile($font_file);

		$data = json_decode($contents, true);

		$translated_google_font = _t('text_style_google_fonts', 'Google Fonts');

		foreach ($data['items'] as $font_item)
		{
			$fonts[] = array(
				'family' => $font_item['family'],
				'group' => $translated_google_font
			);
		}

		return $fonts;
	}

	public function saveSkinCss($theme_id, $skin_id, $css)
	{
		$skin_file = $this->getThemeDataPath($theme_id) . $theme_id . '_' . $skin_id . '.css';

		return $this->saveFile($skin_file, $css);
	}

	public function convertRelative2AbsoluteUrlCss($css) {
		$font_base = $this->common->getStoreBase(0);

		$css = preg_replace('/url\(([^\)]+)\)/', "url({$font_base}$1)", $css);

		return $css;
	}

	/* Newsletter */
	public function getNewsletterLists($type, array $info)
	{
		$mail_service_path = dirname(__FILE__) . '/mail_service/' . $type . '.php';

		if (file_exists($mail_service_path))
		{
			require_once($mail_service_path);

			if ($type == 'icontact')
			{
				// Give the API your information
				iContactApi::getInstance()->setConfig(array(
					'appId' => $info['icontact_app_key'],
					'apiUsername' => $info['icontact_username'],
					'apiPassword' => $info['icontact_password'],
				));

				// Store the singleton
				$oiContact = iContactApi::getInstance();

				try
				{
					$lists = $oiContact->getLists();

					$results = array();

					foreach ($lists as $list)
					{
						$results[$list->listId] = $list->name;
					}
				}
				catch (Exception $e)
				{
					$errors = $oiContact->getErrors();
					throw new Exception(current($errors));
				}
			}
			else if ($type == 'mailchimp')
			{
				$api = new MCAPI($info['mailchimp_api_key']);

				$results = array();

				if($api)
				{
					$lists = $api->lists();

					if ($lists && !empty($lists['data']))
					{
						foreach ($lists['data'] as $list)
						{
							$results[$list['id']] = $list['name'];
						}
					}
				}
			}
		}
		else
		{
			throw new Exception(_t('This mail service is not available!'));
		}

		return $results;
	}

	public function readFile($path)
	{
		return file_get_contents($path);
	}

	public function saveFile($path, $contents)
	{
		return file_put_contents($path, $contents);
	}

	public function decode($data) {
		if (is_array($data)) {
			foreach ($data as $key => $value) {
				unset($data[$key]);

				$data[$this->decode($key)] = $this->decode($value);
			}
		} else {
			$data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');
		}

		return $data;
	}
}