<?php $kuler = Kuler::getInstance(); ?>
<?php
$modules = Kuler::getInstance()->getModules('footer_top');
if ($modules) {
  echo '<div class="footer-top"><div class="container">'. implode('', $modules).'</div></div>';
}
?>
<div id="footer">
	<div class="container">
		<div class="row">
			<div class="col-lg-20 col-sm-6 contact">
				<!-- Contact -->
				<?php if ($kuler->getSkinOption('show_contact')) { ?>
					<?php if ($kuler->getSkinOption('show_contact_title')) { ?>
						<h3 class="box-heading"><span><?php echo $kuler->translate($kuler->getSkinOption('contact_title')); ?></span></h3>
					<?php } ?>
					<ul>
						<?php if (($skype1 = $kuler->getSkinOption('skype_1')) || ($skype2 = $kuler->getSkinOption('skype_2'))) { ?>
							<li class="skype">
								<?php if ($skype1) { ?>
									<span><?php echo $skype1; ?></span>
								<?php } ?>
								<?php if ($skype2 = $kuler->getSkinOption('skype_2') && $skype2) { ?>
									<span><?php echo $skype2; ?></span>
								<?php } ?>
							</li>
						<?php } ?>

						<?php if (($email1 = $kuler->getSkinOption('email_1')) || ($email2 = $kuler->getSkinOption('email_2'))) { ?>
							<li class="email">
								<?php if ($email1) { ?>
									<span><?php echo $email1; ?></span>
								<?php } ?>
								<?php if ($email2 = $kuler->getSkinOption('email_2') && $email2) { ?>
									<span><?php echo $email2; ?></span>
								<?php } ?>
							</li>
						<?php } ?>

						<?php if (($mobile1 = $kuler->getSkinOption('mobile_1')) || ($mobile2 = $kuler->getSkinOption('mobile_2'))) { ?>
							<li class="mobile">
								<?php if ($mobile1) { ?>
									<span><?php echo $mobile1; ?></span>
								<?php } ?>
								<?php if ($mobile2 = $kuler->getSkinOption('mobile_2') && $mobile2) { ?>
									<span><?php echo $mobile2; ?></span>
								<?php } ?>
							</li>
						<?php } ?>

						<?php if (($phone1 = $kuler->getSkinOption('phone_1')) || ($phone2 = $kuler->getSkinOption('phone_2'))) { ?>
							<li class="phone">
								<?php if ($phone1) { ?>
									<span><?php echo $phone1; ?></span>
								<?php } ?>
								<?php if ($phone2 = $kuler->getSkinOption('phone_2') && $phone2) { ?>
									<span><?php echo $phone2; ?></span>
								<?php } ?>
							</li>
						<?php } ?>

						<?php if (($fax1 = $kuler->getSkinOption('fax_1')) || ($fax2 = $kuler->getSkinOption('fax_2'))) { ?>
							<li class="fax">
								<?php if ($fax1) { ?>
									<span><?php echo $fax1; ?></span>
								<?php } ?>
								<?php if ($fax2 = $kuler->getSkinOption('fax_2') && $fax2) { ?>
									<span><?php echo $fax2; ?></span>
								<?php } ?>
							</li>
						<?php } ?>
					</ul>
				<?php } ?>
			</div>
			<?php if ($informations) { ?>
				<div class="col-lg-20 col-sm-6 col-xs-12">
					<h3 class="box-heading"><span><?php echo $text_information; ?></span></h3>
					<ul>
						<?php foreach ($informations as $information) { ?>
							<li><a href="<?php echo $information['href']; ?>"><?php echo $information['title']; ?></a></li>
						<?php } ?>
					</ul>
				</div>
			<?php } ?>
			<div class="col-lg-20 col-sm-6 col-xs-12">
				<h3 class="box-heading"><span><?php echo $text_service; ?></span></h3>
				<ul>
					<li><a href="<?php echo $contact; ?>"><?php echo $text_contact; ?></a></li>
					<li><a href="<?php echo $return; ?>"><?php echo $text_return; ?></a></li>
					<li><a href="<?php echo $sitemap; ?>"><?php echo $text_sitemap; ?></a></li>
				</ul>
			</div>
			<div class="col-lg-20 col-sm-6 col-xs-12">
				<h3 class="box-heading"><span><?php echo $text_extra; ?></span></h3>
				<ul>
					<li><a href="<?php echo $manufacturer; ?>"><?php echo $text_manufacturer; ?></a></li>
					<li><a href="<?php echo $voucher; ?>"><?php echo $text_voucher; ?></a></li>
					<li><a href="<?php echo $affiliate; ?>"><?php echo $text_affiliate; ?></a></li>
					<li><a href="<?php echo $special; ?>"><?php echo $text_special; ?></a></li>
				</ul>
			</div>
			<div class="col-lg-20 col-sm-6 col-xs-12">
				<h3 class="box-heading"><span><?php echo $text_account; ?></span></h3>
				<ul>
					<li><a href="<?php echo $account; ?>"><?php echo $text_account; ?></a></li>
					<li><a href="<?php echo $order; ?>"><?php echo $text_order; ?></a></li>
					<li><a href="<?php echo $wishlist; ?>"><?php echo $text_wishlist; ?></a></li>
					<li><a href="<?php echo $newsletter; ?>"><?php echo $text_newsletter; ?></a></li>
				</ul>
			</div>
		</div>
	</div><!--/.container-->
