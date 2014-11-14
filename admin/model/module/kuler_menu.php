<?php

class ModelModuleKulerMenu extends Model
{
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

		if ($category_ids && $store_category_ids)
		{
			for ($i = 0; $i < count($categories); $i++)
			{
				if (in_array($categories[$i]['category_id'], $category_ids) && in_array($categories[$i]['category_id'], $store_category_ids))
				{
					$results[] = $categories[$i];
				}
			}
		}

		return $results;
	}


	public function getProducts($conditions = array(), $fetch_options = array())
	{
		$join = '';
		$where = '';
		$limitClause = '';

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
}
