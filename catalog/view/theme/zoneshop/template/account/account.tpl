<?php echo $header; ?>
<?php if ($success) { ?>
<div class="success"><?php echo $success; ?></div>
<?php } ?>
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
<?php echo $column_left; ?>
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
      <div class="content">
        <h1><?php echo $text_my_account; ?></h1>
        <ul>
          <li><a href="<?php echo $edit; ?>"><?php echo $text_edit; ?></a></li>
          <li><a href="<?php echo $password; ?>"><?php echo $text_password; ?></a></li>
          <li><a href="<?php echo $address; ?>"><?php echo $text_address; ?></a></li>
          <li><a href="<?php echo $wishlist; ?>"><?php echo $text_wishlist; ?></a></li>
        </ul>
      </div>
      <h2><?php echo $text_my_orders; ?></h2>
      <div class="content">
        <ul>
          <li><a href="<?php echo $order; ?>"><?php echo $text_order; ?></a></li>
          <li><a href="<?php echo $download; ?>"><?php echo $text_download; ?></a></li>
          <?php if ($reward) { ?>
            <li><a href="<?php echo $reward; ?>"><?php echo $text_reward; ?></a></li>
          <?php } ?>
          <li><a href="<?php echo $return; ?>"><?php echo $text_return; ?></a></li>
          <li><a href="<?php echo $transaction; ?>"><?php echo $text_transaction; ?></a></li>
        </ul>
      </div>
      <h2><?php echo $text_my_newsletter; ?></h2>
      <div class="content">
        <ul>
          <li><a href="<?php echo $newsletter; ?>"><?php echo $text_newsletter; ?></a></li>
        </ul>
      </div>
      <?php echo $content_bottom; ?>
    </div>
    <?php echo $column_right; ?>
  </div>
</div>

<?php echo $footer; ?> 