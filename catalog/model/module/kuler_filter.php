<?php

class ModelModuleKulerFilter extends Model
{
	/* PRODUCT ATTRIBUTE */
	public function getAttributes(array $conditions = array())
	{
		$config_language_id = intval($this->config->get('config_language_id'));

		$sql_conditions = array(
			'ad.language_id = ' . $config_language_id,
			'pa.language_id = '. $config_language_id,
			'p.status = 1'
		);

		// Exclude attribute groups
		if (!empty($conditions['exclude_attr_group_id']))
		{
			$conditions['exclude_attr_group_id'] = $this->escapeIds($conditions['exclude_attr_group_id']);

			$sql_conditions[] = 'a.attribute_group_id NOT IN ('. implode(',', $conditions['exclude_attr_group_id']) .')';
		}

		// Exclude attributes
		if (!empty($conditions['exclude_attr_id']))
		{

			$conditions['exclude_attr_id'] = $this->escapeIds($conditions['exclude_attr_id']);

			$sql_conditions[] = 'a.attribute_id NOT IN ('. implode(',', $conditions['exclude_attr_id']) .')';
		}

		$where = implode(' AND ', $sql_conditions);

		$query = $this->db->query("
			SELECT a.attribute_id, a.sort_order, ad.name, COUNT(pa.product_id) AS total_product
			FROM ". DB_PREFIX ."attribute a
			LEFT JOIN ". DB_PREFIX ."attribute_description ad
				ON (a.attribute_id = ad.attribute_id)
			INNER JOIN ". DB_PREFIX ."product_attribute pa
				ON (a.attribute_id = pa.attribute_id)
			INNER JOIN ". DB_PREFIX ."product p
				ON (pa.product_id = p.product_id)
			WHERE $where
			GROUP BY a.attribute_id
			ORDER BY a.sort_order ASC
		");

		// Prepare by id
		$results = array();
		foreach ($query->rows as $row)
		{
			$results[$row['attribute_id']] = $row;
		}

		return $results;
	}

	public function getProductAttributes(array $conditions)
	{
		$where = $this->prepareProductAttributeConditions($conditions);

		$query = $this->db->query("
			SELECT *, COUNT(pa.product_id) AS total_product
			FROM ". DB_PREFIX ."product_attribute pa
			INNER JOIN ". DB_PREFIX ."product p
				ON (pa.product_id = p.product_id)
			WHERE $where
			GROUP BY pa.attribute_id, pa.text
			ORDER BY pa.text ASC
		");

		return $query->rows;
	}

	public function prepareProductAttributeConditions(array $conditions)
	{
		$config_language_id = intval($this->config->get('config_language_id'));

		$sql_conditions = array(
			"pa.language_id = $config_language_id",
			'p.status = 1'
		);

		if (!empty($conditions['product_id']))
		{
			if (is_array($conditions['product_id']))
			{
				$conditions['product_id'] = $this->escapeIds($conditions['product_id']);

				$sql_conditions[] = 'pa.product_id IN (' . implode(', ', $conditions['product_id']) . ')';
			}
		}

		if (!empty($conditions['attribute_id']))
		{
			if (is_array($conditions['attribute_id']))
			{
				$conditions['attribute_id'] = $this->escapeIds($conditions['attribute_id']);

				$sql_conditions[] = 'pa.attribute_id IN (' . implode(',', $conditions['attribute_id']) . ')';
			}
		}

		return implode(' AND ', $sql_conditions);
	}

	public function groupProductAttributesByAttributes(array $product_attributes, array $attributes)
	{
		$results = array();

		foreach ($product_attributes as $product_attribute)
		{
			if (empty($attributes[$product_attribute['attribute_id']]))
			{
				continue;
			}

			if (!isset($results[$product_attribute['attribute_id']]))
			{
				$results[$product_attribute['attribute_id']] = array(
					'attribute_id'  => $product_attribute['attribute_id'],
					'name'          => $attributes[$product_attribute['attribute_id']]['name'],
					'total_product' => $attributes[$product_attribute['attribute_id']]['total_product'],
					'values'        => array()
				);
			}

			$results[$product_attribute['attribute_id']]['values'][$product_attribute['text']] = array(
				'value'         => $product_attribute['text'],
				'total_product' => $product_attribute['total_product']
			);
		}

		return $results;
	}
	/* end PRODUCT ATTRIBUTE */

	/* PRODUCT OPTIONS */
	public function getProductOptions(array $conditions = array())
	{
		$config_language_id = $this->config->get('config_language_id');

		$opt_sql_conditions = array(
			'p.status = 1'
		);

		// Exclude options
		if (!empty($conditions['exclude_opt_id']))
		{
			$conditions['exclude_opt_id'] = $this->escapeIds($conditions['exclude_opt_id']);

			$opt_sql_conditions[] = 'o.option_id NOT IN ('. implode(',', $conditions['exclude_opt_id']) .')';
		}

		$opt_where = empty($opt_sql_conditions) ? '' : 'WHERE ' . implode(' AND ', $opt_sql_conditions);
		
		// Get options
		$query = $this->db->query("
			SELECT o.option_id, o.type, od.name, o.sort_order, COUNT(po.product_id) AS total_product
			FROM `". DB_PREFIX ."option` o
			LEFT JOIN `". DB_PREFIX ."option_description` od
				ON (o.option_id = od.option_id AND od.language_id = $config_language_id)
			INNER JOIN `". DB_PREFIX ."product_option` po
				 ON (o.option_id = po.option_id)
			INNER JOIN `". DB_PREFIX ."product` p
				ON (po.product_id = p.product_id)
			$opt_where
			GROUP BY o.option_id
			ORDER BY o.sort_order ASC
		");

		$options = array();

		foreach ($query->rows as $row)
		{
			$options[$row['option_id']] = $row;
		}

		$opt_value_sql_conditions = array();

		// Exclude options
		if (!empty($conditions['exclude_opt_id']))
		{
			$conditions['exclude_opt_id'] = $this->escapeIds($conditions['exclude_opt_id']);

			$opt_value_sql_conditions[] = 'ov.option_id NOT IN ('. implode(',', $conditions['exclude_opt_id']) .')';
		}

		// Exclude option values
		if (!empty($conditions['exclude_opt_value_id']))
		{
			$conditions['exclude_opt_value_id'] = $this->escapeIds($conditions['exclude_opt_value_id']);

			$opt_value_sql_conditions[] = 'ov.option_value_id NOT IN ('. implode(',', $conditions['exclude_opt_value_id']) .')';
		}

		$opt_value_where = empty($opt_value_sql_conditions) ? '' : 'WHERE ' . implode(' AND ', $opt_value_sql_conditions);

		// Get option values
		$query = $this->db->query("
			SELECT ov.option_value_id, ov.option_id, ovd.name, ov.sort_order
			FROM ". DB_PREFIX ."option_value ov
			LEFT JOIN ". DB_PREFIX ."option_value_description ovd
				ON (ov.option_value_id = ovd.option_value_id AND ovd.language_id = $config_language_id)
			$opt_value_where
			ORDER BY ov.sort_order ASC
		");

		$option_values = array();
		foreach ($query->rows as $row)
		{
			$option_values[$row['option_value_id']] = $row;
		}

		// Get product options

		// Prepare conditions and join tables
		$sql_conditions = array(
			'p.status = 1'
		);
		$join_tables = '';

		if (!empty($conditions['product_id']))
		{
			if (is_array($conditions['product_id']))
			{
				$conditions['product_id'] = $this->escapeIds($conditions['product_id']);

				$sql_conditions[] = 'po.product_id IN (' . implode(',', $conditions['product_id']) . ')';
			}
		}

		if (!empty($conditions['option_id']))
		{
			if (is_array($conditions['option_id']))
			{
				$conditions['option_id'] = $this->escapeIds($conditions['option_id']);

				$sql_conditions[] = 'po.option_id IN (' . implode(', ', $conditions['option_id']) . ')';
			}
		}

		$where = '1 = 1';
		if ($sql_conditions)
		{
			$where = implode(' AND ', $sql_conditions);
		}

		// Query
		$query = $this->db->query("
			SELECT po.product_option_id, po.option_id, po.option_value, pov.product_option_value_id, pov.option_value_id, COUNT(po.product_id) AS total_product
			FROM ". DB_PREFIX ."product_option po
			INNER JOIN ". DB_PREFIX ."product p
				ON (po.product_id = p.product_id)
			LEFT JOIN ". DB_PREFIX ."product_option_value pov
				ON (po.product_option_id = pov.product_option_id)
			$join_tables
			WHERE $where
			GROUP BY po.option_id, po.option_value, pov.option_value_id
		");

		// Prepare options
		$results = array();
		foreach ($query->rows as $row)
		{
			if (isset($options[$row['option_id']]) && isset($option_values[$row['option_value_id']]))
			{
				// Create new option
				if (empty($results[$row['option_id']]))
				{
					$results[$row['option_id']] = array(
						'option_id'     => $row['option_id'],
						'name'          => $options[$row['option_id']]['name'],
						'sort_order'    => $options[$row['option_id']]['sort_order'],
						'total_product' => $options[$row['option_id']]['total_product'],
						'values'        => array()
					);
				}

				// Option value is multiple
				if (!empty($row['option_value_id']))
				{
					$results[$row['option_id']]['values'][$row['option_value_id']] = array(
						'value'         => $row['option_value_id'],
						'name'          => $option_values[$row['option_value_id']]['name'],
						'sort_order'    => $option_values[$row['option_value_id']]['sort_order'],
						'total_product' => $row['total_product'],
						'type'          => 'multiple'
					);
				}
				// Option value is single
				else if (!empty($row['option_value']))
				{
					$results[$row['option_id']]['values'][$row['option_value']] = array(
						'value'         => $row['option_value'],
						'name'          => $row['option_value'],
						'sort_order'    => 1,
						'total_product' => $row['total_product'],
						'type'          => 'single'
					);
				}
			}
		}

		// Sort options
		foreach ($results as &$result)
		{
			if (empty($result['values']))
			{
				unset($result);
				continue;
			}

			$result['values'] = $this->multiSort($result['values']);

			// Preserve key after sort
			$values = array();

			foreach ($result['values'] as $value)
			{
				$values[$value['value']] = $value;
			}

			$result['values'] = $values;
		}

		$results = $this->multiSort($results);

		$new_results = array();
		foreach ($results as $tresult)
		{
			$new_results[$tresult['option_id']] = $tresult;
		}

		return $new_results;
	}
	/* end PRODUCT OPTIONS */

	/* CATEGORY */
	public function getCategories(array $conditions)
	{
		$join_tables = '';

		$sql_conditions = array(
			'cd.language_id = ' . intval($this->config->get('config_language_id')),
			'c2s.store_id = ' . intval($this->config->get('config_store_id')),
			'c.status = 1'
		);

		if (isset($conditions['parent_id']))
		{
			$sql_conditions[] = 'c.parent_id = ' . intval($conditions['parent_id']);
		}

		if (!empty($conditions['product_id']))
		{
			$join_tables = '
				INNER JOIN '. DB_PREFIX .'product_to_category p2c
					ON (c.category_id = p2c.category_id)
			';

			if (is_array($conditions['product_id']))
			{
				$conditions['product_id'] = $this->escapeIds($conditions['product_id']);

				$sql_conditions[] = 'p2c.product_id IN (' . implode(',', $conditions['product_id']) . ')';
			}
		}

		if (!empty($conditions['category_id']))
		{
			if (is_array($conditions['category_id']))
			{
				$conditions['category_id'] = $this->escapeIds($conditions['category_id']);

				$sql_conditions[] = 'c.category_id IN (' . implode(',', $conditions['category_id']) . ')';
			}
		}

		$where = implode(' AND ', $sql_conditions);

		$query = $this->db->query("
			SELECT DISTINCT c.category_id, cd.name
			FROM " . DB_PREFIX . "category c
			LEFT JOIN " . DB_PREFIX . "category_description cd
				ON (c.category_id = cd.category_id)
			LEFT JOIN " . DB_PREFIX . "category_to_store c2s
				ON (c.category_id = c2s.category_id)
			$join_tables
			WHERE $where
			ORDER BY c.sort_order, LCASE(cd.name)
		");

		$results = array();
		foreach ($query->rows as $row)
		{
			$results[$row['category_id']] = $row;
		}

		return $results;
	}
	/* end CATEGORY */

	/* MANUFACTURER */
	public function getManufacturers(array $conditions)
	{
		$join_tables = '';

		$sql_conditions = array(
			'm2s.store_id = ' . intval($this->config->get('config_store_id'))
		);

		if (!empty($conditions['product_id']))
		{
			$join_tables = '
				INNER JOIN '. DB_PREFIX .'product p
					ON (m.manufacturer_id = p.manufacturer_id)
			';

			if (is_array($conditions['product_id']))
			{
				$conditions['product_id'] = $this->escapeIds($conditions['product_id']);

				$sql_conditions[] = 'p.product_id IN (' . implode(', ', $conditions['product_id']) . ')';
			}
		}

		if (!empty($conditions['manufacturer_id']))
		{
			if (is_array($conditions['manufacturer_id']))
			{
				$conditions['manufacturer_id'] = $this->escapeIds($conditions['manufacturer_id']);

				$sql_conditions[] = 'm.manufacturer_id IN (' . implode(', ', $conditions['manufacturer_id']) . ')';
			}
		}

		$where = implode(' AND ',  $sql_conditions);

		$query = $this->db->query("
			SELECT DISTINCT m.manufacturer_id, m.name
			FROM ". DB_PREFIX ."manufacturer m
			INNER JOIN ". DB_PREFIX ."manufacturer_to_store m2s
				ON (m.manufacturer_id = m2s.manufacturer_id)
			$join_tables
			WHERE $where
			ORDER BY m.sort_order, LCASE(m.name)
		");

		$results = array();
		foreach ($query->rows as $row)
		{
			$results[$row['manufacturer_id']] = $row;
		}

		return $results;
	}
	/* end MANUFACTURER */

	/* PRODUCT */
	public function getProducts(array $conditions, array $fetch_options = array())
	{
		$join_options   = $this->prepareProductJoinOptions($conditions);
		$where          = $this->prepareProductConditions($conditions);
		$order_clause   = $this->prepareProductSortOrderOptions($fetch_options);
		$limit_clause   = $this->prepareProductLimitOptions($fetch_options);

		$query = $this->db->query("
			SELECT p.product_id,
			(SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating
			{$join_options['selected_fields']}
			FROM ". DB_PREFIX ."product p
			{$join_options['join_tables']}
			WHERE $where
			$order_clause
			$limit_clause
		");

		$products = array();
		foreach ($query->rows as $row)
		{
			$product = $this->model_catalog_product->getProduct($row['product_id']);

			if ($product)
			{
				$products[$row['product_id']] = $product;
			}
		}

		return $products;
	}

	public function countProducts(array $conditions, array $fetch_options = array())
	{
		$join_options   = $this->prepareProductJoinOptions($conditions);
		$where          = $this->prepareProductConditions($conditions);

		$query = $this->db->query("
			SELECT COUNT(p.product_id) AS total_product
			{$join_options['selected_fields']}
			FROM ". DB_PREFIX ."product p
			{$join_options['join_tables']}
			WHERE $where
		");

		return $query->row? intval($query->row['total_product']) : 0;
	}

	public function prepareProductJoinOptions(array $conditions)
	{
		$selected_fields    = '';
		$join_tables        = '
			INNER JOIN '. DB_PREFIX .'product_description pd
				ON (p.product_id = pd.product_id)
		';

		return array(
			'selected_fields'   => $selected_fields,
			'join_tables'       => $join_tables
		);
	}

	public function prepareProductConditions(array $conditions)
	{
		$config_language_id = intval($this->config->get('config_language_id'));

		$sql_conditions = array(
			'pd.language_id = ' . $config_language_id,
			'p.status = 1'
		);

		if (!empty($conditions['category_id']))
		{
			if (!is_array($conditions['category_id']))
			{
				$conditions['category_id'] = array($conditions['category_id']);
			}

			$category_conditions = array();
			foreach ($conditions['category_id'] as $category_id)
			{
				$category_conditions[] = '(p2c.category_id = ' . intval($category_id) . ')';
			}

			if ($category_conditions)
			{
				$category_where = implode(' OR ', $category_conditions);

				$sql_conditions[] = " (
					SELECT COUNT(*)
					FROM ". DB_PREFIX ."product_to_category p2c
					WHERE p.product_id = p2c.product_id AND ($category_where)
					) = ". count($category_conditions) ."
				";
			}
		}

		if (!empty($conditions['manufacturer_id']))
		{
			if (!is_array($conditions['manufacturer_id']))
			{
				$conditions['manufacturer_id'] = array($conditions['manufacturer_id']);
			}

			foreach ($conditions['manufacturer_id'] as $manufacturer_id)
			{
				$sql_conditions[] = 'p.manufacturer_id = ' . intval($manufacturer_id);
			}
		}

		if (!empty($conditions['attribute_id']))
		{
			if (!is_array($conditions['attribute_id']))
			{
				$conditions['attribute_id'] = array($conditions['attribute_id']);
			}

			$attribute_conditions = array();
			foreach ($conditions['attribute_id'] as $attribute)
			{
				list($attribute_id, $value) = $attribute;

				$attribute_id = intval($attribute_id);
				$value = $this->escapeString($value);

				$attribute_conditions[] = "( pa.attribute_id = $attribute_id AND text = $value )";
			}

			if ($attribute_conditions)
			{
				$attribute_where = implode(' OR ', $attribute_conditions);

				$sql_conditions[] = "(
                    SELECT COUNT(*)
                    FROM ". DB_PREFIX ."product_attribute pa
                    WHERE p.product_id = pa.product_id AND pa.language_id = $config_language_id
                    AND ( $attribute_where )
                ) = " . count($attribute_conditions);
			}
		}

		if (!empty($conditions['option_value']))
		{
			if (!is_array($conditions['option_value']))
			{
				$conditions['option_value'] = array($conditions['option_value']);
			}

			$option_conditions = array();

			foreach ($conditions['option_value'] as $option_value)
			{
				list($option_id, $value) = $option_value;
				$option_id = intval($option_id);
				$value = $this->escapeString($value);

				$option_conditions[] = "(po.option_id = $option_id AND po.option_value = $value)";
			}

			if ($option_conditions)
			{
				$option_where = implode(' OR ', $option_conditions);

				$sql_conditions[] = "(
	                SELECT COUNT(*)
	                FROM ". DB_PREFIX ."product_option po
	                WHERE p.product_id = po.product_id
	                AND ($option_where)
                ) = " . count($option_conditions);
			}
		}

		if (!empty($conditions['option_value_id']))
		{
			if (!is_array($conditions['option_value_id']))
			{
				$conditions['option_value_id'] = array($conditions['option_value_id']);
			}

			$option_conditions = array();
			foreach ($conditions['option_value_id'] as $option)
			{
				list($option_id, $option_value_id) = $option;

				$option_id = intval($option_id);
				$option_value_id = intval($option_value_id);

				$option_conditions[] = "(pov.option_id = $option_id AND pov.option_value_id = $option_value_id)";
			}

			if ($option_conditions)
			{
				$option_where = implode(' OR ', $option_conditions);

				$sql_conditions[] = "(
	                SELECT COUNT(*)
	                FROM ". DB_PREFIX ."product_option_value pov
	                WHERE  p.product_id = pov.product_id
	                AND ($option_where)
                ) = " . count($option_conditions);
			}
		}

		if (!empty($conditions['price_min']))
		{
			$sql_conditions[] = 'p.price >= ' . intval($conditions['price_min']);
		}

		if (!empty($conditions['price_max']))
		{
			$sql_conditions[] = 'p.price <= ' . intval($conditions['price_max']);
		}

		return $sql_conditions ? implode(' AND ', $sql_conditions) : '1 = 1';
	}

	public function prepareProductSortOrderOptions(array $fetch_options)
	{
		$order_by = '';

		if (isset($fetch_options['order']))
		{
			switch ($fetch_options['order'])
			{
				case 'name':
					$order_by .= 'pd.name';
					break;
				case 'price':
					$order_by .= 'p.price';
					break;
				case 'rating':
					$order_by .= 'rating';
					break;
				case 'model':
					$order_by .= 'p.model';
					break;
				case 'sort_order':
				default:
					$order_by .= 'p.sort_order';
			}

			if (isset($fetch_options['direction']))
			{
				$order_by .= ' ' . $this->db->escape($fetch_options['direction']);
			}
			else
			{
				$order_by .= ' ASC';
			}
		}

		return $order_by ? 'ORDER BY '. $order_by : '';
	}

	public function prepareProductLimitOptions(array $fetch_options)
	{
		$limit_clause = '';

		if (!empty($fetch_options['page']) && !empty($fetch_options['per_page']))
		{
			$offset = (intval($fetch_options['page']) - 1) * $fetch_options['per_page'];
			$limit = intval($fetch_options['per_page']);

			$limit_clause = "LIMIT $offset, $limit";
		}

		return $limit_clause;
	}

	public function prepareProduct(array $product, array $options)
	{
		if ($product['image'])
		{
			$image = $this->model_tool_image->resize($product['image'], $options['image_width'], $options['image_height']);
		}
		else
		{
			$image = false;
		}

		if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price'))
		{
			$price = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')));
		}
		else
		{
			$price = false;
		}

		if ((float)$product['special'])
		{
			$special = $this->currency->format($this->tax->calculate($product['special'], $product['tax_class_id'], $this->config->get('config_tax')));
		}
		else
		{
			$special = false;
		}

		if ($this->config->get('config_review_status'))
		{
			$rating = $product['rating'];
		}
		else
		{
			$rating = false;
		}

		if ($this->config->get('config_tax'))
		{
			$tax = $this->currency->format((float)$product['special'] ? $product['special'] : $product['price']);
		}
		else
		{
			$tax = false;
		}

		$product_categories = $this->model_catalog_product->getCategories($product['product_id']);
		$first_category_id = !empty($product_categories) ? $product_categories[0]['category_id'] : 0;

		$result = array(
			'product_id'    => $product['product_id'],
			'image'         => $product['image'],
			'thumb'   	    => $image,
			'name'    	    => $product['name'],
			'description'   => utf8_substr(strip_tags(html_entity_decode($product['description'], ENT_QUOTES, 'UTF-8')), 0, $options['description_text']) . '..',
			'price'   	    => $price,
			'special' 	    => $special,
			'rating'        => $rating,
			'reviews'       => sprintf($this->language->get('text_reviews'), (int)$product['reviews']),
			'tax'           => $tax,
			'href'    	    => $this->url->link('product/product', 'path=' . $this->getRecursivePath($first_category_id) .'&product_id=' . $product['product_id']),
		);

		return $result;
	}

	public function getRecursivePath($category_id, $cats = array())
	{
		static $categories;

		if (empty($categories))
		{
			if (!empty($cats))
			{
				$raw_categories = $cats;
			}
			else
			{
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'  AND c.status = '1' ORDER BY c.sort_order, LCASE(cd.name)");

				$raw_categories = $query->rows;
			}

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
	/* end PRODUCT */

	/* HELPER */
	public function escapeIds($ids)
	{
		if (!is_array($ids))
		{
			$ids = array($ids);
		}

		foreach ($ids as &$id)
		{
			$id = intval($id);
		}

		return $ids;
	}

	public function escapeString($str)
	{
		return "'" . $this->db->escape($str) . "'";
	}

	private function multiSort(array $items)
	{
		$sortOrder = array();
		foreach ($items as $key => $value)
		{
			$sortOrder[$key] = $value['sort_order'];
		}
		array_multisort($sortOrder, SORT_ASC, $items);

		return $items;
	}
	/* end HELPER */
}

