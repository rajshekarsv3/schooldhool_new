<?php
if (!class_exists('ControllerModuleKulerCp'))
{
	class ControllerModuleKulerCp extends Controller
	{
		/* @var ModelKulerCommon */
		protected $common;

		/* @var ModelKulerCp $model */
		protected $model;

		public function __construct($registry)
		{
			parent::__construct($registry);

			$this->load->model('kuler/common');
			$this->common = $this->model_kuler_common;

			$this->load->model('kuler/cp');
			$this->model = $this->model_kuler_cp;
		}

		public function startup()
		{
			if (isset($this->request->get['route']))
			{
				return $this->forward($this->request->get['route']);
			}
		}

		public function liveSearch()
		{
			$kuler = Kuler::getInstance();

			$json = array();

			if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model']) || isset($this->request->get['filter_category_id']))
			{
				$setting = array(
					'product_image_width' => $kuler->getSkinOption('product_image_width'),
					'product_image_height' => $kuler->getSkinOption('product_image_height'),
					'show_product_image' => $kuler->getSkinOption('show_product_image'),
					'show_product_name' => $kuler->getSkinOption('show_product_name'),
					'show_product_description' => $kuler->getSkinOption('show_product_description'),
					'show_product_price' => $kuler->getSkinOption('show_product_price'),
					'show_product_rating' => $kuler->getSkinOption('show_product_rating'),
					'show_add_to_cart_button' => $kuler->getSkinOption('show_add_to_cart_button'),
					'show_wish_list_button' => $kuler->getSkinOption('show_wish_list_button'),
					'show_compare_button' => $kuler->getSkinOption('show_compare_button'),
					'product_image_width' => $kuler->getSkinOption('product_image_width'),
					'product_image_height' => $kuler->getSkinOption('product_image_height'),
					'image' => $kuler->getSkinOption('show_product_image'),
					'name' => $kuler->getSkinOption('show_product_name'),
					'description' => $kuler->getSkinOption('show_product_description'),
					'price' => $kuler->getSkinOption('show_product_price'),
					'rating' => $kuler->getSkinOption('show_product_rating'),
					'add' => $kuler->getSkinOption('show_add_to_cart_button'),
					'wishlist' => $kuler->getSkinOption('show_wish_list_button'),
					'compare' => $kuler->getSkinOption('show_compare_button')
				);

				$page = isset($this->request->request['page']) ? $this->request->request['page'] : 1;
				$limit = $kuler->getSkinOption('search_result_limit');
				$start = ($page - 1) * $limit;

				$this->load->model('catalog/product');
				$this->load->model('tool/image');

				if (isset($this->request->get['filter_category_id']))
				{
					$filter_category_id = $this->request->get['filter_category_id'];
				}
				else
				{
					$filter_category_id = '';
				}

				if (isset($this->request->get['filter_name']))
				{
					$filter_name = $this->request->get['filter_name'];
					$filter_tag = $filter_name;
				}
				else
				{
					$filter_name = $filter_tag = '';
				}

				if (isset($this->request->get['filter_model']))
				{
					$filter_model = $this->request->get['filter_model'];
				}
				else
				{
					$filter_model = '';
				}

				if (isset($this->request->get['filter_manufacturer_id']))
				{
					$filter_manufacturer_id = $this->request->get['filter_manufacturer_id'];
				}
				else
				{
					$filter_manufacturer_id = '';
				}

				$data = array(
					'filter_category_id'    => $filter_category_id,
					'filter_sub_category'   => true,
					'filter_name'           => $filter_name,
					'filter_tag'            => $filter_tag,
					'filter_description'    => $kuler->getSkinOption('search_in_product_description'),
					'filter_manufacturer_id'=> $filter_manufacturer_id,
					'filter_model'          => $filter_model,
					'start'                 => $start,
					'limit'                 => $limit
				);

				// Get products
				$results = $this->model_catalog_product->getProducts($data);
				$json['products'] = array();
				foreach ($results as $result)
				{
					$product_data = $this->common->prepareProduct($result, $setting);
					$product_data['html'] = $this->common->loadProductTemplate($setting, $product_data, 'list');

					$json['products'][] = $product_data;
				}

				// Count products
				$productsCount = $this->model_catalog_product->getTotalProducts($data);
				$json['more'] = (($page - 1) * $limit + count($results)) < $productsCount ? 1 : 0;

				// Prepare response
				$json['status'] = 1;
			}

			$this->response->setOutput(json_encode($json));
		}

		public function quick_view()
		{
			$this->language->load('product/product');

			$this->data['breadcrumbs'] = array();

			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_home'),
				'href'      => $this->url->link('common/home'),
				'separator' => false
			);

			$this->load->model('catalog/category');

			if (isset($this->request->get['path'])) {
				$path = '';

				$parts = explode('_', (string)$this->request->get['path']);

				$category_id = (int)array_pop($parts);

				foreach ($parts as $path_id) {
					if (!$path) {
						$path = $path_id;
					} else {
						$path .= '_' . $path_id;
					}

					$category_info = $this->model_catalog_category->getCategory($path_id);

					if ($category_info) {
						$this->data['breadcrumbs'][] = array(
							'text'      => $category_info['name'],
							'href'      => $this->url->link('product/category', 'path=' . $path),
							'separator' => $this->language->get('text_separator')
						);
					}
				}

				// Set the last category breadcrumb
				$category_info = $this->model_catalog_category->getCategory($category_id);

				if ($category_info) {
					$url = '';

					if (isset($this->request->get['sort'])) {
						$url .= '&sort=' . $this->request->get['sort'];
					}

					if (isset($this->request->get['order'])) {
						$url .= '&order=' . $this->request->get['order'];
					}

					if (isset($this->request->get['page'])) {
						$url .= '&page=' . $this->request->get['page'];
					}

					if (isset($this->request->get['limit'])) {
						$url .= '&limit=' . $this->request->get['limit'];
					}

					$this->data['breadcrumbs'][] = array(
						'text'      => $category_info['name'],
						'href'      => $this->url->link('product/category', 'path=' . $this->request->get['path']),
						'separator' => $this->language->get('text_separator')
					);
				}
			}

			$this->load->model('catalog/manufacturer');

			if (isset($this->request->get['manufacturer_id'])) {
				$this->data['breadcrumbs'][] = array(
					'text'      => $this->language->get('text_brand'),
					'href'      => $this->url->link('product/manufacturer'),
					'separator' => $this->language->get('text_separator')
				);

				$url = '';

				if (isset($this->request->get['sort'])) {
					$url .= '&sort=' . $this->request->get['sort'];
				}

				if (isset($this->request->get['order'])) {
					$url .= '&order=' . $this->request->get['order'];
				}

				if (isset($this->request->get['page'])) {
					$url .= '&page=' . $this->request->get['page'];
				}

				if (isset($this->request->get['limit'])) {
					$url .= '&limit=' . $this->request->get['limit'];
				}

				$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($this->request->get['manufacturer_id']);

				if ($manufacturer_info) {
					$this->data['breadcrumbs'][] = array(
						'text'	    => $manufacturer_info['name'],
						'href'	    => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . $url),
						'separator' => $this->language->get('text_separator')
					);
				}
			}

			if (isset($this->request->get['search']) || isset($this->request->get['tag'])) {
				$url = '';

				if (isset($this->request->get['search'])) {
					$url .= '&search=' . $this->request->get['search'];
				}

				if (isset($this->request->get['tag'])) {
					$url .= '&tag=' . $this->request->get['tag'];
				}

				if (isset($this->request->get['description'])) {
					$url .= '&description=' . $this->request->get['description'];
				}

				if (isset($this->request->get['category_id'])) {
					$url .= '&category_id=' . $this->request->get['category_id'];
				}

				if (isset($this->request->get['sub_category'])) {
					$url .= '&sub_category=' . $this->request->get['sub_category'];
				}

				if (isset($this->request->get['sort'])) {
					$url .= '&sort=' . $this->request->get['sort'];
				}

				if (isset($this->request->get['order'])) {
					$url .= '&order=' . $this->request->get['order'];
				}

				if (isset($this->request->get['page'])) {
					$url .= '&page=' . $this->request->get['page'];
				}

				if (isset($this->request->get['limit'])) {
					$url .= '&limit=' . $this->request->get['limit'];
				}

				$this->data['breadcrumbs'][] = array(
					'text'      => $this->language->get('text_search'),
					'href'      => $this->url->link('product/search', $url),
					'separator' => $this->language->get('text_separator')
				);
			}

			if (isset($this->request->get['product_id'])) {
				$product_id = (int)$this->request->get['product_id'];
			} else {
				$product_id = 0;
			}

			$this->load->model('catalog/product');

			$product_info = $this->model_catalog_product->getProduct($product_id);

			$url = '';

			if (isset($this->request->get['path'])) {
				$url .= '&path=' . $this->request->get['path'];
			}

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['manufacturer_id'])) {
				$url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
			}

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$this->data['breadcrumbs'][] = array(
				'text'      => $product_info['name'],
				'href'      => $this->url->link('product/product', $url . '&product_id=' . $this->request->get['product_id']),
				'separator' => $this->language->get('text_separator')
			);

			$this->document->setTitle($product_info['name']);
			$this->document->setDescription($product_info['meta_description']);
			$this->document->setKeywords($product_info['meta_keyword']);
			$this->document->addLink($this->url->link('product/product', 'product_id=' . $this->request->get['product_id']), 'canonical');
			$this->document->addScript('catalog/view/javascript/jquery/tabs.js');

			$this->data['heading_title'] = $product_info['name'];

			$this->data['text_select'] = $this->language->get('text_select');
			$this->data['text_manufacturer'] = $this->language->get('text_manufacturer');
			$this->data['text_model'] = $this->language->get('text_model');
			$this->data['text_reward'] = $this->language->get('text_reward');
			$this->data['text_points'] = $this->language->get('text_points');
			$this->data['text_discount'] = $this->language->get('text_discount');
			$this->data['text_stock'] = $this->language->get('text_stock');
			$this->data['text_price'] = $this->language->get('text_price');
			$this->data['text_tax'] = $this->language->get('text_tax');
			$this->data['text_discount'] = $this->language->get('text_discount');
			$this->data['text_option'] = $this->language->get('text_option');
			$this->data['text_qty'] = $this->language->get('text_qty');
			$this->data['text_minimum'] = sprintf($this->language->get('text_minimum'), $product_info['minimum']);
			$this->data['text_or'] = $this->language->get('text_or');
			$this->data['text_write'] = $this->language->get('text_write');
			$this->data['text_note'] = $this->language->get('text_note');
			$this->data['text_share'] = $this->language->get('text_share');
			$this->data['text_wait'] = $this->language->get('text_wait');
			$this->data['text_tags'] = $this->language->get('text_tags');

			$this->data['entry_name'] = $this->language->get('entry_name');
			$this->data['entry_review'] = $this->language->get('entry_review');
			$this->data['entry_rating'] = $this->language->get('entry_rating');
			$this->data['entry_good'] = $this->language->get('entry_good');
			$this->data['entry_bad'] = $this->language->get('entry_bad');
			$this->data['entry_captcha'] = $this->language->get('entry_captcha');

			$this->data['button_cart'] = $this->language->get('button_cart');
			$this->data['button_wishlist'] = $this->language->get('button_wishlist');
			$this->data['button_compare'] = $this->language->get('button_compare');
			$this->data['button_upload'] = $this->language->get('button_upload');
			$this->data['button_continue'] = $this->language->get('button_continue');

			$this->load->model('catalog/review');

			$this->data['tab_description'] = $this->language->get('tab_description');
			$this->data['tab_attribute'] = $this->language->get('tab_attribute');
			$this->data['tab_review'] = sprintf($this->language->get('tab_review'), $product_info['reviews']);
			$this->data['tab_related'] = $this->language->get('tab_related');

			$this->data['product_id'] = $this->request->get['product_id'];
			$this->data['manufacturer'] = $product_info['manufacturer'];
			$this->data['manufacturers'] = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id']);
			$this->data['model'] = $product_info['model'];
			$this->data['reward'] = $product_info['reward'];
			$this->data['points'] = $product_info['points'];

			if ($product_info['quantity'] <= 0) {
				$this->data['stock'] = $product_info['stock_status'];
			} elseif ($this->config->get('config_stock_display')) {
				$this->data['stock'] = $product_info['quantity'];
			} else {
				$this->data['stock'] = $this->language->get('text_instock');
			}

			$this->load->model('tool/image');

			if ($product_info['image']) {
				$this->data['popup'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height'));
			} else {
				$this->data['popup'] = '';
			}

			if ($product_info['image']) {
				$this->data['thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'));
			} else {
				$this->data['thumb'] = '';
			}

			$this->data['images'] = array();

			$results = $this->model_catalog_product->getProductImages($this->request->get['product_id']);

			foreach ($results as $result) {
				$this->data['images'][] = array(
					'popup' => $this->model_tool_image->resize($result['image'], $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height')),
					'thumb' => $this->model_tool_image->resize($result['image'], $this->config->get('config_image_additional_width'), $this->config->get('config_image_additional_height'))
				);
			}

			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
				$this->data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
			} else {
				$this->data['price'] = false;
			}

			if ((float)$product_info['special']) {
				$this->data['special'] = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')));
			} else {
				$this->data['special'] = false;
			}

			if ($this->config->get('config_tax')) {
				$this->data['tax'] = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price']);
			} else {
				$this->data['tax'] = false;
			}

			$discounts = $this->model_catalog_product->getProductDiscounts($this->request->get['product_id']);

			$this->data['discounts'] = array();

			foreach ($discounts as $discount) {
				$this->data['discounts'][] = array(
					'quantity' => $discount['quantity'],
					'price'    => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], $this->config->get('config_tax')))
				);
			}

			$this->data['options'] = array();

			foreach ($this->model_catalog_product->getProductOptions($this->request->get['product_id']) as $option) {
				if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') {
					$option_value_data = array();

					foreach ($option['option_value'] as $option_value) {
						if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
							if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
								$price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
							} else {
								$price = false;
							}

							$option_value_data[] = array(
								'product_option_value_id' => $option_value['product_option_value_id'],
								'option_value_id'         => $option_value['option_value_id'],
								'name'                    => $option_value['name'],
								'image'                   => $this->model_tool_image->resize($option_value['image'], 50, 50),
								'price'                   => $price,
								'price_prefix'            => $option_value['price_prefix']
							);
						}
					}

					$this->data['options'][] = array(
						'product_option_id' => $option['product_option_id'],
						'option_id'         => $option['option_id'],
						'name'              => $option['name'],
						'type'              => $option['type'],
						'option_value'      => $option_value_data,
						'required'          => $option['required']
					);
				} elseif ($option['type'] == 'text' || $option['type'] == 'textarea' || $option['type'] == 'file' || $option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') {
					$this->data['options'][] = array(
						'product_option_id' => $option['product_option_id'],
						'option_id'         => $option['option_id'],
						'name'              => $option['name'],
						'type'              => $option['type'],
						'option_value'      => $option['option_value'],
						'required'          => $option['required']
					);
				}
			}

			if ($product_info['minimum']) {
				$this->data['minimum'] = $product_info['minimum'];
			} else {
				$this->data['minimum'] = 1;
			}

			$this->data['review_status'] = $this->config->get('config_review_status');
			$this->data['reviews'] = sprintf($this->language->get('text_reviews'), (int)$product_info['reviews']);
			$this->data['rating'] = (int)$product_info['rating'];
			$this->data['description'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');
			$this->data['attribute_groups'] = $this->model_catalog_product->getProductAttributes($this->request->get['product_id']);

			$this->data['products'] = array();

			$results = $this->model_catalog_product->getProductRelated($this->request->get['product_id']);

			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_related_width'), $this->config->get('config_image_related_height'));
				} else {
					$image = false;
				}

				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$price = false;
				}

				if ((float)$result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$special = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = (int)$result['rating'];
				} else {
					$rating = false;
				}

				$this->data['products'][] = array(
					'product_id' => $result['product_id'],
					'thumb'   	 => $image,
					'name'    	 => $result['name'],
					'price'   	 => $price,
					'special' 	 => $special,
					'rating'     => $rating,
					'reviews'    => sprintf($this->language->get('text_reviews'), (int)$result['reviews']),
					'href'    	 => $this->url->link('product/product', 'product_id=' . $result['product_id'])
				);
			}

			$this->data['tags'] = array();

			if ($product_info['tag']) {
				$tags = explode(',', $product_info['tag']);

				foreach ($tags as $tag) {
					$this->data['tags'][] = array(
						'tag'  => trim($tag),
						'href' => $this->url->link('product/search', 'tag=' . trim($tag))
					);
				}
			}

			$this->model_catalog_product->updateViewed($this->request->get['product_id']);

			if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1')))
			{
				$server = $this->config->get('config_ssl');
			}
			else
			{
				$server = $this->config->get('config_url');
			}

			$this->data['base'] = $server;

			$this->data['product_url'] = $this->url->link('product/product', 'product_id=' . $product_id);

			$this->template = $this->config->get('config_template') . '/template/product/quick_view.tpl';

			$this->response->setOutput($this->render());
		}

		public function subscribeNewsletter()
		{
			try
			{
				if ($this->request->server['REQUEST_METHOD'] != 'POST')
				{
					throw new Exception(_t('error_permission'));
				}

				$mail = $this->request->post['mail'];

				// TODO: Validate mail

				$this->model->subscribeNewsletter(array(
					'mail' => $mail
				));

				$kuler = Kuler::getInstance();

				$result = array(
					'status' => 1,
					'message' => $kuler->translate($kuler->getSkinOption('newsletter_success_message'))
				);
			}
			catch (Exception $e)
			{
				$this->response->addHeader('HTTP/1.1 500 Error');

				$result = array(
					'status' => 0,
					'message' => $e->getMessage()
				);
			}

			$this->response->setOutput(json_encode($result));
		}

		public function onePageCheckoutMethods()
		{
			$this->session->data['order_id'] = -1;

			$kuler = Kuler::getInstance();

			$results = array(
				'shipping_method' => $this->getShippingMethod(),
				'payment_method' => $this->getPaymentMethod(),
				'order_total' => $this->getOrderTotal()
			);

			$this->response->setOutput(json_encode($results));
		}

		public function applyCoupon()
		{
			ModelKulerCommon::loadTexts($this->language->load('checkout/cart'));

			try
			{
				if (!isset($this->request->post['coupon']))
				{
					throw new Exception('Error coupon');
				}

				$this->validateCoupon();

				$this->session->data['coupon'] = $this->request->post['coupon'];

				$results = array(
					'status' => 1,
					'message' => _t('text_coupon')
				);
			}
			catch (Exception $e)
			{
				$results = array(
					'status' => 0,
					'message' => $e->getMessage()
				);
			}

			$this->response->setOutput(json_encode($results));
		}

		public function applyVoucher()
		{
			ModelKulerCommon::loadTexts($this->language->load('checkout/cart'));

			try
			{
				if (!isset($this->request->post['voucher']))
				{
					throw new Exception('Error Voucher');
				}

				$this->validateVoucher();

				$this->session->data['voucher'] = $this->request->post['voucher'];

				$results = array(
					'status' => 1,
					'message' => $this->language->get('text_voucher')
				);
			}
			catch (Exception $e)
			{
				$results = array(
					'status' => 0,
					'message' => $e->getMessage()
				);
			}

			$this->response->setOutput(json_encode($results));
		}

		public function createAddress()
		{
			try
			{
				if ($this->request->server['REQUEST_METHOD'] != 'POST')
				{
					throw new Exception('Error');
				}

				$this->language->load('checkout/checkout');
				$this->load->model('account/customer');

				// Validate address
				$errors = array();

				if ((utf8_strlen($this->request->post['firstname']) < 1) || (utf8_strlen($this->request->post['firstname']) > 32))
				{
					$errors['firstname'] = $this->language->get('error_firstname');
				}

				if ((utf8_strlen($this->request->post['lastname']) < 1) || (utf8_strlen($this->request->post['lastname']) > 32))
				{
					$errors['lastname'] = $this->language->get('error_lastname');
				}

				if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['email']))
				{
					$errors['email'] = $this->language->get('error_email');
				}

				if ($this->model_account_customer->getTotalCustomersByEmail($this->request->post['email']))
				{
					$errors['warning'] = $this->language->get('error_exists');
				}

				if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32))
				{
					$errors['telephone'] = $this->language->get('error_telephone');
				}

				if ((utf8_strlen($this->request->post['address_1']) < 3) || (utf8_strlen($this->request->post['address_1']) > 128))
				{
					$errors['address_1'] = $this->language->get('error_address_1');
				}

				if ((utf8_strlen($this->request->post['city']) < 2) || (utf8_strlen($this->request->post['city']) > 128))
				{
					$errors['city'] = $this->language->get('error_city');
				}

				$this->load->model('localisation/country');

				$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

				if ($country_info && $country_info['postcode_required'] && (utf8_strlen($this->request->post['postcode']) < 2) || (utf8_strlen($this->request->post['postcode']) > 10))
				{
					$errors['postcode'] = $this->language->get('error_postcode');
				}

				if ($this->request->post['country_id'] == '')
				{
					$errors['country'] = $this->language->get('error_country');
				}

				if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '')
				{
					$errors['zone'] = $this->language->get('error_zone');
				}

				if ($errors)
				{
					throw new Exception('error_address');
				}

				if ($this->customer->isLogged())
				{
					$this->load->model('account/address');

					$address_id = $this->model_account_address->addAddress($this->request->post);
				}
				else
				{
					if (!isset($this->session->data['temp_addresses']))
					{
						$this->session->data['temp_addresses'] = array();
					}

					$address = $this->request->post;
					$address_id = 'temp-' + count($this->session->data['temp_addresses']) + 1;
					$address['address_id'] = $address_id;

					$this->load->model('localisation/country');
					$country_info = $this->model_localisation_country->getCountry($address['country_id']);

					$address['country'] = $country_info['name'];

					$this->load->model('localisation/zone');
					$zone = $this->model_localisation_zone->getZone($address['zone_id']);

					$address['zone'] = $zone['name'];

					$this->session->data['temp_addresses'][$address['address_id']] = $address;
				}

				$results = array(
					'status' => 1,
					'addresses' => Kuler::getInstance()->getAddresses(),
					'address_id' => $address_id
				);
			}
			catch (Exception $e)
			{
				$results = array(
					'status' => 0,
					'message' => $e->getMessage()
				);

				if ($e->getMessage() == 'error_address')
				{
					$results['error_fields'] = $errors;
				}
			}

			$this->response->setOutput(json_encode($results));
		}

		public function onePageCheckoutLogin()
		{
			try
			{
				ModelKulerCommon::loadTexts($this->language->load('account/login'));

				if ($this->request->server['REQUEST_METHOD'] != 'POST')
				{
					throw new Exception(_t('error'));
				}

				if (!$this->customer->login($this->request->post['email'], $this->request->post['password']))
				{
					throw new Exception(_t('error_login'));
				}

				$this->load->model('account/customer');

				$customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);

				if ($customer_info && !$customer_info['approved'])
				{
					throw new Exception(_t('error_approved'));
				}

				// Default Addresses
				$this->load->model('account/address');

				$address_info = $this->model_account_address->getAddress($this->customer->getAddressId());

				if ($address_info)
				{
					if ($this->config->get('config_tax_customer') == 'shipping')
					{
						$this->session->data['shipping_country_id'] = $address_info['country_id'];
						$this->session->data['shipping_zone_id'] = $address_info['zone_id'];
						$this->session->data['shipping_postcode'] = $address_info['postcode'];
					}

					if ($this->config->get('config_tax_customer') == 'payment')
					{
						$this->session->data['payment_country_id'] = $address_info['country_id'];
						$this->session->data['payment_zone_id'] = $address_info['zone_id'];
					}
				}
				else
				{
					unset($this->session->data['shipping_country_id']);
					unset($this->session->data['shipping_zone_id']);
					unset($this->session->data['shipping_postcode']);
					unset($this->session->data['payment_country_id']);
					unset($this->session->data['payment_zone_id']);
				}

				$result = array(
					'status' => 1,
					'redirect' => $this->url->link('checkout/checkout', '', 'SSL')
				);
			}
			catch (Exception $e)
			{
				$result = array(
					'status' => 0,
					'message' => $e->getMessage()
				);
			}

			$this->response->setOutput(json_encode($result));
		}

		public function onePageCheckoutValidate()
		{
			try
			{
				if ($this->request->server['REQUEST_METHOD'] != 'POST')
				{
					throw new Exception('Error');
				}

				ModelKulerCommon::loadTexts($this->language->load('module/kuler_cp'));

				$this->language->load('checkout/checkout');
				$this->load->model('account/customer');

				// Validate address
				$errors = array();

				if ((utf8_strlen($this->request->post['firstname']) < 1) || (utf8_strlen($this->request->post['firstname']) > 32))
				{
					$errors['firstname'] = $this->language->get('error_firstname');
				}

				if ((utf8_strlen($this->request->post['lastname']) < 1) || (utf8_strlen($this->request->post['lastname']) > 32))
				{
					$errors['lastname'] = $this->language->get('error_lastname');
				}


				if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['email']))
				{
					$errors['email'] = $this->language->get('error_email');
				}

				if (!$this->customer->isLogged() && !empty($this->request->post['create_new_account']))
				{
					if ($this->model_account_customer->getTotalCustomersByEmail($this->request->post['email']))
					{
						$errors['warning'] = $this->language->get('error_exists');
					}
				}

				if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32))
				{
					$errors['telephone'] = $this->language->get('error_telephone');
				}

				if ((utf8_strlen($this->request->post['address_1']) < 3) || (utf8_strlen($this->request->post['address_1']) > 128))
				{
					$errors['address_1'] = $this->language->get('error_address_1');
				}

				if ((utf8_strlen($this->request->post['city']) < 2) || (utf8_strlen($this->request->post['city']) > 128))
				{
					$errors['city'] = $this->language->get('error_city');
				}

				$this->load->model('localisation/country');

				$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

				if ($country_info && $country_info['postcode_required'] && (utf8_strlen($this->request->post['postcode']) < 2) || (utf8_strlen($this->request->post['postcode']) > 10))
				{
					$errors['postcode'] = $this->language->get('error_postcode');
				}

				if ($this->request->post['country_id'] == '')
				{
					$errors['country'] = $this->language->get('error_country');
				}

				if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '')
				{
					$errors['zone'] = $this->language->get('error_zone');
				}

				// Validate shipping & payment addresses
				if (empty($this->request->post['shipping_address_same']) && empty($this->request->post['shipping_address_id']))
				{
					$errors['shipping_address_id'] = _t('error_shipping_address');
				}

				if (empty($this->request->post['payment_address_same']) && empty($this->request->post['payment_address_id']))
				{
					$errors['payment_address_id'] = _t('error_payment_address');
				}

				// Validate shipping & payment method
				if (empty($this->request->post['shipping_method']))
				{
					$errors['shipping_method'] = _t('error_shipping_method');
				}

				if (empty($this->request->post['payment_method']))
				{
					$errors['payment_method'] = _t('error_payment_method');
				}

				// Order agree
				if (empty($this->request->post['order_agree']) && $this->config->get('config_checkout_id'))
				{
					$this->load->model('catalog/information');

					$information_info = $this->model_catalog_information->getInformation($this->config->get('config_checkout_id'));

					if ($information_info)
					{
						$errors['order_agree'] = sprintf($this->language->get('error_agree'), $information_info['title']);
					}
				}

				// Validate new account
				if (!$this->customer->isLogged() && !empty($this->request->post['create_new_account']))
				{
					if ((utf8_strlen($this->request->post['password']) < 4) || (utf8_strlen($this->request->post['password']) > 20))
					{
						$errors['password'] = $this->language->get('error_password');
					}

					if ($this->request->post['confirm'] != $this->request->post['password'])
					{
						$errors['confirm'] = $this->language->get('error_confirm');
					}

					if (empty($this->request->post['register_agree']) && $this->config->get('config_account_id'))
					{
						$this->load->model('catalog/information');

						$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));

						if ($information_info)
						{
							$errors['register_agree'] = sprintf($this->language->get('error_agree'), $information_info['title']);
						}
					}
				}

				if ($errors)
				{
					throw new Exception('error_fields');
				}

				// Create new account
				if (!$this->customer->isLogged() && !empty($this->request->post['create_new_account']))
				{
					$customer_data = array(
						'firstname' => $this->request->post['firstname'],
						'lastname' => $this->request->post['lastname'],
						'email' => $this->request->post['email'],
						'telephone' => $this->request->post['telephone'],
						'fax' => $this->request->post['fax'],
						'company' => $this->request->post['company'],
						'customer_group_id' => $this->request->post['customer_group_id'],
						'fax' => $this->request->post['fax'],
						'company_id' => $this->request->post['company_id'],
						'tax_id' => $this->request->post['tax_id'],
						'address_1' => $this->request->post['address_1'],
						'address_2' => $this->request->post['address_2'],
						'city' => $this->request->post['city'],
						'postcode' => $this->request->post['postcode'],
						'country_id' => $this->request->post['country_id'],
						'zone_id' => $this->request->post['zone_id'],
						'password' => $this->request->post['password'],
						'confirm' => $this->request->post['confirm'],
						'newsletter' => isset($this->request->post['newsletter']) ? $this->request->post['newsletter'] : 0,
						'agree' => isset($this->request->post['agree']) ? $this->request->post['agree'] : 0,
					);

					$this->load->model('account/customer');
					$this->model_account_customer->addCustomer($customer_data);

					$this->customer->login($this->request->post['email'], $this->request->post['password']);
				}

				$total_data = array();
				$total = 0;
				$taxes = $this->cart->getTaxes();

				$this->load->model('setting/extension');

				$sort_order = array();

				$results = $this->model_setting_extension->getExtensions('total');

				foreach ($results as $key => $value)
				{
					$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
				}

				array_multisort($sort_order, SORT_ASC, $results);

				foreach ($results as $result)
				{
					if ($this->config->get($result['code'] . '_status'))
					{
						$this->load->model('total/' . $result['code']);

						$this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
					}
				}

				$sort_order = array();

				foreach ($total_data as $key => $value)
				{
					$sort_order[$key] = $value['sort_order'];
				}

				array_multisort($sort_order, SORT_ASC, $total_data);

				$data = array();

				$data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
				$data['store_id'] = $this->config->get('config_store_id');
				$data['store_name'] = $this->config->get('config_name');

				if ($data['store_id'])
				{
					$data['store_url'] = $this->config->get('config_url');
				}
				else
				{
					$data['store_url'] = HTTP_SERVER;
				}

				$addresses = Kuler::getInstance()->getAddresses();

				if ($this->customer->isLogged())
				{
					$data['customer_id'] = $this->customer->getId();
					$data['customer_group_id'] = $this->customer->getCustomerGroupId();
					$data['firstname'] = $this->customer->getFirstName();
					$data['lastname'] = $this->customer->getLastName();
					$data['email'] = $this->customer->getEmail();
					$data['telephone'] = $this->customer->getTelephone();
					$data['fax'] = $this->customer->getFax();
				}
				else
				{
					$data['customer_id'] = 0;
					$data['customer_group_id'] = $this->config->get('config_customer_group_id');
					$data['firstname'] = $this->request->post['firstname'];
					$data['lastname'] = $this->request->post['lastname'];
					$data['email'] = $this->request->post['email'];
					$data['telephone'] = $this->request->post['telephone'];
					$data['fax'] = $this->request->post['fax'];
				}

				if (!empty($this->request->post['payment_address_same']))
				{
					$payment_address = $this->request->post;
				}
				else
				{
					$payment_address = $addresses[$this->request->post['payment_address_id']];
				}

				if (empty($payment_address['address_format']))
				{
					$this->load->model('localisation/country');
					$this->load->model('localisation/zone');

					$country = $this->model_localisation_country->getCountry($payment_address['country_id']);

					if ($country)
					{
						$payment_address['country']        = $country['name'];
						$payment_address['address_format'] = $country['address_format'];
					}

					$zone = $this->model_localisation_zone->getZone($payment_address['zone_id']);

					if ($zone)
					{
						$payment_address['zone']           = $zone['name'];
					}
				}

				$data['payment_firstname'] = $payment_address['firstname'];
				$data['payment_lastname'] = $payment_address['lastname'];
				$data['payment_company'] = $payment_address['company'];
				$data['payment_company_id'] = $payment_address['company_id'];
				$data['payment_tax_id'] = $payment_address['tax_id'];
				$data['payment_address_1'] = $payment_address['address_1'];
				$data['payment_address_2'] = $payment_address['address_2'];
				$data['payment_city'] = $payment_address['city'];
				$data['payment_postcode'] = $payment_address['postcode'];
				$data['payment_zone'] = $payment_address['zone'];
				$data['payment_zone_id'] = $payment_address['zone_id'];
				$data['payment_country'] = $payment_address['country'];
				$data['payment_country_id'] = $payment_address['country_id'];
				$data['payment_address_format'] = $payment_address['address_format'];

				if (isset($this->session->data['payment_method']['title']))
				{
					$data['payment_method'] = $this->session->data['payment_method']['title'];
				}
				else
				{
					$data['payment_method'] = '';
				}

				if (isset($this->session->data['payment_method']['code']))
				{
					$data['payment_code'] = $this->session->data['payment_method']['code'];
				}
				else
				{
					$data['payment_code'] = '';
				}

				if ($this->cart->hasShipping())
				{
					if (!empty($this->request->post['shipping_address_same']))
					{
						$shipping_address = $this->request->post;
					}
					else
					{
						$shipping_address = $addresses[$this->request->post['shipping_address_id']];
					}

					if (empty($shipping_address['address_format']))
					{
						$this->load->model('localisation/country');
						$this->load->model('localisation/zone');

						$country = $this->model_localisation_country->getCountry($shipping_address['country_id']);

						if ($country)
						{
							$shipping_address['country']        = $country['name'];
							$shipping_address['address_format'] = $country['address_format'];
						}

						$zone = $this->model_localisation_zone->getZone($shipping_address['zone_id']);

						if ($zone)
						{
							$shipping_address['zone']           = $zone['name'];
						}
					}

					$data['shipping_firstname'] = $shipping_address['firstname'];
					$data['shipping_lastname'] = $shipping_address['lastname'];
					$data['shipping_company'] = $shipping_address['company'];
					$data['shipping_address_1'] = $shipping_address['address_1'];
					$data['shipping_address_2'] = $shipping_address['address_2'];
					$data['shipping_city'] = $shipping_address['city'];
					$data['shipping_postcode'] = $shipping_address['postcode'];
					$data['shipping_zone'] = $shipping_address['zone'];
					$data['shipping_zone_id'] = $shipping_address['zone_id'];
					$data['shipping_country'] = $shipping_address['country'];
					$data['shipping_country_id'] = $shipping_address['country_id'];
					$data['shipping_address_format'] = $shipping_address['address_format'];

					if (isset($this->session->data['shipping_method']['title']))
					{
						$data['shipping_method'] = $this->session->data['shipping_method']['title'];
					}
					else
					{
						$data['shipping_method'] = '';
					}

					if (isset($this->session->data['shipping_method']['code']))
					{
						$data['shipping_code'] = $this->session->data['shipping_method']['code'];
					}
					else
					{
						$data['shipping_code'] = '';
					}
				}
				else
				{
					$data['shipping_firstname'] = '';
					$data['shipping_lastname'] = '';
					$data['shipping_company'] = '';
					$data['shipping_address_1'] = '';
					$data['shipping_address_2'] = '';
					$data['shipping_city'] = '';
					$data['shipping_postcode'] = '';
					$data['shipping_zone'] = '';
					$data['shipping_zone_id'] = '';
					$data['shipping_country'] = '';
					$data['shipping_country_id'] = '';
					$data['shipping_address_format'] = '';
					$data['shipping_method'] = '';
					$data['shipping_code'] = '';
				}

				$product_data = array();

				foreach ($this->cart->getProducts() as $product)
				{
					$option_data = array();

					foreach ($product['option'] as $option)
					{
						if ($option['type'] != 'file')
						{
							$value = $option['option_value'];
						}
						else
						{
							$value = $this->encryption->decrypt($option['option_value']);
						}

						$option_data[] = array(
							'product_option_id'       => $option['product_option_id'],
							'product_option_value_id' => $option['product_option_value_id'],
							'option_id'               => $option['option_id'],
							'option_value_id'         => $option['option_value_id'],
							'name'                    => $option['name'],
							'value'                   => $value,
							'type'                    => $option['type']
						);
					}

					$product_data[] = array(
						'product_id' => $product['product_id'],
						'name'       => $product['name'],
						'model'      => $product['model'],
						'option'     => $option_data,
						'download'   => $product['download'],
						'quantity'   => $product['quantity'],
						'subtract'   => $product['subtract'],
						'price'      => $product['price'],
						'total'      => $product['total'],
						'tax'        => $this->tax->getTax($product['price'], $product['tax_class_id']),
						'reward'     => $product['reward']
					);
				}

				// Gift Voucher
				$voucher_data = array();

				if (!empty($this->session->data['vouchers']))
				{
					foreach ($this->session->data['vouchers'] as $voucher)
					{
						$voucher_data[] = array(
							'description'      => $voucher['description'],
							'code'             => substr(md5(mt_rand()), 0, 10),
							'to_name'          => $voucher['to_name'],
							'to_email'         => $voucher['to_email'],
							'from_name'        => $voucher['from_name'],
							'from_email'       => $voucher['from_email'],
							'voucher_theme_id' => $voucher['voucher_theme_id'],
							'message'          => $voucher['message'],
							'amount'           => $voucher['amount']
						);
					}
				}

				$data['products'] = $product_data;
				$data['vouchers'] = $voucher_data;
				$data['totals'] = $total_data;
				$data['comment'] = $this->request->post['comment'];
				$data['total'] = $total;

				if (isset($this->request->cookie['tracking']))
				{
					$this->load->model('affiliate/affiliate');

					$affiliate_info = $this->model_affiliate_affiliate->getAffiliateByCode($this->request->cookie['tracking']);
					$subtotal = $this->cart->getSubTotal();

					if ($affiliate_info) {
						$data['affiliate_id'] = $affiliate_info['affiliate_id'];
						$data['commission'] = ($subtotal / 100) * $affiliate_info['commission'];
					} else {
						$data['affiliate_id'] = 0;
						$data['commission'] = 0;
					}
				}
				else
				{
					$data['affiliate_id'] = 0;
					$data['commission'] = 0;
				}

				$data['language_id'] = $this->config->get('config_language_id');
				$data['currency_id'] = $this->currency->getId();
				$data['currency_code'] = $this->currency->getCode();
				$data['currency_value'] = $this->currency->getValue($this->currency->getCode());
				$data['ip'] = $this->request->server['REMOTE_ADDR'];

				if (!empty($this->request->server['HTTP_X_FORWARDED_FOR']))
				{
					$data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
				}
				else if(!empty($this->request->server['HTTP_CLIENT_IP']))
				{
					$data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
				}
				else
				{
					$data['forwarded_ip'] = '';
				}

				if (isset($this->request->server['HTTP_USER_AGENT']))
				{
					$data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
				}
				else
				{
					$data['user_agent'] = '';
				}

				if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
					$data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
				} else {
					$data['accept_language'] = '';
				}

				$this->load->model('checkout/order');

				$this->session->data['order_id'] = $this->model_checkout_order->addOrder($data);

				$payment = $this->getChild('payment/' . $this->session->data['payment_method']['code']);

				$result = array(
					'status' => 1,
					'payment' => $payment
				);
			}
			catch (Exception $e)
			{
				$result = array(
					'status' => 0,
					'message' => $e->getMessage()
				);

				if ($e->getMessage() == 'error_fields')
				{
					$result['error_fields'] = $errors;
				}
			}

			$this->response->setOutput(json_encode($result));
		}

		protected function validateCoupon()
		{
			$this->load->model('checkout/coupon');

			$coupon_info = $this->model_checkout_coupon->getCoupon($this->request->post['coupon']);

			if (!$coupon_info)
			{
				throw new Exception(_t('error_coupon'));
			}
		}

		protected function validateVoucher()
		{
			$this->load->model('checkout/voucher');

			$voucher_info = $this->model_checkout_voucher->getVoucher($this->request->post['voucher']);

			if (!$voucher_info)
			{
				throw new Exception(_t('error_voucher'));
			}
		}

		protected function getShippingMethod()
		{
			$this->language->load('checkout/checkout');

			$shipping_address = $this->getShippingAddress();

			if (!empty($shipping_address))
			{
				// Shipping Methods
				$quote_data = array();

				$this->load->model('setting/extension');

				$results = $this->model_setting_extension->getExtensions('shipping');

				foreach ($results as $result) {
					if ($this->config->get($result['code'] . '_status')) {
						$this->load->model('shipping/' . $result['code']);

						$quote = $this->{'model_shipping_' . $result['code']}->getQuote($shipping_address);

						if ($quote) {
							$quote_data[$result['code']] = array(
								'title'      => $quote['title'],
								'quote'      => $quote['quote'],
								'sort_order' => $quote['sort_order'],
								'error'      => $quote['error']
							);
						}
					}
				}

				$sort_order = array();

				foreach ($quote_data as $key => $value) {
					$sort_order[$key] = $value['sort_order'];
				}

				array_multisort($sort_order, SORT_ASC, $quote_data);

				$this->session->data['shipping_methods'] = $quote_data;
			}

			if (empty($this->session->data['shipping_methods']))
			{
				$this->data['error_warning'] = sprintf($this->language->get('error_no_shipping'), $this->url->link('information/contact'));
			}
			else
			{
				$this->data['error_warning'] = '';
			}

			if (!empty($this->session->data['shipping_methods']))
			{
				$this->data['shipping_methods'] = $this->session->data['shipping_methods'];

				// Check shipping code from input or session
				if (!empty($this->request->get['shipping_method']) || !empty($this->session->data['shipping_method']))
				{
					$code = !empty($this->request->get['shipping_method']) ? $this->request->get['shipping_method'] : $this->session->data['shipping_method']['code'];

					$codes = explode('.', $code);

					if (empty($this->session->data['shipping_methods'][$codes[0]]) || empty($this->session->data['shipping_methods'][$codes[0]]['quote'][$codes[1]]))
					{
						unset($this->session->data['shipping_method']);
					}
					else
					{
						$this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$codes[0]]['quote'][$codes[1]];
					}
				}

				if (empty($this->session->data['shipping_method']))
				{
					$first_shipping_method = current($this->session->data['shipping_methods']);
					$first_quote = current($first_shipping_method['quote']);

					$this->session->data['shipping_method'] = $first_quote;
				}
			}
			else
			{
				$this->data['shipping_methods'] = array();
			}

			if (isset($this->session->data['shipping_method']['code']))
			{
				$this->data['code'] = $this->session->data['shipping_method']['code'];
			} else {
				$this->data['code'] = '';
			}

			$this->template = $this->config->get('config_template') . '/template/checkout/shipping_method.tpl';

			return $this->render();
		}

		protected function getPaymentMethod()
		{
			$this->language->load('checkout/checkout');

			$payment_address = $this->getPaymentAddress();

			if (!empty($payment_address))
			{
				// Totals
				$total_data = array();
				$total = 0;
				$taxes = $this->cart->getTaxes();

				$this->load->model('setting/extension');

				$sort_order = array();

				$results = $this->model_setting_extension->getExtensions('total');

				foreach ($results as $key => $value)
				{
					$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
				}

				array_multisort($sort_order, SORT_ASC, $results);

				foreach ($results as $result)
				{
					if ($this->config->get($result['code'] . '_status'))
					{
						$this->load->model('total/' . $result['code']);

						$this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
					}
				}

				// Payment Methods
				$method_data = array();

				$this->load->model('setting/extension');

				$results = $this->model_setting_extension->getExtensions('payment');

				// Set config for get payment information
				$this->config->set('inline_payment', true);

				foreach ($results as $result)
				{
					if ($this->config->get($result['code'] . '_status')) {
						$this->load->model('payment/' . $result['code']);

						$method = $this->{'model_payment_' . $result['code']}->getMethod($payment_address, $total);

						if ($method) {
							$method_data[$result['code']] = $method;
						}
					}
				}

				$sort_order = array();

				foreach ($method_data as $key => $value)
				{
					$sort_order[$key] = $value['sort_order'];
				}

				array_multisort($sort_order, SORT_ASC, $method_data);

				$this->session->data['payment_methods'] = $method_data;

			}

			if (empty($this->session->data['payment_methods']))
			{
				$this->data['error_warning'] = sprintf($this->language->get('error_no_payment'), $this->url->link('information/contact'));
			}
			else
			{
				$this->data['error_warning'] = '';
			}

			if (!empty($this->session->data['payment_methods']))
			{
				$this->data['payment_methods'] = $this->session->data['payment_methods'];

				if (!empty($this->request->get['payment_method']) || !empty($this->session->data['payment_method']))
				{
					$code = !empty($this->request->get['payment_method']) ? $this->request->get['payment_method'] : $this->session->data['payment_method']['code'];

					if (empty($this->session->data['payment_methods'][$code]))
					{
						unset($this->session->data['payment_method']['code']);
					}
					else
					{
						$this->session->data['payment_method'] = $this->session->data['payment_methods'][$code];
					}
				}

				if (empty($this->session->data['payment_method']))
				{
					$this->session->data['payment_method'] = current($this->session->data['payment_methods']);
				}
			}

			if (isset($this->session->data['payment_method']['code']))
			{
				$this->data['code'] = $this->session->data['payment_method']['code'];
			}
			else
			{
				$this->data['code'] = '';
			}

			$this->template = $this->config->get('config_template') . '/template/checkout/payment_method.tpl';

			return $this->render();
		}

		protected function getOrderTotal()
		{
			$redirect = '';

			try
			{
				if ($this->cart->hasShipping())
				{
					$shipping_address = $this->getShippingAddress();
				}
				else
				{
					unset($this->session->data['shipping_method']);
					unset($this->session->data['shipping_methods']);
				}

				$payment_address = $this->getPaymentAddress();

				// Validate if payment method has been set.
				if (!isset($this->session->data['payment_method']))
				{
					throw new Exception(_t('error_please_select_a_payment_method'));
				}

				$total_data = array();
				$total = 0;
				$taxes = $this->cart->getTaxes();

				$this->load->model('setting/extension');

				$sort_order = array();

				$results = $this->model_setting_extension->getExtensions('total');

				foreach ($results as $key => $value)
				{
					$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
				}

				array_multisort($sort_order, SORT_ASC, $results);

				foreach ($results as $result)
				{
					if ($this->config->get($result['code'] . '_status'))
					{
						$this->load->model('total/' . $result['code']);

						$this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
					}
				}

				$sort_order = array();

				foreach ($total_data as $key => $value)
				{
					$sort_order[$key] = $value['sort_order'];
				}

				array_multisort($sort_order, SORT_ASC, $total_data);

				$this->language->load('checkout/checkout');
				$this->load->model('checkout/order');

				$this->data['column_name'] = $this->language->get('column_name');
				$this->data['column_model'] = $this->language->get('column_model');
				$this->data['column_quantity'] = $this->language->get('column_quantity');
				$this->data['column_price'] = $this->language->get('column_price');
				$this->data['column_total'] = $this->language->get('column_total');

				$this->data['products'] = array();

				foreach ($this->cart->getProducts() as $product)
				{
					$option_data = array();

					foreach ($product['option'] as $option)
					{
						if ($option['type'] != 'file')
						{
							$value = $option['option_value'];
						}
						else
						{
							$filename = $this->encryption->decrypt($option['option_value']);

							$value = utf8_substr($filename, 0, utf8_strrpos($filename, '.'));
						}

						$option_data[] = array(
							'name'  => $option['name'],
							'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
						);
					}

					$this->data['products'][] = array(
						'product_id' => $product['product_id'],
						'name'       => $product['name'],
						'model'      => $product['model'],
						'option'     => $option_data,
						'quantity'   => $product['quantity'],
						'subtract'   => $product['subtract'],
						'price'      => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'))),
						'total'      => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity']),
						'href'       => $this->url->link('product/product', 'product_id=' . $product['product_id'])
					);
				}

				// Gift Voucher
				$this->data['vouchers'] = array();

				if (!empty($this->session->data['vouchers']))
				{
					foreach ($this->session->data['vouchers'] as $voucher)
					{
						$this->data['vouchers'][] = array(
							'description' => $voucher['description'],
							'amount'      => $this->currency->format($voucher['amount'])
						);
					}
				}

				$this->data['totals'] = $total_data;

				if (!empty($this->session->data['payment_method']) && !empty($this->session->data['payment_method']['code'])) {
					$this->data['payment'] = $this->getChild('payment/' . $this->session->data['payment_method']['code']);
				} else {
					$this->data['payment'] = '';
				}
			}
			catch (Exception $e)
			{

			}

			$this->template = $this->config->get('config_template') . '/template/checkout/confirm.tpl';

			return $this->render();
		}

		protected function getShippingAddress()
		{
			if (!empty($this->request->get['country_id']))
			{
				if (!empty($this->request->get['shipping_address_same']))
				{
					$shipping_address = $this->request->get;
				}
				else
				{
					$address_id = $this->request->get['shipping_address_id'];

					$addresses = Kuler::getInstance()->getAddresses();

					if (!empty($addresses[$address_id]))
					{
						$shipping_address = $addresses[$address_id];
					}
					else
					{
						$shipping_address = $this->request->get;
					}
				}

				if (empty($shipping_address['zone_id'])) {
					$this->load->model('localisation/zone');

					$zones = $this->model_localisation_zone->getZonesByCountryId($shipping_address['country_id']);

					if (empty($zones)) {
						$shipping_address['zone_id'] = 0;
					} else {
						$zone = current($zones);
						$shipping_address['zone_id'] = $zone['zone_id'];
					}
				}
			}

			return $shipping_address;
		}

		protected function getPaymentAddress()
		{
			if (!empty($this->request->get['country_id']))
			{
				if (!empty($this->request->get['payment_address_same']))
				{
					$payment_address = $this->request->get;
				}
				else
				{
					$address_id = $this->request->get['payment_address_id'];

					$addresses = Kuler::getInstance()->getAddresses();

					if (!empty($addresses[$address_id]))
					{
						$payment_address = $addresses[$address_id];
					}
					else
					{
						$payment_address = $this->request->get;
					}
				}

				if (empty($payment_address['zone_id'])) {
					$this->load->model('localisation/zone');

					$zones = $this->model_localisation_zone->getZonesByCountryId($payment_address['country_id']);

					if (empty($zones)) {
						$payment_address['zone_id'] = 0;
					} else {
						$zone = current($zones);
						$payment_address['zone_id'] = $zone['zone_id'];
					}
				}
			}

			return $payment_address;
		}
	}

	class Kuler extends Controller
	{
		protected static $instance;

		protected $scripts = array();

		protected $bodyScripts = array();

		protected $styles = array();

		/* @var ModelKulerCommon $common */
		public $common;

		/* @var ModelKulerCp $model */
		public $model;

		protected $theme_id;
		protected $skin_id;

		public $mobile;

		/**
		 * @return Kuler
		 */
		public static function getInstance()
		{
			if (!self::$instance)
			{
				global $registry;

				self::$instance = new Kuler($registry);
			}

			return self::$instance;
		}

		public function __construct($registry)
		{
			parent::__construct($registry);

			// Load model
			$this->load->model('kuler/common');
			$this->common = $this->model_kuler_common;

			ModelKulerCommon::loadTexts($this->language->load('module/kuler_cp'));

			$this->load->model('kuler/cp');
			$this->model = $this->model_kuler_cp;

			$this->theme_id = $this->model->getCurrentThemeId();
			$this->skin_id = $this->model->getCurrentSkinId();

			$this->model->getSkinOptions($this->theme_id, $this->skin_id);

			// Override
			

			$useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "";
			$this->mobile = new Mobile_Detect();
			$this->mobile->setDetectionType('extended');
		}

		protected function overrideResponse()
		{
			global $registry;
			global $response;

			$kuler_response = new KulerResponse();

			$response = $kuler_response;

			$registry->set('response', $kuler_response);
		}

		public function getTheme()
		{
			return $this->config->get('config_template');
		}

		public function getScripts($body = false)
		{
			return !$body ? $this->scripts : $this->bodyScripts;
		}

		public function getStyles()
		{
			return $this->styles;
		}

		public function addScript($scripts, $insertBody = false)
		{
			if (!is_array($scripts))
			{
				$scripts = array($scripts);
			}

			foreach ($scripts as $script)
			{
				if (!$insertBody)
				{
					$this->scripts[md5($script)] = $script;
				}
				else
				{
					$this->bodyScripts[md5($script)] = $script;
				}
			}
		}

		public function getThemeResource($file)
		{
			if (!file_exists($file))
			{
				$file = str_replace($this->getTheme(), 'default', $file);
			}

			return $file;
		}

		public function addStyle($hrefs)
		{
			if (!is_array($hrefs))
			{
				$hrefs = array($hrefs);
			}

			foreach ($hrefs as $href)
			{
				$this->styles[md5($href)] = array(
					'href' => $href,
					'rel'   => 'stylesheet',
					'media' => 'screen'
				);
			}
		}

		public function getDocument()
		{
			return $this->document;
		}

		public function processScripts($body = false)
		{
			$html = '';

			if (!$body)
			{
				$kuler = array(
					'theme'                         => $this->getTheme(),
					'skin'                          => $this->getRootSkin(),
					'theme_version'                 => $this->getThemeVersion(),
					'kuler_version'                 => $this->getKulerVersion(),

					'login_popup'                   => $this->getSkinOption('login_popup'),
					'fixed_header'                  => $this->getSkinOption('fixed_header'),
					'live_search'                   => $this->getSkinOption('live_search_status'),
					'show_quick_view'               => $this->getSkinOption('show_quick_view'),
					'show_custom_notification'      => $this->getSkinOption('show_custom_notification'),
					'notification_show_time'        => $this->getSkinOption('notification_show_time'),
					'enable_scroll_up'              => $this->getSkinOption('enable_scroll_up'),
					'show_sale_badge'               => $this->getSkinOption('show_sale_badge'),
					'enable_swap_image'             => $this->getSkinOption('enable_swap_image'),
					'category_menu_type'            => $this->getSkinOption('category_menu_type'),
					'image_lightbox'                => $this->getSkinOption('image_lightbox'),
					'image_zoom_type'               => $this->getSkinOption('image_zoom_type'),
					'zoom_window_width'             => $this->getSkinOption('zoom_window_width'),
					'zoom_window_height'            => $this->getSkinOption('zoom_window_height'),
					'lens_zoom_shape'               => $this->getSkinOption('lens_zoom_shape'),

					'is_logged'                     => $this->customer->isLogged(),
					'login_url'                     => $this->url->link('account/login', '', 'SSL'),
					'popup_login_url'               => $this->url->link('module/kuler_cp/onePageCheckoutLogin', '', 'SSL'),

					'cart_product_total'            => $this->cart->countProducts()
				);

				$html .= '<script>var Kuler = '. json_encode($kuler) .'</script>';
			}

			// Add script
			if (!$body)
			{
				$scripts = array_merge($this->scripts, $this->document->getScripts());
			}
			else
			{
				$scripts = $this->bodyScripts;
			}

			foreach ($scripts as $script)
			{
				$html .= sprintf('<script src="%s"></script>', $script);
			}

			if ($body && $this->getSkinOption('custom_js_status') && $custom_js = $this->getSkinOption('custom_js'))
			{
				$html .= '<script>' . $custom_js . '</script>';
			}

			if (!empty($this->request->request['style_customization']) || !empty($this->session->data['style_customization']))
			{
				$this->session->data['style_customization'] = 1;

				$html .= <<<SC_SCRIPT
<script>
window.addEventListener('message', kulerStyleCustomizationProcess, false);

function kulerStyleCustomizationProcess(evt) {
	if (evt.data.cmd) {
		$('#style-customization').remove();
		if (!$('#custom-style').length) {
			$('head').append('<style id="custom-style"></style>');
		}

		if (evt.data.cmd == 'css') {
			$('#custom-style').html(evt.data.css);
		} else if (evt.data.cmd == 'font') {
			$('head').append('<link href="//fonts.googleapis.com/css?family='+ evt.data.font +'" rel="stylesheet" type="text/css" />');
		}
	}
};
</script>
SC_SCRIPT;
			}

			return $html;
		}

		public function processStyles()
		{
			$html = '';

			// Load Google fonts
			$theme_id = $this->config->get('theme_id');
			$skin_id = $this->config->get('skin_id');

			$theme_options = $this->model->loadThemeOptions($theme_id);

			$font_properties = array();

			foreach ($theme_options['styles'] as $style_group)
			{
				foreach ($style_group['items'] as $property => $style)
				{
					if ($style['type'] == 'style_font')
					{
						$font_properties[] = $property;
					}
				}
			}

			$skin_options = $this->model->getSkinOptions($theme_id, $skin_id);

			// TODO: Improve Google font loading
			$google_fonts = array();
			foreach ($font_properties as $font_property)
			{
				if (!empty($skin_options[$font_property]) && !empty($skin_options[$font_property]['font_family']))
				{
					if ($font = $this->model->processFontFamily($skin_options[$font_property]['font_family']))
					{
						$google_fonts[] = $font;
					}
				}
			}

			if (!empty($google_fonts))
			{
				$html .= sprintf('<link href="%s" type="text/css" rel="stylesheet" />', '//fonts.googleapis.com/css?family=' . implode('|', $google_fonts) . '&amp;subset=all');
			}

			// Process style
			$theme_id = $this->config->get('theme_id');
			$skin_id = $this->config->get('skin_id');

			$styles = array_merge($this->styles, $this->document->getStyles());

			$styles[] = array(
				'href' => 'catalog/view/theme/'. $theme_id .'/data/'. $theme_id . '_' . $skin_id .'.css',
				'rel'   => 'stylesheet',
				'media' => 'screen',
				'custom' => true
			);

			foreach ($styles as $style)
			{
				if (empty($style['custom']))
				{
					$html .= sprintf('<link rel="%s" type="text/css" href="%s" media="%s" />', $style['rel'], $style['href'], $style['media']);
				}
				else
				{
					$html .= sprintf('<link rel="%s" id="style-customization" type="text/css" href="%s" media="%s" />', $style['rel'], $style['href'], $style['media']);
				}
			}

			if ($this->getSkinOption('custom_css_file_status'))
			{
				$html .= '<link rel="stylesheet" type="text/css" href="catalog/view/theme/'. $this->getTheme() . '/stylesheet/custom/' . $this->getSkinOption('custom_css_file') .'" />';
			}

			if ($this->getSkinOption('custom_css_status') && $custom_css = $this->getSkinOption('custom_css'))
			{
				$html .= '<style>'. $custom_css .'</style>';
			}

			return $html;
		}

		public function getSkin()
		{
			return $this->skin_id;
		}

		public function getRootSkin()
		{
			return $this->model->getCurrentRootSkinId();
		}

		public function getThemeVersion()
		{
			return $this->model->getCurrentThemeVersion();
		}

		public function getKulerVersion()
		{
			return $this->common->getKulerVersion();
		}

		public function getSkinOption($property)
		{
			$value = $this->model->getSkinOption($this->theme_id, $this->skin_id, $property);

			if (is_string($value))
			{
				$value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
			}

			return $value;
		}

		public function translate($text)
		{
			return $this->common->translate($text);
		}

		public function getBodyClass()
		{
			$body_classes = array();

			if ($this->getSkinOption('layout_style') == 'boxed')
			{
				$body_classes[] = 'boxed';
			}
			else
			{
				$body_classes[] = 'fullwidth';
			}

			$body_classes[] = $this->getRootSkin();

			if ($this->mobile->isHandHeld())
			{
				$body_classes[] = 'handheld';
			}

			if ($this->mobile->isMobile())
			{
				$body_classes[] = 'mobile';
			}

			if ($this->mobile->isTablet())
			{
				$body_classes[] = 'tablet';
			}

			if (preg_match('/MSIE (.*?);/', $this->request->server['HTTP_USER_AGENT'], $matches))
			{
				$body_classes[] = 'ie';
			}

			return implode(' ', $body_classes);
		}

		public function getRecursiveCategories()
		{
			return $this->model->getRecursiveCategories();
		}

		/* Live Search */
		public function getLiveSearchData()
		{
			// 3 Level Category Search
			$this->language->load('product/search');
			$this->load->model('catalog/category');

			if (isset($this->request->get['category_id']))
			{
				$category_id = $this->request->get['category_id'];
			}
			else
			{
				if (isset($this->request->get['path']))
				{
					$part = explode('_', $this->request->get['path']);
					$category_id = array_pop($part);
				}
				else
				{
					$category_id = 0;
				}
			}

			$categories = array();

			$categories_1 = $this->model_catalog_category->getCategories(0);

			foreach ($categories_1 as $category_1) {
				$level_2_data = array();

				$categories_2 = $this->model_catalog_category->getCategories($category_1['category_id']);

				foreach ($categories_2 as $category_2) {
					$level_3_data = array();

					$categories_3 = $this->model_catalog_category->getCategories($category_2['category_id']);

					foreach ($categories_3 as $category_3) {
						$level_3_data[] = array(
							'category_id' => $category_3['category_id'],
							'name'        => $category_3['name'],
						);
					}

					$level_2_data[] = array(
						'category_id' => $category_2['category_id'],
						'name'        => $category_2['name'],
						'children'    => $level_3_data
					);
				}

				$categories[] = array(
					'category_id' => $category_1['category_id'],
					'name'        => $category_1['name'],
					'children'    => $level_2_data
				);
			}

			// Manufacturers
			$manufacturers = array();
			$this->load->model('catalog/manufacturer');
			$manufacturer_rows = $this->model_catalog_manufacturer->getManufacturers();

			foreach ($manufacturer_rows as $manufacturer_row)
			{
				$manufacturers[$manufacturer_row['manufacturer_id']] = $manufacturer_row['name'];
			}

			return array(
				'categories' => $categories,
				'category_id' => $category_id,
				'manufacturers' => $manufacturers,
				'text_load_more' => _t('text_load_more'),
				'text_no_results' => _t('text_no_results')
			);
		}

		/* Quick View */
		public function getQuickViewUrl($product)
		{
			if (is_array($product))
			{
				$product = $product['product_id'];
			}

			return 'index.php?route=module/kuler_cp/quick_view&amp;product_id=' . $product;
		}

		/* Payment Icons */
		public function getPaymentIcons()
		{
			$payment_icons = $this->getSkinOption('payment_icons');

			if (is_array($payment_icons))
			{
				foreach ($payment_icons as &$payment_icon)
				{
					$payment_icon['thumb'] = 'image/' . $payment_icon['image'];
				}
			}

			return $this->common->sortByField($payment_icons, 'sort');
		}

		/* Facebook */
		public function getFacebookScriptCode()
		{
			return '
			<div id="fb-root"></div>
			<script>(function(d, s, id) {
			  var js, fjs = d.getElementsByTagName(s)[0];
			  if (d.getElementById(id)) return;
			  js = d.createElement(s); js.id = id;
			  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=499585646778888";
			  js.async = true;
			  fjs.parentNode.insertBefore(js, fjs);
			}(document, \'script\', \'facebook-jssdk\'));</script>
		';
		}

		public function getFacebook()
		{
			$facebook_id = $this->getSkinOption('facebook_page_id');
			$theme = $this->getSkinOption('facebook_theme');
			$show_friend_faces = $this->getSkinOption('show_facebook_friend_faces');
			$show_posts = $this->getSkinOption('show_facebook_posts');
			$width = $this->getSkinOption('facebook_width');
			$height = $this->getSkinOption('facebook_height');

			return '<div class="fb-like-box" data-width="'. $width .'" data-height="'. $height .'" data-href="https://www.facebook.com/'. $facebook_id .'" data-colorscheme="'. $theme .'" data-show-faces="'. ($show_friend_faces ? 'true' : 'false') .'" data-header="false" data-stream="'. ($show_posts ? 'true' : 'false') .'" data-show-border="false" ></div>';
		}

		/* Twitter */
		public function getTwitter()
		{
			$username = $this->getSkinOption('twitter_username');
			$widget_id = $this->getSkinOption('twitter_widget_id');
			$theme_id = $this->getSkinOption('twitter_theme');
			$number_of_tweets = $this->getSkinOption('number_of_tweets');
			$no_header = $this->getSkinOption('show_twitter_header') ? '' : 'noheader';
			$no_footer = $this->getSkinOption('show_twitter_footer') ? '' : 'nofooter';
			$transparent = $this->getSkinOption('twitter_transparent_background') ? 'transparent' : '';
			$width = $this->getSkinOption('twitter_width');
			$height = $this->getSkinOption('twitter_height');

			return '
			<a class="twitter-timeline" href="https://twitter.com/'. $username .'" data-widget-id="'. $widget_id .'" data-theme="'. $theme_id .'" data-tweet-limit="'. $number_of_tweets .'" data-chrome="'. "$no_header $no_footer $transparent" .'" width="'. $width .'" height="'. $height .'">Tweets by @'. $username .'</a>
			<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?\'http\':\'https\';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
		';
		}

		/* Social Icons */
		public function getSocialIcons()
		{
			$social_icons = array();

			if ($this->getSkinOption('social_icon_facebook')) {
				$social_icons[] = array(
					'class' => 'facebook',
					'link' => $this->getSkinOption('social_icon_facebook_link')
				);
			}

			if ($this->getSkinOption('social_icon_twitter')) {
				$social_icons[] = array(
					'class' => 'twitter',
					'link' => $this->getSkinOption('social_icon_twitter_link')
				);
			}

			if ($this->getSkinOption('social_icon_google_plus'))
			{
				$social_icons[] = array(
					'class' => 'google_plus',
					'link' => $this->getSkinOption('social_icon_google_plus_link')
				);
			}

			if ($this->getSkinOption('social_icon_youtube'))
			{
				$social_icons[] = array(
					'class' => 'youtube',
					'link' => $this->getSkinOption('social_icon_youtube_link')
				);
			}

			if ($this->getSkinOption('social_icon_pinterest'))
			{
				$social_icons[] = array(
					'class' => 'pinterest',
					'link' => $this->getSkinOption('social_icon_pinterest_link')
				);
			}

			if ($this->getSkinOption('social_icon_instagram'))
			{
				$social_icons[] = array(
					'class' => 'instagram',
					'link' => $this->getSkinOption('social_icon_instagram_link')
				);
			}

			if ($this->getSkinOption('social_icon_rss'))
			{
				$social_icons[] = array(
					'class' => 'rss',
					'link' => $this->getSkinOption('social_icon_rss_link')
				);
			}

			return $social_icons;
		}

		/* Newsletter */
		public function getNewsletterSubscribeLink()
		{
			return $this->url->link('module/kuler_cp/subscribeNewsletter');
		}

		/* Sale Badge */
		public function calculateSalePercent($special, $price)
		{
			$left_symbol = $this->currency->getSymbolLeft();
			$right_symbol = $this->currency->getSymbolRight();

			$sale_price = str_replace($left_symbol, '', $special);
			$sale_price = str_replace($right_symbol, '', $sale_price);
			$sale_price = (float)$sale_price;

			$old_price = str_replace($left_symbol, '', $price);
			$old_price = str_replace($right_symbol, '', $old_price);
			$old_price = (float)$old_price;

			return round(($old_price - $sale_price) / $old_price * 100);
		}

		/* Swap Images */
		public function getProductImages($product_id, $limit = 1)
		{
			if (!$this->model_catalog_product)
			{
				$this->load->model('catalog/product');
			}

			/* @var ModelCatalogProduct $product_model */
			$product_model = $this->model_catalog_product;

			$images = array();

			$rows = $product_model->getProductImages($product_id);
			foreach ($rows as $row)
			{
				if ($limit && count($images) < $limit)
				{
					$images[] = $row['image'];
				}
			}

			return $images;
		}

		public function getImageSizeByPath($image_path)
		{
			$size = array(
				'width' => 0,
				'height' => 0
			);

			if (preg_match('/(\d+)x(\d+)\./',$image_path, $matches))
			{
				$size['width'] = $matches[1];
				$size['height'] = $matches[2];
			}

			return $size;
		}

		public function resizeImage($path, $width, $height)
		{
			if (!$this->model_tool_image)
			{
				$this->load->model('tool/image');
			}

			return $this->model_tool_image->resize($path, $width, $height);
		}

		/* Category Page */
		public function getSubCategories($path, $sub_categories)
		{
			$paths = explode('_', $path);

			$category_id = end($paths);

			$rows = $this->model_catalog_category->getCategories($category_id);

			$size = $this->getSkinOption('sub_category_image_size') || 80;

			$results = array();

			foreach ($rows as $index => $row) {
				$data = array(
					'filter_category_id'  => $row['category_id'],
					'filter_sub_category' => true
				);

				$product_total = $this->model_catalog_product->getTotalProducts($data);

				$result = array(
					'category_id' => $row['category_id'],
					'thumb' => $this->resizeImage($row['image'], $size, $size),
					'image' => $row['image'],
					'name'  => $sub_categories[$index]['name'],
					'href'  => $sub_categories[$index]['href']
				);

				if (!$result['thumb'])
				{
					$result['thumb'] = $this->resizeImage('no_image.jpg', $size, $size);
				}

				$results[] = $result;
			}

			return $results;
		}

		/* Product Page */
		public function getManufacturerImage($product_id)
		{
			$product = $this->model_catalog_product->getProduct($product_id);

			$manufacturer = $this->model_catalog_manufacturer->getManufacturer($product['manufacturer_id']);

			return $this->resizeImage($manufacturer['image'], $this->getSkinOption('brand_logo_width'), $this->getSkinOption('brand_logo_height'));
		}

		/* Checkout Page */
		public function onePageCheckoutInit(&$data)
		{
			if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout')))
			{
				$redirect = $this->url->link('checkout/cart');
			}

			// Validate minimum quantity requirments.
			$products = $this->cart->getProducts();

			foreach ($products as $product) {
				$product_total = 0;

				foreach ($products as $product_2) {
					if ($product_2['product_id'] == $product['product_id']) {
						$product_total += $product_2['quantity'];
					}
				}

				if ($product['minimum'] > $product_total) {
					$redirect = $this->url->link('checkout/cart');

					break;
				}
			}

			if (!empty($redirect))
			{
				$this->redirect($redirect);
				exit(0);
			}

			ModelKulerCommon::loadTexts($this->language->load('checkout/checkout'));
			ModelKulerCommon::loadTexts($this->language->load('account/register'));
			ModelKulerCommon::loadTexts($this->language->load('account/login'));
			ModelKulerCommon::loadTexts($this->language->load('checkout/cart'));

			$data = array(
				'customer_groups' => array()
			);

			$data['customer_group_id'] = $this->config->get('config_customer_group_id');

			$this->load->model('account/customer_group');

			if (is_array($this->config->get('config_customer_group_display'))) {
				$customer_groups = $this->model_account_customer_group->getCustomerGroups();

				foreach ($customer_groups as $customer_group) {
					if (in_array($customer_group['customer_group_id'], $this->config->get('config_customer_group_display'))) {
						$data['customer_groups'][] = $customer_group;
					}
				}
			}

			if (isset($this->session->data['shipping_postcode']))
			{
				$data['postcode'] = $this->session->data['shipping_postcode'];
			}
			else
			{
				$data['postcode'] = '';
			}

			$this->load->model('localisation/country');

			$data['countries'] = $this->model_localisation_country->getCountries();

			// Text agree term and condition
			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_checkout_id'));

			if ($information_info)
			{
				$data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information/info', 'information_id=' . $this->config->get('config_checkout_id'), 'SSL'), $information_info['title'], $information_info['title']);
			}
			else
			{
				$data['text_agree'] = '';
			}

			// Text agree register
			if ($this->config->get('config_account_id'))
			{
				$this->load->model('catalog/information');

				$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));

				if ($information_info)
				{
					$data['text_agree_register'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information/info', 'information_id=' . $this->config->get('config_account_id'), 'SSL'), $information_info['title'], $information_info['title']);
				}
				else
				{
					$data['text_agree_register'] = '';
				}
			}
			else
			{
				$data['text_agree_register'] = '';
			}

			$data['one_page_checkout_methods_url'] = 'index.php?route=module/kuler_cp/onePageCheckoutMethods';
			$data['redirect'] = $this->url->link('checkout/checkout', 'SSL');

			$data['is_logged'] = $this->customer->isLogged();

			// Personal Details
			$this->load->model('account/address');
			if ($this->customer->isLogged())
			{
				$data['address_id']         = $this->customer->getAddressId();

				$address_info = $this->model_account_address->getAddress($data['address_id']);

				$data['first_name']         = $address_info['firstname'];
				$data['last_name']          = $address_info['lastname'];
				$data['company']            = $address_info['company'];
				$data['company_id']         = $address_info['company_id'];
				$data['tax_id']             = $address_info['tax_id'];
				$data['address_1']          = $address_info['address_1'];
				$data['address_2']          = $address_info['address_2'];
				$data['city']               = $address_info['city'];
				$data['country_id']         = $address_info['country_id'];
				$data['zone_id']            = $address_info['zone_id'];

				$data['email']              = $this->customer->getEmail();
				$data['telephone']          = $this->customer->getTelephone();
				$data['fax']                = $this->customer->getFax();

				// Shipping
				if (!isset($this->session->data['shipping_address_id']))
				{
					$this->session->data['shipping_address_id'] = $this->customer->getAddressId();
				}

				$data['shipping_address_id'] = $this->session->data['shipping_address_id'];

				if (!isset($this->session->data['shipping_country_id']))
				{
					$this->session->data['shipping_country_id'] = $address_info['country_id'];
				}

				if (!isset($this->session->data['shipping_zone_id']))
				{
					$this->session->data['shipping_zone_id'] = $address_info['zone_id'];
				}

				if (!isset($this->session->data['shipping_postcode']))
				{
					$this->session->data['shipping_postcode'] = $address_info['postcode'];
				}

				// Payment
				if (!isset($this->session->data['payment_address_id']))
				{
					$this->session->data['payment_address_id'] = $this->customer->getAddressId();
				}

				$data['payment_address_id'] = $this->session->data['payment_address_id'];

				if (!isset($this->session->data['payment_country_id']))
				{
					$this->session->data['payment_country_id'] = $address_info['country_id'];
				}

				if (!isset($this->session->data['payment_zone_id']))
				{
					$this->session->data['payment_zone_id'] = $address_info['zone_id'];
				}
			}
			else
			{
				$data['address_id']         = 0;

				$data['first_name']         = '';
				$data['last_name']          = '';
				$data['company']            = '';
				$data['company_id']         = '';
				$data['tax_id']             = '';
				$data['address_1']          = '';
				$data['address_2']          = '';
				$data['city']               = '';

				$first_country = current($data['countries']);

				$data['country_id']         = $first_country['country_id'];
				$data['zone_id']            = '';

				$data['email']              = '';
				$data['telephone']          = '';
				$data['fax']                = '';
			}

			$data['addresses'] = $this->getAddresses();

			// Voucher & Coupon
			$data['voucher'] = isset($this->session->data['voucher']) ? $this->session->data['voucher'] : '';
			$data['coupon'] = isset($this->session->data['coupon']) ? $this->session->data['coupon'] : '';

			$data['login_url'] = 'index.php?route=module/kuler_cp/onePageCheckoutLogin';
			$data['forgotten'] = $this->url->link('account/forgotten', '', 'SSL');

			$data['order_confirm_url'] = 'index.php?route=module/kuler_cp/onePageCheckoutValidate';
		}

		/* Login Popup */
		public function loginPopupInit(&$data)
		{
			ModelKulerCommon::loadTexts($this->language->load('account/login'));

			$data['popup_login_url']    = 'index.php?route=module/kuler_cp/onePageCheckoutLogin';
			$data['register_url']       = $this->url->link('account/register', '', 'SSL');
			$data['forgotten_url']      = $this->url->link('account/forgotten', '', 'SSL');
		}

		/* Extra Positions */
		public function getModules($position)
		{
			$this->load->model('design/layout');
			$this->load->model('catalog/category');
			$this->load->model('catalog/product');
			$this->load->model('catalog/information');

			if (isset($this->request->get['route']))
			{
				$route = (string)$this->request->get['route'];
			}
			else
			{
				$route = 'common/home';
			}

			$layout_id = 0;

			if ($route == 'product/category' && isset($this->request->get['path']))
			{
				$path = explode('_', (string)$this->request->get['path']);

				$layout_id = $this->model_catalog_category->getCategoryLayoutId(end($path));
			}

			if ($route == 'product/product' && isset($this->request->get['product_id']))
			{
				$layout_id = $this->model_catalog_product->getProductLayoutId($this->request->get['product_id']);
			}

			if ($route == 'information/information' && isset($this->request->get['information_id']))
			{
				$layout_id = $this->model_catalog_information->getInformationLayoutId($this->request->get['information_id']);
			}

			if (!$layout_id)
			{
				$layout_id = $this->model_design_layout->getLayout($route);
			}

			if (!$layout_id)
			{
				$layout_id = $this->config->get('config_layout_id');
			}

			$module_data = array();

			$this->load->model('setting/extension');

			$extensions = $this->model_setting_extension->getExtensions('module');

			foreach ($extensions as $extension)
			{
				$modules = $this->config->get($extension['code'] . '_module');

				if ($modules)
				{
					foreach ($modules as $module)
					{
						if (($module['layout_id'] == $layout_id || $module['layout_id'] == -1) && $module['position'] == $position && $module['status'])
						{
							$module_data[] = array(
								'code'       => $extension['code'],
								'setting'    => $module,
								'sort_order' => $module['sort_order']
							);
						}
					}
				}
			}

			$sort_order = array();

			foreach ($module_data as $key => $value)
			{
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $module_data);

			$modules = array();

			foreach ($module_data as $module)
			{
				$module = $this->getChild('module/' . $module['code'], $module['setting']);

				if ($module)
				{
					$modules[] = $module;
				}
			}

			return $modules;
		}

		public function getAddresses()
		{
			if ($this->customer->isLogged())
			{
				$this->load->model('account/address');

				$addresses = $this->model_account_address->getAddresses();
			}
			else
			{
				$addresses = isset($this->session->data['temp_addresses']) ? $this->session->data['temp_addresses'] : array();
			}

			return $addresses;
		}

		public function processOutput($output)
		{
			// Add styles
			$output = str_replace('<!-- {STYLES} -->', $this->processStyles(), $output);

			// Add scripts
			$output = str_replace('<!-- {SCRIPTS} -->', $this->processScripts(), $output);

			// Add body scripts
			$output = str_replace('<!-- {BODY_SCRIPTS} -->', $this->processScripts(true), $output);

			return $output;
		}
	}

	class KulerResponse extends Response
	{
		private $headers = array();
		private $level = 0;
		private $output;

		public function addHeader($header) {
			$this->headers[] = $header;
		}

		public function redirect($url) {
			header('Location: ' . $url);
			exit;
		}

		public function setCompression($level) {
			$this->level = $level;
		}

		public function setOutput($output) {
			$this->output = $output;
		}

		private function compress($data, $level = 0) {
			if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false)) {
				$encoding = 'gzip';
			}

			if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false)) {
				$encoding = 'x-gzip';
			}

			if (!isset($encoding)) {
				return $data;
			}

			if (!extension_loaded('zlib') || ini_get('zlib.output_compression')) {
				return $data;
			}

			if (headers_sent()) {
				return $data;
			}

			if (connection_status()) {
				return $data;
			}

			$this->addHeader('Content-Encoding: ' . $encoding);

			return gzencode($data, (int)$level);
		}

		public function output() {
			if ($this->output) {
				$this->output = Kuler::getInstance()->processOutput($this->output);

				if ($this->level) {
					$ouput = $this->compress($this->output, $this->level);
				} else {
					$ouput = $this->output;
				}

				if (!headers_sent()) {
					foreach ($this->headers as $header) {
						header($header, true);
					}
				}

				echo $ouput;
			}
		}
	}

	/**
	 * Mobile Detect Library
	 * =====================
	 *
	 * Motto: "Every business should have a mobile detection script to detect mobile readers"
	 *
	 * Mobile_Detect is a lightweight PHP class for detecting mobile devices (including tablets).
	 * It uses the User-Agent string combined with specific HTTP headers to detect the mobile environment.
	 *
	 * @author      Current authors: Serban Ghita <serbanghita@gmail.com>, Nick Ilyin <nick.ilyin@gmail.com>
	 *              Original author: Victor Stanciu <vic.stanciu@gmail.com>
	 *
	 * @license     Code and contributions have 'MIT License'
	 *              More details: https://github.com/serbanghita/Mobile-Detect/blob/master/LICENSE.txt
	 *
	 * @link        Homepage:     http://mobiledetect.net
	 *              GitHub Repo:  https://github.com/serbanghita/Mobile-Detect
	 *              Google Code:  http://code.google.com/p/php-mobile-detect/
	 *              README:       https://github.com/serbanghita/Mobile-Detect/blob/master/README.md
	 *              HOWTO:        https://github.com/serbanghita/Mobile-Detect/wiki/Code-examples
	 *
	 * @version     2.7.6
	 */

	class Mobile_Detect
	{
		/**
		 * Mobile detection type.
		 *
		 * @deprecated since version 2.6.9
		 */
		const DETECTION_TYPE_MOBILE     = 'mobile';

		/**
		 * Extended detection type.
		 *
		 * @deprecated since version 2.6.9
		 */
		const DETECTION_TYPE_EXTENDED   = 'extended';

		/**
		 * A frequently used regular expression to extract version #s.
		 *
		 * @deprecated since version 2.6.9
		 */
		const VER                       = '([\w._\+]+)';

		/**
		 * Top-level device.
		 */
		const MOBILE_GRADE_A            = 'A';

		/**
		 * Mid-level device.
		 */
		const MOBILE_GRADE_B            = 'B';

		/**
		 * Low-level device.
		 */
		const MOBILE_GRADE_C            = 'C';

		/**
		 * Stores the version number of the current release.
		 */
		const VERSION                   = '2.7.6';

		/**
		 * A type for the version() method indicating a string return value.
		 */
		const VERSION_TYPE_STRING       = 'text';

		/**
		 * A type for the version() method indicating a float return value.
		 */
		const VERSION_TYPE_FLOAT        = 'float';

		/**
		 * The User-Agent HTTP header is stored in here.
		 * @var string
		 */
		protected $userAgent = null;

		/**
		 * HTTP headers in the PHP-flavor. So HTTP_USER_AGENT and SERVER_SOFTWARE.
		 * @var array
		 */
		protected $httpHeaders = array();

		/**
		 * The detection type, using self::DETECTION_TYPE_MOBILE or self::DETECTION_TYPE_EXTENDED.
		 *
		 * @deprecated since version 2.6.9
		 *
		 * @var string
		 */
		protected $detectionType = self::DETECTION_TYPE_MOBILE;

		/**
		 * HTTP headers that trigger the 'isMobile' detection
		 * to be true.
		 *
		 * @var array
		 */
		protected static $mobileHeaders = array(

			'HTTP_ACCEPT'                  => array('matches' => array(
				// Opera Mini; @reference: http://dev.opera.com/articles/view/opera-binary-markup-language/
				'application/x-obml2d',
				// BlackBerry devices.
				'application/vnd.rim.html',
				'text/vnd.wap.wml',
				'application/vnd.wap.xhtml+xml'
			)),
			'HTTP_X_WAP_PROFILE'           => null,
			'HTTP_X_WAP_CLIENTID'          => null,
			'HTTP_WAP_CONNECTION'          => null,
			'HTTP_PROFILE'                 => null,
			// Reported by Opera on Nokia devices (eg. C3).
			'HTTP_X_OPERAMINI_PHONE_UA'    => null,
			'HTTP_X_NOKIA_IPADDRESS'       => null,
			'HTTP_X_NOKIA_GATEWAY_ID'      => null,
			'HTTP_X_ORANGE_ID'             => null,
			'HTTP_X_VODAFONE_3GPDPCONTEXT' => null,
			'HTTP_X_HUAWEI_USERID'         => null,
			// Reported by Windows Smartphones.
			'HTTP_UA_OS'                   => null,
			// Reported by Verizon, Vodafone proxy system.
			'HTTP_X_MOBILE_GATEWAY'        => null,
			// Seend this on HTC Sensation. @ref: SensationXE_Beats_Z715e.
			'HTTP_X_ATT_DEVICEID'          => null,
			// Seen this on a HTC.
			'HTTP_UA_CPU'                  => array('matches' => array('ARM')),
		);

		/**
		 * List of mobile devices (phones).
		 *
		 * @var array
		 */
		protected static $phoneDevices = array(
			'iPhone'        => '\biPhone.*Mobile|\biPod', // |\biTunes
			'BlackBerry'    => 'BlackBerry|\bBB10\b|rim[0-9]+',
			'HTC'           => 'HTC|HTC.*(Sensation|Evo|Vision|Explorer|6800|8100|8900|A7272|S510e|C110e|Legend|Desire|T8282)|APX515CKT|Qtek9090|APA9292KT|HD_mini|Sensation.*Z710e|PG86100|Z715e|Desire.*(A8181|HD)|ADR6200|ADR6400L|ADR6425|001HT|Inspire 4G|Android.*\bEVO\b|T-Mobile G1|Z520m',
			'Nexus'         => 'Nexus One|Nexus S|Galaxy.*Nexus|Android.*Nexus.*Mobile',
			// @todo: Is 'Dell Streak' a tablet or a phone? ;)
			'Dell'          => 'Dell.*Streak|Dell.*Aero|Dell.*Venue|DELL.*Venue Pro|Dell Flash|Dell Smoke|Dell Mini 3iX|XCD28|XCD35|\b001DL\b|\b101DL\b|\bGS01\b',
			'Motorola'      => 'Motorola|\bDroid\b.*Build|DROIDX|Android.*Xoom|HRI39|MOT-|A1260|A1680|A555|A853|A855|A953|A955|A956|Motorola.*ELECTRIFY|Motorola.*i1|i867|i940|MB200|MB300|MB501|MB502|MB508|MB511|MB520|MB525|MB526|MB611|MB612|MB632|MB810|MB855|MB860|MB861|MB865|MB870|ME501|ME502|ME511|ME525|ME600|ME632|ME722|ME811|ME860|ME863|ME865|MT620|MT710|MT716|MT720|MT810|MT870|MT917|Motorola.*TITANIUM|WX435|WX445|XT300|XT301|XT311|XT316|XT317|XT319|XT320|XT390|XT502|XT530|XT531|XT532|XT535|XT603|XT610|XT611|XT615|XT681|XT701|XT702|XT711|XT720|XT800|XT806|XT860|XT862|XT875|XT882|XT883|XT894|XT901|XT907|XT909|XT910|XT912|XT928|XT926|XT915|XT919|XT925',
			'Samsung'       => 'Samsung|SGH-I337|BGT-S5230|GT-B2100|GT-B2700|GT-B2710|GT-B3210|GT-B3310|GT-B3410|GT-B3730|GT-B3740|GT-B5510|GT-B5512|GT-B5722|GT-B6520|GT-B7300|GT-B7320|GT-B7330|GT-B7350|GT-B7510|GT-B7722|GT-B7800|GT-C3010|GT-C3011|GT-C3060|GT-C3200|GT-C3212|GT-C3212I|GT-C3262|GT-C3222|GT-C3300|GT-C3300K|GT-C3303|GT-C3303K|GT-C3310|GT-C3322|GT-C3330|GT-C3350|GT-C3500|GT-C3510|GT-C3530|GT-C3630|GT-C3780|GT-C5010|GT-C5212|GT-C6620|GT-C6625|GT-C6712|GT-E1050|GT-E1070|GT-E1075|GT-E1080|GT-E1081|GT-E1085|GT-E1087|GT-E1100|GT-E1107|GT-E1110|GT-E1120|GT-E1125|GT-E1130|GT-E1160|GT-E1170|GT-E1175|GT-E1180|GT-E1182|GT-E1200|GT-E1210|GT-E1225|GT-E1230|GT-E1390|GT-E2100|GT-E2120|GT-E2121|GT-E2152|GT-E2220|GT-E2222|GT-E2230|GT-E2232|GT-E2250|GT-E2370|GT-E2550|GT-E2652|GT-E3210|GT-E3213|GT-I5500|GT-I5503|GT-I5700|GT-I5800|GT-I5801|GT-I6410|GT-I6420|GT-I7110|GT-I7410|GT-I7500|GT-I8000|GT-I8150|GT-I8160|GT-I8190|GT-I8320|GT-I8330|GT-I8350|GT-I8530|GT-I8700|GT-I8703|GT-I8910|GT-I9000|GT-I9001|GT-I9003|GT-I9010|GT-I9020|GT-I9023|GT-I9070|GT-I9082|GT-I9100|GT-I9103|GT-I9220|GT-I9250|GT-I9300|GT-I9305|GT-I9500|GT-I9505|GT-M3510|GT-M5650|GT-M7500|GT-M7600|GT-M7603|GT-M8800|GT-M8910|GT-N7000|GT-S3110|GT-S3310|GT-S3350|GT-S3353|GT-S3370|GT-S3650|GT-S3653|GT-S3770|GT-S3850|GT-S5210|GT-S5220|GT-S5229|GT-S5230|GT-S5233|GT-S5250|GT-S5253|GT-S5260|GT-S5263|GT-S5270|GT-S5300|GT-S5330|GT-S5350|GT-S5360|GT-S5363|GT-S5369|GT-S5380|GT-S5380D|GT-S5560|GT-S5570|GT-S5600|GT-S5603|GT-S5610|GT-S5620|GT-S5660|GT-S5670|GT-S5690|GT-S5750|GT-S5780|GT-S5830|GT-S5839|GT-S6102|GT-S6500|GT-S7070|GT-S7200|GT-S7220|GT-S7230|GT-S7233|GT-S7250|GT-S7500|GT-S7530|GT-S7550|GT-S7562|GT-S7710|GT-S8000|GT-S8003|GT-S8500|GT-S8530|GT-S8600|SCH-A310|SCH-A530|SCH-A570|SCH-A610|SCH-A630|SCH-A650|SCH-A790|SCH-A795|SCH-A850|SCH-A870|SCH-A890|SCH-A930|SCH-A950|SCH-A970|SCH-A990|SCH-I100|SCH-I110|SCH-I400|SCH-I405|SCH-I500|SCH-I510|SCH-I515|SCH-I600|SCH-I730|SCH-I760|SCH-I770|SCH-I830|SCH-I910|SCH-I920|SCH-I959|SCH-LC11|SCH-N150|SCH-N300|SCH-R100|SCH-R300|SCH-R351|SCH-R400|SCH-R410|SCH-T300|SCH-U310|SCH-U320|SCH-U350|SCH-U360|SCH-U365|SCH-U370|SCH-U380|SCH-U410|SCH-U430|SCH-U450|SCH-U460|SCH-U470|SCH-U490|SCH-U540|SCH-U550|SCH-U620|SCH-U640|SCH-U650|SCH-U660|SCH-U700|SCH-U740|SCH-U750|SCH-U810|SCH-U820|SCH-U900|SCH-U940|SCH-U960|SCS-26UC|SGH-A107|SGH-A117|SGH-A127|SGH-A137|SGH-A157|SGH-A167|SGH-A177|SGH-A187|SGH-A197|SGH-A227|SGH-A237|SGH-A257|SGH-A437|SGH-A517|SGH-A597|SGH-A637|SGH-A657|SGH-A667|SGH-A687|SGH-A697|SGH-A707|SGH-A717|SGH-A727|SGH-A737|SGH-A747|SGH-A767|SGH-A777|SGH-A797|SGH-A817|SGH-A827|SGH-A837|SGH-A847|SGH-A867|SGH-A877|SGH-A887|SGH-A897|SGH-A927|SGH-B100|SGH-B130|SGH-B200|SGH-B220|SGH-C100|SGH-C110|SGH-C120|SGH-C130|SGH-C140|SGH-C160|SGH-C170|SGH-C180|SGH-C200|SGH-C207|SGH-C210|SGH-C225|SGH-C230|SGH-C417|SGH-C450|SGH-D307|SGH-D347|SGH-D357|SGH-D407|SGH-D415|SGH-D780|SGH-D807|SGH-D980|SGH-E105|SGH-E200|SGH-E315|SGH-E316|SGH-E317|SGH-E335|SGH-E590|SGH-E635|SGH-E715|SGH-E890|SGH-F300|SGH-F480|SGH-I200|SGH-I300|SGH-I320|SGH-I550|SGH-I577|SGH-I600|SGH-I607|SGH-I617|SGH-I627|SGH-I637|SGH-I677|SGH-I700|SGH-I717|SGH-I727|SGH-i747M|SGH-I777|SGH-I780|SGH-I827|SGH-I847|SGH-I857|SGH-I896|SGH-I897|SGH-I900|SGH-I907|SGH-I917|SGH-I927|SGH-I937|SGH-I997|SGH-J150|SGH-J200|SGH-L170|SGH-L700|SGH-M110|SGH-M150|SGH-M200|SGH-N105|SGH-N500|SGH-N600|SGH-N620|SGH-N625|SGH-N700|SGH-N710|SGH-P107|SGH-P207|SGH-P300|SGH-P310|SGH-P520|SGH-P735|SGH-P777|SGH-Q105|SGH-R210|SGH-R220|SGH-R225|SGH-S105|SGH-S307|SGH-T109|SGH-T119|SGH-T139|SGH-T209|SGH-T219|SGH-T229|SGH-T239|SGH-T249|SGH-T259|SGH-T309|SGH-T319|SGH-T329|SGH-T339|SGH-T349|SGH-T359|SGH-T369|SGH-T379|SGH-T409|SGH-T429|SGH-T439|SGH-T459|SGH-T469|SGH-T479|SGH-T499|SGH-T509|SGH-T519|SGH-T539|SGH-T559|SGH-T589|SGH-T609|SGH-T619|SGH-T629|SGH-T639|SGH-T659|SGH-T669|SGH-T679|SGH-T709|SGH-T719|SGH-T729|SGH-T739|SGH-T746|SGH-T749|SGH-T759|SGH-T769|SGH-T809|SGH-T819|SGH-T839|SGH-T919|SGH-T929|SGH-T939|SGH-T959|SGH-T989|SGH-U100|SGH-U200|SGH-U800|SGH-V205|SGH-V206|SGH-X100|SGH-X105|SGH-X120|SGH-X140|SGH-X426|SGH-X427|SGH-X475|SGH-X495|SGH-X497|SGH-X507|SGH-X600|SGH-X610|SGH-X620|SGH-X630|SGH-X700|SGH-X820|SGH-X890|SGH-Z130|SGH-Z150|SGH-Z170|SGH-ZX10|SGH-ZX20|SHW-M110|SPH-A120|SPH-A400|SPH-A420|SPH-A460|SPH-A500|SPH-A560|SPH-A600|SPH-A620|SPH-A660|SPH-A700|SPH-A740|SPH-A760|SPH-A790|SPH-A800|SPH-A820|SPH-A840|SPH-A880|SPH-A900|SPH-A940|SPH-A960|SPH-D600|SPH-D700|SPH-D710|SPH-D720|SPH-I300|SPH-I325|SPH-I330|SPH-I350|SPH-I500|SPH-I600|SPH-I700|SPH-L700|SPH-M100|SPH-M220|SPH-M240|SPH-M300|SPH-M305|SPH-M320|SPH-M330|SPH-M350|SPH-M360|SPH-M370|SPH-M380|SPH-M510|SPH-M540|SPH-M550|SPH-M560|SPH-M570|SPH-M580|SPH-M610|SPH-M620|SPH-M630|SPH-M800|SPH-M810|SPH-M850|SPH-M900|SPH-M910|SPH-M920|SPH-M930|SPH-N100|SPH-N200|SPH-N240|SPH-N300|SPH-N400|SPH-Z400|SWC-E100|SCH-i909|GT-N7100|GT-N7105|SCH-I535|SM-N900A|SGH-I317|SGH-T999L|GT-S5360B',
			'LG'            => '\bLG\b;|LG[- ]?(C800|C900|E400|E610|E900|E-900|F160|F180K|F180L|F180S|730|855|L160|LS840|LS970|LU6200|MS690|MS695|MS770|MS840|MS870|MS910|P500|P700|P705|VM696|AS680|AS695|AX840|C729|E970|GS505|272|C395|E739BK|E960|L55C|L75C|LS696|LS860|P769BK|P350|P500|P509|P870|UN272|US730|VS840|VS950|LN272|LN510|LS670|LS855|LW690|MN270|MN510|P509|P769|P930|UN200|UN270|UN510|UN610|US670|US740|US760|UX265|UX840|VN271|VN530|VS660|VS700|VS740|VS750|VS910|VS920|VS930|VX9200|VX11000|AX840A|LW770|P506|P925|P999)',
			'Sony'          => 'SonyST|SonyLT|SonyEricsson|SonyEricssonLT15iv|LT18i|E10i|LT28h|LT26w|SonyEricssonMT27i',
			'Asus'          => 'Asus.*Galaxy|PadFone.*Mobile',
			// @ref: http://www.micromaxinfo.com/mobiles/smartphones
			// Added because the codes might conflict with Acer Tablets.
			'Micromax'      => 'Micromax.*\b(A210|A92|A88|A72|A111|A110Q|A115|A116|A110|A90S|A26|A51|A35|A54|A25|A27|A89|A68|A65|A57|A90)\b',
			'Palm'          => 'PalmSource|Palm', // avantgo|blazer|elaine|hiptop|plucker|xiino ; @todo - complete the regex.
			'Vertu'         => 'Vertu|Vertu.*Ltd|Vertu.*Ascent|Vertu.*Ayxta|Vertu.*Constellation(F|Quest)?|Vertu.*Monika|Vertu.*Signature', // Just for fun ;)
			// @ref: http://www.pantech.co.kr/en/prod/prodList.do?gbrand=VEGA (PANTECH)
			// Most of the VEGA devices are legacy. PANTECH seem to be newer devices based on Android.
			'Pantech'       => 'PANTECH|IM-A850S|IM-A840S|IM-A830L|IM-A830K|IM-A830S|IM-A820L|IM-A810K|IM-A810S|IM-A800S|IM-T100K|IM-A725L|IM-A780L|IM-A775C|IM-A770K|IM-A760S|IM-A750K|IM-A740S|IM-A730S|IM-A720L|IM-A710K|IM-A690L|IM-A690S|IM-A650S|IM-A630K|IM-A600S|VEGA PTL21|PT003|P8010|ADR910L|P6030|P6020|P9070|P4100|P9060|P5000|CDM8992|TXT8045|ADR8995|IS11PT|P2030|P6010|P8000|PT002|IS06|CDM8999|P9050|PT001|TXT8040|P2020|P9020|P2000|P7040|P7000|C790',
			// @ref: http://www.fly-phone.com/devices/smartphones/ ; Included only smartphones.
			'Fly'           => 'IQ230|IQ444|IQ450|IQ440|IQ442|IQ441|IQ245|IQ256|IQ236|IQ255|IQ235|IQ245|IQ275|IQ240|IQ285|IQ280|IQ270|IQ260|IQ250',
			// Added simvalley mobile just for fun. They have some interesting devices.
			// @ref: http://www.simvalley.fr/telephonie---gps-_22_telephonie-mobile_telephones_.html
			'SimValley'     => '\b(SP-80|XT-930|SX-340|XT-930|SX-310|SP-360|SP60|SPT-800|SP-120|SPT-800|SP-140|SPX-5|SPX-8|SP-100|SPX-8|SPX-12)\b',
			// @Tapatalk is a mobile app; @ref: http://support.tapatalk.com/threads/smf-2-0-2-os-and-browser-detection-plugin-and-tapatalk.15565/#post-79039
			'GenericPhone'  => 'Tapatalk|PDA;|SAGEM|\bmmp\b|pocket|\bpsp\b|symbian|Smartphone|smartfon|treo|up.browser|up.link|vodafone|\bwap\b|nokia|Series40|Series60|S60|SonyEricsson|N900|MAUI.*WAP.*Browser'
		);

		/**
		 * List of tablet devices.
		 *
		 * @var array
		 */
		protected static $tabletDevices = array(
			'iPad'              => 'iPad|iPad.*Mobile', // @todo: check for mobile friendly emails topic.
			'NexusTablet'       => '^.*Android.*Nexus(((?:(?!Mobile))|(?:(\s(7|10).+))).)*$',
			'SamsungTablet'     => 'SAMSUNG.*Tablet|Galaxy.*Tab|SC-01C|GT-P1000|GT-P1003|GT-P1010|GT-P3105|GT-P6210|GT-P6800|GT-P6810|GT-P7100|GT-P7300|GT-P7310|GT-P7500|GT-P7510|SCH-I800|SCH-I815|SCH-I905|SGH-I957|SGH-I987|SGH-T849|SGH-T859|SGH-T869|SPH-P100|GT-P3100|GT-P3108|GT-P3110|GT-P5100|GT-P5110|GT-P6200|GT-P7320|GT-P7511|GT-N8000|GT-P8510|SGH-I497|SPH-P500|SGH-T779|SCH-I705|SCH-I915|GT-N8013|GT-P3113|GT-P5113|GT-P8110|GT-N8010|GT-N8005|GT-N8020|GT-P1013|GT-P6201|GT-P7501|GT-N5100|GT-N5110|SHV-E140K|SHV-E140L|SHV-E140S|SHV-E150S|SHV-E230K|SHV-E230L|SHV-E230S|SHW-M180K|SHW-M180L|SHW-M180S|SHW-M180W|SHW-M300W|SHW-M305W|SHW-M380K|SHW-M380S|SHW-M380W|SHW-M430W|SHW-M480K|SHW-M480S|SHW-M480W|SHW-M485W|SHW-M486W|SHW-M500W|GT-I9228|SCH-P739|SCH-I925|GT-I9200|GT-I9205|GT-P5200|GT-P5210|SM-T311|SM-T310|SM-T210|SM-T210R|SM-T211|SM-P600|SM-P601|SM-P605|SM-P900|SM-T217|SM-T217A|SM-T217S|SM-P6000|SM-T3100|SGH-I467|XE500',
			// @reference: http://www.labnol.org/software/kindle-user-agent-string/20378/
			'Kindle'            => 'Kindle|Silk.*Accelerated|Android.*\b(KFOT|KFTT|KFJWI|KFJWA|KFOTE|KFSOWI|KFTHWI|KFTHWA|KFAPWI|KFAPWA|WFJWAE)\b',
			// Only the Surface tablets with Windows RT are considered mobile.
			// @ref: http://msdn.microsoft.com/en-us/library/ie/hh920767(v=vs.85).aspx
			'SurfaceTablet'     => 'Windows NT [0-9.]+; ARM;',
			// @ref: http://shopping1.hp.com/is-bin/INTERSHOP.enfinity/WFS/WW-USSMBPublicStore-Site/en_US/-/USD/ViewStandardCatalog-Browse?CatalogCategoryID=JfIQ7EN5lqMAAAEyDcJUDwMT
			'HPTablet'          => 'HP Slate 7|HP ElitePad 900|hp-tablet|EliteBook.*Touch',
			// @note: watch out for PadFone, see #132
			'AsusTablet'        => '^.*PadFone((?!Mobile).)*$|Transformer|TF101|TF101G|TF300T|TF300TG|TF300TL|TF700T|TF700KL|TF701T|TF810C|ME171|ME301T|ME302C|ME371MG|ME370T|ME372MG|ME172V|ME173X|ME400C|Slider SL101',
			'BlackBerryTablet'  => 'PlayBook|RIM Tablet',
			'HTCtablet'         => 'HTC Flyer|HTC Jetstream|HTC-P715a|HTC EVO View 4G|PG41200',
			'MotorolaTablet'    => 'xoom|sholest|MZ615|MZ605|MZ505|MZ601|MZ602|MZ603|MZ604|MZ606|MZ607|MZ608|MZ609|MZ615|MZ616|MZ617',
			'NookTablet'        => 'Android.*Nook|NookColor|nook browser|BNRV200|BNRV200A|BNTV250|BNTV250A|BNTV400|BNTV600|LogicPD Zoom2',
			// @ref: http://www.acer.ro/ac/ro/RO/content/drivers
			// @ref: http://www.packardbell.co.uk/pb/en/GB/content/download (Packard Bell is part of Acer)
			// @ref: http://us.acer.com/ac/en/US/content/group/tablets
			// @note: Can conflict with Micromax and Motorola phones codes.
			'AcerTablet'        => 'Android.*; \b(A100|A101|A110|A200|A210|A211|A500|A501|A510|A511|A700|A701|W500|W500P|W501|W501P|W510|W511|W700|G100|G100W|B1-A71|B1-710|B1-711|A1-810)\b|W3-810',
			// @ref: http://eu.computers.toshiba-europe.com/innovation/family/Tablets/1098744/banner_id/tablet_footerlink/
			// @ref: http://us.toshiba.com/tablets/tablet-finder
			// @ref: http://www.toshiba.co.jp/regza/tablet/
			'ToshibaTablet'     => 'Android.*(AT100|AT105|AT200|AT205|AT270|AT275|AT300|AT305|AT1S5|AT500|AT570|AT700|AT830)|TOSHIBA.*FOLIO',
			// @ref: http://www.nttdocomo.co.jp/english/service/developer/smart_phone/technical_info/spec/index.html
			'LGTablet'          => '\bL-06C|LG-V900|LG-V909\b',
			'FujitsuTablet'     => 'Android.*\b(F-01D|F-05E|F-10D|M532|Q572)\b',
			// Prestigio Tablets http://www.prestigio.com/support
			'PrestigioTablet'   => 'PMP3170B|PMP3270B|PMP3470B|PMP7170B|PMP3370B|PMP3570C|PMP5870C|PMP3670B|PMP5570C|PMP5770D|PMP3970B|PMP3870C|PMP5580C|PMP5880D|PMP5780D|PMP5588C|PMP7280C|PMP7280|PMP7880D|PMP5597D|PMP5597|PMP7100D|PER3464|PER3274|PER3574|PER3884|PER5274|PER5474|PMP5097CPRO|PMP5097|PMP7380D|PMP5297C|PMP5297C_QUAD',
			// @ref: http://support.lenovo.com/en_GB/downloads/default.page?#
			'LenovoTablet'      => 'IdeaTab|S2110|S6000|K3011|A3000|A1000|A2107|A2109|A1107|ThinkPad([ ]+)?Tablet',
			'YarvikTablet'      => 'Android.*(TAB210|TAB211|TAB224|TAB250|TAB260|TAB264|TAB310|TAB360|TAB364|TAB410|TAB411|TAB420|TAB424|TAB450|TAB460|TAB461|TAB464|TAB465|TAB467|TAB468)',
			'MedionTablet'      => 'Android.*\bOYO\b|LIFE.*(P9212|P9514|P9516|S9512)|LIFETAB',
			'ArnovaTablet'      => 'AN10G2|AN7bG3|AN7fG3|AN8G3|AN8cG3|AN7G3|AN9G3|AN7dG3|AN7dG3ST|AN7dG3ChildPad|AN10bG3|AN10bG3DT',
			// IRU.ru Tablets http://www.iru.ru/catalog/soho/planetable/
			'IRUTablet'         => 'M702pro',
			'MegafonTablet'     => 'MegaFon V9|\bZTE V9\b|Android.*\bMT7A\b',
			// @ref: http://www.e-boda.ro/tablete-pc.html
			'EbodaTablet'       => 'E-Boda (Supreme|Impresspeed|Izzycomm|Essential)',
			// @ref: http://www.allview.ro/produse/droseries/lista-tablete-pc/
			'AllViewTablet'           => 'Allview.*(Viva|Alldro|City|Speed|All TV|Frenzy|Quasar|Shine|TX1|AX1|AX2)',
			// @reference: http://wiki.archosfans.com/index.php?title=Main_Page
			'ArchosTablet'      => '\b(101G9|80G9|A101IT)\b|Qilive 97R',
			// @ref: http://www.ainol.com/plugin.php?identifier=ainol&module=product
			'AinolTablet'       => 'NOVO7|NOVO8|NOVO10|Novo7Aurora|Novo7Basic|NOVO7PALADIN|novo9-Spark',
			// @todo: inspect http://esupport.sony.com/US/p/select-system.pl?DIRECTOR=DRIVER
			// @ref: Readers http://www.atsuhiro-me.net/ebook/sony-reader/sony-reader-web-browser
			// @ref: http://www.sony.jp/support/tablet/
			'SonyTablet'        => 'Sony.*Tablet|Xperia Tablet|Sony Tablet S|SO-03E|SGPT12|SGPT13|SGPT114|SGPT121|SGPT122|SGPT123|SGPT111|SGPT112|SGPT113|SGPT131|SGPT132|SGPT133|SGPT211|SGPT212|SGPT213|SGP311|SGP312|SGP321|EBRD1101|EBRD1102|EBRD1201',
			// @ref: db + http://www.cube-tablet.com/buy-products.html
			'CubeTablet'        => 'Android.*(K8GT|U9GT|U10GT|U16GT|U17GT|U18GT|U19GT|U20GT|U23GT|U30GT)|CUBE U8GT',
			// @ref: http://www.cobyusa.com/?p=pcat&pcat_id=3001
			'CobyTablet'        => 'MID1042|MID1045|MID1125|MID1126|MID7012|MID7014|MID7015|MID7034|MID7035|MID7036|MID7042|MID7048|MID7127|MID8042|MID8048|MID8127|MID9042|MID9740|MID9742|MID7022|MID7010',
			// @ref: http://www.match.net.cn/products.asp
			'MIDTablet'         => 'M9701|M9000|M9100|M806|M1052|M806|T703|MID701|MID713|MID710|MID727|MID760|MID830|MID728|MID933|MID125|MID810|MID732|MID120|MID930|MID800|MID731|MID900|MID100|MID820|MID735|MID980|MID130|MID833|MID737|MID960|MID135|MID860|MID736|MID140|MID930|MID835|MID733',
			// @ref: http://pdadb.net/index.php?m=pdalist&list=SMiT (NoName Chinese Tablets)
			// @ref: http://www.imp3.net/14/show.php?itemid=20454
			'SMiTTablet'        => 'Android.*(\bMID\b|MID-560|MTV-T1200|MTV-PND531|MTV-P1101|MTV-PND530)',
			// @ref: http://www.rock-chips.com/index.php?do=prod&pid=2
			'RockChipTablet'    => 'Android.*(RK2818|RK2808A|RK2918|RK3066)|RK2738|RK2808A',
			// @ref: http://www.fly-phone.com/devices/tablets/ ; http://www.fly-phone.com/service/
			'FlyTablet'         => 'IQ310|Fly Vision',
			// @ref: http://www.bqreaders.com/gb/tablets-prices-sale.html
			'bqTablet'          => 'bq.*(Elcano|Curie|Edison|Maxwell|Kepler|Pascal|Tesla|Hypatia|Platon|Newton|Livingstone|Cervantes|Avant)|Maxwell.*Lite|Maxwell.*Plus',
			// @ref: http://www.huaweidevice.com/worldwide/productFamily.do?method=index&directoryId=5011&treeId=3290
			// @ref: http://www.huaweidevice.com/worldwide/downloadCenter.do?method=index&directoryId=3372&treeId=0&tb=1&type=software (including legacy tablets)
			'HuaweiTablet'      => 'MediaPad|IDEOS S7|S7-201c|S7-202u|S7-101|S7-103|S7-104|S7-105|S7-106|S7-201|S7-Slim',
			// Nec or Medias Tab
			'NecTablet'         => '\bN-06D|\bN-08D',
			// Pantech Tablets: http://www.pantechusa.com/phones/
			'PantechTablet'     => 'Pantech.*P4100',
			// Broncho Tablets: http://www.broncho.cn/ (hard to find)
			'BronchoTablet'     => 'Broncho.*(N701|N708|N802|a710)',
			// @ref: http://versusuk.com/support.html
			'VersusTablet'      => 'TOUCHPAD.*[78910]|\bTOUCHTAB\b',
			// @ref: http://www.zync.in/index.php/our-products/tablet-phablets
			'ZyncTablet'        => 'z1000|Z99 2G|z99|z930|z999|z990|z909|Z919|z900',
			// @ref: http://www.positivoinformatica.com.br/www/pessoal/tablet-ypy/
			'PositivoTablet'    => 'TB07STA|TB10STA|TB07FTA|TB10FTA',
			// @ref: https://www.nabitablet.com/
			'NabiTablet'        => 'Android.*\bNabi',
			'KoboTablet'        => 'Kobo Touch|\bK080\b|\bVox\b Build|\bArc\b Build',
			// French Danew Tablets http://www.danew.com/produits-tablette.php
			'DanewTablet'       => 'DSlide.*\b(700|701R|702|703R|704|802|970|971|972|973|974|1010|1012)\b',
			// Texet Tablets and Readers http://www.texet.ru/tablet/
			'TexetTablet'       => 'NaviPad|TB-772A|TM-7045|TM-7055|TM-9750|TM-7016|TM-7024|TM-7026|TM-7041|TM-7043|TM-7047|TM-8041|TM-9741|TM-9747|TM-9748|TM-9751|TM-7022|TM-7021|TM-7020|TM-7011|TM-7010|TM-7023|TM-7025|TM-7037W|TM-7038W|TM-7027W|TM-9720|TM-9725|TM-9737W|TM-1020|TM-9738W|TM-9740|TM-9743W|TB-807A|TB-771A|TB-727A|TB-725A|TB-719A|TB-823A|TB-805A|TB-723A|TB-715A|TB-707A|TB-705A|TB-709A|TB-711A|TB-890HD|TB-880HD|TB-790HD|TB-780HD|TB-770HD|TB-721HD|TB-710HD|TB-434HD|TB-860HD|TB-840HD|TB-760HD|TB-750HD|TB-740HD|TB-730HD|TB-722HD|TB-720HD|TB-700HD|TB-500HD|TB-470HD|TB-431HD|TB-430HD|TB-506|TB-504|TB-446|TB-436|TB-416|TB-146SE|TB-126SE',
			// @note: Avoid detecting 'PLAYSTATION 3' as mobile.
			'PlaystationTablet' => 'Playstation.*(Portable|Vita)',
			// @ref: http://www.galapad.net/product.html
			'GalapadTablet'     => 'Android.*\bG1\b',
			// @ref: http://www.micromaxinfo.com/tablet/funbook
			'MicromaxTablet'    => 'Funbook|Micromax.*\b(P250|P560|P360|P362|P600|P300|P350|P500|P275)\b',
			// http://www.karbonnmobiles.com/products_tablet.php
			'KarbonnTablet'     => 'Android.*\b(A39|A37|A34|ST8|ST10|ST7|Smart Tab3|Smart Tab2)\b',
			// @ref: http://www.myallfine.com/Products.asp
			'AllFineTablet'     => 'Fine7 Genius|Fine7 Shine|Fine7 Air|Fine8 Style|Fine9 More|Fine10 Joy|Fine11 Wide',
			// @ref: http://www.proscanvideo.com/products-search.asp?itemClass=TABLET&itemnmbr=
			'PROSCANTablet'     => '\b(PEM63|PLT1023G|PLT1041|PLT1044|PLT1044G|PLT1091|PLT4311|PLT4311PL|PLT4315|PLT7030|PLT7033|PLT7033D|PLT7035|PLT7035D|PLT7044K|PLT7045K|PLT7045KB|PLT7071KG|PLT7072|PLT7223G|PLT7225G|PLT7777G|PLT7810K|PLT7849G|PLT7851G|PLT7852G|PLT8015|PLT8031|PLT8034|PLT8036|PLT8080K|PLT8082|PLT8088|PLT8223G|PLT8234G|PLT8235G|PLT8816K|PLT9011|PLT9045K|PLT9233G|PLT9735|PLT9760G|PLT9770G)\b',
			// @ref: http://www.yonesnav.com/products/products.php
			'YONESTablet' => 'BQ1078|BC1003|BC1077|RK9702|BC9730|BC9001|IT9001|BC7008|BC7010|BC708|BC728|BC7012|BC7030|BC7027|BC7026',
			// @ref: http://www.cjshowroom.com/eproducts.aspx?classcode=004001001
			// China manufacturer makes tablets for different small brands (eg. http://www.zeepad.net/index.html)
			'ChangJiaTablet'    => 'TPC7102|TPC7103|TPC7105|TPC7106|TPC7107|TPC7201|TPC7203|TPC7205|TPC7210|TPC7708|TPC7709|TPC7712|TPC7110|TPC8101|TPC8103|TPC8105|TPC8106|TPC8203|TPC8205|TPC8503|TPC9106|TPC9701|TPC97101|TPC97103|TPC97105|TPC97106|TPC97111|TPC97113|TPC97203|TPC97603|TPC97809|TPC97205|TPC10101|TPC10103|TPC10106|TPC10111|TPC10203|TPC10205|TPC10503',
			// @ref: http://www.gloryunion.cn/products.asp
			// @ref: http://www.allwinnertech.com/en/apply/mobile.html
			// @ref: http://www.ptcl.com.pk/pd_content.php?pd_id=284 (EVOTAB)
			// aka. Cute or Cool tablets. Not sure yet, must research to avoid collisions.
			'GUTablet'          => 'TX-A1301|TX-M9002|Q702', // A12R|D75A|D77|D79|R83|A95|A106C|R15|A75|A76|D71|D72|R71|R73|R77|D82|R85|D92|A97|D92|R91|A10F|A77F|W71F|A78F|W78F|W81F|A97F|W91F|W97F|R16G|C72|C73E|K72|K73|R96G
			// @ref: http://www.pointofview-online.com/showroom.php?shop_mode=product_listing&category_id=118
			'PointOfViewTablet' => 'TAB-P506|TAB-navi-7-3G-M|TAB-P517|TAB-P-527|TAB-P701|TAB-P703|TAB-P721|TAB-P731N|TAB-P741|TAB-P825|TAB-P905|TAB-P925|TAB-PR945|TAB-PL1015|TAB-P1025|TAB-PI1045|TAB-P1325|TAB-PROTAB[0-9]+|TAB-PROTAB25|TAB-PROTAB26|TAB-PROTAB27|TAB-PROTAB26XL|TAB-PROTAB2-IPS9|TAB-PROTAB30-IPS9|TAB-PROTAB25XXL|TAB-PROTAB26-IPS10|TAB-PROTAB30-IPS10',
			// @ref: http://www.overmax.pl/pl/katalog-produktow,p8/tablety,c14/
			// @todo: add more tests.
			'OvermaxTablet'     => 'OV-(SteelCore|NewBase|Basecore|Baseone|Exellen|Quattor|EduTab|Solution|ACTION|BasicTab|TeddyTab|MagicTab|Stream|TB-08|TB-09)',
			// @ref: http://hclmetablet.com/India/index.php
			'HCLTablet'         => 'HCL.*Tablet|Connect-3G-2.0|Connect-2G-2.0|ME Tablet U1|ME Tablet U2|ME Tablet G1|ME Tablet X1|ME Tablet Y2|ME Tablet Sync',
			// @ref: http://www.edigital.hu/Tablet_es_e-book_olvaso/Tablet-c18385.html
			'DPSTablet'         => 'DPS Dream 9|DPS Dual 7',
			// @ref: http://www.visture.com/index.asp
			'VistureTablet'     => 'V97 HD|i75 3G|Visture V4( HD)?|Visture V5( HD)?|Visture V10',
			// @ref: http://www.mijncresta.nl/tablet
			'CrestaTablet'     => 'CTP(-)?810|CTP(-)?818|CTP(-)?828|CTP(-)?838|CTP(-)?888|CTP(-)?978|CTP(-)?980|CTP(-)?987|CTP(-)?988|CTP(-)?989',
			// MediaTek - http://www.mediatek.com/_en/01_products/02_proSys.php?cata_sn=1&cata1_sn=1&cata2_sn=309
			'MediatekTablet' => '\bMT8125|MT8389|MT8135|MT8377\b',
			// Concorde tab
			'ConcordeTablet' => 'Concorde([ ]+)?Tab|ConCorde ReadMan',
			// GoClever Tablets - http://www.goclever.com/uk/products,c1/tablet,c5/
			'GoCleverTablet' => 'GOCLEVER TAB|A7GOCLEVER|M1042|M7841|M742|R1042BK|R1041|TAB A975|TAB A7842|TAB A741|TAB A741L|TAB M723G|TAB M721|TAB A1021|TAB I921|TAB R721|TAB I720|TAB T76|TAB R70|TAB R76.2|TAB R106|TAB R83.2|TAB M813G|TAB I721|GCTA722|TAB I70|TAB I71|TAB S73|TAB R73|TAB R74|TAB R93|TAB R75|TAB R76.1|TAB A73|TAB A93|TAB A93.2|TAB T72|TAB R83|TAB R974|TAB R973|TAB A101|TAB A103|TAB A104|TAB A104.2|R105BK|M713G|A972BK|TAB A971|TAB R974.2|TAB R104|TAB R83.3|TAB A1042',
			// Modecom Tablets - http://www.modecom.eu/tablets/portal/
			'ModecomTablet' => 'FreeTAB 9000|FreeTAB 7.4|FreeTAB 7004|FreeTAB 7800|FreeTAB 2096|FreeTAB 7.5|FreeTAB 1014|FreeTAB 1001 |FreeTAB 8001|FreeTAB 9706|FreeTAB 9702|FreeTAB 7003|FreeTAB 7002|FreeTAB 1002|FreeTAB 7801|FreeTAB 1331|FreeTAB 1004|FreeTAB 8002|FreeTAB 8014|FreeTAB 9704|FreeTAB 1003',
			// @ref: http://www.tesco.com/direct/hudl/
			'Hudl'              => 'Hudl HT7S3',
			// @ref: http://www.telstra.com.au/home-phone/thub-2/
			'TelstraTablet'     => 'T-Hub2',
			'GenericTablet'     => 'Android.*\b97D\b|Tablet(?!.*PC)|ViewPad7|BNTV250A|MID-WCDMA|LogicPD Zoom2|\bA7EB\b|CatNova8|A1_07|CT704|CT1002|\bM721\b|rk30sdk|\bEVOTAB\b|SmartTabII10|SmartTab10|M758A|ET904',
		);

		/**
		 * List of mobile Operating Systems.
		 *
		 * @var array
		 */
		protected static $operatingSystems = array(
			'AndroidOS'         => 'Android',
			'BlackBerryOS'      => 'blackberry|\bBB10\b|rim tablet os',
			'PalmOS'            => 'PalmOS|avantgo|blazer|elaine|hiptop|palm|plucker|xiino',
			'SymbianOS'         => 'Symbian|SymbOS|Series60|Series40|SYB-[0-9]+|\bS60\b',
			// @reference: http://en.wikipedia.org/wiki/Windows_Mobile
			'WindowsMobileOS'   => 'Windows CE.*(PPC|Smartphone|Mobile|[0-9]{3}x[0-9]{3})|Window Mobile|Windows Phone [0-9.]+|WCE;',
			// @reference: http://en.wikipedia.org/wiki/Windows_Phone
			// http://wifeng.cn/?r=blog&a=view&id=106
			// http://nicksnettravels.builttoroam.com/post/2011/01/10/Bogus-Windows-Phone-7-User-Agent-String.aspx
			'WindowsPhoneOS'   => 'Windows Phone 8.0|Windows Phone OS|XBLWP7|ZuneWP7',
			'iOS'               => '\biPhone.*Mobile|\biPod|\biPad',
			// http://en.wikipedia.org/wiki/MeeGo
			// @todo: research MeeGo in UAs
			'MeeGoOS'           => 'MeeGo',
			// http://en.wikipedia.org/wiki/Maemo
			// @todo: research Maemo in UAs
			'MaemoOS'           => 'Maemo',
			'JavaOS'            => 'J2ME/|\bMIDP\b|\bCLDC\b', // '|Java/' produces bug #135
			'webOS'             => 'webOS|hpwOS',
			'badaOS'            => '\bBada\b',
			'BREWOS'            => 'BREW',
		);

		/**
		 * List of mobile User Agents.
		 *
		 * @var array
		 */
		protected static $browsers = array(
			// @reference: https://developers.google.com/chrome/mobile/docs/user-agent
			'Chrome'          => '\bCrMo\b|CriOS|Android.*Chrome/[.0-9]* (Mobile)?',
			'Dolfin'          => '\bDolfin\b',
			'Opera'           => 'Opera.*Mini|Opera.*Mobi|Android.*Opera|Mobile.*OPR/[0-9.]+|Coast/[0-9.]+',
			'Skyfire'         => 'Skyfire',
			'IE'              => 'IEMobile|MSIEMobile', // |Trident/[.0-9]+
			'Firefox'         => 'fennec|firefox.*maemo|(Mobile|Tablet).*Firefox|Firefox.*Mobile',
			'Bolt'            => 'bolt',
			'TeaShark'        => 'teashark',
			'Blazer'          => 'Blazer',
			// @reference: http://developer.apple.com/library/safari/#documentation/AppleApplications/Reference/SafariWebContent/OptimizingforSafarioniPhone/OptimizingforSafarioniPhone.html#//apple_ref/doc/uid/TP40006517-SW3
			'Safari'          => 'Version.*Mobile.*Safari|Safari.*Mobile',
			// @ref: http://en.wikipedia.org/wiki/Midori_(web_browser)
			//'Midori'          => 'midori',
			'Tizen'           => 'Tizen',
			'UCBrowser'       => 'UC.*Browser|UCWEB',
			// @ref: https://github.com/serbanghita/Mobile-Detect/issues/7
			'DiigoBrowser'    => 'DiigoBrowser',
			// http://www.puffinbrowser.com/index.php
			'Puffin'            => 'Puffin',
			// @ref: http://mercury-browser.com/index.html
			'Mercury'          => '\bMercury\b',
			// @reference: http://en.wikipedia.org/wiki/Minimo
			// http://en.wikipedia.org/wiki/Vision_Mobile_Browser
			'GenericBrowser'  => 'NokiaBrowser|OviBrowser|OneBrowser|TwonkyBeamBrowser|SEMC.*Browser|FlyFlow|Minimo|NetFront|Novarra-Vision|MQQBrowser|MicroMessenger'
		);

		/**
		 * Utilities.
		 *
		 * @var array
		 */
		protected static $utilities = array(
			// Experimental. When a mobile device wants to switch to 'Desktop Mode'.
			// @ref: http://scottcate.com/technology/windows-phone-8-ie10-desktop-or-mobile/
			// @ref: https://github.com/serbanghita/Mobile-Detect/issues/57#issuecomment-15024011
			'DesktopMode' => 'WPDesktop',
			'TV'          => 'SonyDTV|HbbTV', // experimental
			'WebKit'      => '(webkit)[ /]([\w.]+)',
			'Bot'         => 'Googlebot|DoCoMo|YandexBot|bingbot|ia_archiver|AhrefsBot|Ezooms|GSLFbot|WBSearchBot|Twitterbot|TweetmemeBot|Twikle|PaperLiBot|Wotbox|UnwindFetchor|facebookexternalhit',
			'MobileBot'   => 'Googlebot-Mobile|DoCoMo|YahooSeeker/M1A1-R2D2',
			'Console'     => '\b(Nintendo|Nintendo WiiU|PLAYSTATION|Xbox)\b',
			'Watch'       => 'SM-V700',
		);

		/**
		 * All possible HTTP headers that represent the
		 * User-Agent string.
		 *
		 * @var array
		 */
		protected static $uaHttpHeaders = array(
			// The default User-Agent string.
			'HTTP_USER_AGENT',
			// Header can occur on devices using Opera Mini.
			'HTTP_X_OPERAMINI_PHONE_UA',
			// Vodafone specific header: http://www.seoprinciple.com/mobile-web-community-still-angry-at-vodafone/24/
			'HTTP_X_DEVICE_USER_AGENT',
			'HTTP_X_ORIGINAL_USER_AGENT',
			'HTTP_X_SKYFIRE_PHONE',
			'HTTP_X_BOLT_PHONE_UA',
			'HTTP_DEVICE_STOCK_UA',
			'HTTP_X_UCBROWSER_DEVICE_UA'
		);

		/**
		 * The individual segments that could exist in a User-Agent string. VER refers to the regular
		 * expression defined in the constant self::VER.
		 *
		 * @var array
		 */
		protected static $properties = array(

			// Build
			'Mobile'        => 'Mobile/[VER]',
			'Build'         => 'Build/[VER]',
			'Version'       => 'Version/[VER]',
			'VendorID'      => 'VendorID/[VER]',

			// Devices
			'iPad'          => 'iPad.*CPU[a-z ]+[VER]',
			'iPhone'        => 'iPhone.*CPU[a-z ]+[VER]',
			'iPod'          => 'iPod.*CPU[a-z ]+[VER]',
			//'BlackBerry'    => array('BlackBerry[VER]', 'BlackBerry [VER];'),
			'Kindle'        => 'Kindle/[VER]',

			// Browser
			'Chrome'        => array('Chrome/[VER]', 'CriOS/[VER]', 'CrMo/[VER]'),
			'Coast'         => array('Coast/[VER]'),
			'Dolfin'        => 'Dolfin/[VER]',
			// @reference: https://developer.mozilla.org/en-US/docs/User_Agent_Strings_Reference
			'Firefox'       => 'Firefox/[VER]',
			'Fennec'        => 'Fennec/[VER]',
			// @reference: http://msdn.microsoft.com/en-us/library/ms537503(v=vs.85).aspx
			'IE'      => array('IEMobile/[VER];', 'IEMobile [VER]', 'MSIE [VER];'),
			// http://en.wikipedia.org/wiki/NetFront
			'NetFront'      => 'NetFront/[VER]',
			'NokiaBrowser'  => 'NokiaBrowser/[VER]',
			'Opera'         => array( ' OPR/[VER]', 'Opera Mini/[VER]', 'Version/[VER]' ),
			'Opera Mini'    => 'Opera Mini/[VER]',
			'Opera Mobi'    => 'Version/[VER]',
			'UC Browser'    => 'UC Browser[VER]',
			'MQQBrowser'    => 'MQQBrowser/[VER]',
			'MicroMessenger' => 'MicroMessenger/[VER]',
			// @note: Safari 7534.48.3 is actually Version 5.1.
			// @note: On BlackBerry the Version is overwriten by the OS.
			'Safari'        => array( 'Version/[VER]', 'Safari/[VER]' ),
			'Skyfire'       => 'Skyfire/[VER]',
			'Tizen'         => 'Tizen/[VER]',
			'Webkit'        => 'webkit[ /][VER]',

			// Engine
			'Gecko'         => 'Gecko/[VER]',
			'Trident'       => 'Trident/[VER]',
			'Presto'        => 'Presto/[VER]',

			// OS
			'iOS'              => ' \bOS\b [VER] ',
			'Android'          => 'Android [VER]',
			'BlackBerry'       => array('BlackBerry[\w]+/[VER]', 'BlackBerry.*Version/[VER]', 'Version/[VER]'),
			'BREW'             => 'BREW [VER]',
			'Java'             => 'Java/[VER]',
			// @reference: http://windowsteamblog.com/windows_phone/b/wpdev/archive/2011/08/29/introducing-the-ie9-on-windows-phone-mango-user-agent-string.aspx
			// @reference: http://en.wikipedia.org/wiki/Windows_NT#Releases
			'Windows Phone OS' => array( 'Windows Phone OS [VER]', 'Windows Phone [VER]'),
			'Windows Phone'    => 'Windows Phone [VER]',
			'Windows CE'       => 'Windows CE/[VER]',
			// http://social.msdn.microsoft.com/Forums/en-US/windowsdeveloperpreviewgeneral/thread/6be392da-4d2f-41b4-8354-8dcee20c85cd
			'Windows NT'       => 'Windows NT [VER]',
			'Symbian'          => array('SymbianOS/[VER]', 'Symbian/[VER]'),
			'webOS'            => array('webOS/[VER]', 'hpwOS/[VER];'),
		);

		/**
		 * Construct an instance of this class.
		 *
		 * @param array $headers Specify the headers as injection. Should be PHP _SERVER flavored.
		 *                       If left empty, will use the global _SERVER['HTTP_*'] vars instead.
		 * @param string $userAgent Inject the User-Agent header. If null, will use HTTP_USER_AGENT
		 *                          from the $headers array instead.
		 */
		public function __construct(
			array $headers = null,
			$userAgent = null
		){
			$this->setHttpHeaders($headers);
			$this->setUserAgent($userAgent);
		}

		/**
		 * Get the current script version.
		 * This is useful for the demo.php file,
		 * so people can check on what version they are testing
		 * for mobile devices.
		 *
		 * @return string The version number in semantic version format.
		 */
		public static function getScriptVersion()
		{
			return self::VERSION;
		}

		/**
		 * Set the HTTP Headers. Must be PHP-flavored. This method will reset existing headers.
		 *
		 * @param array $httpHeaders The headers to set. If null, then using PHP's _SERVER to extract
		 *                           the headers. The default null is left for backwards compatibilty.
		 */
		public function setHttpHeaders($httpHeaders = null)
		{
			//use global _SERVER if $httpHeaders aren't defined
			if (!is_array($httpHeaders) || !count($httpHeaders)) {
				$httpHeaders = $_SERVER;
			}

			//clear existing headers
			$this->httpHeaders = array();

			//Only save HTTP headers. In PHP land, that means only _SERVER vars that
			//start with HTTP_.
			foreach ($httpHeaders as $key => $value) {
				if (substr($key,0,5) == 'HTTP_') {
					$this->httpHeaders[$key] = $value;
				}
			}
		}

		/**
		 * Retrieves the HTTP headers.
		 *
		 * @return array
		 */
		public function getHttpHeaders()
		{
			return $this->httpHeaders;
		}

		/**
		 * Retrieves a particular header. If it doesn't exist, no exception/error is caused.
		 * Simply null is returned.
		 *
		 * @param string $header The name of the header to retrieve. Can be HTTP compliant such as
		 *                       "User-Agent" or "X-Device-User-Agent" or can be php-esque with the
		 *                       all-caps, HTTP_ prefixed, underscore seperated awesomeness.
		 *
		 * @return string|null The value of the header.
		 */
		public function getHttpHeader($header)
		{
			//are we using PHP-flavored headers?
			if (strpos($header, '_') === false) {
				$header = str_replace('-', '_', $header);
				$header = strtoupper($header);
			}

			//test the alternate, too
			$altHeader = 'HTTP_' . $header;

			//Test both the regular and the HTTP_ prefix
			if (isset($this->httpHeaders[$header])) {
				return $this->httpHeaders[$header];
			} elseif (isset($this->httpHeaders[$altHeader])) {
				return $this->httpHeaders[$altHeader];
			}
		}

		public function getMobileHeaders()
		{
			return self::$mobileHeaders;
		}

		/**
		 * Get all possible HTTP headers that
		 * can contain the User-Agent string.
		 *
		 * @return array List of HTTP headers.
		 */
		public function getUaHttpHeaders()
		{
			return self::$uaHttpHeaders;
		}

		/**
		 * Set the User-Agent to be used.
		 *
		 * @param string $userAgent The user agent string to set.
		 */
		public function setUserAgent($userAgent = null)
		{
			if (!empty($userAgent)) {
				return $this->userAgent = $userAgent;
			} else {

				$this->userAgent = null;

				foreach($this->getUaHttpHeaders() as $altHeader){
					if(!empty($this->httpHeaders[$altHeader])){ // @todo: should use getHttpHeader(), but it would be slow. (Serban)
						$this->userAgent .= $this->httpHeaders[$altHeader] . " ";
					}
				}

				return $this->userAgent = (!empty($this->userAgent) ? trim($this->userAgent) : null);

			}
		}

		/**
		 * Retrieve the User-Agent.
		 *
		 * @return string|null The user agent if it's set.
		 */
		public function getUserAgent()
		{
			return $this->userAgent;
		}

		/**
		 * Set the detection type. Must be one of self::DETECTION_TYPE_MOBILE or
		 * self::DETECTION_TYPE_EXTENDED. Otherwise, nothing is set.
		 *
		 * @deprecated since version 2.6.9
		 *
		 * @param string $type The type. Must be a self::DETECTION_TYPE_* constant. The default
		 *                     parameter is null which will default to self::DETECTION_TYPE_MOBILE.
		 */
		public function setDetectionType($type = null)
		{
			if ($type === null) {
				$type = self::DETECTION_TYPE_MOBILE;
			}

			if ($type != self::DETECTION_TYPE_MOBILE && $type != self::DETECTION_TYPE_EXTENDED) {
				return;
			}

			$this->detectionType = $type;
		}

		/**
		 * Retrieve the list of known phone devices.
		 *
		 * @return array List of phone devices.
		 */
		public static function getPhoneDevices()
		{
			return self::$phoneDevices;
		}

		/**
		 * Retrieve the list of known tablet devices.
		 *
		 * @return array List of tablet devices.
		 */
		public static function getTabletDevices()
		{
			return self::$tabletDevices;
		}

		/**
		 * Alias for getBrowsers() method.
		 *
		 * @return array List of user agents.
		 */
		public static function getUserAgents()
		{
			return self::getBrowsers();
		}

		/**
		 * Retrieve the list of known browsers. Specifically, the user agents.
		 *
		 * @return array List of browsers / user agents.
		 */
		public static function getBrowsers()
		{
			return self::$browsers;
		}

		/**
		 * Retrieve the list of known utilities.
		 *
		 * @return array List of utilities.
		 */
		public static function getUtilities()
		{
			return self::$utilities;
		}

		/**
		 * Method gets the mobile detection rules. This method is used for the magic methods $detect->is*().
		 *
		 * @deprecated since version 2.6.9
		 *
		 * @return array All the rules (but not extended).
		 */
		public static function getMobileDetectionRules()
		{
			static $rules;

			if (!$rules) {
				$rules = array_merge(
					self::$phoneDevices,
					self::$tabletDevices,
					self::$operatingSystems,
					self::$browsers
				);
			}

			return $rules;

		}

		/**
		 * Method gets the mobile detection rules + utilities.
		 * The reason this is separate is because utilities rules
		 * don't necessary imply mobile. This method is used inside
		 * the new $detect->is('stuff') method.
		 *
		 * @deprecated since version 2.6.9
		 *
		 * @return array All the rules + extended.
		 */
		public function getMobileDetectionRulesExtended()
		{
			static $rules;

			if (!$rules) {
				// Merge all rules together.
				$rules = array_merge(
					self::$phoneDevices,
					self::$tabletDevices,
					self::$operatingSystems,
					self::$browsers,
					self::$utilities
				);
			}

			return $rules;
		}

		/**
		 * Retrieve the current set of rules.
		 *
		 * @deprecated since version 2.6.9
		 *
		 * @return array
		 */
		public function getRules()
		{
			if ($this->detectionType == self::DETECTION_TYPE_EXTENDED) {
				return self::getMobileDetectionRulesExtended();
			} else {
				return self::getMobileDetectionRules();
			}
		}

		/**
		 * Retrieve the list of mobile operating systems.
		 *
		 * @return array The list of mobile operating systems.
		 */
		public static function getOperatingSystems()
		{
			return self::$operatingSystems;
		}

		/**
		 * Check the HTTP headers for signs of mobile.
		 * This is the fastest mobile check possible; it's used
		 * inside isMobile() method.
		 *
		 * @return bool
		 */
		public function checkHttpHeadersForMobile()
		{

			foreach($this->getMobileHeaders() as $mobileHeader => $matchType){
				if( isset($this->httpHeaders[$mobileHeader]) ){
					if( is_array($matchType['matches']) ){
						foreach($matchType['matches'] as $_match){
							if( strpos($this->httpHeaders[$mobileHeader], $_match) !== false ){
								return true;
							}
						}
						return false;
					} else {
						return true;
					}
				}
			}

			return false;

		}

		/**
		 * Magic overloading method.
		 *
		 * @method boolean is[...]()
		 * @param  string                 $name
		 * @param  array                  $arguments
		 * @return mixed
		 * @throws BadMethodCallException when the method doesn't exist and doesn't start with 'is'
		 */
		public function __call($name, $arguments)
		{
			//make sure the name starts with 'is', otherwise
			if (substr($name, 0, 2) != 'is') {
				throw new BadMethodCallException("No such method exists: $name");
			}

			$this->setDetectionType(self::DETECTION_TYPE_MOBILE);

			$key = substr($name, 2);

			return $this->matchUAAgainstKey($key);
		}

		/**
		 * Find a detection rule that matches the current User-agent.
		 *
		 * @param null $userAgent deprecated
		 * @return boolean
		 */
		protected function matchDetectionRulesAgainstUA($userAgent = null)
		{
			// Begin general search.
			foreach ($this->getRules() as $_regex) {
				if (empty($_regex)) {
					continue;
				}
				if ($this->match($_regex, $userAgent)) {
					return true;
				}
			}

			return false;
		}

		/**
		 * Search for a certain key in the rules array.
		 * If the key is found the try to match the corresponding
		 * regex agains the User-Agent.
		 *
		 * @param string $key
		 * @param null $userAgent deprecated
		 * @return mixed
		 */
		protected function matchUAAgainstKey($key, $userAgent = null)
		{
			// Make the keys lowercase so we can match: isIphone(), isiPhone(), isiphone(), etc.
			$key = strtolower($key);

			//change the keys to lower case
			$_rules = array_change_key_case($this->getRules());

			if (array_key_exists($key, $_rules)) {
				if (empty($_rules[$key])) {
					return null;
				}

				return $this->match($_rules[$key], $userAgent);
			}

			return false;
		}

		/**
		 * Check if the device is mobile.
		 * Returns true if any type of mobile device detected, including special ones
		 * @param null $userAgent deprecated
		 * @param null $httpHeaders deprecated
		 * @return bool
		 */
		public function isMobile($userAgent = null, $httpHeaders = null)
		{

			if ($httpHeaders) {
				$this->setHttpHeaders($httpHeaders);
			}

			if ($userAgent) {
				$this->setUserAgent($userAgent);
			}

			$this->setDetectionType(self::DETECTION_TYPE_MOBILE);

			if ($this->checkHttpHeadersForMobile()) {
				return true;
			} else {
				return $this->matchDetectionRulesAgainstUA();
			}

		}

		/**
		 * Check if the device is a tablet.
		 * Return true if any type of tablet device is detected.
		 *
		 * @param  string $userAgent   deprecated
		 * @param  array  $httpHeaders deprecated
		 * @return bool
		 */
		public function isTablet($userAgent = null, $httpHeaders = null)
		{
			$this->setDetectionType(self::DETECTION_TYPE_MOBILE);

			foreach (self::$tabletDevices as $_regex) {
				if ($this->match($_regex, $userAgent)) {
					return true;
				}
			}

			return false;
		}

		/**
		 * This method checks for a certain property in the
		 * userAgent.
		 * @todo: The httpHeaders part is not yet used.
		 *
		 * @param $key
		 * @param  string        $userAgent   deprecated
		 * @param  string        $httpHeaders deprecated
		 * @return bool|int|null
		 */
		public function is($key, $userAgent = null, $httpHeaders = null)
		{
			// Set the UA and HTTP headers only if needed (eg. batch mode).
			if ($httpHeaders) {
				$this->setHttpHeaders($httpHeaders);
			}

			if ($userAgent) {
				$this->setUserAgent($userAgent);
			}

			$this->setDetectionType(self::DETECTION_TYPE_EXTENDED);

			return $this->matchUAAgainstKey($key);
		}

		/**
		 * Some detection rules are relative (not standard),
		 * because of the diversity of devices, vendors and
		 * their conventions in representing the User-Agent or
		 * the HTTP headers.
		 *
		 * This method will be used to check custom regexes against
		 * the User-Agent string.
		 *
		 * @param $regex
		 * @param  string $userAgent
		 * @return bool
		 *
		 * @todo: search in the HTTP headers too.
		 */
		public function match($regex, $userAgent = null)
		{
			// Escape the special character which is the delimiter.
			$regex = str_replace('/', '\/', $regex);

			return (bool) preg_match('/'.$regex.'/is', (!empty($userAgent) ? $userAgent : $this->userAgent));
		}

		/**
		 * Get the properties array.
		 *
		 * @return array
		 */
		public static function getProperties()
		{
			return self::$properties;
		}

		/**
		 * Prepare the version number.
		 *
		 * @todo Remove the error supression from str_replace() call.
		 *
		 * @param string $ver The string version, like "2.6.21.2152";
		 *
		 * @return float
		 */
		public function prepareVersionNo($ver)
		{
			$ver = str_replace(array('_', ' ', '/'), '.', $ver);
			$arrVer = explode('.', $ver, 2);

			if (isset($arrVer[1])) {
				$arrVer[1] = @str_replace('.', '', $arrVer[1]); // @todo: treat strings versions.
			}

			return (float) implode('.', $arrVer);
		}

		/**
		 * Check the version of the given property in the User-Agent.
		 * Will return a float number. (eg. 2_0 will return 2.0, 4.3.1 will return 4.31)
		 *
		 * @param string $propertyName The name of the property. See self::getProperties() array
		 *                              keys for all possible properties.
		 * @param string $type Either self::VERSION_TYPE_STRING to get a string value or
		 *                      self::VERSION_TYPE_FLOAT indicating a float value. This parameter
		 *                      is optional and defaults to self::VERSION_TYPE_STRING. Passing an
		 *                      invalid parameter will default to the this type as well.
		 *
		 * @return string|float The version of the property we are trying to extract.
		 */
		public function version($propertyName, $type = self::VERSION_TYPE_STRING)
		{
			if (empty($propertyName)) {
				return false;
			}

			//set the $type to the default if we don't recognize the type
			if ($type != self::VERSION_TYPE_STRING && $type != self::VERSION_TYPE_FLOAT) {
				$type = self::VERSION_TYPE_STRING;
			}

			$properties = self::getProperties();

			// Check if the property exists in the properties array.
			if (array_key_exists($propertyName, $properties)) {

				// Prepare the pattern to be matched.
				// Make sure we always deal with an array (string is converted).
				$properties[$propertyName] = (array) $properties[$propertyName];

				foreach ($properties[$propertyName] as $propertyMatchString) {

					$propertyPattern = str_replace('[VER]', self::VER, $propertyMatchString);

					// Escape the special character which is the delimiter.
					$propertyPattern = str_replace('/', '\/', $propertyPattern);

					// Identify and extract the version.
					preg_match('/'.$propertyPattern.'/is', $this->userAgent, $match);

					if (!empty($match[1])) {
						$version = ( $type == self::VERSION_TYPE_FLOAT ? $this->prepareVersionNo($match[1]) : $match[1] );

						return $version;
					}

				}

			}

			return false;
		}

		/**
		 * Retrieve the mobile grading, using self::MOBILE_GRADE_* constants.
		 *
		 * @return string One of the self::MOBILE_GRADE_* constants.
		 */
		public function mobileGrade()
		{
			$isMobile = $this->isMobile();

			if (
				// Apple iOS 3.2-5.1 - Tested on the original iPad (4.3 / 5.0), iPad 2 (4.3), iPad 3 (5.1), original iPhone (3.1), iPhone 3 (3.2), 3GS (4.3), 4 (4.3 / 5.0), and 4S (5.1)
				$this->version('iPad', self::VERSION_TYPE_FLOAT)>=4.3 ||
				$this->version('iPhone', self::VERSION_TYPE_FLOAT)>=3.1 ||
				$this->version('iPod', self::VERSION_TYPE_FLOAT)>=3.1 ||

				// Android 2.1-2.3 - Tested on the HTC Incredible (2.2), original Droid (2.2), HTC Aria (2.1), Google Nexus S (2.3). Functional on 1.5 & 1.6 but performance may be sluggish, tested on Google G1 (1.5)
				// Android 3.1 (Honeycomb)  - Tested on the Samsung Galaxy Tab 10.1 and Motorola XOOM
				// Android 4.0 (ICS)  - Tested on a Galaxy Nexus. Note: transition performance can be poor on upgraded devices
				// Android 4.1 (Jelly Bean)  - Tested on a Galaxy Nexus and Galaxy 7
				( $this->version('Android', self::VERSION_TYPE_FLOAT)>2.1 && $this->is('Webkit') ) ||

				// Windows Phone 7-7.5 - Tested on the HTC Surround (7.0) HTC Trophy (7.5), LG-E900 (7.5), Nokia Lumia 800
				$this->version('Windows Phone OS', self::VERSION_TYPE_FLOAT)>=7.0 ||

				// Blackberry 7 - Tested on BlackBerry® Torch 9810
				// Blackberry 6.0 - Tested on the Torch 9800 and Style 9670
				$this->is('BlackBerry') && $this->version('BlackBerry', self::VERSION_TYPE_FLOAT)>=6.0 ||
				// Blackberry Playbook (1.0-2.0) - Tested on PlayBook
				$this->match('Playbook.*Tablet') ||

				// Palm WebOS (1.4-2.0) - Tested on the Palm Pixi (1.4), Pre (1.4), Pre 2 (2.0)
				( $this->version('webOS', self::VERSION_TYPE_FLOAT)>=1.4 && $this->match('Palm|Pre|Pixi') ) ||
				// Palm WebOS 3.0  - Tested on HP TouchPad
				$this->match('hp.*TouchPad') ||

				// Firefox Mobile (12 Beta) - Tested on Android 2.3 device
				( $this->is('Firefox') && $this->version('Firefox', self::VERSION_TYPE_FLOAT)>=12 ) ||

				// Chrome for Android - Tested on Android 4.0, 4.1 device
				( $this->is('Chrome') && $this->is('AndroidOS') && $this->version('Android', self::VERSION_TYPE_FLOAT)>=4.0 ) ||

				// Skyfire 4.1 - Tested on Android 2.3 device
				( $this->is('Skyfire') && $this->version('Skyfire', self::VERSION_TYPE_FLOAT)>=4.1 && $this->is('AndroidOS') && $this->version('Android', self::VERSION_TYPE_FLOAT)>=2.3 ) ||

				// Opera Mobile 11.5-12: Tested on Android 2.3
				( $this->is('Opera') && $this->version('Opera Mobi', self::VERSION_TYPE_FLOAT)>11 && $this->is('AndroidOS') ) ||

				// Meego 1.2 - Tested on Nokia 950 and N9
				$this->is('MeeGoOS') ||

				// Tizen (pre-release) - Tested on early hardware
				$this->is('Tizen') ||

				// Samsung Bada 2.0 - Tested on a Samsung Wave 3, Dolphin browser
				// @todo: more tests here!
				$this->is('Dolfin') && $this->version('Bada', self::VERSION_TYPE_FLOAT)>=2.0 ||

				// UC Browser - Tested on Android 2.3 device
				( ($this->is('UC Browser') || $this->is('Dolfin')) && $this->version('Android', self::VERSION_TYPE_FLOAT)>=2.3 ) ||

				// Kindle 3 and Fire  - Tested on the built-in WebKit browser for each
				( $this->match('Kindle Fire') ||
					$this->is('Kindle') && $this->version('Kindle', self::VERSION_TYPE_FLOAT)>=3.0 ) ||

				// Nook Color 1.4.1 - Tested on original Nook Color, not Nook Tablet
				$this->is('AndroidOS') && $this->is('NookTablet') ||

				// Chrome Desktop 11-21 - Tested on OS X 10.7 and Windows 7
				$this->version('Chrome', self::VERSION_TYPE_FLOAT)>=11 && !$isMobile ||

				// Safari Desktop 4-5 - Tested on OS X 10.7 and Windows 7
				$this->version('Safari', self::VERSION_TYPE_FLOAT)>=5.0 && !$isMobile ||

				// Firefox Desktop 4-13 - Tested on OS X 10.7 and Windows 7
				$this->version('Firefox', self::VERSION_TYPE_FLOAT)>=4.0 && !$isMobile ||

				// Internet Explorer 7-9 - Tested on Windows XP, Vista and 7
				$this->version('MSIE', self::VERSION_TYPE_FLOAT)>=7.0 && !$isMobile ||

				// Opera Desktop 10-12 - Tested on OS X 10.7 and Windows 7
				// @reference: http://my.opera.com/community/openweb/idopera/
				$this->version('Opera', self::VERSION_TYPE_FLOAT)>=10 && !$isMobile

			){
				return self::MOBILE_GRADE_A;
			}

			if (
				$this->version('iPad', self::VERSION_TYPE_FLOAT)<4.3 ||
				$this->version('iPhone', self::VERSION_TYPE_FLOAT)<3.1 ||
				$this->version('iPod', self::VERSION_TYPE_FLOAT)<3.1 ||

				// Blackberry 5.0: Tested on the Storm 2 9550, Bold 9770
				$this->is('Blackberry') && $this->version('BlackBerry', self::VERSION_TYPE_FLOAT)>=5 && $this->version('BlackBerry', self::VERSION_TYPE_FLOAT)<6 ||

				//Opera Mini (5.0-6.5) - Tested on iOS 3.2/4.3 and Android 2.3
				( $this->version('Opera Mini', self::VERSION_TYPE_FLOAT)>=5.0 && $this->version('Opera Mini', self::VERSION_TYPE_FLOAT)<=6.5 &&
					($this->version('Android', self::VERSION_TYPE_FLOAT)>=2.3 || $this->is('iOS')) ) ||

				// Nokia Symbian^3 - Tested on Nokia N8 (Symbian^3), C7 (Symbian^3), also works on N97 (Symbian^1)
				$this->match('NokiaN8|NokiaC7|N97.*Series60|Symbian/3') ||

				// @todo: report this (tested on Nokia N71)
				$this->version('Opera Mobi', self::VERSION_TYPE_FLOAT)>=11 && $this->is('SymbianOS')
			){
				return self::MOBILE_GRADE_B;
			}

			if (
				// Blackberry 4.x - Tested on the Curve 8330
				$this->version('BlackBerry', self::VERSION_TYPE_FLOAT)<5.0 ||
				// Windows Mobile - Tested on the HTC Leo (WinMo 5.2)
				$this->match('MSIEMobile|Windows CE.*Mobile') || $this->version('Windows Mobile', self::VERSION_TYPE_FLOAT)<=5.2

			){
				return self::MOBILE_GRADE_C;
			}

			//All older smartphone platforms and featurephones - Any device that doesn't support media queries
			//will receive the basic, C grade experience.
			return self::MOBILE_GRADE_C;
		}

		public function isHandHeld()
		{
			return($this->isMobile() || $this->isIphone() || $this->isIpad() || $this->isIpod() || $this->isAndroid() || $this->isBlackberry() || $this->isoperaMobile() || $this->isWebos() || $this->isSymbian() || $this->isWindowsMobile() || $this->isMotorola() || $this->isSamsung() || $this->isSamsungTablet() || $this->isSony() || $this->isnintendo());
		}
	}

	$kuler = Kuler::getInstance();	
}