<?php
$kuler->addScript('catalog/view/theme/' . $kuler->getTheme() . '/js/one_page_checkout.js', true);
?>
<script>
	Kuler.one_page_checkout_methods_url = <?php echo json_encode($one_page_checkout_methods_url); ?>;
</script>
<div id="one-page-checkout">
	<?php if (!$is_logged) { ?>
	<a id="login"><?php echo _t('text_already_registered_click_here_to_login', 'Already registered? Click here to login'); ?></a>
	<?php } ?>
	<div class="checkout-info row">
		<form id="checkout-form">
			<input type="hidden" name="address_id" value="<?php echo $address_id; ?>" />
			<div class="customer-info col-md-5">
				<h2><?php echo _t('text_your_details'); ?></h2>
        <div class="row">
          <div class="col-md-6">
            <span class="required">*</span> <?php echo _t('entry_firstname'); ?><br />
            <input type="text" name="firstname" class="large-field" value="<?php echo $first_name; ?>" />

            <span class="required">*</span> <?php echo _t('entry_email'); ?><br />
            <input type="text" name="email" class="large-field" value="<?php echo $email; ?>" />

            <?php echo _t('entry_fax'); ?><br />
            <input type="text" name="fax" class="large-field" value="<?php echo $fax; ?>" />

            <div id="company-id-display">
              <?php echo _t('entry_company_id'); ?><br />
              <input type="text" name="company_id" class="large-field" <?php echo $company_id; ?> />
            </div>

            <span class="required">*</span> <?php echo _t('entry_address_1'); ?><br />
            <input type="text" name="address_1" class="large-field" value="<?php echo $address_1; ?>" />

            <span class="required">*</span> <?php echo _t('entry_city'); ?><br />
            <input type="text" name="city" class="large-field" value="<?php echo $city; ?>" />
          </div>
          <div class="col-md-6">
            <span class="required">*</span> <?php echo _t('entry_lastname'); ?><br />
            <input type="text" name="lastname" class="large-field" value="<?php echo $last_name; ?>" />

            <span class="required">*</span> <?php echo _t('entry_telephone'); ?><br />
            <input type="text" name="telephone" class="large-field" value="<?php echo $telephone; ?>" />

            <?php echo _t('entry_company'); ?><br />
            <input type="text" name="company" class="large-field" value="<?php echo $company; ?>" />

            <div id="tax-id-display"><?php echo _t('entry_tax_id'); ?><br />
              <input type="text" name="tax_id" class="large-field" value="<?php echo $tax_id; ?>" />
            </div>

            <input type="text" name="address_2" class="large-field" value="<?php echo $address_2; ?>" />

            <span id="postcode-required" class="required">*</span> <?php echo _t('entry_postcode'); ?><br />
            <input type="text" name="postcode" value="<?php echo $postcode; ?>" class="large-field" />
          </div>
        </div>

				<span class="required">*</span> <?php echo _t('entry_country'); ?><br />
				<select name="country_id" class="large-field country-selector" id="details-country-selector" data-post-code-required="#postcode-required" data-zone="#zone">
					<option><?php echo _t('text_select'); ?></option>
					<?php foreach ($countries as $country) { ?>
						<?php if ($country['country_id'] == $country_id) { ?>
							<option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
						<?php } else { ?>
							<option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
						<?php } ?>
					<?php } ?>
				</select>
				<br />
				<br />
				<span class="required">*</span> <?php echo _t('entry_zone'); ?><br />
				<select name="zone_id" id="zone" class="large-field" data-value="<?php echo $zone_id; ?>">
                    <option value=""></option>
				</select>
				<p>
					<label><input type="checkbox" name="shipping_address_same" value="1" class="toggle-option" data-target="#shipping-address-selector" data-reserve="true" checked="checked" /> <?php echo _t('text_my_delivery_address_and_personal_details_are_the_same'); ?></label>
					<div id="shipping-address-selector">
						<select name="shipping_address_id" class="address-selector" data-type="shipping">
							<option value="0"><?php echo _t('text_please_select'); ?></option>
							<?php foreach ($addresses as $address) { ?>
							<option value="<?php echo $address['address_id']; ?>"><?php echo $address['firstname']; ?> <?php echo $address['lastname']; ?>, <?php echo $address['address_1']; ?>, <?php echo $address['city']; ?>, <?php echo $address['zone']; ?>, <?php echo $address['country']; ?></option>
							<?php } ?>
							<option value="new"><?php echo _t('text_i_want_to_use_a_new_address'); ?></option>
					</select>
				</div>
				</p>
				<p>
					<label><input type="checkbox" name="payment_address_same" value="1" class="toggle-option" data-target="#payment-address-selector" data-reserve="true" checked="checked" /> <?php echo _t('text_my_payment_address_and_personal_details_are_the_same'); ?></label>
				<div id="payment-address-selector">
					<select name="payment_address_id" class="address-selector" data-type="payment">
						<option value="0"><?php echo _t('text_please_select'); ?></option>
						<?php foreach ($addresses as $address) { ?>
							<option value="<?php echo $address['address_id']; ?>"><?php echo $address['firstname']; ?> <?php echo $address['lastname']; ?>, <?php echo $address['address_1']; ?>, <?php echo $address['city']; ?>, <?php echo $address['zone']; ?>, <?php echo $address['country']; ?></option>
						<?php } ?>
						<option value="new"><?php echo _t('text_i_want_to_use_a_new_address'); ?></option>
					</select>
					</div>
				</p>
				<?php if (!$is_logged) { ?>
					<p>
						<label><input type="checkbox" name="create_new_account" class="toggle-option" data-target="#password-container" value="1" /> <?php echo _t('text_create_new_account'); ?></label>
						<div id="password-container">
							<div style="display: <?php echo (count($customer_groups) > 1 ? 'table-row' : 'none'); ?>;">
								<?php echo _t('entry_customer_group'); ?><br />
								<?php foreach ($customer_groups as $customer_group) { ?>
									<?php if ($customer_group['customer_group_id'] == $customer_group_id) { ?>
										<input type="radio" name="customer_group_id" value="<?php echo $customer_group['customer_group_id']; ?>" id="customer_group_id<?php echo $customer_group['customer_group_id']; ?>" checked="checked" />
										<label for="customer_group_id<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></label>
										<br />
									<?php } else { ?>
										<input type="radio" name="customer_group_id" value="<?php echo $customer_group['customer_group_id']; ?>" id="customer_group_id<?php echo $customer_group['customer_group_id']; ?>" />
										<label for="customer_group_id<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></label>
										<br />
									<?php } ?>
								<?php } ?>
								<br />
							</div>

							<p><label><?php echo _t('entry_password'); ?></label><input type="password" name="password" /></p>
							<p><label><?php echo _t('entry_confirm'); ?></label><input type="password" name="confirm" /></p>
							<p><label><input type="checkbox" name="newsletter" value="1" /> <?php echo _t('text_subscribe_newsletter'); ?></label></p>
							<p><label><input type="checkbox" name="register_agree" value="1" /> <?php echo $text_agree_register; ?></label></p>
						</div>
					</p>
				<?php } ?>
			</div>
			<div class="order-info col-md-7">
        <div class="method row">
          <div class="shipping-method col-md-6">
            <h2><?php echo _t('text_opcheckout_shipping_method'); ?></h2>
            <div id="shipping-method-content"></div>
          </div>
          <div class="payment-method col-md-6">
            <h2><?php echo _t('text_opcheckout_payment_method'); ?></h2>
            <div id="payment-method-content"></div>
          </div>
        </div>
				<div class="order-total">
					<h2><?php echo _t('text_opcheckout_confirm'); ?></h2>
					<div id="order-total-content"></div>
				</div>

				<p>
					<label><input type="checkbox" name="add_comment" value="1" class="toggle-option" data-target="#comment" /> <?php echo _t('text_comments'); ?></label>
					<textarea name="comment" cols="50" rows="10" id="comment"></textarea>
				</p>
				<p>
					<label><input type="checkbox" value="1" class="toggle-option" data-target="#coupon-container" /> <?php echo _t('text_use_coupon'); ?></label>
					<div id="coupon-container">
						<label><?php echo _t('entry_coupon'); ?></label>
						<input type="text" id="coupon" value="<?php echo $coupon; ?>" />
						<input type="button" id="apply-coupon" class="button" value="<?php echo _t('button_coupon'); ?>" />
					</div>
				</p>
				<p>
					<label><input type="checkbox" value="1" class="toggle-option" data-target="#voucher-container" /> <?php echo _t('text_use_voucher'); ?></label>
					<div id="voucher-container">
						<label><?php echo _t('entry_voucher'); ?></label>
						<input type="text" id="voucher" value="<?php echo $voucher; ?>" />
						<input type="button" id="apply-voucher" class="button" value="<?php echo _t('button_voucher'); ?>" />
					</div>
				</p>
				<p>
					<label><input type="checkbox" name="order_agree" value="1" /> <?php echo $text_agree; ?></label>
				</p>
				<p style="text-align: right">
					<input type="submit" id="confirm-order" class="button" value="<?php echo _t('button_confirm'); ?>" />
				</p>
			</div>
		</form>
	</div>
