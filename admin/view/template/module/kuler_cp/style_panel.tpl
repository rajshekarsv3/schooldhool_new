<?php include_once('functions.tpl'); ?>
<?php $row = 0; ?>
<?php foreach ($group_styles as $group_style_key => $group_style) { ?>
	<div class="panel panel-default" ng-init="style_groups.<?php echo $group_style_key ?> = <?php echo $row ? 'false' : 'true'; ?>;" ng-class="{active: style_groups.<?php echo $group_style_key; ?>}">
		<div class="panel-heading" ng-click="toggleGroupStyle('<?php echo $group_style_key ?>')">
			<h4 class="panel-title">
				<a><?php echo $group_style['label']; ?></a>
				<span class="panel-toggle fa" ng-class="{'fa-caret-up': !style_groups.<?php echo $group_style_key; ?>, 'fa-caret-down': style_groups.<?php echo $group_style_key; ?>}"></span>
			</h4>
		</div>
		<div class="panel-collapse" collapse="!style_groups.<?php echo $group_style_key; ?>">
			<div class="panel-body">
				<div class="form-horizontal">
					<?php if ($group_style['items']) { ?>
					<?php foreach ($group_style['items'] as $style_key => $style) { ?>
					<?php $style['name'] = $style_key; ?>
					<?php echo renderStyleOption($style); ?>
					<?php } ?>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
	<?php $row++; ?>
<?php } ?>