<?php
$kuler = Kuler::getInstance();
?>
<?php echo $header; ?>
<div id="content">
  <div class="container">
	  <div class="row">
		  <?php echo $column_left; ?>
		  <?php if ($column_left && $column_right) { ?>
			  <?php $class = 'col-md-6'; ?>
		  <?php } elseif ($column_left || $column_right) { ?>
			  <?php $class = 'col-md-9'; ?>
		  <?php } else { ?>
			  <?php $class = 'col-md-12'; ?>
		  <?php } ?>
		  <div class="<?php echo $class; ?>">
			  <?php echo $content_top; ?>
			  <?php echo $content_bottom; ?>
	    </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
