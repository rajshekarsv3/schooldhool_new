<?php require_once(DIR_APPLICATION . 'controller/module/kuler_cp.php'); ?>
<?php if ($modules) { ?>
  <div id="column-left" class="sidebar col-md-3 col-sm-3 col-xs-12 hidden-xs hidden-sm">
    <?php foreach ($modules as $module) { ?>
      <?php echo $module; ?>
    <?php } ?>
  </div>
<?php } ?>
