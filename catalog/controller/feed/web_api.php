<?php

/*

Added by Raj ,With Reference to code by Adithya.
For more information Please refer https://github.com/zenwalker/opencart-webapi

*/

class ControllerFeedWebApi extends Controller {
	# Use print_r($json) instead json_encode($json)
	private $debug = false;
	public function categories() {
		$this->init();
		$this->load->model('catalog/category');
		$json = array('success' => true);
		# -- $_GET params ------------------------------
		
		if (isset($this->request->get['parent'])) {
			$parent = $this->request->get['parent'];
		} else {
			$parent = 0;
		}
		if (isset($this->request->get['level'])) {
			$level = $this->request->get['level'];
		} else {
			$level = 1;
		}
		# -- End $_GET params --------------------------
		$json['categories'] = $this->getCategoriesTree($parent, $level);
		if ($this->debug) {
			echo '<pre>';
			print_r($json);
		} else {
			$this->response->setOutput(json_encode($json));
		}
	}
	public function category() {
		$this->init();
		$this->load->model('catalog/category');
		$this->load->model('tool/image');
		$json = array('success' => true);
		# -- $_GET params ------------------------------
		
		if (isset($this->request->get['id'])) {
			$category_id = $this->request->get['id'];
		} else {
			$category_id = 0;
		}
		# -- End $_GET params --------------------------
		$category = $this->model_catalog_category->getCategory($category_id);
		
		$json['category'] = array(
			'id'                    => $category['category_id'],
			'name'                  => $category['name'],
			'description'           => $category['description'],
			'href'                  => $this->url->link('product/category', 'category_id=' . $category['category_id'])
		);
		if ($this->debug) {
			echo '<pre>';
			print_r($json);
		} else {
			$this->response->setOutput(json_encode($json));
		}
	}
	public function products() {
		$this->init();
		$this->load->model('catalog/product');
		$this->load->model('tool/image');
		$json = array('success' => true, 'products' => array());
		# -- $_GET params ------------------------------
		
		if (isset($this->request->get['category'])) {
			$category_id = $this->request->get['category'];
		} else {
			$category_id = 0;
		}
		# -- End $_GET params --------------------------
		$products = $this->model_catalog_product->getProducts(array(
			'filter_category_id'	=> $category_id
		));
		foreach ($products as $product) {
			if ($product['image']) {
				$image = $this->model_tool_image->resize($product['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
			} else {
				$image = false;
			}
			if ((float)$product['special']) {
				$special = $this->currency->format($this->tax->calculate($product['special'], $product['tax_class_id'], $this->config->get('config_tax')));
			} else {
				$special = false;
			}
			$json['products'][] = array(
				'id'                    => $product['product_id'],
				'name'                  => $product['name'],
				'description'           => $product['description'],
				'pirce'                 => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'))),
				'href'                  => $this->url->link('product/product', 'product_id=' . $product['product_id']),
				'thumb'                 => $image,
				'special'               => $special,
				'rating'                => $product['rating']
			);
		}
		if ($this->debug) {
			echo '<pre>';
			print_r($json);
		} else {
			$this->response->setOutput(json_encode($json));
		}
	}
	public function product() {
		$this->init();
		$this->load->model('catalog/product');
		$this->load->model('tool/image');
		$json = array('success' => true);
		# -- $_GET params ------------------------------
		
		if (isset($this->request->get['id'])) {
			$product_id = $this->request->get['id'];
		} else {
			$product_id = 0;
		}
		# -- End $_GET params --------------------------
		$product = $this->model_catalog_product->getProduct($product_id);
		# product image
		if ($product['image']) {
			$image = $this->model_tool_image->resize($product['image'], $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height'));
		} else {
			$image = '';
		}
		#additional images
		$additional_images = $this->model_catalog_product->getProductImages($product['product_id']);
		$images = array();
		foreach ($additional_images as $additional_image) {
			$images[] = $this->model_tool_image->resize($additional_image, $this->config->get('config_image_additional_width'), $this->config->get('config_image_additional_height'));
		}
		#specal
		if ((float)$product['special']) {
			$special = $this->currency->format($this->tax->calculate($product['special'], $product['tax_class_id'], $this->config->get('config_tax')));
		} else {
			$special = false;
		}
		#discounts
		$discounts = array();
		$data_discounts =  $this->model_catalog_product->getProductDiscounts($product['product_id']);
		foreach ($data_discounts as $discount) {
			$discounts[] = array(
				'quantity' => $discount['quantity'],
				'price'    => $this->currency->format($this->tax->calculate($discount['price'], $product['tax_class_id'], $this->config->get('config_tax')))
			);
		}
		#options
		$options = array();
		foreach ($this->model_catalog_product->getProductOptions($product['product_id']) as $option) { 
			if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') { 
				$option_value_data = array();
				
				foreach ($option['option_value'] as $option_value) {
					if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
						if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
							$price = $this->currency->format($this->tax->calculate($option_value['price'], $product['tax_class_id'], $this->config->get('config_tax')));
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
				
				$options[] = array(
					'product_option_id' => $option['product_option_id'],
					'option_id'         => $option['option_id'],
					'name'              => $option['name'],
					'type'              => $option['type'],
					'option_value'      => $option_value_data,
					'required'          => $option['required']
				);					
			} elseif ($option['type'] == 'text' || $option['type'] == 'textarea' || $option['type'] == 'file' || $option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') {
				$options[] = array(
					'product_option_id' => $option['product_option_id'],
					'option_id'         => $option['option_id'],
					'name'              => $option['name'],
					'type'              => $option['type'],
					'option_value'      => $option['option_value'],
					'required'          => $option['required']
				);						
			}
		}
		#minimum
		if ($product['minimum']) {
			$minimum = $product['minimum'];
		} else {
			$minimum = 1;
		}
		$json['product'] = array(
			'id'                            => $product['product_id'],
			'seo_h1'                        => $product['seo_h1'],
			'name'                          => $product['name'],
			'manufacturer'                  => $product['manufacturer'],
			'model'                         => $product['model'],
			'reward'                        => $product['reward'],
			'points'                        => $product['points'],
			'image'                         => $image,
			'images'                        => $images,
			'price'                         => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'))),
			'special'                       => $special,
			'discounts'                     => $discounts,
			'options'                       => $options,
			'minimum'                       => $minimum,
			'rating'                        => (int)$product['rating'],
			'description'                   => html_entity_decode($product['description'], ENT_QUOTES, 'UTF-8'),
			'attribute_groups'              => $this->model_catalog_product->getProductAttributes($product['product_id'])
		);
		if ($this->debug) {
			echo '<pre>';
			print_r($json);
		} else {
			$this->response->setOutput(json_encode($json));
		}
	}
	/**
	 * Generation of category tree
	 * 
	 * @param  int    $parent  Prarent category id
	 * @param  int    $level   Depth level
	 * @return array           Tree
	 */
	private function getCategoriesTree($parent = 0, $level = 1) {
		$this->load->model('catalog/category');
		$this->load->model('tool/image');
		
		$result = array();
		$categories = $this->model_catalog_category->getCategories($parent);
		if ($categories && $level > 0) {
			$level--;
			foreach ($categories as $category) {
				if ($category['image']) {
					$image = $this->model_tool_image->resize($category['image'], $this->config->get('config_image_category_width'), $this->config->get('config_image_category_height'));
				} else {
					$image = false;
				}
				$result[] = array(
					'category_id'   => $category['category_id'],
					'parent_id'     => $category['parent_id'],
					'name'          => $category['name'],
					'image'         => $image,
					'href'          => $this->url->link('product/category', 'category_id=' . $category['category_id']),
					'categories'    => $this->getCategoriesTree($category['category_id'], $level)
				);
			}
			return $result;
		}
	}
	/**
	 * 
	 */
	private function init() {
		$this->response->addHeader('Content-Type: application/json');
		if (!$this->config->get('web_api_status')) {
			$this->error(10, 'API is disabled');
		}
		if ($this->config->get('web_api_key') && (!isset($this->request->get['key']) || $this->request->get['key'] != $this->config->get('web_api_key'))) {
			$this->error(20, 'Invalid secret key');
		}
	}
	/**
	 * Error message responser
	 *
	 * @param string $message  Error message
	 */
	private function error($code = 0, $message = '') {
		# setOutput() is not called, set headers manually
		header('Content-Type: application/json');
		$json = array(
			'success'       => false,
			'code'          => $code,
			'message'       => $message
		);
		if ($this->debug) {
			echo '<pre>';
			print_r($json);
		} else {
			echo json_encode($json);
		}
		
		exit();
	}
    /*
     * API to get all the customer and their respective amount remaining
     */
    public function getCustomerDetails() {
        $this->load->model("account/api");
        $total_amount_details = $this->model_account_api->getVoucherDetails();
        echo json_encode($total_amount_details);
    }
    /*
     * API to add customer
     */
    public function addCustomer() {
        $this->load->model("account/customer");
        $data['firstname'] = isset($this->request->get['name'])?$this->request->get['name']:"";
        $data['lastname'] = "";
        $data['email'] = isset($this->request->get['email'])?$this->request->get['email']:"";
        $data['telephone'] = isset($this->request->get['telephone'])?$this->request->get['telephone']:"";
        $data['password'] = isset($this->request->get['password'])?$this->request->get['password']:"";
        $data['address_1'] = isset($this->request->get['school_address'])?$this->request->get['school_address']:"";
        $data['city'] = isset($this->request->get['city'])?$this->request->get['city']:"";
        $data['postcode'] = isset($this->request->get['postcode'])?$this->request->get['postcode']:"";
        $data['country_id'] = $this->getCountryId($this->request->get['country']);
        $data['zone_id'] = $this->getZoneId($this->request->get['state']);
        //Dummy fields for safe execution
        $data['fax'] = "";
        $data['company'] = "";
        $data['company_id'] = "";
        $data['tax_id'] = "";
        $data['address_2'] = "";
        $customer_id = $this->model_account_customer->addCustomer($data);
        echo $customer_id;
    }
    private function getCountryId($country){
        $this->load->model("account/api");
        $country_details = $this->model_account_api->getCountryId($country);
        return $country_details->rows[0]['country_id'];
    }
    private function getZoneId($state) {
        $this->load->model("account/api");
        $zone_details = $this->model_account_api->getZoneId($state);
        return $zone_details->rows[0]['zone_id'];
    }
    /*
     * To add voucher to a customer
     */
    public function addVoucher() {
        $this->load->model("account/api");
        $customer_id = $this->request->get['customer_id'];
        $amount = $this->request->get['amount'];
        $this->model_account_api->addTransaction($customer_id,$amount);
        return;
    }
    /*
     * To add products to cart in batch
     */
    public function addToCart(){
        $cart = array();
        $cart_products = $this->request->post['product'];
        foreach($cart_products as $cart_product){
            $cart = $this->emulateCart($cart_product['id'],$cart_product['quantity'],array(),'',$cart);
        }
        print_r($cart);
        $this->load->model("account/api");
        $this->model_account_api->addCartData($cart,$this->request->post['user_id']);
    }
    /*
     * Internal function to emulate the function cart in session so that we can save in the database
     */
    private function emulateCart($product_id, $qty = 1, $option, $profile_id = '',$cart = array()) {
        $key = (int)$product_id . ':';
        if ($option) {
            $key .= base64_encode(serialize($option)) . ':';
        }  else {
            $key .= ':';
        }
        if ($profile_id) {
            $key .= (int)$profile_id;
        }
        if ((int)$qty && ((int)$qty > 0)) {
            if (!isset($cart[$key])) {
                $cart[$key] = (int)$qty;
            } else {
                $cart[$key] += (int)$qty;
            }
        }
        return $cart;
    }
    /*
     * Get the list of stores which are not mapped
     */
    public function getUnmappedStoreList() {
        $mapped_store_list = $this->request->get['exclude_list'];
        $this->load->model("account/api");
        $unmapped_store_array = $this->model_account_api->getUnmappedStoreList($mapped_store_list);
        echo json_encode($unmapped_store_array);
    }
    /*
     * Get store by id
     */
    public function getStoreById() {
        $store_id = $this->request->get['store_id'];
        $this->load->model("account/api");
        $store_details = $this->model_account_api->getStoreById($store_id);
        echo json_encode($store_details);
    }
}