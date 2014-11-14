<?php  
class ControllerModuleKulerFilter extends Controller {
	protected $common;

	public function __construct($registry)
	{
		parent::__construct($registry);

		$this->load->model('kuler/common');
		$this->common = $this->model_kuler_common;
	}

	protected function index($setting) {
		if (!$this->common->isKulerTheme($this->config->get('config_template')))
		{
			return false;
		}

		static $module = 0;

		$this->load->model('module/kuler_filter');
		/* @var $model ModelModuleKulerFilter */
		$model = $this->model_module_kuler_filter;

		// Language
		$this->data['__'] = $this->language->load('module/kuler_filter');

		// Prepare settings
		$setting['attribute']           = !isset($setting['attribute']) || $setting['attribute'] ? true : false;
		$setting['option']              = !isset($setting['option']) || $setting['option'] ? true : false;

		if (!$setting['price_filter'])
		{
			unset($setting['price_min'], $setting['price_max']);
		}

		$get = $this->request->get;
		unset($get['route']);

		// Get products match the filter
		$product_conditions = $this->prepareFilter();

		if (empty($product_conditions))
		{
			$conditions = array();
		}
		else
		{
			$products = $model->getProducts($product_conditions);

			if (empty($products))
			{
				$conditions = array(
					'product_id' => array(0)
				);
			}
			else
			{
				$conditions = array(
					'product_id' => array_keys($products)
				);
			}
		}

		// Prepare categories
		if (isset($setting['category']))
		{
			$this->load->model('catalog/product');
			/* @var $product_model ModelCatalogProduct */
			$product_model = $this->model_catalog_product;

			$this->load->model('catalog/category');
			/* @var $category_model ModelCatalogCategory */
			$category_model = $this->model_catalog_category;

			if (empty($get['category_id']) && !empty($this->request->get['path'])){
				$paths = explode('_', $this->request->get['path']);

				$get['category_id'] = end($paths);
			}
			// Prepare selected category
			$selected_category_ids = !empty($get['category_id']) ? explode('!!', $get['category_id']) : array();
			$selected_categories = array();
			$request = $get;

			foreach ($selected_category_ids as $selected_category_id)
			{
				$request['category_id'] = $selected_category_ids;
				unset($request['category_id'][array_search($selected_category_id, $selected_category_ids)]);

				if (empty($request['category_id']))
				{
					unset($request['category_id']);
				}
				else
				{
					$request['category_id'] = implode('!!', $request['category_id']);
				}

				$selected_categories[$selected_category_id] = $this->url->link('module/kuler_filter_result', http_build_query($request));
			}

			$this->data['selected_category_ids'] = $selected_category_ids;
			$this->data['selected_categories'] = $selected_categories;

			// Prepare category conditions
			$category_conditions = empty($products) && !empty($selected_categories) ? array('category_id' => $selected_category_ids) : $conditions;

			//  Get raw categories
			$unfiltered_categories  = $category_model->getCategories(0);
			$filtered_categories    = $model->getCategories(array('parent_id' => 0) + $category_conditions);
			$categories             = array();

			// Prepare categories that match the filter
			foreach ($unfiltered_categories as &$unfiltered_category)
			{
				$filtered_children = $model->getCategories(array('parent_id' => $unfiltered_category['category_id']) + $category_conditions);

				if (isset($filtered_categories[$unfiltered_category['category_id']]))
				{
					$categories[$unfiltered_category['category_id']] = array(
						'category_id'   => $unfiltered_category['category_id'],
						'name'          => $unfiltered_category['name'],
						'prefix'        => '',
						'total_product' => $product_model->getTotalProducts(array('filter_category_id' => $unfiltered_category['category_id'])),
					);
				}

				foreach ($filtered_children as $filtered_child)
				{
					$categories[$filtered_child['category_id']] = array(
						'category_id'   => $filtered_child['category_id'],
						'name'          => '&nbsp;&nbsp;&nbsp;&nbsp;' . $filtered_child['name'],
						'total_product' => $product_model->getTotalProducts(array('filter_category_id' => $filtered_child['category_id'])),
					);
				}
			}

			$this->data['categories'] = $categories;
		}

		// Prepare manufacturers
		if ($setting['manufacture'])
		{
			// Get selected manufacturers
			$selected_manufacturer_ids = empty($this->request->get['manufacturer_id']) ? array() : explode('!!', $this->request->get['manufacturer_id']);
			$selected_manufacturers = array();
			$request = $get;

			foreach ($selected_manufacturer_ids as $selected_manufacturer_id)
			{
				$request['manufacturer_id'] = $selected_manufacturer_ids;
				unset($request['manufacturer_id'][array_search($selected_manufacturer_id, $selected_manufacturer_ids)]);

				if (empty($request['manufacturer_id']))
				{
					unset($request['manufacturer_id']);
				}
				else
				{
					$request['manufacturer_id'] = implode('!!', $request['manufacturer_id']);
				}

				$selected_manufacturers[$selected_manufacturer_id] = $this->url->link('module/kuler_filter_result', http_build_query($request));
			}

			$this->data['selected_manufacturer_ids'] = $selected_manufacturer_ids;
			$this->data['selected_manufacturers'] = $selected_manufacturers;

			// Prepare manufacturer conditions
			$manufacturer_conditions = empty($products) && !empty($selected_manufacturer_ids) ? array('manufacturer_id' => $selected_manufacturer_ids) : $conditions;

			// Get manufacturers
			$manufacturers = $model->getManufacturers($manufacturer_conditions);
			foreach ($manufacturers as &$manufacturer)
			{
				$manufacturer['total_product'] = $product_model->getTotalProducts(array('filter_manufacturer_id' => $manufacturer['manufacturer_id']));
			}

			$this->data['manufacturers'] = $manufacturers;
		}

		// Prepare attributes
		if ($setting['attribute'])
		{
			// Prepare selected attributes
			$selected_attributes = array();
			$selected_attribute_values = empty($this->request->get['attribute_id']) ? array() : explode('!!', $this->request->get['attribute_id']);
			$request = $get;

			foreach ($selected_attribute_values as $selected_attribute_value)
			{
				$request['attribute_id'] = $selected_attribute_values;
				unset($request['attribute_id'][array_search($selected_attribute_value, $selected_attribute_values)]);

				if (empty($request['attribute_id']))
				{
					unset($request['attribute_id']);
				}
				else
				{
					$request['attribute_id'] = implode('!!', $request['attribute_id']);
				}

				list ($attribute_id, $value) = explode('!', $selected_attribute_value);

				if (!empty($selected_attributes[$attribute_id]))
				{
					$selected_attributes[$attribute_id] = array();
				}

				$selected_attributes[$attribute_id][] = array(
					'value' => $value,
					'link'  => $this->url->link('module/kuler_filter_result', http_build_query($request))
				);
			}

			$this->data['selected_attributes'] = $selected_attributes;

			// Prepare attribute conditions
			$attribute_conditions = empty($products) && !empty($selected_attribute_values) ? array('attribute_id' => array_keys($selected_attributes)) : $conditions;

			// Get attributes
			$attr_conditions = array();

			if (!empty($setting['exclude_attr_group_id']))
			{
				$attr_conditions['exclude_attr_group_id'] = $setting['exclude_attr_group_id'];
			}

			if (!empty($setting['exclude_attr_id']))
			{
				$attr_conditions['exclude_attr_id'] = $setting['exclude_attr_id'];
			}

			$attributes             = $model->getAttributes($attr_conditions);
			$product_attributes     = $model->getProductAttributes($attribute_conditions);

			$this->data['attributes'] = $model->groupProductAttributesByAttributes($product_attributes, $attributes);
		}

		// Prepare options
        if ($setting['option'])
        {
	        // Get selected options
	        $selected_options = array();
	        $selected_option_values = empty($this->request->get['option_value']) ? array() : explode('!!', $this->request->get['option_value']);
	        $request = $get;

	        // Option value
	        foreach ($selected_option_values as $selected_option_value)
	        {
		        $request['option_value'] = $selected_option_values;
		        unset($request['option_value'][array_search($selected_option_value, $selected_option_values)]);

		        if (empty($request['option_value']))
		        {
			        unset($request['option_value']);
		        }
		        else
		        {
			        $request['option_value'] = implode('!!', $request['option_value']);
		        }

		        list ($option_id, $value) = explode('!', $selected_option_value);

		        if (empty($selected_options[$option_id]))
		        {
			        $selected_options[$option_id] = array();
		        }

		        $selected_options[$option_id][] = array(
			        'value' => $value,
			        'link'  => $this->url->link('module/kuler_filter_result', http_build_query($request))
		        );
	        }

	        // Option value id
	        $selected_option_values = empty($this->request->get['option_value_id']) ? array() : explode('!!', $this->request->get['option_value_id']);
	        $request = $get;
	        foreach ($selected_option_values as $selected_option_value)
	        {
		        $request['option_value_id'] = $selected_option_values;
		        unset($request['option_value_id'][array_search($selected_option_value, $selected_option_values)]);

		        if (empty($request['option_value_id']))
		        {
			        unset($request['option_value_id']);
		        }
		        else
		        {
			        $request['option_value_id'] = implode('!!', $request['option_value_id']);
		        }

		        list ($option_id, $value) = explode('!', $selected_option_value);

		        if (empty($selected_options[$option_id]))
		        {
			        $selected_options[$option_id] = array();
		        }

		        $selected_options[$option_id][] = array(
			        'value' => $value,
			        'link'  => $this->url->link('module/kuler_filter_result', http_build_query($request))
		        );
	        }

	        $this->data['selected_options'] = $selected_options;

	        // Prepare option conditions
	        $option_conditions = empty($products) && !empty($selected_options) ? array('option_id' => array_keys($selected_options)) : $conditions;

	        if (!empty($setting['exclude_opt_id']))
	        {
		        $option_conditions['exclude_opt_id'] = $setting['exclude_opt_id'];
	        }

	        if (!empty($setting['exclude_opt_value_id']))
	        {
		        $option_conditions['exclude_opt_value_id'] = $setting['exclude_opt_value_id'];
	        }

	        // Get options
            $this->data['options'] = $model->getProductOptions($option_conditions);
        }

		// Prepare prices
		if($setting['price_filter'])
		{
			$this->data['price_min'] = isset($this->request->get['price_min']) ? $this->request->get['price_min'] : $setting['price_min'];
			if ($this->data['price_min'])
			{
				$filter['price_min'] = $this->data['price_min'];
			}

			$this->data['price_max'] = isset($this->request->get['price_max']) ? $this->request->get['price_max'] : $setting['price_max'];
			if ($this->data['price_max'])
			{
				$filter['price_max'] = $this->data['price_max'];
			}
		}

		$this->data['link'] = $this->url->link('module/kuler_filter_result');

		$this->data['module_title']     = $this->translate($setting['title'], $this->config->get('config_language_id'));
		$this->data['show_title']       = $setting['show_title'];
		$this->data['setting']          = $setting;

		$this->data['module'] = $module++;

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/kuler_filter.phtml'))
		{
			$this->template = $this->config->get('config_template') . '/template/module/kuler_filter.phtml';
		}
		else
		{
			$this->template = 'default/template/module/kuler_filter.phtml';
		}

		$this->render();
	}

