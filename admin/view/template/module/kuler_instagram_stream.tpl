<?php include_once(DIR_TEMPLATE . 'module/kuler_helper.tpl'); ?>
<?php echo $header; ?>
<section id="main-content" class="kuler-module" ng-app="kulerModule" ng-controller="InstagramCtrl">
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
									'label' => _t('entry_short_code', 'Short Code'),
									'type' => 'input',
									'name' => 'module.short_code',
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
                                    <legend><?php echo _t('text_general_settings', 'Instagram Stream') ?></legend>
                                <?php  echo renderOption(array(
                                  'label' => _t('text_username', 'Username'),
                                  'type' => 'input',
                                  'name' => 'module.username',
                                  'column' => 2,
                                  'hint' =>_t('text_hint_username','Input the username whose feed you want displayed.'),
                                  'hint_out'=>true
                                )); ?>

                                <?php  echo renderOption(array(
                                  'label' => _t('entry_thumbsize', 'Thumbnails Size'),
                                  'type' => 'input',
                                  'name' => 'module.thumbsize',
                                  'column' => 2,
                                  'hint' =>_t('text_hint_thumbsize','This is the size of each photo display. (20 to 250 pixels)'),
                                  'options' => $positions,
                                  'hint_out' =>true
                                )); ?>

                                  <?php echo renderOption(array(
                                  'label' => _t('entry_entry_grid', 'Grid'),
                                  'type' => 'html',
                                  'html' => renderSelect(array(
                                          'format' => 'select',
                                          'column' => 2,
                                          'name' => 'module.horizontal',
                                          'options' => $grid_options
                                      )) . renderSelect(array(
                                          'type' => 'select',
                                          'name' => 'module.vertical',
                                          'column' => 2,
                                          'options' => $grid_options,
                                      )),
                                )); ?>
                                </fieldset>
                                  <?php echo renderCloseOptionContainer(); ?>
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