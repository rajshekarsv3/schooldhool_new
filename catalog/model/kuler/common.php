<?php
if (!function_exists('_t'))
{
	function _t($text, $placeholder = '')
	{
		$args = func_get_args();
		return call_user_func_array(array('ModelKulerCommon', '__'), $args);
	}
}

class ModelKulerCommon extends Model
{
	const TYPE_FEATURED     = 'featured';
	const TYPE_LATEST       = 'latest';
	const TYPE_POPULAR      = 'popular';
	const TYPE_BEST_SELLER  = 'best_seller';
	const TYPE_SPECIAL      = 'special';

	static $VERSION = '2.0.5';

	public static $__ = array();

	public function __construct($registry)
	{
		parent::__construct($registry);

		$this->load->model('tool/image');
	}

	public static function loadTexts(array $texts)
	{
		self::$__ = array_merge(self::$__, $texts);
	}

	public static function __($text)
	{
		$args = func_get_args();
		$text = $args[0];
		array_shift($args);

		if (isset(self::$__[$text]))
		{
			array_unshift($args, self::$__[$text]);

			return call_user_func_array('sprintf', $args);
		}
		else
		{
			return $text;
		}
	}

	public static function getTexts()
	{
		return self::$__;
	}

	public function getKulerVersion()
	{
		return self::$VERSION;
	}

	public function isDevelopment()
	{
		if (isset($_COOKIE['kdev']) && $_COOKIE['kdev'] == 1)
		{
			return true;
		}

		return false;
	}

	public function translate($text)
	{
		if (is_array($text))
		{
			$config_language = $this->config->get('config_language');

			if (!empty($text[$config_language]))
			{
				$text = $text[$config_language];
			}
			else
			{
				// Use first value if config language is not available

				$text = current($text);
			}
		}

		return html_entity_decode($text, ENT_QUOTES, 'UTF-8');
	}

