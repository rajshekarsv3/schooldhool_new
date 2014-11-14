<?php include_once('functions.tpl'); ?>
<?php echo $header; ?>
	<section id="main-content" ng-app="kulerModule" ng-controller="ControlPanelCtrl" body>
		<section class="wrapper">
			<div class="alert alert-success fade in" ng-if="theme_updated">
				<button data-dismiss="alert" class="close close-sm" type="button">
					<i class="fa fa-times"></i>
				</button>
				{{theme_updated_message}}
			</div>
			<div class="alert alert-{{messageType}} fade in" ng-if="message">
				<button data-dismiss="alert" class="close close-sm" type="button">
					<i class="fa fa-times"></i>
				</button>
				{{message}}
			</div>

			<div class="row">
				<div class="col-md-12">
					<ul class="breadcrumb">
						<?php $breadcrumb_index = 0; ?>
						<?php foreach ($breadcrumbs as $breadcrumb) { ?>
						<li><?php if ($breadcrumb_index > 0) { ?><i class="fa fa-angle-double-right"></i><?php } else { ?><i class="fa fa-home"></i><?php } ?> <a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
							<?php $breadcrumb_index++; ?>
						</li>
						<?php } ?>
					</ul>
				</div>
			</div>

			<div class="row">
				<div class="col-sm-12">
					<div class="panel navigation-panel">
						<div class="panel-body">
							<div class="form-inline pull-left" id="panel-skin">
								<div class="form-group">
									<select class="form-control" ng-model="store_id" ng-change="_loadTheme(store_id);" style="width: 200px;" tooltip="<?php echo _t('text_hint_store'); ?>">
										<?php foreach ($stores as $store_id => $name) { ?>
											<option value="<?php echo $store_id; ?>"><?php echo $name; ?></option>
										<?php } ?>
									</select>
								</div>
								<div class="form-group">
									<select class="form-control" style="width: 200px;" ng-model="store.skin_id" ng-options="skin_id as skin.name group by skin.group for (skin_id, skin) in skins" tooltip="<?php echo _t('text_hint_skin'); ?>"></select>
								</div>
								<button class="btn btn-danger" ng-if="canRemoveSkin();" ng-click="removeSkin()" tooltip="<?php echo _t('text_hint_remove_skin'); ?>"><?php echo _t('button_remove_skin', 'Remove Skin'); ?></button>
								<button class="btn btn-default" ng-click="exportSkins()" tooltip="<?php echo _t('text_hint_export_skins'); ?>"><?php echo _t('button_export_skins', 'Export Skins'); ?></button>
								<span class="btn btn-default btn-file" tooltip="<?php echo _t('text_hint_import_skins'); ?>">
									<span class="fileupload-new"><i class="fa fa-paper-clip"></i> <?php echo _t('button_import_skins', 'Import Skins'); ?></span>
									<input type="file" class="default" ng-file-select="importSkins($files)" />
								</span>
							</div>
							<div class="pull-right main-actions">
								<div class="input-group">
									<input type="text" class="form-control" ng-model="new_skin_name" ng-enter="saveSkinAs()" placeholder="<?php echo _t('text_enter_skin_name', 'Enter skin name'); ?>" />
									<span class="input-group-btn">
										<button class="btn btn-default" type="button" ng-click="saveSkinAs()" tooltip="<?php echo _t('text_hint_save_skin_as'); ?>"><?php echo _t('button_save_skin_as', 'Save Skin As'); ?></button>
									</span>
								</div>
								<button class="btn btn-success" ng-click="save()"><i class="fa fa-check-circle-o"></i> <?php echo _t('button_save', 'Save'); ?></button>
								<button class="btn btn-danger" ng-click="cancel()"><i class="fa fa-times-circle"></i> <?php echo _t('button_cancel', 'Cancel'); ?></button>
							</div>
						</div>
					</div>

					<section class="panel">
						<nav class="navbar navbar-inverse" role="navigation">
							<div class="navbar-header col-sm-2">
								<h2><img src="view/kuler/image/icon/kuler_logo.png" /> <?php echo _t('text_control_panel', 'Control Panel'); ?> <span id="theme-version" ng-if="theme_version">v{{theme_version}}</span></h2>
							</div>

							<div class="collapse navbar-collapse">
								<ul id="main-tab" class="nav navbar-nav" persistent-tab="kcp_section">
									<li data-target="#tab-general"><a><?php echo _t('text_general_options', 'General Options'); ?></a></li>
									<li data-target="#tab-footer"><a><?php echo _t('text_footer', 'Footer'); ?></a></li>
									<li data-target="#tab-styles" data-target-disabled="true" ng-click="showStylePanel()"><a><?php echo _t('text_style_customization', 'Style Customization'); ?></a></li>
									<li data-target="#tab-custom-code"><a><?php echo _t('text_custom_code', 'Custom Code'); ?></a></li>
								</ul>

								<ul class="nav navbar-nav navbar-right" id="usedful-links">
									<li><a ng-href="{{documentation_url}}" target="_blank"><?php echo _t('text_documentation', 'Documentation'); ?></a></li>
									<li><a ng-href="{{demo_url}}" target="_blank">{{theme_name}} <?php echo _t('text_demo', 'Demo'); ?></a></li>
									<li><a href="<?php echo $support_url; ?>" target="_blank"><?php echo _t('text_support', 'Support'); ?></a></li>
								</ul>
							</div>
						</nav>
					</section>

					<section class="panel" style="margin-top: -10px;">
						<div class="panel-body" id="main-section">
							<div id="tab-general" class="section-tab">
								<ul class="nav nav-pills nav-stacked col-sm-2" persistent-tab="kcp_group_general">
									<li data-target="#tab-layout"><a><i class="fa fa-desktop"></i> {{'text_layout' | _t:'Layout'}}</a></li>
									<li data-target="#tab-header"><a><i class="fa fa-arrows-h"></i> {{'text_header' | _t:'Header'}}</a></li>
									<li data-target="#tab-live-search"><a><i class="fa fa-search"></i> {{'text_live_search' | _t:'Live Search' }}</a></li>
									<li data-target="#tab-category-page"><a><i class="fa fa-list"></i> {{'text_category_page' | _t:'Category Page'}}</a></li>
									<li data-target="#tab-product-page"><a><i class="fa fa-paste"></i> {{'text_product_page' | _t:'Product Page'}}</a></li>
									<li data-target="#tab-contact-page"><a><i class="fa fa-phone"></i> {{'text_contact_page' | _t:'Contact Page'}}</a></li>
									<li data-target="#tab-checkout-page"><a><i class="fa fa-shopping-cart"></i> {{'text_checkout_page' | _t:'Checkout Page'}}</a></li>
									<li data-target="#tab-others"><a><i class="fa fa-wrench"></i> {{'text_others' | _t:'Others'}}</a></li>
								</ul>
								<div class="col-sm-10 tab-container">
									<div id="tab-layout" class="tab-content">
										<?php echo renderBeginOptionContainer(); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_layout_style', 'Layout Style'),
											'type' => 'select',
											'format' => 'radio',
											'name' => 'layout_style',
											'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/layout_style.png',
											'options' => array(
												'boxed' => _t('text_boxed', 'Boxed'),
												'full_width' => _t('text_full_width', 'Full Width')
											)
										)); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_max_width', 'Max Width'),
											'type' => 'select',
											'format' => 'radio',
											'name' => 'maximum_width',
											'options' => array(
												'1050px' => '1050px',
												'1170px' => '1170px',
											),
											'rowAttrs' => array('ng-if="options.layout_style == \'boxed\'"')
										)); ?>
										<?php echo renderCloseOptionContainer(); ?>
									</div>

									<div id="tab-header" class="tab-content">
										<?php echo renderBeginOptionContainer(); ?>

										<?php echo renderOption(array(
											'label' => _t('entry_fixed_header', 'Fixed Header'),
											'type' => 'switch',
											'name' => 'fixed_header',
											'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/fixed_header.png',
										)); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_logo_position', 'Logo Position'),
											'type' => 'select',
											'format' => 'radio',
											'name' => 'logo_position',
											'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/logo_position.png',
											'options' => array(
												'left' => _t('text_left', 'Left'),
												'center' => _t('text_center', 'Center'),
												'right' => _t('text_right', 'Right')
											),
											'rowAttrs' => array('ng-if="!excluded_options.header_logo_position"'),
										)); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_home_icon_type', 'Home Icon Type'),
											'type' => 'select',
											'format' => 'radio',
											'name' => 'home_icon_type',
											'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/home_icon_type.png',
											'options' => array(
												'icon' => _t('text_icon', 'Icon'),
												'text' => _t('text_text', 'Text')
											)
										)); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_mobile_menu_title', 'Mobile Menu Title'),
											'type' => 'multilingual_input',
											'name' => 'mobile_menu_title',
											'format' => 'textbox',
											'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/mobile_menu_title.png',
										)); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_multi_level_for_default_menu', 'Multi-Level for Default Menu'),
											'type' => 'switch',
											'name' => 'multi_level_default_menu',
											'column' => 1,
											'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/multi_level_default_menu.png',
											'hint' => _t('text_hint_multi_level_default_menu', 'If there is no module in Menu position, the default menu of OpenCart is used.'),
											'hint_out' => true
										)); ?>

										<?php echo renderCloseOptionContainer(); ?>
									</div>

									<div id="tab-live-search" class="tab-content">
										<?php echo renderBeginOptionContainer(); ?>
										<fieldset>
											<legend><?php echo _t('text_live_search', 'Live Search'); ?> <img src="view/kuler/image/icon/q.png" image-modal image="http://demo.kulerthemes.com/{{theme_id}}/image-docs/live_search.png" /></legend>
										<?php echo renderOption(array(
											'label' => _t('entry_status', 'Status'),
											'type' => 'switch',
											'name' => 'live_search_status'
										)); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_search_field_text', 'Search Field Text'),
											'type' => 'multilingual_input',
											'name' => 'search_field_text',
											'format' => 'textbox',
											'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/search_field_text.png'
										)); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_search_in_specific_category', 'Search in Specific Category'),
											'type' => 'switch',
											'name' => 'search_in_specific_category',
										)); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_search_in_specific_manufacturer', 'Search in Specific Manufacturer'),
											'type' => 'switch',
											'name' => 'search_in_specific_manufacturer',
										)); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_search_in_product_description', 'Search in Product Description'),
											'type' => 'switch',
											'name' => 'search_in_product_description',
										)); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_select_category_text', 'Select Category Text'),
											'type' => 'multilingual_input',
											'name' => 'select_category_text',
											'format' => 'textbox',
											'rowAttrs' => array('ng-if="options.search_in_specific_category"'),
											'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/select_category_text.png'
										)); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_select_manufacturer_text', 'Select Manufacturer Text'),
											'type' => 'multilingual_input',
											'name' => 'select_manufacturer_text',
											'format' => 'textbox',
											'rowAttrs' => array('ng-if="options.search_in_specific_manufacturer"'),
											'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/select_manufacturer_text.png'
										)); ?>
										</fielset>

										<fieldset>
											<legend><?php echo _t('text_result_display', 'Result Display'); ?> <img src="view/kuler/image/icon/q.png" image-modal image="http://demo.kulerthemes.com/{{theme_id}}/image-docs/result_display.png" /></legend>
											<?php echo renderOption(array(
												'label' => _t('entry_search_result_limit', 'Search Result Limit'),
												'type' => 'html',
												'html' => renderInput(array(
													'name' => 'search_result_limit',
													'column' => 1,
													'format' => 'number',
												)) . _t('text_hint_search_result_limit'),

											)); ?>
											<?php echo renderOption(array(
												'label' => _t('entry_product_image_size', 'Product Image Size'),
												'type' => 'html',
												'html' => renderInput(array(
													'name' => 'product_image_width',
													'format' => 'number',
													'column' => 1
												)) . renderInput(array(
														'name' => 'product_image_height',
														'format' => 'number',
														'column' => 1
													))
											)); ?>
											<?php echo renderOption(array(
												'label' => _t('entry_show_product_image', 'Show Product Image'),
												'type' => 'switch',
												'name' => 'show_product_image'
											)); ?>
											<?php echo renderOption(array(
												'label' => _t('entry_show_product_name', 'Show Product Name'),
												'type' => 'switch',
												'name' => 'show_product_name'
											)); ?>
											<?php echo renderOption(array(
												'label' => _t('entry_show_product_description', 'Show Product Description'),
												'type' => 'switch',
												'name' => 'show_product_description'
											)); ?>
											<?php echo renderOption(array(
												'label' => _t('entry_show_product_price', 'Show Product Price'),
												'type' => 'switch',
												'name' => 'show_product_price'
											)); ?>
											<?php echo renderOption(array(
												'label' => _t('entry_show_product_rating', 'Show Product Rating'),
												'type' => 'switch',
												'name' => 'show_product_rating'
											)); ?>
											<?php echo renderOption(array(
												'label' => _t('entry_show_add_to_cart_button', 'Show Add to Cart Button'),
												'type' => 'switch',
												'name' => 'show_add_to_cart_button'
											)); ?>
											<?php echo renderOption(array(
												'label' => _t('entry_show_wish_list_button', 'Show Wish List Button'),
												'type' => 'switch',
												'name' => 'show_wish_list_button'
											)); ?>
											<?php echo renderOption(array(
												'label' => _t('entry_show_compare_button', 'Show Compare Button'),
												'type' => 'switch',
												'name' => 'show_compare_button'
											)); ?>
										</fieldset>

										<?php echo renderCloseOptionContainer(); ?>
									</div>

									<div id="tab-category-page" class="tab-content">
										<?php echo renderBeginOptionContainer(); ?>

										<fieldset>
											<legend><?php echo _t('text_category_menu', 'Category Menu'); ?></legend>
											<?php echo renderOption(array(
												'label' => _t('entry_category_menu_type', 'Category Menu Type'),
												'type' => 'select',
												'name' => 'category_menu_type',
												'options' => array(
													'default' => _t('text_default', 'Default'),
													'accordion' => _t('text_accordion', 'Accordion')
												),
												'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/category_menu_type.png'
											)); ?>
										</fieldset>

										<fieldset>
											<legend><?php echo _t('text_category_image', 'Category Image'); ?></legend>

											<?php echo renderOption(array(
												'label' => _t('entry_hide_category_image', 'Hide Category Image'),
												'type' => 'switch',
												'name' => 'hide_category_image',
												'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/hide_category_image.png'
											)); ?>
										</fieldset>

										<fieldset>
											<legend><?php echo _t('text_sub_category_image', 'Sub-Category Image'); ?></legend>
											<?php echo renderOption(array(
												'label' => _t('entry_sub_category_image', 'Sub-Category Image'),
												'type' => 'switch',
												'name' => 'sub_category_image',
												'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/sub_category_image.png'
											)); ?>
											<?php echo renderOption(array(
												'label' => _t('entry_sub_category_image_size', 'Sub-Category Image Size'),
												'type' => 'input',
												'format' => 'number',
												'column' => 1,
												'name' => 'sub_category_image_size',
												'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/sub_category_image_size.png'
											)); ?>
											<?php echo renderOption(array(
												'label' => _t('entry_sub_categories_image_carousel', 'Sub-Categories Image Carousel'),
												'type' => 'switch',
												'name' => 'sub_categories_image_carousel',
												'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/sub_categories_image_carousel.png'
											)); ?>
										</fieldset>
										<fieldset>
											<legend><?php echo _t('text_product_display', 'Product Display'); ?></legend>
											<?php echo renderOption(array(
												'label' => _t('entry_display_product_mode', 'Display Product Mode'),
												'type' => 'select',
												'name' => 'display_product_mode',
												'options' => array(
													'list' => _t('text_list', 'List'),
													'grid' => _t('text_grid', 'Grid')
												),
												'hint' => _t('text_hint_display_product_mode')
											)); ?>
										</fieldset>

										<?php echo renderCloseOptionContainer(); ?>
									</div>

									<div id="tab-product-page" class="tab-content">
										<?php echo renderBeginOptionContainer(); ?>

										<fieldset>
											<legend><?php echo _t('text_buy_section', 'Buy Section'); ?></legend>
											<?php echo renderOption(array(
												'label' => _t('entry_show_brand_logo', 'Show Brand Logo'),
												'type' => 'switch',
												'name' => 'show_brand_logo',
												'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/show_brand_logo.png'
											)); ?>
											<?php echo renderOption(array(
												'label' => _t('entry_brand_logo_size', 'Brand Logo Size'),
												'type' => 'html',
												'html' => renderInput(array(
													'format' => 'number',
													'column' => 1,
													'name' => 'brand_logo_width'
												)) . renderInput(array(
														'format' => 'number',
														'column' => 1,
														'name' => 'brand_logo_height'
													)),
												'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/show_brand_logo.png',
												'rowAttrs' => array('ng-if="options.show_brand_logo"'),
											)); ?>
											<?php echo renderOption(array(
												'label' => _t('entry_show_save_percent', 'Show Save Percent'),
												'type' => 'switch',
												'name' => 'show_save_percent',
												'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/show_save_percent.png'
											)); ?>
											<?php echo renderOption(array(
												'label' => _t('entry_save_text', 'Save Text'),
												'type' => 'multilingual_input',
												'name' => 'save_text',
												'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/show_save_percent.png',
												'rowAttrs' => array('ng-if="options.show_save_percent"'),
											)); ?>
											<?php echo renderOption(array(
												'label' => _t('entry_show_tax', 'Show Tax'),
												'type' => 'switch',
												'name' => 'show_tax',
												'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/show_tax.png'
											)); ?>
											<?php echo renderOption(array(
												'label' => _t('entry_button_select_list', 'Button Select List'),
												'type' => 'switch',
												'name' => 'button_select_list',
												'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/button_select_list.png',
												'rowAttrs' => array('ng-if="!excluded_options.product_page_button_select_list"')
											)); ?>
											<?php echo renderOption(array(
												'label' => _t('entry_show_increment_decrement_quantity', 'Show Increment/Decrement Quantify'),
												'type' => 'switch',
												'name' => 'show_number_quantity',
												'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/show_number_quantity.png'
											)); ?>
										</fieldset>

										<fieldset>
											<legend><?php echo _t('text_product_sharing', 'Product Sharing'); ?></legend>
											<?php echo renderOption(array(
												'label' => _t('entry_default_sharing', 'Default Sharing'),
												'type' => 'switch',
												'name' => 'default_sharing',
												'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/default_sharing.png'
											)); ?>
										</fieldset>

										<fieldset>
											<legend><?php echo _t('text_image_zoom', 'Image Zoom'); ?></legend>
											<?php echo renderOption(array(
												'label' => _t('entry_image_lightbox', 'Image Lightbox'),
												'type' => 'switch',
												'name' => 'image_lightbox',
												'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/image_lightbox.png'
											)); ?>
											<?php echo renderOption(array(
												'label' => _t('entry_image_zoom_type', 'Image Zoom Type'),
												'type' => 'select',
												'name' => 'image_zoom_type',
												'options' => array(
													'none' => _t('text_none', 'None'),
													'outer_cloud' => _t('text_outer_cloud', 'Outer Cloud'),
													'inner_cloud' => _t('text_inner_cloud', 'Inner Cloud')
												),
												'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/image_zoom_type.png'
											)); ?>
											<?php echo renderOption(array(
												'label' => _t('entry_lens_zoom_size', 'Zoom Window Size'),
												'type' => 'html',
												'html' => renderInput(array(
													'name' => 'zoom_window_width',
													'format' => 'number',
													'column' => 1
												)) . renderInput(array(
														'name' => 'zoom_window_height',
														'format' => 'number',
														'column' => 1
													)),
												'rowAttrs' => array('ng-if="options.image_zoom_type == \'outer_cloud\'"'),
												'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/lens_zoom_size.png'
											)); ?>
											<?php echo renderOption(array(
												'label' => _t('entry_lens_zoom_shape', 'Lens Zoom Shape'),
												'type' => 'select',
												'name' => 'lens_zoom_shape',
												'options' => array(
													'basic' => _t('text_basic', 'Basic'),
													'rounded' => _t('text_rounded', 'Rounded')
												),
												'rowAttrs' => array('ng-if="options.image_zoom_type == \'outer_cloud\'"'),
												'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/lens_zoom_shape.png'
											)); ?>
										</fieldset>

										<fieldset>
											<legend><?php echo _t('text_custom_block', 'Custom Block'); ?> <img src="view/kuler/image/icon/q.png" image-modal image="http://demo.kulerthemes.com/{{theme_id}}/image-docs/custom_block.png" /></legend>

											<?php echo renderOption(array(
												'label' => _t('entry_show_custom_block', 'Show Custom Block'),
												'type' => 'switch',
												'name' => 'show_custom_block'
											)); ?>

											<div persistent-tab="kcp_product_page_custom_block" class="htabs">
												<?php foreach ($languages as $language) { ?>
													<a data-target="#tab-custom-block-<?php echo $language['language_id']; ?>" style="display: inline;"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>"> <?php echo $language['name']; ?></a>
												<?php } ?>
											</div>

											<?php foreach ($languages as $language) { ?>
												<div id="tab-custom-block-<?php echo $language['language_id']; ?>">
													<?php echo renderEditor(array(
														'name' => 'custom_block_content[\'' . $language['code'] . '\']'
													)); ?>
												</div>
											<?php } ?>
										</fieldset>

										<fieldset>
											<legend><?php echo _t('text_related_products', 'Related Products'); ?></legend>
											<?php echo renderOption(array(
												'label' => _t('entry_show_related_products', 'Show Related Products'),
												'type' => 'switch',
												'name' => 'show_related_products'
											)); ?>
											<?php echo renderOption(array(
												'label' => _t('entry_related_products_position', 'Related Products Position'),
												'type' => 'select',
												'name' => 'related_products_position',
												'options' => array(
													'bottom' => _t('text_bottom', 'Bottom'),
													'right' => _t('text_right', 'Right')
												),
												'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/related_products_position.png',
												'rowAttrs' => array('ng-if="!excluded_options.product_page_related_products_position"')
											)); ?>
											<?php echo renderOption(array(
												'label' => _t('entry_related_products_style', 'Related Products Style'),
												'type' => 'select',
												'name' => 'related_products_style',
												'options' => array(
													'grid' => _t('text_grid', 'Grid'),
													'slider' => _t('text_slider', 'Slider')
												),
												'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/related_products_style.png'
											)); ?>
										</fieldset>

										<fieldset>
											<legend><?php echo _t('text_custom_tab_1', 'Custom Tab 1'); ?> <img src="view/kuler/image/icon/q.png" image-modal image="http://demo.kulerthemes.com/{{theme_id}}/image-docs/custom_tab_1.png" /></legend>

											<?php echo renderOption(array(
												'label' => _t('entry_show_custom_tab_1', 'Show Custom Tab 1'),
												'type' => 'switch',
												'name' => 'show_custom_tab_1',
											)); ?>

											<div persistent-tab="kcp_product_page_custom_tab_1" class="htabs">
												<?php foreach ($languages as $language) { ?>
													<a data-target="#tab-custom-tab-1-<?php echo $language['language_id']; ?>" style="display: inline;"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>"> <?php echo $language['name']; ?></a>
												<?php } ?>
											</div>

											<?php foreach ($languages as $language) { ?>
												<div id="tab-custom-tab-1-<?php echo $language['language_id']; ?>">
													<?php echo renderOption(array(
														'label' => _t('entry_tab_title', 'Tab Title'),
														'type' => 'input',
														'name' => 'custom_tab_1_title[\'' . $language['code'] . '\']'
													)); ?>

													<?php echo renderOption(array(
														'label' => _t('entry_tab_content', 'Tab Content'),
														'type' => 'editor',
														'name' => 'custom_tab_1_content[\'' . $language['code'] . '\']',
														'wrapper' => true,
														'column' => 8
													)); ?>
												</div>
											<?php } ?>
										</fieldset>

										<fieldset>
											<legend><?php echo _t('text_custom_tab_2', 'Custom Tab 2'); ?> <img src="view/kuler/image/icon/q.png" image-modal image="http://demo.kulerthemes.com/{{theme_id}}/image-docs/custom_tab_2.png" /></legend>

											<?php echo renderOption(array(
												'label' => _t('entry_show_custom_tab_2', 'Show Custom Tab 2'),
												'type' => 'switch',
												'name' => 'show_custom_tab_2'
											)); ?>

											<div persistent-tab="kcp_product_page_custom_tab_2" class="htabs">
												<?php foreach ($languages as $language) { ?>
													<a data-target="#tab-custom-tab-2-<?php echo $language['language_id']; ?>" style="display: inline;"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>"> <?php echo $language['name']; ?></a>
												<?php } ?>
											</div>

											<?php foreach ($languages as $language) { ?>
												<div id="tab-custom-tab-2-<?php echo $language['language_id']; ?>">
													<?php echo renderOption(array(
														'label' => _t('entry_tab_title', 'Tab Title'),
														'type' => 'input',
														'name' => 'custom_tab_2_title[\'' . $language['code'] . '\']'
													)); ?>

													<?php echo renderOption(array(
														'label' => _t('entry_tab_content', 'Tab Content'),
														'type' => 'editor',
														'name' => 'custom_tab_2_content[\'' . $language['code'] . '\']',
														'wrapper' => true,
														'column' => 8
													)); ?>
												</div>
											<?php } ?>
										</fieldset>

										<?php echo renderCloseOptionContainer(); ?>
									</div>

									<div id="tab-contact-page" class="tab-content">
										<?php echo renderBeginOptionContainer(); ?>

										<fieldset>
											<legend><?php echo _t('text_custom_information', 'Custom Information'); ?> <img src="view/kuler/image/icon/q.png" image-modal image="http://demo.kulerthemes.com/{{theme_id}}/image-docs/custom_information.png" /></legend>
											<?php echo renderOption(array(
												'label' => _t('show_custom_information', 'Show Custom Information'),
												'type' => 'switch',
												'name' => 'show_custom_information',
											)); ?>

											<div persistent-tab="kcp_contact_page_custom_information" class="htabs">
												<?php foreach ($languages as $language) { ?>
													<a data-target="#tab-custom-information-<?php echo $language['language_id']; ?>" style="display: inline;"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>"> <?php echo $language['name']; ?></a>
												<?php } ?>
											</div>

											<?php foreach ($languages as $language) { ?>
												<div id="tab-custom-information-<?php echo $language['language_id']; ?>">
													<?php echo renderEditor(array(
														'type' => 'editor',
														'name' => 'custom_information[\'' . $language['code'] . '\']'
													)); ?>
												</div>
											<?php } ?>
										</fieldset>

										<fieldset>
											<legend><?php echo _t('text_google_map', 'Google Map'); ?> <img src="view/kuler/image/icon/q.png" image-modal image="http://demo.kulerthemes.com/{{theme_id}}/image-docs/google_map.png" /></legend>
											<p><?php echo _t('text_hint_google_map'); ?></p>
											<?php echo renderOption(array(
												'label' => _t('entry_show_google_map', 'Google Map'),
												'type' => 'switch',
												'name' => 'show_google_map'
											)); ?>
											<?php echo renderOption(array(
												'label' => _t('entry_latitude', 'Latitude'),
												'type' => 'input',
												'name' => 'latitude'
											)); ?>
											<?php echo renderOption(array(
												'label' => _t('entry_longitude', 'Longitude'),
												'type' => 'input',
												'name' => 'longitude'
											)); ?>
											<?php echo renderOption(array(
												'label' => _t('entry_map_type', 'Map Type'),
												'type' => 'select',
												'name' => 'map_type',
												'options' => array(
													'ROADMAP' => 'ROADMAP',
													'SATELLITE' => 'SATELLITE',
													'HYBRID' => 'HYBRID',
													'TERRAIN' => 'TERRAIN'
												)
											)); ?>
										</fieldset>

										<?php echo renderCloseOptionContainer(); ?>
									</div>

									<div id="tab-checkout-page" class="tab-content">
										<?php echo renderBeginOptionContainer(); ?>

										<?php echo renderOption(array(
											'label' => _t('entry_enable_one_page_checkout', 'Enable One Page Checkout'),
											'type' => 'switch',
											'name' => 'enable_one_page_checkout',
											'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/one_page_checkout.png',
											'hint' => _t('text_hint_one_page_checkout')
										)); ?>

										<?php echo renderCloseOptionContainer(); ?>
									</div>

									<div id="tab-others" class="tab-content">
										<?php echo renderBeginOptionContainer(); ?>

										<fieldset>
											<legend><?php echo _t('text_login', 'Login'); ?></legend>
											<?php echo renderOption(array(
												'label' => _t('entry_login_popup', 'Login Popup'),
												'type' => 'switch',
												'name' => 'login_popup',
												'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/login_popup.png'
											)); ?>
										</fieldset>

										<fieldset>
											<legend><?php echo _t('text_custom_notification', 'Custom Notification'); ?></legend>
											<?php echo renderOption(array(
												'label' => _t('entry_show_custom_notification', 'Show Custom Notification'),
												'type' => 'switch',
												'name' => 'show_custom_notification',
												'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/show_custom_notification.png'
											)); ?>
											<?php echo renderOption(array(
												'label' => _t('entry_notification_show_time', 'Notification Show Time'),
												'type' => 'input',
												'format' => 'number',
												'name' => 'notification_show_time',
												'column' => 1,
												'hint' => _t('entry_text_hint_notification_show_time', 'Number of milliseconds determining how long the custom notification display.'),
												'hint_out' => true
											)); ?>
										</fieldset>

										<fieldset>
											<legend><?php echo _t('text_quick_view', 'Quick View'); ?> <img src="view/kuler/image/icon/q.png" image-modal image="http://demo.kulerthemes.com/{{theme_id}}/image-docs/quick_view.png" /></legend>
											<?php echo renderOption(array(
												'label' => _t('entry_show_quick_view', 'Show Quick View'),
												'type' => 'switch',
												'name' => 'show_quick_view'
											)); ?>
											<?php echo renderOption(array(
												'label' => _t('entry_view_button_text', 'View Button Text'),
												'type' => 'multilingual_input',
												'name' => 'view_button_text',
												'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/view_button_text.png'
											)); ?>
											<?php echo renderOption(array(
												'label' => _t('entry_more_details_button_text', 'More Details Button Text'),
												'type' => 'multilingual_input',
												'name' => 'more_details_button_text'
											)); ?>
											<?php echo renderOption(array(
												'label' => _t('entry_product_description_limit', 'Product Description Limit'),
												'type' => 'input',
												'format' => 'number',
												'name' => 'product_description_limit',
												'column' => 1
											)); ?>
										</fieldset>

										<fieldset>
											<legend><?php echo _t('text_sale_badge', 'Sale Badge'); ?> <img src="view/kuler/image/icon/q.png" image-modal image="http://demo.kulerthemes.com/{{theme_id}}/image-docs/sale_badge.png" /></legend>
											<?php echo renderOption(array(
												'label' => _t('entry_show_sale_badge', 'Show Sale Badge'),
												'type' => 'switch',
												'name' => 'show_sale_badge'
											)); ?>
										</fieldset>

										<fieldset>
											<legend><?php echo _t('text_swap_image', 'Swap Image'); ?> <img src="view/kuler/image/icon/q.png" image-modal image="http://demo.kulerthemes.com/{{theme_id}}/image-docs/swap_image.png" /></legend>
											<?php echo renderOption(array(
												'label' => _t('entry_enable_swap_image', 'Enable Swap Image'),
												'type' => 'switch',
												'name' => 'enable_swap_image'
											)); ?>
										</fieldset>

										<fieldset>
											<legend><?php echo _t('text_scroll_up_button', 'Scroll up Button'); ?> <img src="view/kuler/image/icon/q.png" image-modal image="http://demo.kulerthemes.com/{{theme_id}}/image-docs/scroll_up_button.png" /></legend>
											<?php echo renderOption(array(
												'label' => _t('entry_enable_scroll_up', 'Enable Scroll up'),
												'type' => 'switch',
												'name' => 'enable_scroll_up'
											)); ?>
											<?php echo renderOption(array(
												'label' => _t('entry_scroll_up_text', 'Scroll up Text'),
												'type' => 'multilingual_input',
												'name' => 'scroll_up_text'
											)); ?>
										</fieldset>

										<?php echo renderCloseOptionContainer(); ?>
									</div>
								</div>
							</div>

							<div id="tab-footer" class="section-tab">
								<ul class="nav nav-pills nav-stacked col-sm-2" persistent-tab="kcp_group_footer">
									<li data-target="#tab-information"><a><i class="fa fa-info"></i> <?php echo _t('text_information', 'Information'); ?></a></li>
									<li data-target="#tab-contact"><a><i class="fa fa-phone-square"></i> <?php echo _t('text_contact', 'Contact'); ?></a></li>
									<li data-target="#tab-facebook" ng-show="!excluded_options.facebook"><a><i class="fa fa-facebook"></i> <?php echo _t('text_facebook', 'Facebook'); ?></a></li>
									<li data-target="#tab-twitter" ng-show="!excluded_options.twitter"><a><i class="fa fa-twitter"></i> <?php echo _t('text_twitter', 'Twitter'); ?></a></li>
									<li data-target="#tab-social-icons"><a><i class="fa fa-users"></i> <?php echo _t('text_social_icons', 'Social Icons'); ?></a></li>
									<li data-target="#tab-newsletter"><a><i class="fa fa-envelope"></i> <?php echo _t('text_newsletter', 'Newsletter'); ?></a></li>
									<li data-target="#tab-custom-copyright"><a><i class="fa fa-quote-right"></i> <?php echo _t('text_custom_copyright', 'Custom Copyright'); ?></a></li>
									<li data-target="#tab-payment-icons"><a><i class="fa fa-money"></i> <?php echo _t('text_payment_icons', 'Payment Icons'); ?></a></li>
								</ul>

								<div class="col-sm-10 tab-container">
									<div id="tab-information" class="tab-content">
										<?php echo renderBeginOptionContainer(); ?>

										<?php echo renderOption(array(
											'label' => _t('entry_show_information', 'Show Information'),
											'type' => 'switch',
											'name' => 'show_information',
											'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/show_information.png'
										)); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_show_title', 'Show Title'),
											'type' => 'switch',
											'name' => 'show_information_title'
										)); ?>

										<div persistent-tab="kcp_information" class="htabs">
											<?php foreach ($languages as $language) { ?>
												<a data-target="#tab-information-<?php echo $language['language_id']; ?>" style="display: inline;"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>"> <?php echo $language['name']; ?></a>
											<?php } ?>
										</div>

										<?php foreach ($languages as $language) { ?>
											<div id="tab-information-<?php echo $language['language_id']; ?>">
												<?php echo renderOption(array(
													'label' => _t('entry_title', 'Title'),
													'type' => 'input',
													'name' => 'information_title[\'' . $language['code'] . '\']'
												)); ?>

												<?php echo renderOption(array(
													'label' => _t('entry_content', 'Content'),
													'type' => 'editor',
													'name' => 'information_content[\'' . $language['code'] . '\']',
													'wrapper' => true,
													'column' => 8
												)); ?>
											</div>
										<?php } ?>

										<?php echo renderCloseOptionContainer(); ?>
									</div>

									<div id="tab-contact" class="tab-content">
										<?php echo renderBeginOptionContainer(); ?>

										<?php echo renderOption(array(
											'label' => _t('entry_show_contact', 'Show Contact'),
											'type' => 'switch',
											'name' => 'show_contact',
											'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/show_contact.png'
										)); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_show_title', 'Show Title'),
											'type' => 'switch',
											'name' => 'show_contact_title'
										)); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_title', 'Title'),
											'type' => 'multilingual_input',
											'name' => 'contact_title'
										)); ?>

										<?php echo renderOption(array(
											'label' => _t('entry_skype', 'Skype'),
											'type' => 'html',
											'html' => renderInput(array(
												'name' => 'skype_1'
											)) . renderInput(array(
													'name' => 'skype_2'
												))
										)); ?>

										<?php echo renderOption(array(
											'label' => _t('entry_email', 'Email'),
											'type' => 'html',
											'html' => renderInput(array(
													'name' => 'email_1'
												)) . renderInput(array(
													'name' => 'email_2'
												))
										)); ?>

										<?php echo renderOption(array(
											'label' => _t('entry_mobile', 'Mobile'),
											'type' => 'html',
											'html' => renderInput(array(
													'name' => 'mobile_1'
												)) . renderInput(array(
													'name' => 'mobile_2'
												))
										)); ?>

										<?php echo renderOption(array(
											'label' => _t('entry_phone', 'Phone'),
											'type' => 'html',
											'html' => renderInput(array(
													'name' => 'phone_1'
												)) . renderInput(array(
													'name' => 'phone_2'
												))
										)); ?>

										<?php echo renderOption(array(
											'label' => _t('entry_fax', 'Fax'),
											'type' => 'html',
											'html' => renderInput(array(
													'name' => 'fax_1'
												)) . renderInput(array(
													'name' => 'fax_2'
												))
										)); ?>

										<?php echo renderCloseOptionContainer(); ?>
									</div>

									<div id="tab-facebook" class="tab-content" ng-show="!excluded_options.facebook">
										<?php echo renderBeginOptionContainer(); ?>

										<?php echo renderOption(array(
											'label' => _t('entry_show_facebook', 'Show Facebook'),
											'type' => 'switch',
											'name' => 'show_facebook',
											'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/facebook.png'
										)); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_show_title', 'Show Title'),
											'type' => 'switch',
											'name' => 'show_facebook_title'
										)); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_title', 'Title'),
											'type' => 'multilingual_input',
											'name' => 'facebook_title'
										)); ?>

										<?php echo renderOption(array(
											'label' => _t('entry_facebook_page_id', 'Facebook Page ID'),
											'type' => 'input',
											'name' => 'facebook_page_id'
										)); ?>

										<?php echo renderOption(array(
											'label' => _t('entry_theme', 'Theme'),
											'type' => 'select',
											'name' => 'facebook_theme',
											'options' => array(
												'light' => _t('text_light', 'Light'),
												'dark' => _t('text_dark', 'Dark')
											)
										)); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_show_friend_faces', 'Show Friend\'s Faces'),
											'type' => 'switch',
											'name' => 'show_facebook_friend_faces'
										)); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_show_posts', 'Show Posts'),
											'type' => 'switch',
											'name' => 'show_facebook_posts'
										)); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_box_size', 'Box Size'),
											'type' => 'html',
											'html' => renderInput(array(
												'name' => 'facebook_width',
												'format' => 'number',
												'column' => 1
											)) . renderInput(array(
													'name' => 'facebook_height',
													'format' => 'number',
													'column' => 1
												))
										)); ?>

										<?php echo renderCloseOptionContainer(); ?>
									</div>

									<div id="tab-twitter" class="tab-content" ng-show="!excluded_options.twitter">
										<?php echo renderBeginOptionContainer(); ?>

										<?php echo renderOption(array(
											'label' => _t('entry_show_twitter', 'Show Twitter'),
											'type' => 'switch',
											'name' => 'show_twitter',
											'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/twitter.png'
										)); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_show_title', 'Show Title'),
											'type' => 'switch',
											'name' => 'show_twitter_title'
										)); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_title', 'Title'),
											'type' => 'multilingual_input',
											'name' => 'twitter_title'
										)); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_twitter_username', 'Twitter Username'),
											'type' => 'input',
											'name' => 'twitter_username'
										)); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_widget_id', 'Widget ID'),
											'type' => 'input',
											'name' => 'twitter_widget_id',
											'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/twitter_widget_id.png',
											'hint'      => _t('text_hint_twitter_widget_id'),
											'hint_out'  => true
										)); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_theme', 'Theme'),
											'type' => 'select',
											'name' => 'twitter_theme',
											'options' => array(
												'light' => _t('text_light', 'Light'),
												'dark' => _t('text_dark', 'Dark')
											)
										)); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_number_of_tweets', 'Number of Tweets'),
											'type' => 'select',
											'name' => 'number_of_tweets',
											'options' => array(
												2 => 2,
												3 => 3,
												4 => 4,
												5 => 5
											)
										)); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_box_size', 'Box Size'),
											'type' => 'html',
											'html' => renderInput(array(
													'name' => 'twitter_width',
													'format' => 'number',
													'column' => 1
												)) . renderInput(array(
													'name' => 'twitter_height',
													'format' => 'number',
													'column' => 1
												))
										)); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_show_header', 'Show Header'),
											'type' => 'switch',
											'name' => 'show_twitter_header'
										)); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_show_footer', 'Show Footer'),
											'type' => 'switch',
											'name' => 'show_twitter_footer'
										)); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_transparent_background', 'Transparent Background'),
											'type' => 'switch',
											'name' => 'twitter_transparent_background'
										)); ?>

										<?php echo renderCloseOptionContainer(); ?>
									</div>

									<div id="tab-social-icons" class="tab-content">
										<?php echo renderBeginOptionContainer(); ?>

										<?php echo renderOption(array(
											'label' => _t('entry_show_social_icons', 'Show Social Icons'),
											'type' => 'switch',
											'name' => 'show_social_icons',
											'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/social_icons.png'
										)); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_show_title', 'Show Title'),
											'type' => 'switch',
											'name' => 'show_social_icons_title'
										)); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_title', 'Title'),
											'type' => 'multilingual_input',
											'name' => 'social_icons_title'
										)); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_icon_style', 'Icon Style'),
											'type' => 'select',
											'name' => 'icon_style',
											'options' => array(
												'icon' => _t('text_icon', 'Icon'),
												'square' => _t('text_square', 'Square'),
												'circle' => _t('text_circle', 'Circle')
											)
										)); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_icon_size', 'Icon Size'),
											'type' => 'select',
											'name' => 'icon_size',
											'options' => array(
												'24px' => '24px',
												'32px' => '32px',
												'48px' => '48px'
											)
										)); ?>

										<fieldset>
											<legend><?php echo _t('text_social_networks', 'Social Networks'); ?></legend>
											<?php echo renderOption(array(
												'label' => _t('entry_facebook', 'Facebook'),
												'type' => 'html',
												'html' => renderSwitch(array(
													'name' => 'social_icon_facebook',
													'type' => 'switch',
													'column' => 1
												)) . renderInput(array(
														'name' => 'social_icon_facebook_link'
													))
											)); ?>

											<?php echo renderOption(array(
												'label' => _t('entry_twitter', 'Twitter'),
												'type' => 'html',
												'html' => renderSwitch(array(
														'name' => 'social_icon_twitter',
														'type' => 'switch',
														'column' => 1
													)) . renderInput(array(
														'name' => 'social_icon_twitter_link'
													)),
											)); ?>

											<?php echo renderOption(array(
												'label' => _t('entry_google_plus', 'Google +'),
												'type' => 'html',
												'html' => renderSwitch(array(
														'name' => 'social_icon_google_plus',
														'type' => 'switch',
														'column' => 1
													)) . renderInput(array(
														'name' => 'social_icon_google_plus_link'
													))
											)); ?>
											<?php echo renderOption(array(
												'label' => _t('entry_youtube', 'Youtube'),
												'type' => 'html',
												'html' => renderSwitch(array(
														'name' => 'social_icon_youtube',
														'type' => 'switch',
														'column' => 1
													)) . renderInput(array(
														'name' => 'social_icon_youtube_link'
													))
											)); ?>
											<?php echo renderOption(array(
												'label' => _t('entry_pinterest', 'Pinterest'),
												'type' => 'html',
												'html' => renderSwitch(array(
														'name' => 'social_icon_pinterest',
														'type' => 'switch',
														'column' => 1
													)) . renderInput(array(
														'name' => 'social_icon_pinterest_link'
													))
											)); ?>
											<?php echo renderOption(array(
												'label' => _t('entry_instagram', 'Instagram'),
												'type' => 'html',
												'html' => renderSwitch(array(
														'name' => 'social_icon_instagram',
														'type' => 'switch',
														'column' => 1
													)) . renderInput(array(
														'name' => 'social_icon_instagram_link'
													))
											)); ?>
											<?php echo renderOption(array(
												'label' => _t('entry_rss', 'RSS'),
												'type' => 'html',
												'html' => renderSwitch(array(
														'name' => 'social_icon_rss',
														'type' => 'switch',
														'column' => 1
													)) . renderInput(array(
														'name' => 'social_icon_rss_link'
													))
											)); ?>
										</fieldset>

										<?php echo renderCloseOptionContainer(); ?>
									</div>

									<div id="tab-newsletter" class="tab-content">
										<?php echo renderBeginOptionContainer(); ?>

										<?php echo renderOption(array(
											'label' => _t('entry_show_newsletter', 'Show Newsletter'),
											'type' => 'switch',
											'name' => 'show_newsletter',
											'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/newsletter.png'
										)); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_show_title', 'Show Title'),
											'type' => 'switch',
											'name' => 'show_newsletter_title'
										)); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_title', 'Title'),
											'type' => 'multilingual_input',
											'name' => 'newsletter_title'
										)); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_description_text', 'Description Text'),
											'type' => 'multilingual_input',
											'name' => 'newsletter_description_text',
											'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/newsletter.png'
										)); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_input_text', 'Input Text'),
											'type' => 'multilingual_input',
											'name' => 'newsletter_input_text',
											'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/newsletter.png'
										)); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_subscription_success_message', 'Subscription Success Message'),
											'type' => 'multilingual_input',
											'name' => 'newsletter_success_message',
											'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/newsletter_success_message.png'
										)); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_subscribe_button_text', 'Subscribe Button Text'),
											'type' => 'multilingual_input',
											'name' => 'newsletter_button_text',
											'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/newsletter.png'
										)); ?>

										<filedset ng-controller="MailServiceCtrl">
											<legend><?php echo _t('Mail Service'); ?></legend>
											<?php echo renderOption(array(
												'label' => _t('entry_mail_service', 'Mail Service'),
												'type' => 'select',
												'name' => 'mail_service',
												'options' => array(
													''      => _t('text_select', '-- Select --'),
													'icontact' => 'iContact',
													'mailchimp' => 'MailChimp',
												),
												'inputAttrs' => 'ng-change="getLists(options.mail_service)"'
											)); ?>

											<div ng-if="options.mail_service == 'icontact'">
												<?php echo renderOption(array(
													'label' => _t('entry_service_app_key', 'Service App Key'),
													'type' => 'input',
													'name' => 'icontact_app_key',
													'hint' => _t('text_hint_service_app_key'),
													'hint_out' => true
												)); ?>
												<?php echo renderOption(array(
													'label' => _t('entry_username', 'Username'),
													'type' => 'input',
													'name' => 'icontact_username'
												)); ?>
												<?php echo renderOption(array(
													'label' => _t('entry_password', 'Password'),
													'type' => 'input',
													'name' => 'icontact_password'
												)); ?>
											</div>

											<div ng-if="options.mail_service == 'mailchimp'">
												<?php echo renderOption(array(
													'label' => _t('entry_api_key', 'API Key'),
													'type' => 'input',
													'name' => 'mailchimp_api_key',
													'hint' => _t('text_hint_mailchimp_api_key'),
													'hint_out' => true
												)); ?>
											</div>

											<?php echo renderOption(array(
												'label' => _t('entry_contact_list', 'Contact List'),
												'type' => 'html',
												'html' => renderSelect(array(
													'name' => 'contact_list',
													'inputAttrs' => 'ng-options="key as value for (key, value) in lists" ng-disabled="listsLoading"',
													'options' => array()
												)) . '<button class="btn btn-white btn-sm" ng-click="getLists(options.mail_service)" style="margin-right: 15px;">'. _t('button_get_contact_lists', 'Get Contact Lists') .'</button>' . _t('text_hint_contact_list')
											)); ?>
										</filedset>

										<?php echo renderCloseOptionContainer(); ?>
									</div>

									<div id="tab-custom-copyright" class="tab-content">
										<?php echo renderBeginOptionContainer(); ?>

										<?php echo renderOption(array(
											'label' => _t('entry_show_custom_copyright', 'Show Custom Copyright'),
											'type' => 'switch',
											'name' => 'show_custom_copyright',
											'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/custom_copyright.png'
										)); ?>

										<div persistent-tab="kcp_custom_copyright" class="htabs">
											<?php foreach ($languages as $language) { ?>
												<a data-target="#tab-custom-copyright-<?php echo $language['language_id']; ?>" style="display: inline;"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>"> <?php echo $language['name']; ?></a>
											<?php } ?>
										</div>

										<?php foreach ($languages as $language) { ?>
											<div id="tab-custom-copyright-<?php echo $language['language_id']; ?>">
												<?php echo renderEditor(array(
													'name' => 'custom_copyright[\'' . $language['code'] . '\']'
												)) ?>
											</div>
										<?php } ?>

										<?php echo renderCloseOptionContainer(); ?>
									</div>

									<div id="tab-payment-icons" class="tab-content">
										<?php echo renderBeginOptionContainer(); ?>

										<?php echo renderOption(array(
											'label' => _t('entry_show_payment_icons', 'Show Payment Icons'),
											'type' => 'switch',
											'name' => 'show_payment_icons',
											'image_hint' => 'http://demo.kulerthemes.com/{{theme_id}}/image-docs/payment_icons.png'
										)); ?>

										<table class="table" ng-controller="PaymentIconsCtrl">
											<thead>
											<tr>
												<th><?php echo _t('entry_icon', 'Icon'); ?></th>
												<th><?php echo _t('entry_name', 'Name'); ?></th>
												<th><?php echo _t('entry_link', 'Link'); ?></th>
												<th><?php echo _t('entry_sort', 'Sort'); ?></th>
												<th><?php echo _t('entry_new_tab', 'New Tab'); ?></th>
												<th><?php echo _t('entry_actions', 'Actions'); ?></th>
											</tr>
											</thead>
											<tfoot>
											<tr>
												<td colspan="100%"><button type="button" class="btn btn-success" ng-click="addPaymentIcon()"><?php echo _t('button_add_payment_icon', 'Add Payment Icon'); ?></button></td>
											</tr>
											</tfoot>
											<tbody>
											<tr ng-repeat="icon in icons">
												<td style="width: 180px;"><image-selector image="icon.image" has-button="false" /></td>
												<td style="width: 250px;"><multilingual-input languages="languages" input="icon.name"></multilingual-input></td>
												<td style="width: 300px;"><input type="text" class="form-control" ng-model="icon.link" /></td>
												<td style="width: 90px;"><input type="number" class="form-control" ng-model="icon.sort" /></td>
												<td style="width: 70px;"><switch input="icon.new_tab" /></td>
												<td><button type="button" class="btn btn-danger" ng-click="removePaymentIcon($index)"><?php echo _t('button_remove', 'Remove'); ?></button></td>
											</tr>
											</tbody>
										</table>

										<?php echo renderCloseOptionContainer(); ?>
									</div>
								</div>
							</div>

							<div id="panel-styles" ng-class="{'active': stylePanelDisplay}" ng-if="stylePanelDisplay">
								<div class="row">
									<div class="col-sm-12 clearfix">
										<div id="customize-controls" ng-class="{hiding: hiddenStylePanel}">
											<div id="customize-controls-actions">
												<button class="btn btn-success btn-sm btn-save" ng-click="save();"><?php echo _t('button_save'); ?></button>
												<button class="btn btn-danger btn-sm" ng-click="cancelStyleCustomization();"><?php echo _t('button_cancel'); ?></button>
											</div>
											<div id="customize-controls-container">
												<div class="panel-group" ng-include="style_panel_template" onload="onLoadStylePanel()"></div>
												<div class="style-panel-loading" ng-if="stylePanelLoading"></div>
											</div>
											<div id="customize-controls-second-actions">
												<span class="pull-right btn-collapse" ng-click="toggleStylePanel()"><span class="fa" ng-class="{'fa-arrow-circle-left': !hiddenStylePanel, 'fa-arrow-circle-right': hiddenStylePanel}"></span> <span class="btn-collapse-text"><?php echo _t('button_collapse', 'Collapse'); ?></span></span>
											</div>
										</div>
										<div id="customize-preview">
											<iframe ng-src="{{param_preview_url | trusted}}" name="preview_frame" frameborder="0" preview></iframe>
										</div>
									</div>
								</div>
							</div>

							<div id="tab-custom-code" class="section-tab">
								<ul class="nav nav-pills nav-stacked col-sm-2" persistent-tab="kcp_group_custom_code">
									<li data-target="#tab-custom-css-file"><a><i class="fa fa-file-text-o"></i> <?php echo _t('text_custom_css_file', 'Custom CSS File'); ?></a></li>
									<li data-target="#tab-custom-css"><a><i class="fa fa-css3"></i> <?php echo _t('text_custom_css', 'Custom CSS'); ?></a></li>
									<li data-target="#tab-custom-javascript"><a><i class="fa fa-code"></i> <?php echo _t('text_custom_javascript', 'Custom JavaScript'); ?></a></li>
								</ul>
								<div class="col-sm-10 tab-container">
									<div id="tab-custom-css-file" class="tab-content">
										<?php echo renderBeginOptionContainer(); ?>

										<?php echo renderOption(array(
											'label' => _t('entry_status', 'Status'),
											'type' => 'switch',
											'name' => 'custom_css_file_status'
										)); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_file', 'File'),
											'type' => 'input',
											'name' => 'custom_css_file',
											'hint' => _t('text_hint_custom_css_file'),
											'column' => '8'
										)); ?>

										<?php echo renderCloseOptionContainer(); ?>
									</div>

									<div id="tab-custom-css" class="tab-content">
										<?php echo renderBeginOptionContainer(); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_status', 'Status'),
											'type' => 'switch',
											'name' => 'custom_css_status'
										)); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_custom_css', 'Custom CSS'),
											'type' => 'textarea',
											'name' => 'custom_css'
										)); ?>
										<?php echo renderCloseOptionContainer(); ?>
									</div>

									<div id="tab-custom-javascript" class="tab-content">
										<?php echo renderBeginOptionContainer(); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_status', 'Status'),
											'type' => 'switch',
											'name' => 'custom_js_status'
										)); ?>
										<?php echo renderOption(array(
											'label' => _t('entry_custom_javascript', 'Custom JavaScript'),
											'type' => 'textarea',
											'name' => 'custom_js'
										)); ?>
										<?php echo renderCloseOptionContainer(); ?>
									</div>
								</div>
							</div>
						</div>
					</section>
				</div>
			</div>
		</section>
		<div id="kuler-loader" ng-if="loading"></div>
		<iframe ng-src="{{download_url}}" id="download-frame"></iframe>
	</section>
	<script>
		var Kuler = {
			front_base: <?php echo json_encode($front_base); ?>,
			token: '<?php echo $token; ?>',
			languages: <?php echo json_encode($languages); ?>,
			defaultLanguage: <?php echo json_encode($default_language); ?>,
			store_id: <?php echo json_encode($selected_store_id); ?>,
			store_url: '<?php echo $store_url ?>',
			skin_url: '<?php echo $skin_url; ?>',
			save_url: '<?php echo $save_url; ?>',
			module_url: '<?php echo $module_url; ?>',
			save_skin_as_url: '<?php echo $save_skin_as_url; ?>',
			remove_skin_url: '<?php echo $remove_skin_url; ?>',
			export_skins_url: '<?php echo $export_skins_url; ?>',
			import_skins_url: '<?php echo $import_skins_url; ?>',
			style_panel_url: '<?php echo $style_panel_url; ?>',
			newsletter_lists_url: '<?php echo $newsletter_lists_url; ?>',
			fonts: <?php echo json_encode($fonts); ?>
		};
	</script>
<?php echo $footer; ?>