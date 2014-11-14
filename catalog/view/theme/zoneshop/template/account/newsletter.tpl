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
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
          <div class="content">
            <table class="form">
              <tr>
                <td><?php echo $entry_newsletter; ?></td>
                <td><?php if ($newsletter) { ?>
                    <input type="radio" name="newsletter" value="1" checked="checked" />
                    <?php echo $text_yes; ?>&nbsp;
                    <input type="radio" name="newsletter" value="0" />
                    <?php echo $text_no; ?>
                  <?php } else { ?>
                    <input type="radio" name="newsletter" value="1" />
                    <?php echo $text_yes; ?>&nbsp;
                    <input type="radio" name="newsletter" value="0" checked="checked" />
                    <?php echo $text_no; ?>
                  <?php } ?></td>
              </tr>
            </table>
          </div>
          <div class="buttons">
            <div class="left"><a href="<?php echo $back; ?>" class="button"><?php echo $button_back; ?></a></div>
            <div class="right"><input type="submit" value="<?php echo $button_continue; ?>" class="button" /></div>
          </div>
        </form>
        <?php echo $content_bottom; ?></div>
      <?php echo $column_right; ?>
    </div>
  </div>


<?php echo $footer; ?>