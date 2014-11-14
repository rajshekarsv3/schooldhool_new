<?php if (count($languages) > 1) { ?>
  <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="language">
    <div class="btn-group">
      <button type="button" class="dropdown-toggle">
        <?php foreach ($languages as $language) { ?>
          <?php if ($language['code'] == $language_code) { ?>
            <?php $language_name = $language['name']; ?>
            <img src="image/flags/<?php echo $language['image']; ?>" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>">
          <?php } ?>
        <?php } ?>
        <span><?php echo $language_name; ?></span> <i class="fa fa-caret-down"></i></button>
      <ul class="dropdown-menu">
        <?php foreach ($languages as $language) { ?>
          <li><a href="<?php echo $language['code']; ?>"><img src="image/flags/<?php echo $language['image']; ?>" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
    <input type="hidden" name="language_code" value="" />
    <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
  </form>
<?php } ?>