</div><!--/.footer-->
<?php if ($kuler->getSkinOption('show_social_icons') || $kuler->getSkinOption('show_newsletter')) { ?>
  <div class="social-newsletter">
    <div class="container">
      <div class="row">
        <?php if ($kuler->getSkinOption('show_social_icons')) { ?>
          <?php if ($kuler->getSkinOption('show_social_icons') && $kuler->getSkinOption('show_newsletter')) { ?>
            <?php $class = 'col-md-5'; ?>
          <?php } else { ?>
            <?php $class = 'col-md-12 non-newsletter'; ?>
          <?php } ?>
          <div class="<?php echo $class; ?> social">
            <?php if ($kuler->getSkinOption('show_social_icons_title')) { ?>
              <h3><span><?php echo $kuler->translate($kuler->getSkinOption('social_icon_title')); ?></span></h3>
            <?php } ?>
            <?php if ($social_icons = $kuler->getSocialIcons()) { ?>
              <ul class="icon-style-<?php echo $kuler->getSkinOption('icon_style') ?> icon-size-<?php echo $kuler->getSkinOption('icon_size'); ?>">
                <?php foreach ($social_icons as $social_icon) { ?>
                  <li><a href="<?php echo $social_icon['link']; ?>" target="_blank" class="<?php echo $social_icon['class']; ?>"></a></li>
                <?php } ?>
              </ul>
            <?php } ?>
          </div><!--/social icons-->
        <?php } ?>
        <?php if ($kuler->getSkinOption('show_newsletter')) { ?>
          <div class="col-md-7 col-xs-12 newsletter">
            <?php if ($kuler->getSkinOption('show_newsletter_title')) { ?>
              <h3><span><?php echo $kuler->translate($kuler->getSkinOption('newsletter_title')); ?></span></h3>
            <?php } ?>
            <form id="newsletter-form">
              <?php echo $kuler->translate($kuler->getSkinOption('newsletter_description_text')); ?>
              <input type="email" id="newsletter-mail"
                     placeholder="<?php echo $kuler->translate($kuler->getSkinOption('newsletter_input_text')); ?>"/>
              <button
                id="newsletter-submit"><?php echo $kuler->translate($kuler->getSkinOption('newsletter_button_text')); ?></button>
            </form>
            <script>
              Kuler.show_newsletter = <?php echo json_encode($kuler->getSkinOption('show_newsletter')); ?>;
              Kuler.newsletter_subscribe_link = <?php echo json_encode($kuler->getNewsletterSubscribeLink()); ?>;
            </script>
          </div><!--/newsletter-->
        <?php } ?>
      </div>
    </div>
  </div>
<?php } ?>
<div id="powered">
	<div class="container">
    <div class="row">
      <div class="col-lg-4 col-md-6 col-sm-5 col-xs-12 copyright">
        <?php if ($kuler->getSkinOption('show_custom_copyright')) { ?>
          <?php echo $kuler->translate($kuler->getSkinOption('custom_copyright')); ?>
        <?php } else { ?>
          <?php echo $powered; ?>
        <?php } ?>
      </div>
      <?php if ($kuler->getSkinOption('show_payment_icons') && $payment_icons = $kuler->getPaymentIcons()) { ?>
        <div class="col-lg-8 col-md-6 col-sm-7 col-xs-12 payment">
          <ul>
            <?php foreach ($payment_icons as $payment_icon) { ?>
              <li><a href="<?php echo $payment_icon['link']; ?>"<?php if ($payment_icon['new_tab']) echo ' target="_blank"'; ?> title="<?php echo $kuler->translate($payment_icon['name']); ?>"><img src="<?php echo $payment_icon['thumb']; ?>" /></a></li>
            <?php } ?>
          </ul>
        </div>
      <?php } ?>
    </div>
	</div>
</div>
<?php
$modules = Kuler::getInstance()->getModules('footer_bottom');
if ($modules) {
  echo implode('', $modules);
}
?>
</div>
<?php if ($kuler->getSkinOption('login_popup')) { ?>
<?php $kuler->loginPopupInit($data); ?>
<div style="display: none">
	<div id="login-popup">
		<div class="col-sm-6 left">
			<h2><?php echo _t('text_new_customer'); ?></h2>
			<div class="content">
				<p><b><?php echo _t('text_register'); ?></b></p>
				<p><?php echo _t('text_register_account'); ?></p>
				<a href="<?php echo $data['register_url']; ?>" class="button"><?php echo _t('button_continue'); ?></a></div>
		</div>
		<div class="col-sm-6 right">
			<h2><?php echo _t('text_returning_customer'); ?></h2>
			<form id="popup-login-form">
				<div class="content">
					<p><?php echo _t('text_i_am_returning_customer'); ?></p>
					<b><?php echo _t('entry_email'); ?></b><br />
					<input type="text" name="email" />
					<br />
					<br />
					<b><?php echo _t('entry_password'); ?></b><br />
					<input type="password" name="password" />
					<br />
					<a href="<?php echo $data['forgotten_url']; ?>"><?php echo _t('text_forgotten'); ?></a><br />
					<br />
					<input type="submit" value="<?php echo _t('button_login'); ?>" class="button" />
				</div>
			</form>
		</div>
	</div>
</div>
<?php } ?>
<?php if ($kuler->getSkinOption('enable_scroll_up')) { ?>
<a class="scrollup"><?php echo $kuler->translate($kuler->getSkinOption('scroll_up_text')); ?></a>
<?php } ?>
<!-- {BODY_SCRIPTS} -->
<!-- Theme Version: <?php echo $kuler->getThemeVersion(); ?> | Kuler Version: <?php echo $kuler->getKulerVersion(); ?> | Skin: <?php echo $kuler->getRootSkin(); ?> -->
<?php if(!$kuler->mobile->isMobile()){ ?>
<?php } ?>
</body>
</html>