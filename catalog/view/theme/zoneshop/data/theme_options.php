<?php
return array(
	'id' => 'zoneshop',
	'name' => 'Zoneshop',
	'version' => '1.0.0',
	'default_skin_id' => 'skin1',
	'styles' => array(
		'body' => array(
			'label' => _t('text_style_general', 'General Style'),
			'items' => array(
        'body_container_bg_color' => array(
          'label' => _t('text_style_body_container_bg_color', 'Container Background Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
        'body_link_color' => array(
          'label' => _t('text_style_body_link_color', 'Link Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
        'body_link_hover_color' => array(
          'label' => _t('text_style_body_link_hover_color', 'Link Hover Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
				'body_font' => array(
					'label' => _t('text_style_font', 'Body Font'),
					'type' => 'style_font',
					'font_family' => 1,
					'font_size' => 0,
					'font_weight' => 0,
					'font_style' => 0,
					'text_transform' => 0
				),
        'body_text_color' => array(
          'label' => _t('text_style_body_text_color', 'Body Text Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
        'body_bg_color' => array(
          'label' => _t('text_style_body_bg_color', 'Body Background Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
        'body_bg_image' => array(
          'label' => _t('text_style_body_bg_image', 'Body Background Image'),
          'type' => 'style_image_selector'
        ),
        'body_pattern' => array(
          'label' => _t('text_style_pattern', 'Body Background Pattern'),
          'type' => 'style_pattern'
        ),
        'heading_font' => array(
          'label' => _t('text_style_heading_font', 'Heading Font'),
          'type' => 'style_font',
          'font_family' => 1,
          'font_size' => 0,
          'font_weight' => 1,
          'font_style' => 1,
          'text_transform' => 1
        ),
        'heading_color' => array(
          'label' => _t('text_style_heading_color', 'Heading Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
        'social_newsletter_bg_color' => array(
          'label' => _t('text_style_social_newsletter_bg_color', 'Social/Newsletters Background Color'),
          'type' => 'color',
          'format' => 'hex'
        )
			)
		),
    'topbar' => array(
      'label' => _t('text_style_topbar', 'Top Bar Style'),
      'items' => array(
        'topbar_background_color' => array(
          'label' => _t('text_style_background_color', 'Background Color'),
          'type' => 'color',
          'format' => 'rgba'
        ),
        'topbar_link_color' => array(
          'label' => _t('text_style_topbar_link_color', 'Link Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
        'topbar_link_hover_color' => array(
          'label' => _t('text_style_topbar_link_hover_color', 'Link Hover Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
        'topbar_text_color' => array(
          'label' => _t('text_style_topbar_text_color', 'Text Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
        'topbar_border_color' => array(
          'label' => _t('text_style_topbar_border_color', 'Border Bottom Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
      )
    ),
    'header' => array(
      'label' => _t('text_style_header', 'Header Style'),
      'items' => array(
        'header_background_color' => array(
          'label' => _t('text_style_background_color', 'Background Color'),
          'type' => 'color',
          'format' => 'rgba'
        ),
        'header_background_image' => array(
          'label' => _t('text_style_header_background_image', 'Background Image'),
          'type' => 'style_image_selector'
        ),
        'header_pattern' => array(
          'label' => _t('text_style_header_pattern', 'Header Background Pattern'),
          'type' => 'style_pattern'
        ),
        'header_search_border_color' => array(
          'label' => _t('text_style_search_border_color', 'Search Box Border Color'),
          'type' => 'color',
          'format' => 'rgba'
        ),
        'header_search_bg_color' => array(
          'label' => _t('text_style_search_bg_color', 'Search Box Background Color'),
          'type' => 'color',
          'format' => 'rgba'
        ),
        'header_mini_cart_color' => array(
          'label' => _t('text_style_header_mini_cart_color', 'Mini Cart Title Color'),
          'type' => 'color',
          'format' => 'rgba'
        ),
        'header_mini_cart_icon_color' => array(
          'label' => _t('text_style_header_mini_cart_icon_color', 'Mini Cart Icon Color'),
          'type' => 'color',
          'format' => 'rgba'
        ),
        'header_mini_cart_icon_bg_color' => array(
          'label' => _t('text_style_header_mini_cart_bg_color', 'Mini Cart Icon Background Color'),
          'type' => 'color',
          'format' => 'rgba'
        ),
        'header_mini_cart_bg_color' => array(
          'label' => _t('text_style_header_mini_cart_bg_color', 'Mini Cart Background Color'),
          'type' => 'color',
          'format' => 'rgba'
        ),
      )
    ),
		'menu' => array(
			'label' => _t('text_style_menu', 'Menu Style'),
			'items' => array(
				'menu_item_color' => array(
					'label' => _t('text_style_item_color', 'Item Color'),
					'type' => 'color',
					'format' => 'hex'
				),
        'menu_item_hover_color' => array(
          'label' => _t('text_style_menu_item_hover_color', 'Item Hover Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
        'menu_bg_color' => array(
          'label' => _t('text_style_menu_bg_color', 'Background Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
        'menu_border_color' => array(
          'label' => _t('text_style_menu_border_color', 'Border Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
				'menu_font' => array(
					'label' => _t('text_style_font', 'Font'),
					'type' => 'style_font',
					'font_family' => 1,
					'font_size' => 1,
					'font_weight' => 1,
					'font_style' => 1,
					'text_transform' => 1
				),
			)
		),
		'footer' => array(
			'label' => _t('text_style_footer', 'Footer Style'),
			'items' => array(
				'footer_background_color' => array(
					'label' => _t('text_style_footer_background_color', 'Background Color'),
					'type' => 'color',
					'format' => 'hex'
				),
				'footer_background_image' => array(
					'label' => _t('text_style_footer_background_image', 'Background Image'),
					'type' => 'style_image_selector'
				),
				'footer_pattern' => array(
					'label' => _t('text_style_footer_pattern', 'Background Pattern'),
					'type' => 'style_pattern'
				),
				'footer_heading_color' => array(
					'label' => _t('text_style_heading_color', 'Heading Color'),
					'type' => 'color',
					'format' => 'hex'
				),
				'footer_text_color' => array(
					'label' => _t('text_style_footer_text_color', 'Text Color'),
					'type' => 'color',
					'format' => 'hex'
				),
				'footer_link_color' => array(
					'label' => _t('text_style_footer_link_color', 'Link Color'),
					'type' => 'color',
					'format' => 'hex'
				),
				'footer_link_hover_color' => array(
					'label' => _t('text_style_footer_link_hover_color', 'Link Hover Color'),
					'type' => 'color',
					'format' => 'hex'
				),
				'footer_border_color' => array(
					'label' => _t('text_style_footer_border_color', 'Border Color'),
					'type' => 'color',
					'format' => 'hex'
				),
			)
		),
    'powered' => array(
      'label' => _t('text_style_powered', 'Powered Style'),
      'items' => array(
        'powered_background_color' => array(
          'label' => _t('text_style_powered_background_color', 'Background Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
        'powered_background_image' => array(
          'label' => _t('text_style_powered_background_image', 'Background Image'),
          'type' => 'style_image_selector'
        ),
        'powered_pattern' => array(
          'label' => _t('text_style_powered_pattern', 'Background Pattern'),
          'type' => 'style_pattern'
        ),
        'powered_text_color' => array(
          'label' => _t('text_style_powered_text_color', 'Text Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
        'powered_link_color' => array(
          'label' => _t('text_style_powered_link_color', 'Link Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
        'powered_link_hover_color' => array(
          'label' => _t('text_style_powered_link_hover_color', 'Link Hover Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
        'powered_border_color' => array(
          'label' => _t('text_style_powered_border_color', 'Border Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
      )
    ),
    'social' => array(
      'label' => _t('text_style_social', 'Social Style'),
      'items' => array(
        'social_icon_bg' => array(
          'label' => _t('text_style_social_icon_bg', 'Social Icon Background'),
          'type' => 'color',
          'format' => 'hex'
        ),
        'social_icon_color' => array(
          'label' => _t('text_style_social_icon_color', 'Social Icon Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
        'social_icon_hover_color' => array(
          'label' => _t('text_style_social_icon_hover_color', 'Social Icon Hover Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
        'social_icon_hover_bg' => array(
          'label' => _t('text_style_social_icon_hover_bg', 'Social Icon Hover Background'),
          'type' => 'color',
          'format' => 'hex'
        )
      )
    ),
    'contact' => array(
      'label' => _t('text_style_social', 'Contact Style'),
      'items' => array(
        'contact_icon_bg' => array(
          'label' => _t('text_style_contact_icon_bg', 'Contact Icon Background'),
          'type' => 'color',
          'format' => 'hex'
        ),
        'contact_icon_color' => array(
          'label' => _t('text_style_contact_icon_color', 'Contact Icon Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
        'contact_icon_hover_bg' => array(
          'label' => _t('text_style_contact_icon_hover_bg', 'Contact Icon Hover Background'),
          'type' => 'color',
          'format' => 'hex'
        )
      )
    ),
    'product' => array(
      'label' => _t('text_style_product', 'Product Style'),
      'items' => array(
        'product_name_color' => array(
          'label' => _t('text_style_product_name_color', 'Product Name Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
        'product_price_color' => array(
          'label' => _t('text_style_product_price_color', 'Price Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
        'product_old_price_color' => array(
          'label' => _t('text_style_product_old_price_color', 'Old Price Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
        'product_sale_color' => array(
          'label' => _t('text_style_product_sale_color', 'Sale Badge Background Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
        'product_sale_text_color' => array(
          'label' => _t('text_style_product_sale_text_color', 'Sale Badge Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
        'product_cart_color' => array(
          'label' => _t('text_style_product_cart_color', 'Add to cart Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
        'product_cart_bg_color' => array(
          'label' => _t('text_style_product_cart_bg_color', 'Add to cart Background Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
        'product_cart_hover_color' => array(
          'label' => _t('text_style_product_cart_hover_color', 'Add to cart Hover Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
        'product_cart_hover_bg_color' => array(
          'label' => _t('text_style_product_cart_hover_bg_color', 'Add to cart Hover Background Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
        'product_buttons_color' => array(
          'label' => _t('text_style_product_buttons_color', 'Wishlist/Compare/Quickview Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
        'product_buttons_bg_color' => array(
          'label' => _t('text_style_product_buttons_bg_color', 'Wishlist/Compare/Quickview Background Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
        'product_buttons_hover_color' => array(
          'label' => _t('text_style_product_buttons_color', 'Wishlist/Compare/Quickview Hover Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
        'product_buttons_bg_hover_color' => array(
          'label' => _t('text_style_product_buttons_bg_hover_color', 'Wishlist/Compare/Quickview Hover Background Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
      )
    ),
    'kuler_slides' => array(
      'label' => _t('text_style_product', 'Kuler Slides Style'),
      'items' => array(
        'kuler_slides_buttons_color' => array(
          'label' => _t('text_style_kuler_slides_buttons_color', 'Next/Previous Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
        'kuler_slides_buttons_bg_color' => array(
          'label' => _t('text_style_kuler_slides_buttons_bg_color', 'Next/Previous Background Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
        'kuler_slides_buttons_hover_color' => array(
          'label' => _t('text_style_kuler_slides_buttons_color', 'Next/Previous Hover Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
        'kuler_slides_buttons_bg_hover_color' => array(
          'label' => _t('text_style_kuler_slides_buttons_bg_hover_color', 'Next/Previous Hover Background Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
      )
    ),
    'button' => array(
      'label' => _t('text_style_product', 'Button Style'),
      'items' => array(
        'button_color' => array(
          'label' => _t('text_style_button_color', 'Button Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
        'button_background_color' => array(
          'label' => _t('text_style_button_background_color', 'Button Background Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
        'button_border_color' => array(
          'label' => _t('text_style_button_border_color', 'Button Border Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
        'button_hover_background' => array(
          'label' => _t('text_style_button_hover_background', 'Button Hover Background'),
          'type' => 'color',
          'format' => 'hex'
        ),
        'button_hover_color' => array(
          'label' => _t('text_style_button_hover_color', 'Button Hover Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
        'button_hover_border_color' => array(
          'label' => _t('text_style_button_hover_border_color', 'Button Hover Border Color'),
          'type' => 'color',
          'format' => 'hex'
        ),
      )
    )
	),
	'positions' => array(
		'header_extra_info' => _t('text_position_header_extra_info', 'Header Extra Info'),
		'header_top' => _t('text_position_header_top', 'Header Top'),
		'menu' => _t('text_position_menu', 'Menu'),
    'promo' => _t('text_position_promotion', 'Promo'),
    'vertical_menu' => _t('text_position_vertical_menu', 'Vertical Menu'),
    'footer_top' => _t('text_position_footer_top', 'Footer Top'),
		'footer_bottom' => _t('text_position_footer_bottom', 'Footer Bottom'),
		//'footer_extra_info' => _t('text_position_footer_extra_info', 'Footer Extra Info')
	),
  'excluded_options' => array(
    'header_logo_position' => true,
    'facebook' => true,
    'twitter' => true,
    'product_page_button_select_list' => true,
    'product_page_related_products_position' => true
  )
);