</div>

<div style="display: none;">
	<?php if (!$is_logged) { ?>
	<div id="login-form" class="popup-container">
		<form>
			<b><?php echo _t('entry_email'); ?></b><br />
			<input type="text" name="email" />
			<br />
			<br />
			<b><?php echo _t('entry_password'); ?></b><br />
			<input type="password" name="password" />
			<br />
			<a href="<?php echo $forgotten; ?>"><?php echo _t('text_forgotten'); ?></a><br />
			<br />
			<input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
			<input type="submit" value="<?php echo _t('button_login'); ?>" class="button" />
		</form>
	</div>
	<?php } ?>

	<div id="address-form-container" class="popup-container">
		<h2 data-payment-title="<?php echo _t('text_payment_address'); ?>" data-shipping-title="<?php echo _t('text_shipping_title'); ?>"></h2>

		<form id="address-form">
			<div class="row">
        <div class="col-md-6">
          <span class="required">*</span> <?php echo _t('entry_firstname'); ?><br />
          <input type="text" name="firstname" class="large-field" />
          <br />
          <br />
          <span class="required">*</span> <?php echo _t('entry_lastname'); ?><br />
          <input type="text" name="lastname" class="large-field" />
          <br />
          <br />
          <span class="required">*</span> <?php echo _t('entry_email'); ?><br />
          <input type="text" name="email" class="large-field" />
          <br />
          <br />
          <span class="required">*</span> <?php echo _t('entry_telephone'); ?><br />
          <input type="text" name="telephone" class="large-field" />
          <br />
          <br />
          <?php echo _t('entry_fax'); ?><br />
          <input type="text" name="fax" class="large-field" />
          <br />
          <br />
          <?php echo _t('entry_company'); ?><br />
          <input type="text" name="company" class="large-field" />
          <br />
          <div id="company-id-display"><span id="company-id-required" class="required">*</span> <?php echo _t('entry_company_id'); ?><br />
            <input type="text" name="company_id" class="large-field" />
            <br />
            <br />
          </div>
          <div id="tax-id-display"><span id="tax-id-required" class="required">*</span> <?php echo _t('entry_tax_id'); ?><br />
            <input type="text" name="tax_id" class="large-field" />
            <br />
            <br />
          </div>

        </div>
        <div class="col-md-6">
          <span class="required">*</span> <?php echo _t('entry_address_1'); ?><br />
          <input type="text" name="address_1" class="large-field" />
          <br />
          <br />
          <input type="text" name="address_2" class="large-field" />
          <br />
          <br />
          <span class="required">*</span> <?php echo _t('entry_city'); ?><br />
          <input type="text" name="city" class="large-field" />
          <br />
          <br />
          <span id="new-postcode-required" class="required">*</span> <?php echo _t('entry_postcode'); ?><br />
          <input type="text" name="postcode" value="<?php echo $postcode; ?>" class="large-field" />
          <br />
          <span class="required">*</span> <?php echo _t('entry_country'); ?><br />
          <select class="large-field country-selector" name="country_id" data-post-code-required="#new-postcode-required" data-zone="#new-zone">
            <option><?php echo _t('text_select'); ?></option>
            <?php foreach ($countries as $country) { ?>
              <?php if ($country['country_id'] == $country_id) { ?>
                <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
              <?php } else { ?>
                <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
              <?php } ?>
            <?php } ?>
          </select>
          <br />
          <br />
          <span class="required">*</span> <?php echo _t('entry_zone'); ?><br />
          <select id="new-zone" name="zone_id" class="large-field">
          </select>

          <p>
            <input type="submit" class="button" value="<?php echo _t('button_continue'); ?>" />
          </p>
        </div>
			</div>
		</form>
	</div>
	<div id="payment-form" class="popup-container"></div>
</div>
<script>
	Kuler.one_page_checkout_login_url = <?php echo json_encode($login_url); ?>;
	Kuler.order_confirm_url = <?php echo json_encode($order_confirm_url); ?>;
</script>