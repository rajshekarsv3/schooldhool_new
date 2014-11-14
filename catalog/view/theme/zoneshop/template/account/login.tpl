<?php echo $header; ?>
<div class="container">
  <?php if ($success) { ?>
    <div class="success"><?php echo $success; ?></div>
  <?php } ?>
  <?php if ($error_warning) { ?>
    <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
</div>
<div class="container login_page">
	<div class="breadcrumb">
		<div class="container">
			<div class="row">
				<div class="col-md-12 hidden-xs">
					<?php foreach ($breadcrumbs as $breadcrumb) { ?>
						<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
  <div class="row">
    <div id="content" class="col-sm-12">
    <div class="login-content">
      <div class="row">
	      <div class="col-md-12">
		      <h2><?php echo $heading_title; ?></h2>
	      </div>
        <div class="col-md-6 left">
          <h3><?php echo $text_new_customer; ?></h3>
          <div class="content">
            <p><?php echo $text_register; ?></p>
            <p><?php echo $text_register_account; ?></p>
            <a href="<?php echo $register; ?>" class="button"><?php echo $button_continue; ?></a></div>
        </div>
        <div class="col-md-6 right">
          <h3><?php echo $text_returning_customer; ?></h3>
          <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
            <div class="content">
              <p><?php echo $text_i_am_returning_customer; ?></p>
              <div class="row">
                <div class="col-md-3">
                  <?php echo $entry_email; ?>
                </div>
                <div class="col-md-9">
                  <input type="text" name="email" value="<?php echo $email; ?>" />
                </div>
              </div>
              <div class="row">
                <div class="col-md-3 col-xs-12">
                  <?php echo $entry_password; ?>
                </div>
                <div class="col-md-9 col-xs-12">
                  <input type="password" name="password" value="<?php echo $password; ?>" />
                </div>
              </div>
              <div>
                <a href="<?php echo $forgotten; ?>"><?php echo $text_forgotten; ?></a>
                <input type="submit" value="<?php echo $button_login; ?>" class="button" />
                <?php if ($redirect) { ?>
                  <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
                <?php } ?>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
    <?php echo $content_bottom; ?>
  </div>
  </div>
</div>
<script type="text/javascript"><!--
$('#login input').keydown(function(e) {
	if (e.keyCode == 13) {
		$('#login').submit();
	}
});
//--></script> 
<?php echo $footer; ?>