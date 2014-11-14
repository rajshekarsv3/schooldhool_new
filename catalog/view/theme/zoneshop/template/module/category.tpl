<?php require_once(DIR_APPLICATION . 'controller/module/kuler_cp.php'); ?>
<?php $kuler = Kuler::getInstance(); $category_menu_type = $kuler->getSkinOption('category_menu_type'); ?>
<div class="box categories">
  <div class="box-heading"><span><?php echo $heading_title; ?></span></div>
  <div class="box-content">
    <ul class="box-category">
      <?php foreach ($categories as $category) { ?>
      <li<?php if ($category['category_id'] == $category_id) { ?> class="active"<?php } ?>>
        <a href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a>
	    <?php if ($category_menu_type == 'accordion' && $category['children']) { ?>
	    <span class="toggle"></span>
	    <?php } ?>
        <?php if ($category['children']) { ?>
        <ul>
          <?php foreach ($category['children'] as $child) { ?>
          <li>
            <?php if ($child['category_id'] == $child_id) { ?>
            <a href="<?php echo $child['href']; ?>" class="active"> - <?php echo $child['name']; ?></a>
            <?php } else { ?>
            <a href="<?php echo $child['href']; ?>"> - <?php echo $child['name']; ?></a>
            <?php } ?>
          </li>
          <?php } ?>
        </ul>
        <?php } ?>
      </li>
      <?php } ?>
    </ul>
  </div>
</div>
