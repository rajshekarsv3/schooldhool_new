<?php include_once(DIR_TEMPLATE . 'module/kuler_helper.tpl'); ?>
<?php echo $header; ?>
<section id="main-content" class="kuler-module" ng-app="kulerModule" ng-controller="ShowcaseCtrl">
<section class="wrapper">
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
				<div class="col-lg-10 col-sm-9">
					<div class="pull-left">
						<label><?php echo _t('text_current_store', 'Current Store'); ?></label>
						<select class="form-control" id="store-selector" ng-model="store_id" ng-change="selectStore(store_id)" tooltip="<?php echo _t('text_hint_store', 'Select store to configure this module'); ?>">
							<?php foreach ($stores as $index => $store_name) { ?>
								<option value="<?php echo $index; ?>"><?php echo $store_name; ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="col-lg-2 col-md-3 col-sm-5">
						<button class="btn btn-success" id="module-adder" ng-click="addModule()"><i class="fa fa-plus-circle"></i> <?php echo _t('button_add_module', 'Add Module'); ?></button>
					</div>
				</div>
				<div class="pull-right main-actions">
					<button class="btn btn-success" ng-click="save()"><i class="fa fa-check-circle-o"></i> <?php echo _t('button_save'); ?></button>
					<a class="btn btn-danger" href="<?php echo $cancel_url; ?>"><i class="fa fa-times-circle"></i> <?php echo _t('button_cancel'); ?></a>
				</div>
			</div>
		</div>

		<section class="panel">
			<nav class="navbar navbar-inverse navbar-module" role="navigation">
				<div class="navbar-header col-lg-3 col-sm-3">
					<h2>
						<img id="logo" src="view/kuler/image/icon/kuler_logo.png" />
						<?php echo _t('heading_kuler_module'); ?>
					</h2>
				</div>
			</nav>
		</section>

		<section class="panel page-content kuler-module-content">
			<div class="panel-body">
				<tabset vertical="true" main-tab="true" type="pills" id="main-tab" class="clearfix">
					<tab ng-repeat="module in modules" active="module.active" select="onSelectModule($index)">
						<tab-heading>
							<i class="fa fa-file-text-o"></i>
							{{module.mainTitle}}
							<span class="module-remover" ng-click="removeModule($index)" tooltip="<?php echo _t('button_remove', 'Remove') ?>" event-prevent-default event-stop-propagation><i class="fa fa-minus-circle"></i></span>
						</tab-heading>
						<div class="module" id="module-{{$index}}" ng-init="moduleIndex = $index">
							<?php echo renderBeginOptionContainer(); ?>
							<?php echo renderOption(array(
								'label' => _t('entry_title', 'Title'),
								'type' => 'multilingual_input',
								'name' => 'module.title',
								'inputAttrs' => 'index="{{$index}}" on-change="onTitleChanged"'
							)); ?>
							<?php echo renderOption(array(
								'label' => _t('entry_shortcode', 'Short Code'),
								'type' => 'input',
								'name' => 'module.shortcode',
								'inputAttrs' => 'disabled'
							)); ?>
							<?php echo renderOption(array(
								'label' => _t('entry_layout', 'Layout'),
								'type' => 'select',
								'name' => 'module.layout_id',
								'options' => $layouts
							)); ?>
							<?php echo renderOption(array(
								'label' => _t('entry_position', 'Position'),
								'type' => 'select',
								'name' => 'module.position',
								'options' => $positions
							)); ?>
							<?php echo renderOption(array(
								'label' => _t('entry_sort_order', 'Sort Order'),
								'type' => 'input',
								'name' => 'module.sort_order',
								'column' => 2,
								'options' => $positions
							)); ?>
							<?php echo renderOption(array(
								'label' => _t('entry_show_title', 'Show Title'),
								'type' => 'switch',
								'name' => 'module.show_title'
							)); ?>
							<?php echo renderOption(array(
								'label' => _t('entry_status', 'Status'),
								'type' => 'switch',
								'name' => 'module.status'
							)); ?>

							<fieldset>
								<legend><?php echo _t('text_general_settings', 'General Settings') ?></legend>
								<?php  echo renderOption(array(
									'label' =>_t('entry_css_class', 'CSS Class'),
									'type' => 'input',
									'name' => 'module.css_class',
									'column' => 2,
								));?>
								<?php echo renderOption(array(
									'label' => _t('entry_type', 'Type'),
									'type' => 'select',
									'name' => 'module.type',
									'options' => array(
										'tab' => 'Tab',
										'slide' => 'Slide',
//										'accordion' => 'Accordion'
									),
									'column' => 2
								)); ?>
							</fieldset>

							<?php echo renderCloseOptionContainer(); ?>
							<fieldset>
								<legend><?php echo _t('text_showcases', 'Showcases') ?></legend>
								<tabset>
									<tab ng-repeat="showcase in module.showcases" active="showcase.active">
										<tab-heading>
											{{showcase.mainTitle}}
											<span ng-click="removeShowcase(moduleIndex, $index)" tooltip="<?php echo _t('button_remove', 'Remove'); ?>"><i class="fa fa-minus-circle"></i></span>
										</tab-heading>
										<?php echo renderBeginOptionContainer() ?>
										<fieldset ng-init="showcaseIndex = $index">
											<legend><?php echo _t('text_showcase_settings', 'Showcase Settings'); ?></legend>
											<?php echo renderOption(array(
												'label' => _t('entry_showcase_title', 'Showcase Title'),
												'type' => 'multilingual_input',
												'name' => 'showcase.title',
												'inputAttrs' => 'index=\'{"moduleIndex": {{moduleIndex}}, "showcaseIndex": {{showcaseIndex}}}\' on-change="onShowcaseTitleChanged"'
											)); ?>
											<?php echo renderOption(array(
												'label' => _t('entry_show_showcase_title', 'Show Showcase Title'),
												'type' => 'switch',
												'name' => 'showcase.show_title'
											)); ?>
											<?php echo renderOption(array(
												'label' => _t('entry_status', 'Status'),
												'type' => 'switch',
												'name' => 'showcase.status'
											)); ?>
											<?php echo renderOption(array(
												'label' => _t('entry_sort_order', 'Sort Order'),
												'type' => 'input',
												'name' => 'showcase.sort_order',
												'column' => 2
											)); ?>
											<?php  echo renderOption(array(
												'label' =>_t('entry_css_class', 'CSS Class'),
												'type' => 'input',
												'name' => 'showcase.css_class',
												'column' => 2,
											));?>
										</fieldset>
										<fieldset>
											<legend><?php echo _t('text_showcase_items', 'Showcase Items'); ?></legend>
											<accordion close-others="oneAtATime" class="item-group-container">
												<accordion-group ng-repeat="item in showcase.items" is-open="item.open" class="item-group">
													<accordion-heading ng-init="itemIndex = $index">{{item.mainTitle}} <i class="fa fa-minus-circle item-remover" ng-click="removeItem(moduleIndex, showcaseIndex, itemIndex)" tooltip="<?php echo _t('text_remove_item', 'Remove Item') ?>"></i></accordion-heading>
													<?php echo renderOption(array(
														'label' => _t('entry_showcase_title', 'Item Title'),
														'type' => 'multilingual_input',
														'name' => 'item.title',
														'inputAttrs' => 'index=\'{"moduleIndex": {{moduleIndex}}, "showcaseIndex": {{showcaseIndex}}, "itemIndex": {{itemIndex}}}\' on-change="onItemTitleChanged"'
													)); ?>
													<?php echo renderOption(array(
														'label' => _t('entry_status', 'Status'),
														'type' => 'switch',
														'name' => 'item.status'
													)); ?>
													<?php echo renderOption(array(
														'label' => _t('entry_sort_order', 'Sort Order'),
														'type' => 'input',
														'name' => 'item.sort_order',
														'column' => 2
													)); ?>
													<?php  echo renderOption(array(
														'label' =>_t('entry_css_class', 'CSS Class'),
														'type' => 'input',
														'name' => 'item.css_class',
														'column' => 2,
													));?>
													<?php echo renderOption(array(
														'label' => _t('entry_item_type', 'Item Type'),
														'type' => 'select',
														'name' => 'item.type',
														'options' => array(
															'product' => _t('text_product', 'Product'),
															'html' => _t('text_html', 'HTML')
														),
														'column' => 2
													)); ?>

													<!-- HTML Type -->
													<div class="form-group" ng-if="item.type == 'html'">
														<label class="col-lg-2 col-sm-2 control-label"><?php echo _t('text_html_content', 'HTML Content') ?></label>
														<div class="col-sm-10">
															<tabset class="clearfix">
																<?php foreach ($languages as $language) { ?>
																	<tab>
																		<tab-heading>
																			<img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>"> <?php echo $language['name']; ?>
																		</tab-heading>
																		<?php echo renderEditor(array(
																			'name' => 'item.html_content[\'' . $language['code'] . '\']'
																		)) ?>
																	</tab>
																<?php } ?>
															</tabset>
														</div>
													</div>
													<!-- / HTML Type -->

													<!-- Product Type -->
													<div ng-if="item.type == 'product'">
														<?php echo renderOption(array(
															'label' => _t('entry_product_type', 'Product Type'),
															'type' => 'select',
															'name' => 'item.product_type',
															'options' => array(
																'featured' => _t('text_featured', 'Featured'),
																'latest' => _t('text_latest', 'Latest'),
																'popular' => _t('text_popular', 'Popular'),
//																'best_seller' => _t('text_best_seller', 'Best Seller'),
//																'special' => _t('text_special', 'Special')
															),
															'column' => 2
														)); ?>

														<div ng-if="item.product_type == 'featured'">
															<?php  echo renderOption(array(
																'label' =>_t('entry_featured_products', 'Featured Products'),
																'type' => 'autocomplete',
																'name' => 'item.featured_products',
																'item_type' => 'product'
															));?>
														</div>
														<div ng-if="item.product_type == 'best_seller' || item.product_type == 'latest' || item.product_type == 'special'">
															<?php echo renderOption(array(
																'label' => _t('entry_product_category', 'Product Category'),
																'type' => 'select',
																'name' => 'item.product_category',
																'options' => $category_options,
																'column' => 2
															)); ?>
															<?php echo renderOption(array(
																'label' => _t('entry_product_limit', 'Product Limit'),
																'type' => 'input',
																'name' => 'item.product_limit',
																'column' => 2
															)); ?>
														</div>

														<?php echo renderOption(array(
															'label' => _t('entry_products_per_row', 'Products Per Row'),
															'type' => 'input',
															'name' => 'item.products_per_row',
															'column' => 2
														)); ?>
														<?php echo renderOption(array(
															'label' => _t('entry_show_product_image', 'Show Product Deal Date'),
															'type' => 'switch',
															'name' => 'item.show_product_deal_date'
														)); ?>
														<?php echo renderOption(array(
															'label' => _t('entry_show_product_image', 'Show Product Image'),
															'type' => 'switch',
															'name' => 'item.show_product_image'
														)); ?>
														<?php echo renderOption(array(
															'label' => _t('entry_show_product_name', 'Show Product Name'),
															'type' => 'switch',
															'name' => 'item.show_product_name'
														)); ?>
														<?php echo renderOption(array(
															'label' => _t('entry_show_product_description', 'Show Product Description'),
															'type' => 'switch',
															'name' => 'item.show_product_description'
														)); ?>
														<?php echo renderOption(array(
															'label' => _t('entry_show_product_price', 'Show Product Price'),
															'type' => 'switch',
															'name' => 'item.show_product_price'
														)); ?>
														<?php echo renderOption(array(
															'label' => _t('entry_show_product_rating', 'Show Product Rating'),
															'type' => 'switch',
															'name' => 'item.show_product_rating'
														)); ?>
														<?php echo renderOption(array(
															'label' => _t('entry_show_add_to_cart_button', 'Show Add to Cart Button'),
															'type' => 'switch',
															'name' => 'item.show_add_to_cart_button'
														)); ?>
														<?php echo renderOption(array(
															'label' => _t('entry_show_wish_list_button', 'Show Wish List Button'),
															'type' => 'switch',
															'name' => 'item.show_wish_list_button'
														)); ?>
														<?php echo renderOption(array(
															'label' => _t('entry_show_compare_button', 'Show Compare Button'),
															'type' => 'switch',
															'name' => 'item.show_compare_button'
														)); ?>
														<?php echo renderOption(array(
															'label' => _t('entry_product_image_size', 'Product Image Size'),
															'type' => 'html',
															'html' => renderInput(array(
																	'name' => 'item.product_image_width',
																	'column' => 1
																)) . renderInput(array(
																	'name' => 'item.product_image_height',
																	'column' => 1
																)),
															'rowAttrs' => array('ng-if="item.show_product_image"')
														)); ?>
														<?php echo renderOption(array(
															'label' => _t('entry_product_description_limit', 'Product Description Limit'),
															'type' => 'input',
															'name' => 'item.product_description_limit',
															'column' => 2,
															'rowAttrs' => array('ng-if="item.show_product_description"')
														)); ?>
													</div>
													<!-- / Product Type -->
												</accordion-group>
												<accordion-group is-disabled="true">
													<accordion-heading><div ng-click="addItem(moduleIndex, showcaseIndex)"><i class="fa fa-plus-circle"></i> <?php echo _t('text_add_item', 'Add Item') ?></div></accordion-heading>
												</accordion-group>
											</accordion>
										</fieldset>
										<?php echo renderCloseOptionContainer() ?>
									</tab>
									<tab select="addShowcase(moduleIndex)">
										<tab-heading>
											<span><i class="fa fa-plus-circle"></i> <?php echo _t('button_add_showcase', 'Add Showcase') ?></span>
										</tab-heading>
									</tab>
								</tabset>
							</fieldset>
						</div>
					</tab>
				</tabset>
			</div>
		</section>
	</div>
</div>
<div id="kuler-loader" ng-if="loading"></div>
</section>
<script>
	var Kuler = {
		store_id: <?php echo $store_id ?>,
		actionUrl: <?php echo json_encode($action_url); ?>,
		cancelUrl: <?php echo json_encode($cancel_url); ?>,
		front_base: <?php echo json_encode($front_base); ?>,
		storeUrl: <?php echo json_encode($store_url); ?>,
		token: <?php echo json_encode($token); ?>,
		extensionCode: <?php echo json_encode($extension_code); ?>,
		modules: <?php echo json_encode($modules); ?>,
		languages: <?php echo json_encode($languages); ?>,
		configLanguage: <?php echo json_encode($config_language); ?>,
		messages: <?php echo json_encode($messages); ?>,
		defaultModule: <?php echo json_encode($default_module); ?>
	};
</script>
<?php echo $footer; ?>