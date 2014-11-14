<?php
class ModelModuleKulerAdvancedHTML extends Model
{
	public function getProducts($conditions = array(), $fetch_options = array())
	{
		$join = '';
		$where = '';
		$limitClause = '';

		if (!empty($conditions['filter_name']))
		{
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