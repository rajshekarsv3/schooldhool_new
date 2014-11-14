<?php echo $header; ?>
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
      <div id="content" class="<?php echo $class; ?>">
        <?php echo $content_top; ?>
	      <div class="breadcrumb">
		      <div class="container">
			      <div class="row">
				      <div class="col-md-12">
					      <ul>
						      <?php foreach ($breadcrumbs as $breadcrumb) { ?>
							      <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
						      <?php } ?>
					      </ul>
				      </div>
			      </div>
		      </div>
	      </div><!--/.breadcrumb-->
        <h1><?php echo $heading_title; ?></h1>
        <?php echo $description; ?>
        <div class="buttons">
          <div class="right"><a href="<?php echo $continue; ?>" class="button"><?php echo $button_continue; ?></a></div>
        </div>
        <?php echo $content_bottom; ?></div>
      <?php echo $column_right; ?>
    </div>
  </div>

<?php echo $footer; ?>