	public function getLanguages()
	{
		$this->load->model('localisation/language');

		return $this->model_localisation_language->getLanguages();
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

	public function getProducts(array $data) {
		$products = array();

		$limit = empty($data['limit']) ? 10 : intval($data['limit']);
		$store_id = intval($this->config->get('config_store_id'));

		if ($data['type'] == self::TYPE_LATEST || $data['type'] == self::TYPE_POPULAR) {
			// join
			$join = array(" LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)");

			if (!empty($data['category_id'])) {
				$join[] = " LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)";
			}

			$join = implode('', $join);

			// where
			$where = "p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = $store_id";

			if (!empty($data['category_id']))
			{
				$where .= " AND category_id = " . intval($data['category_id']);
			}

			// order by
			$order_by = '';

			if ($data['type'] == self::TYPE_LATEST) {
				$order_by = 'p.date_added DESC';
			} else if ($data['type'] == self::TYPE_POPULAR) {
				$order_by = 'p.viewed DESC, p.date_added DESC';
			}

			// limit clause
			$limit_clause = ' ';
			if (!empty($data['limit'])) {
				$limit_clause = " LIMIT $limit";
			}

			$query = $this->db->query("
				SELECT p.product_id
				FROM " . DB_PREFIX . "product p
				$join
				WHERE $where
				ORDER BY $order_by" .
				$limit_clause
			);

			$this->load->model('catalog/product');

			foreach ($query->rows as $row) {
				$products[] = $this->getProductDeal($row['product_id']);
			}
		} else if ($data['type'] == self::TYPE_FEATURED) {
			if (!empty($data['product_ids'])) {
				foreach ($data['product_ids'] as $product_id) {
					$product = $this->getProductDeal($product_id);

					if ($product) {
						$products[] = $product;
					}
				}
			}
		}

		return $products;
	}
	public function getProductDeal($product_id) {
		if ($this->customer->isLogged()) {
			$customer_group_id = $this->customer->getCustomerGroupId();
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}

		$query = $this->db->query("SELECT DISTINCT *, pd.name AS name, p.image, m.name AS manufacturer, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$customer_group_id . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$customer_group_id . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special,(SELECT ps.date_start FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$customer_group_id . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS date_start ,(SELECT ps.date_end FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$customer_group_id . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS date_end, (SELECT points FROM " . DB_PREFIX . "product_reward pr WHERE pr.product_id = p.product_id AND customer_group_id = '" . (int)$customer_group_id . "') AS reward, (SELECT ss.name FROM " . DB_PREFIX . "stock_status ss WHERE ss.stock_status_id = p.stock_status_id AND ss.language_id = '" . (int)$this->config->get('config_language_id') . "') AS stock_status, (SELECT wcd.unit FROM " . DB_PREFIX . "weight_class_description wcd WHERE p.weight_class_id = wcd.weight_class_id AND wcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS weight_class, (SELECT lcd.unit FROM " . DB_PREFIX . "length_class_description lcd WHERE p.length_class_id = lcd.length_class_id AND lcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS length_class, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, (SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r2 WHERE r2.product_id = p.product_id AND r2.status = '1' GROUP BY r2.product_id) AS reviews, p.sort_order FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			return array(
				'product_id'       => $query->row['product_id'],
				'name'             => $query->row['name'],
				'description'      => $query->row['description'],
				'meta_description' => $query->row['meta_description'],
				'meta_keyword'     => $query->row['meta_keyword'],
				'tag'              => $query->row['tag'],
				'model'            => $query->row['model'],
				'sku'              => $query->row['sku'],
				'upc'              => $query->row['upc'],
				'ean'              => $query->row['ean'],
				'jan'              => $query->row['jan'],
				'isbn'             => $query->row['isbn'],
				'mpn'              => $query->row['mpn'],
				'location'         => $query->row['location'],
				'quantity'         => $query->row['quantity'],
				'stock_status'     => $query->row['stock_status'],
				'image'            => $query->row['image'],
				'manufacturer_id'  => $query->row['manufacturer_id'],
				'manufacturer'     => $query->row['manufacturer'],
				'price'            => ($query->row['discount'] ? $query->row['discount'] : $query->row['price']),
				'special'          => $query->row['special'],
				'reward'           => $query->row['reward'],
				'points'           => $query->row['points'],
				'tax_class_id'     => $query->row['tax_class_id'],
				'date_available'   => $query->row['date_available'],
				'weight'           => $query->row['weight'],
				'weight_class_id'  => $query->row['weight_class_id'],
				'length'           => $query->row['length'],
				'width'            => $query->row['width'],
				'height'           => $query->row['height'],
				'length_class_id'  => $query->row['length_class_id'],
				'subtract'         => $query->row['subtract'],
				'rating'           => round($query->row['rating']),
				'reviews'          => $query->row['reviews'] ? $query->row['reviews'] : 0,
				'minimum'          => $query->row['minimum'],
				'sort_order'       => $query->row['sort_order'],
				'status'           => $query->row['status'],
				'date_added'       => $query->row['date_added'],
				'date_modified'    => $query->row['date_modified'],
				'viewed'           => $query->row['viewed'],
				'date_start'       => $query->row['date_start'],
				'date_end'         => $query->row['date_end']
			);
		} else {
			return false;
		}
	}

	public function prepareProduct(array $product, array $setting)
	{
		if (empty($setting['product_image_width']))
		{
			$setting['product_image_width'] = 80;
		}

		if (empty($setting['product_image_height']))
		{
			$setting['product_image_height'] = 80;
		}

		if (!isset($setting['show_product_image']))
		{
			$setting['show_product_image'] = false;
		}

		if (empty($setting['product_description_limit']))
		{
			$setting['product_description_limit'] = 100;
		}

		$image = $product['image'] && $setting['show_product_image'] ? $this->model_tool_image->resize($product['image'], $setting['product_image_width'], $setting['product_image_height']) : false;

		if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price'))
		{
			$product['price'] = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')));
		}
		else
		{
			$product['price'] = false;
		}

		$special = (float)$product['special'] ? $this->currency->format($this->tax->calculate($product['special'], $product['tax_class_id'], $this->config->get('config_tax'))) : false;
		$rating = $this->config->get('config_review_status') ? $product['rating'] : false;

		$product_categories = $this->model_catalog_product->getCategories($product['product_id']);
		$first_category_id = isset($product_categories[0]) ? $product_categories[0]['category_id'] : 0;

		$product_data = array(
			'product_id' => $product['product_id'],
			'thumb'      => $image,
			'image' => $product['image'],
			'name'       => strip_tags(html_entity_decode($product['name'], ENT_QUOTES, 'UTF-8')),
			'description'	 => utf8_substr(strip_tags(html_entity_decode($product['description'], ENT_QUOTES, 'UTF-8')), 0, $setting['product_description_limit']) . '..',
			'price'      => $product['price'],
			'special' => $special,
			'rating'	 => $rating,
			'reviews'    => sprintf($this->language->get('text_reviews'), (int)$product['reviews']),
			'href'       => $this->url->link('product/product', 'product_id=' . $product['product_id']),
		);

		$product_data['date_start'] = !empty($product['date_start']) ? $product['date_start']: null;
		$product_data['date_end'] = !empty($product['date_end']) ? $product['date_end'] : null;

		return $product_data;
	}

