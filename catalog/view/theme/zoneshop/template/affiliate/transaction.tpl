<?php echo $header; ?>
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
        <div class="breadcrumb">
          <p><?php echo $text_balance; ?><b> <?php echo $balance; ?></b>.</p>
          <table class="list">
            <thead>
            <tr>
              <td class="left"><?php echo $column_date_added; ?></td>
              <td class="left"><?php echo $column_description; ?></td>
              <td class="right"><?php echo $column_amount; ?></td>
            </tr>
            </thead>
            <tbody>
            <?php if ($transactions) { ?>
              <?php foreach ($transactions  as $transaction) { ?>
                <tr>
                  <td class="left"><?php echo $transaction['date_added']; ?></td>
                  <td class="left"><?php echo $transaction['description']; ?></td>
                  <td class="right"><?php echo $transaction['amount']; ?></td>
                </tr>
              <?php } ?>
            <?php } else { ?>
              <tr>
                <td class="center" colspan="5"><?php echo $text_empty; ?></td>
              </tr>
            <?php } ?>
            </tbody>
          </table>
          <div class="pagination"><?php echo $pagination; ?></div>
          <div class="buttons">
            <div class="right"><a href="<?php echo $continue; ?>" class="button"><?php echo $button_continue; ?></a></div>
          </div>
          <?php echo $content_bottom; ?></div>
      <?php echo $column_right; ?>
    </div>
  </div>

<?php echo $footer; ?>