<?php
require_once(DIR_APPLICATION . 'controller/module/kuler_cp.php');
$kuler = Kuler::getInstance();
$kuler->addScript($kuler->getThemeResource('catalog/view/theme/'. $kuler->getTheme() .'/js/kuler_contact_form.js'), true);
?>
<div class="kcf-module" id="kcf-module-<?php echo $module; ?>">
	<div class="box kuler-module">
		<div class="container">
			<?php if (!empty($settings['show_title'])) { ?>
				<div class="box-heading"><span><?php echo $kuler->translate($settings['title']); ?></span></div>
			<?php } ?>
			<div class="box-content">
				<div class="row">
					<div class="col-md-6" style="overflow: hidden">
						<?php if (!empty($settings['show_custom_information'])) { ?>
							<?php echo $kuler->translate($settings['custom_information']); ?>
						<?php } ?>
					</div>
					<div class="col-md-6">
						<form action="<?php echo $action_url; ?>">
							<p class="kcf-field-name">
								<input type="text" name="name" placeholder="<?php echo $entry_name; ?>" />
							</p>
							<p class="kcf-field-email">
								<input type="email" name="email" placeholder="<?php echo $entry_email; ?>" />
							</p>
							<p class="kcf-field-enquiry">
								<textarea name="enquiry" cols="60" rows="5" placeholder="<?php echo $entry_enquiry; ?>"></textarea>
							</p>
							<p>
								<button class="kcp-button-submit"><?php echo $button_send_message; ?></button>
							</p>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>