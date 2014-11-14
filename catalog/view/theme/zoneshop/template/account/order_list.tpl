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
        <?php if ($orders) { ?>
          <?php foreach ($orders as $order) { ?>
            <div class="order-list">
              <div class="order-id"><b><?php echo $text_order_id; ?></b> #<?php echo $order['order_id']; ?></div>
              <div class="order-status"><b><?php echo $text_status; ?></b> <?php echo $order['status']; ?></div>
              <div class="order-content">
                <div><b><?php echo $text_date_added; ?></b> <?php echo $order['date_added']; ?><br />
                  <b><?php echo $text_products; ?></b> <?php echo $order['products']; ?></div>
                <div><b><?php echo $text_customer; ?></b> <?php echo $order['name']; ?><br />
                  <b><?php echo $text_total; ?></b> <?php echo $order['total']; ?></div>
                <div class="order-info"><a href="<?php echo $order['href']; ?>"><img src="catalog/view/theme/default/image/info.png" alt="<?php echo $button_view; ?>" title="<?php echo $button_view; ?>" /></a>&nbsp;&nbsp;<a href="<?php echo $order['reorder']; ?>"><img src="catalog/view/theme/default/image/reorder.png" alt="<?php echo $button_reorder; ?>" title="<?php echo $button_reorder; ?>" /></a></div>
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