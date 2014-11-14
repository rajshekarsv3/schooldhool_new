<?php echo $header; ?>
  <div class="container">
	  <div class="breadcrumb">
		  <div class="container">
			  <div class="row">
				  <div class="col-md-8">
					  <h2><?php echo $heading_title; ?></h2>
				  </div>
				  <div class="col-md-4">
					  <ul>
						  <?php foreach ($breadcrumbs as $breadcrumb) { ?>
							  <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
						  <?php } ?>
					  </ul>
				  </div>
			  </div>
		  </div>
	  </div><!--/.breadcrumb-->
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
        <?php if ($categories) { ?>
          <p><b><?php echo $text_index; ?></b>
            <?php foreach ($categories as $category) { ?>
              &nbsp;&nbsp;&nbsp;<a href="index.php?route=product/manufacturer#<?php echo $category['name']; ?>"><b><?php echo $category['name']; ?></b></a>
            <?php } ?>
          </p>
          <?php foreach ($categories as $category) { ?>
            <div class="manufacturer-list">
              <div class="manufacturer-heading"><?php echo $category['name']; ?><a id="<?php echo $category['name']; ?>"></a></div>
              <div class="manufacturer-content">
                <?php if ($category['manufacturer']) { ?>
                  <?php for ($i = 0; $i < count($category['manufacturer']);) { ?>
                    <ul>
                      <?php $j = $i + ceil(count($category['manufacturer']) / 4); ?>
                      <?php for (; $i < $j; $i++) { ?>
                        <?php if (isset($category['manufacturer'][$i])) { ?>
                          <li><a href="<?php echo $category['manufacturer'][$i]['href']; ?>"><?php echo $category['manufacturer'][$i]['name']; ?></a></li>
                        <?php } ?>
                      <?php } ?>
                    </ul>
                  <?php } ?>
                <?php } ?>
              </div>
            </div>
          <?php } ?>
        <?php } else { ?>
          <div class="content"><?php echo $text_empty; ?></div>
          <div class="buttons">
            <div class="right"><a href="<?php echo $continue; ?>" class="button"><?php echo $button_continue; ?></a></div>
          </div>
        <?php } ?>
        <?php echo $content_bottom; ?></div>
      <?php echo $column_right; ?>
    </div>
  </div>

<?php echo $footer; ?>