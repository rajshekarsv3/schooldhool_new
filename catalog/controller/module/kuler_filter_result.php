<?php
/**
 * Class ControllerModuleKulerFilterResult
 */
class ControllerModuleKulerFilterResult extends Controller
{
	public function index()
	{
		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
		$this->load->model('module/kuler_filter');
		$this->load->model('tool/image');

		/* @var $model ModelModuleKulerFilter */
		$model = $this->model_module_kuler_filter;

		$this->getLanguages();

		// Request
		$request = $this->request->get;
		unset($request['page']);

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

		// Resolve Price Filter
		$this->load->library('currency');

		$currency_code = isset($this->request->get['currency_code']) ? $this->request->get['currency_code'] : $this->config->get('config_currency');
		$default_currency_code = $this->config->get('config_currency');

		if (!empty($this->request->get['price_min']))
		{
			$conditions['price_min'] = $this->currency->convert($this->request->get['price_min'], $currency_code, $default_currency_code);
		}

		if (!empty($this->request->get['price_max']))
		{
			$conditions['price_max'] = $this->currency->convert($this->request->get['price_max'], $currency_code, $default_currency_code);
		}

		// Count products
		$product_total = $model->countProducts($conditions);

		// Pagination
		$page       = isset($this->request->get['page']) ? $this->request->get['page'] : 1;
		$per_page   = isset($this->request->get['limit']) ? $this->request->get['limit'] : intval($this->config->get('config_catalog_limit'));

		$pagination             = new Pagination();
		$pagination->total      = $product_total;
		$pagination->page       = $page;
		$pagination->limit      = $per_page;
		$pagination->text       = $this->language->get('text_pagination');

		unset($request['route']);
		$page_url               = http_build_query($request + array('page' => '{page}'));
		$page_url               = str_replace('%7B', '{', $page_url);
		$page_url               = str_replace('%7D', '}', $page_url);
		$pagination->url        = $this->url->link('module/kuler_filter_result', $page_url);

		$this->data['pagination'] = $pagination->render();

		// Products
		$fetch_options = array(
			'page'      => $page,
			'per_page'  => $per_page,
			'order'     => '',
			'direction' => ''
		);

		if (isset($this->request->get['order']))
		{
			$fetch_options['order'] = $this->request->get['order'];
		}

		if (isset($this->request->get['direction']))
		{
			$fetch_options['direction'] = $this->request->get['direction'];
		}

		$products = $model->getProducts($conditions, $fetch_options);

		foreach ($products as &$product)
		{
			$product = $model->prepareProduct($product, array(
				'image_width'       => $this->config->get('config_image_product_width'),
				'image_height'      => $this->config->get('config_image_product_height'),
				'description_text'  => 100
			));
		}

		$this->data['products'] = $products;

		// Breadcrumbs
		$this->data['breadcrumbs'] = array();
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),
       		'separator' => false
   		);
		$this->data['breadcrumbs'][] = array(
			'text'      => 'Kuler Filter',
			'href'      => $this->url->link('module/kuler_filter_result', http_build_query($request)),
			'separator' => $this->language->get('text_separator')
		);

		// Fake vars for category layout
		$this->data['thumb']        = '';
		$this->data['categories']   = '';
		$this->data['description']  = '';

		// Sort options
		$sort_filter = $this->request->get;
		unset($sort_filter['order'], $sort_filter['direction'], $sort_filter['route']);

		$this->data['sorts'] = array();

		$this->data['sorts'][] = array(
			'text'  => $this->language->get('text_default'),
			'value' => 'sort_order-ASC',
			'href'  => $this->url->link('module/kuler_filter_result', http_build_query($sort_filter + array('order' => 'sort_order', 'direction' => 'ASC')))
		);

		$this->data['sorts'][] = array(
			'text'  => $this->language->get('text_name_asc'),
			'value' => 'name-ASC',
			'href'  => $this->url->link('module/kuler_filter_result', http_build_query($sort_filter + array('order' => 'name', 'direction' => 'ASC')))
		);

		$this->data['sorts'][] = array(
			'text'  => $this->language->get('text_name_desc'),
			'value' => 'name-DESC',
			'href'  => $this->url->link('module/kuler_filter_result', http_build_query($sort_filter + array('order' => 'name', 'direction' => 'DESC')))
		);

		$this->data['sorts'][] = array(
			'text'  => $this->language->get('text_price_asc'),
			'value' => 'price-ASC',
			'href'  => $this->url->link('module/kuler_filter_result', http_build_query($sort_filter + array('order' => 'price', 'direction' => 'ASC')))
		);

		$this->data['sorts'][] = array(
			'text'  => $this->language->get('text_price_desc'),
			'value' => 'price-DESC',
			'href'  => $this->url->link('module/kuler_filter_result', http_build_query($sort_filter + array('order' => 'price', 'direction' => 'DESC')))
		);

		if ($this->config->get('config_review_status')) {
			$this->data['sorts'][] = array(
				'text'  => $this->language->get('text_rating_desc'),
				'value' => 'rating-DESC',
				'href'  => $this->url->link('module/kuler_filter_result', http_build_query($sort_filter + array('order' => 'rating', 'direction' => 'DESC')))
			);

			$this->data['sorts'][] = array(
				'text'  => $this->language->get('text_rating_asc'),
				'value' => 'rating-ASC',
				'href'  => $this->url->link('module/kuler_filter_result', http_build_query($sort_filter + array('order' => 'rating', 'direction' => 'ASC')))
			);
		}

		$this->data['sorts'][] = array(
			'text'  => $this->language->get('text_model_asc'),
			'value' => 'model-ASC',
			'href'  => $this->url->link('module/kuler_filter_result', http_build_query($sort_filter + array('order' => 'model', 'direction' => 'ASC')))
		);

		$this->data['sorts'][] = array(
			'text'  => $this->language->get('text_model_desc'),
			'value' => 'model-DESC',
			'href'  => $this->url->link('module/kuler_filter_result', http_build_query($sort_filter + array('order' => 'model', 'direction' => 'DESC')))
		);

		$this->data['sort']     = $fetch_options['order'];
		$this->data['order']    = $fetch_options['direction'];

		// Limit
		$limit_filter = $this->request->get;
		unset($limit_filter['limit']);

		$limits = array_unique(array($this->config->get('config_catalog_limit'), 25, 50, 75, 100));

		sort($limits);

		foreach($limits as $limit)
		{
			$this->data['limits'][] = array(
				'text'  => $limit,
				'value' => $limit,
				'href'  => $this->url->link('module/kuler_filter_result', http_build_query($limit_filter + array('limit' => $limit)))
			);
		}

		$this->data['limit'] = $fetch_options['per_page'];

		// Page
		$this->document->setTitle($this->language->get('heading_title_result'));

		// View
        $this->document->addScript('catalog/view/javascript/jquery/jquery.total-storage.min.js');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/category.tpl'))
        {
			$this->template = $this->config->get('config_template') . '/template/product/category.tpl';
		}
        else
        {
			$this->template = 'default/template/product/category.tpl';
		}

		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
		);

		$output = $this->render();
		$output = str_replace('category-info', 'category-info kuler-filter-result', $output);

		$this->response->setOutput($output);
  	}
	
	private function getLanguages()
	{
		$this->language->load('product/category');
        $this->language->load('module/kuler_filter');

        $this->data['heading_title'] = $this->language->get('heading_title_result');
		
		$this->data['text_refine'] = $this->language->get('text_refine');
		$this->data['text_empty'] = $this->language->get('text_empty');			
		$this->data['text_quantity'] = $this->language->get('text_quantity');
		$this->data['text_manufacturer'] = $this->language->get('text_manufacturer');
		$this->data['text_model'] = $this->language->get('text_model');
		$this->data['text_price'] = $this->language->get('text_price');
		$this->data['text_tax'] = $this->language->get('text_tax');
		$this->data['text_points'] = $this->language->get('text_points');
		$this->data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));
		$this->data['text_display'] = $this->language->get('text_display');
		$this->data['text_list'] = $this->language->get('text_list');
		$this->data['text_grid'] = $this->language->get('text_grid');
		$this->data['text_sort'] = $this->language->get('text_sort');
		$this->data['text_limit'] = $this->language->get('text_limit');

		$this->data['button_cart'] = $this->language->get('button_cart');
		$this->data['button_wishlist'] = $this->language->get('button_wishlist');
		$this->data['button_compare'] = $this->language->get('button_compare');
		$this->data['button_continue'] = $this->language->get('button_continue');
	}
}
?>