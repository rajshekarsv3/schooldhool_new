<?php
$search_in_category = $kuler->getSkinOption('search_in_specific_category');
$live_search_data = $kuler->getLiveSearchData();
$category_id = $live_search_data['category_id'];

$kuler->addScript('catalog/view/theme/' . $kuler->getTheme() . '/js/live_search.js', true);
?>

<div id="search" class="live-search-container">
	<div id="search-inner">
		<input class="<?php echo $search_in_category ? 'category' : 'no-category' ?> kf_search" type="text" name="search" placeholder="<?php echo $kuler->translate($kuler->getSkinOption('search_field_text')); ?>" />
		<div class="button-search"></div>
	</div>
	<?php if ($search_in_category) { ?>
		<select name="category_id" class="kf_category">
			<option value="0"><?php echo $kuler->translate($kuler->getSkinOption('select_category_text')); ?></option>
			<?php foreach ($live_search_data['categories'] as $category_1) { ?>
				<?php if ($category_1['category_id'] == $category_id) { ?>
					<option value="<?php echo $category_1['category_id']; ?>" selected="selected"><?php echo $category_1['name']; ?></option>
				<?php } else { ?>
					<option value="<?php echo $category_1['category_id']; ?>"><?php echo $category_1['name']; ?></option>
				<?php } ?>
				<?php foreach ($category_1['children'] as $category_2) { ?>
					<?php if ($category_2['category_id'] == $category_id) { ?>
						<option value="<?php echo $category_2['category_id']; ?>" selected="selected">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_2['name']; ?></option>
					<?php } else { ?>
						<option value="<?php echo $category_2['category_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_2['name']; ?></option>
					<?php } ?>
					<?php foreach ($category_2['children'] as $category_3) { ?>
						<?php if ($category_3['category_id'] == $category_id) { ?>
							<option value="<?php echo $category_3['category_id']; ?>" selected="selected">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_3['name']; ?></option>
						<?php } else { ?>
							<option value="<?php echo $category_3['category_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_3['name']; ?></option>
						<?php } ?>
					<?php } ?>
				<?php } ?>
			<?php } ?>
		</select>
	<?php } ?>
	<?php if ($kuler->getSkinOption('search_in_specific_manufacturer')) { ?>
		<select name="manufacturer_id" class="kf_manufacturer">
			<option value="0"><?php echo $kuler->translate($kuler->getSkinOption('select_manufacturer_text')); ?></option>
			<?php foreach ($live_search_data['manufacturers'] as $manufacturer_id => $manufacturer_name) { ?>
				<option value="<?php echo $manufacturer_id; ?>"><?php echo $manufacturer_name; ?></option>
			<?php } ?>
		</select>
	<?php } ?>
</div>
<script>
	Kuler.text_load_more = <?php echo json_encode($live_search_data['text_load_more']); ?>;
	Kuler.text_no_results = <?php echo json_encode($live_search_data['text_no_results']); ?>;
</script>