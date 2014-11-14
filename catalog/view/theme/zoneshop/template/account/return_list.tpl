<?php echo $header; ?>
  <div class="breadcrumb">
    <div class="container">
      <div class="row">
        <div class="col-md-3">
          <h2><?php echo $heading_title; ?></h2>
        </div>
        <div class="col-md-9">
          <ul>
            <?php foreach ($breadcrumbs as $breadcrumb) { ?>
              <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
            <?php } ?>
          </ul>
        </div>
      </div>
    </div>
  </div><!--/.breadcrumb-->


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
        <?php if ($returns) { ?>
          <?php foreach ($returns as $return) { ?>
            <div class="return-list">
              <div class="return-id"><b><?php echo $text_return_id; ?></b> #<?php echo $return['return_id']; ?></div>
              <div class="return-status"><b><?php echo $text_status; ?></b> <?php echo $return['status']; ?></div>
              <div class="return-content">
                <div><b><?php echo $text_date_added; ?></b> <?php echo $return['date_added']; ?><br />
                  <b><?php echo $text_order_id; ?></b> <?php echo $return['order_id']; ?></div>
                <div><b><?php echo $text_customer; ?></b> <?php echo $return['name']; ?></div>
                <div class="return-info"><a href="<?php echo $return['href']; ?>"><img src="catalog/view/theme/default/image/info.png" alt="<?php echo $button_view; ?>" title="<?php echo $button_view; ?>" /></a></div>
              </div>
            </div>
          <?php } ?>
          <div class="pagination"><?php echo $pagination; ?></div>
        <?php } else { ?>
          <div class="content"><?php echo $text_empty; ?></div>
        <?php } ?>
        <div class="buttons">
          <div class="right"><a href="<?php echo $continue; ?>" class="button"><?php echo $button_continue; ?></a></div>
        </div>
        <?php echo $content_bottom; ?></div>
      <?php echo $column_right; ?>
    </div>
  </div>

<?php echo $footer; ?>