	public function mapProductDisplayOptions(array $settings) {
		if (!empty($settings['show_product_name'])) {
			$settings['name']               = $settings['show_product_name'];
			$settings['deal_date']               = $settings['show_product_deal_date'];
			$settings['image']              = $settings['show_product_image'];
			$settings['description']        = $settings['show_product_description'];
			$settings['price']              = $settings['show_product_price'];
			$settings['rating']             = $settings['show_product_rating'];
			$settings['add']                = $settings['show_add_to_cart_button'];
			$settings['wishlist']           = $settings['show_wish_list_button'];
			$settings['compare']            = $settings['show_compare_button'];
			$settings['width']              = !empty($settings['product_image_width']) ? intval($settings['product_image_width']) : 100;
			$settings['height']             = !empty($settings['product_image_height']) ? intval($settings['product_image_height']) : 100;
			$settings['description_limit']  = !empty($settings['product_description_limit']) ? intval($settings['product_description_limit']) : 100;
		} else if (!empty($settings['name'])) {
			$settings['show_product_name']              = $settings['name'];
			$settings['show_product_deal_date']         = $settings['deal_date'];
			$settings['show_product_image']             = $settings['image'];
			$settings['show_product_description']       = $settings['description'];
			$settings['show_product_price']             = $settings['price'];
			$settings['show_add_to_cart_button']        = $settings['rating'];
			$settings['show_add_to_cart_button']        = $settings['add'];
			$settings['show_wish_list_button']          = $settings['wishlist'];
			$settings['show_compare_button']            = $settings['compare'];
			$settings['product_image_width']            = !empty($settings['width']) ? intval($settings['width']) : 100;
			$settings['product_image_height']           = !empty($settings['height']) ? intval($settings['height']) : 100;
			$settings['product_description_limit']      = !empty($settings['description_limit']) ? intval($settings['description_limit']) : 100;
		}

		$settings['products_per_row']               = !empty($settings['products_per_row']) ? intval($settings['products_per_row']) : 4;

		return $settings;
	}

	public function loadProductTemplate(array $setting, array $product, $type)
	{
		$template = new Template();
		$template->data = array(
			'setting'           => $setting,
			'button_cart'       => _t('button_cart'),
			'button_wishlist'   => _t('button_wishlist'),
			'button_compare'    => _t('button_compare')
		);

		$template->data['product'] = $product;

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . "/template/common/_{$type}_product.tpl"))
		{
			return $template->fetch($this->config->get('config_template') . "/template/common/_{$type}_product.tpl");
		}
		else
		{
			return $template->fetch("default/template/common/_{$type}_product.tpl");
		}
	}

	public function getRecursivePath($category_id)
	{
		static $categories;

		if (empty($categories))
		{
			$this->load->model('catalog/category');

			/* @var $category_model ModelCatalogCategory */
			$category_model = $this->model_catalog_category;

			$raw_categories = $category_model->getCategories();

			$categories = array();
			foreach ($raw_categories as $raw_category)
			{
				$categories[$raw_category['category_id']] = $raw_category['parent_id'];
			}
		}

		if (!isset($categories[$category_id]))
		{
			return '';
		}

		$path = $category_id;
		$parent_id = $categories[$category_id];

		while (true)
		{
			if (!$parent_id)
			{
				break;
			}

			$path = $parent_id . '_' . $path;
			$parent_id = $categories[$parent_id];
		}

		return $path;
	}

	public function sortByField($items, $field = 'sort_order')
	{
		if (!is_array($items))
		{
			return $items;
		}

		$sort_order = array();

		foreach ($items as $key => $value)
		{
			if (!empty($value[$field]))
			{
				$sort_order[$key] = $value[$field];
			}
			else
			{
				$sort_order[$key] = 0;
			}
		}

		array_multisort($sort_order, SORT_ASC, $items);

		return $items;
	}

	/**
	 * Check where theme belongs KulerThemes or not
	 * @param $theme_id string
	 * @return boolean
	 */
	public function isKulerTheme($theme_id)
	{
		if (file_exists(DIR_TEMPLATE . $theme_id . '/data/theme_options.php'))
		{
			return true;
		}

		return false;
	}
}