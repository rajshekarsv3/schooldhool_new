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
        <h2><?php echo $text_my_account; ?></h2>
        <div class="content">
          <ul>
            <li><a href="<?php echo $edit; ?>"><?php echo $text_edit; ?></a></li>
            <li><a href="<?php echo $password; ?>"><?php echo $text_password; ?></a></li>
            <li><a href="<?php echo $payment; ?>"><?php echo $text_payment; ?></a></li>
          </ul>
        </div>
        <h2><?php echo $text_my_tracking; ?></h2>
        <div class="content">
          <ul>
            <li><a href="<?php echo $tracking; ?>"><?php echo $text_tracking; ?></a></li>
          </ul>
        </div>
        <h2><?php echo $text_my_transactions; ?></h2>
        <div class="content">
          <ul>
            <li><a href="<?php echo $transaction; ?>"><?php echo $text_transaction; ?></a></li>
          </ul>
        </div>
        <?php echo $content_bottom; ?></div>
      <?php echo $column_right; ?>
    </div>
  </div>

<?php echo $footer; ?>