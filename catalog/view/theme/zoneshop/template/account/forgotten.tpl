<?php echo $header; ?>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
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
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
          <p><?php echo $text_email; ?></p>
          <h2><?php echo $text_your_email; ?></h2>
          <div class="content">
            <table class="form">
              <tr>
                <td><?php echo $entry_email; ?></td>
                <td><input type="text" name="email" value="" /></td>
              </tr>
            </table>
          </div>
          <div class="buttons">
            <div class="left"><a href="<?php echo $back; ?>" class="button"><?php echo $button_back; ?></a></div>
            <div class="right">
              <input type="submit" value="<?php echo $button_continue; ?>" class="button" />
            </div>
          </div>
        </form>
        <?php echo $content_bottom; ?></div>
      <?php echo $column_right; ?>
    </div>
  </div>

<?php echo $footer; ?>