	private function prepareFilter()
	{
		// Prepare filter data
		$conditions = array();

		if (!empty($this->request->get['category_id']))
		{
			$conditions['category_id'] = explode('!!', $this->request->get['category_id']);
		}

		if (!empty($this->request->get['manufacturer_id']))
		{
			$conditions['manufacturer_id'] = explode('!!', $this->request->get['manufacturer_id']);
		}

		// Resolve filter format: {filter_key}={id}!{value}!!{id}!{value}

		// Resolve attribute filter
		if (!empty($this->request->get['attribute_id']))
		{
			$conditions['attribute_id'] = explode('!!', $this->request->get['attribute_id']);

			foreach ($conditions['attribute_id'] as &$attribute)
			{
				$attribute = explode('!', $attribute);
			}
		}

		// Resolve option value filter
		if (!empty($this->request->get['option_value']))
		{
			$conditions['option_value'] = explode('!!', $this->request->get['option_value']);

			foreach ($conditions['option_value'] as &$option_value)
			{
				$option_value = explode('!', $option_value);
			}
		}

		// Resolve option value id
		if (!empty($this->request->get['option_value_id']))
		{
			$conditions['option_value_id'] = explode('!!', $this->request->get['option_value_id']);

			foreach ($conditions['option_value_id'] as &$option)
			{
				$option = explode('!', $option);
			}
		}

		return $conditions;
	}

    private function translate($texts, $language_id)
    {
        if (is_array($texts))
        {
            $first = current($texts);

            if (is_string($first))
            {
                $texts = empty($texts[$language_id]) ? $first : $texts[$language_id];
            }
            else if (is_array($texts))
            {
                if (!isset($texts[$language_id]))
                {
                    $texts[$language_id] = array();
                }

                foreach ($first as $key => $value)
                {
                    if (empty($texts[$language_id][$key]))
                    {
                        $texts[$language_id][$key] = $value;
                    }
                }
            }
        }

        return $texts;
    }
}
?>