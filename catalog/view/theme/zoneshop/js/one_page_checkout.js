jQuery(document).ready(function ($) {
	// Popup
	$('.colorbox').magnificPopup({
		type: 'iframe'
	});

	// Init Toggle option
	$('.toggle-option')
		.each(function () {
			var $this = $(this),
				checked = $this.prop('checked'),
				checked = $this.data('reserve') ? !checked : checked;

			if (checked) {
				$($this.data('target')).show();
			} else {
				$($this.data('target')).hide();
			}
		})
		.on('click', function () {
			var $this = $(this),
				checked = $this.prop('checked'),
				checked = $this.data('reserve') ? !checked : checked;

			if (checked) {
				$($this.data('target')).show();
			} else {
				$($this.data('target')).hide();
			}
		});

	// Coupon
	var $couponButton = $('#apply-coupon');
	$couponButton.on('click', function () {
		$.ajax({
			url: 'index.php?route=module/kuler_cp/applyCoupon',
			type: 'POST',
			dataType: 'json',
			data: {
				coupon: $('#coupon').val()
			},
			beforeSend: function () {
				$('#coupon-container .success, #coupon-container .warning').remove();

				$couponButton
					.prop('disabled', true)
					.after(Kuler.waitHtml);
			},
			complete: function () {
				$couponButton.prop('disabled', false);
				$couponButton.next().remove();
			},
			success: function (data) {
				if (data.status) {
					$('#coupon-container').prepend('<div class="success">'+ data.message +'</div>');
					loadMethods('payment', $checkoutForm.serialize());
				} else {
					$('#coupon-container').prepend('<div class="warning">'+ data.message +'</div>');
				}
			}
		});
	});

	// Voucher
	var $voucherButton = $('#apply-voucher');
	$voucherButton.on('click', function () {
		$.ajax({
			url: 'index.php?route=module/kuler_cp/applyVoucher',
			type: 'POST',
			dataType: 'json',
			data: {
				voucher: $('#voucher').val()
			},
			beforeSend: function () {
				$('#voucher-container .success, #voucher-container .warning').remove();

				$voucherButton
					.prop('disabled', true)
					.after(Kuler.waitHtml);
			},
			complete: function () {
				$voucherButton.prop('disabled', false);
				$voucherButton.next().remove();
			},
			success: function (data) {
				if (data.status) {
					$('#voucher-container').prepend('<div class="success">'+ data.message +'</div>');
					loadMethods('payment', $checkoutForm.serialize());
				} else {
					$('#voucher-container').prepend('<div class="warning">'+ data.message +'</div>');
				}
			}
		});
	});

	function loadMethods(method_type, data) {
		data = data + '&' || '';

		data += 'method_type=' + method_type;

		$.ajax({
			url: Kuler.one_page_checkout_methods_url,
			type: 'GET',
			dataType: 'json',
			data: data,
			beforeSend: function () {
				if (method_type == 'all') {
					$('#shipping-method-content').html(Kuler.waitHtml);
					$('#payment-method-content').html(Kuler.waitHtml);
					$('#order-total-content').html(Kuler.waitHtml);
				} else if (method_type == 'shipping') {
					$('#payment-method-content').html(Kuler.waitHtml);
					$('#order-total-content').html(Kuler.waitHtml);
				} else if (method_type == 'payment') {
					$('#order-total-content').html(Kuler.waitHtml);
				}

			},
			complete: function () {

			},
			success: function (data) {
				if (method_type == 'all') {
					$('#shipping-method-content').html(data.shipping_method);
					$('#payment-method-content').html(data.payment_method);
					$('#order-total-content').html(data.order_total);
				} else if (method_type == 'shipping') {
					$('#payment-method-content').html(data.payment_method);
					$('#order-total-content').html(data.order_total);
				} else if (method_type == 'payment') {
					$('#order-total-content').html(data.order_total);
				}
			}
		});
	}

	// Login form
	$('#login').on('click', function (evt) {
		evt.preventDefault();

		$.magnificPopup.open({
			items: [
				{
					src: '#login-form'
				}
			],
			type: 'inline',
			mainClass: 'mfp-fade'
		});
	});

	var $loginForm = $('#login-form form');
	$loginForm.on('submit', function (evt) {
		evt.preventDefault();

		$.ajax({
			url: Kuler.one_page_checkout_login_url,
			type: 'POST',
			dataType: 'json',
			data: $loginForm.serialize(),
			beforeSend: function () {
				$loginForm.find('[type="submit"]').after(Kuler.waitHtml);
				$loginForm.find('.warning').remove();
			},
			complete: function () {
				$loginForm.find('.wait').remove();
			},
			success: function (data) {
				if (data.status) {
					location = data.redirect;
				} else {
					$loginForm.prepend('<div class="warning">'+ data.message +'</div>');
				}
			}
		})
	});

	// Address form
	var $addressFormContainer = $('#address-form-container');
	$('.address-selector').on('change', function () {
		var $this = $(this);

        $this.data('value', this.value);

		if ($this.val() == 'new') {
            $addressForm.data('selector', this);

			$.magnificPopup.open({
				items: [
					{
						src: '#address-form-container'
					}
				],
				type: 'inline',
				mainClass: 'mfp-fade',
				callbacks: {
					close: function() {
						if ($this.val() == 'new') {
							$this
								.val('0')
								.trigger('change');
						}
					}
				}
			});
		}
	});

	var $addressForm = $('#address-form');
	$addressForm.on('submit', function (evt) {
		evt.preventDefault();

		$.ajax({
			url: 'index.php?route=module/kuler_cp/createAddress',
			type: 'POST',
			dataType: 'json',
			data: $addressForm.serialize(),
			beforeSend: function () {
				$addressForm.find('[type="submit"]')
					.prop('disabled', true)
					.after(Kuler.waitHtml);

				$addressForm.find('.warning, .error').remove();
			},
			complete: function () {
				$addressForm.find('[type="submit"]')
					.prop('disabled', false)
					.next()
					.remove();
			},
			success: function (data) {
				if (data.status) {
                    // Fill address options
                    $('.address-selector option:not([value="0"]):not([value="new"])').remove();

                    for (var i in data.addresses) {
                        var address = data.addresses[i];

                        $('.address-selector option[value="new"]').before('<option value="'+ i +'">'+ address.firstname + ' ' + address.lastname + ', ' + address.address_1 + ', ' + address.city + ', ' + address.zone + ', ' + address.country +'</option>');
                    }

                    // Select new address
                    $('.address-selector').each(function () {
                        var $this = $(this);

                        this.value = $this.data('value');
                        $this.trigger('change');
                    });

                    $addressForm.data('selector').value = data.address_id;
                    $($addressForm.data('selector')).trigger('change');

					$.magnificPopup.close();
				} else {
					if (typeof data.error_fields != 'undefined') {
						var error_fields = data.error_fields;

						if (error_fields['warning'])
						{
							$addressForm.prepend('<span class="error">' + error_fields['warning'] + '</span>');
						}

						if (error_fields['firstname']) {
							$('#address-form input[name=\'firstname\']').after('<span class="error">' + error_fields['firstname'] + '</span>');
						}

						if (error_fields['lastname']) {
							$('#address-form input[name=\'lastname\']').after('<span class="error">' + error_fields['lastname'] + '</span>');
						}

						if (error_fields['email']) {
							$('#address-form input[name=\'email\'] + br').after('<span class="error">' + error_fields['email'] + '</span>');
						}

						if (error_fields['telephone']) {
							$('#address-form input[name=\'telephone\'] + br').after('<span class="error">' + error_fields['telephone'] + '</span>');
						}

						if (error_fields['company_id']) {
							$('#address-form input[name=\'company_id\']').after('<span class="error">' + error_fields['company_id'] + '</span>');
						}

						if (error_fields['tax_id']) {
							$('#address-form input[name=\'tax_id\']').after('<span class="error">' + error_fields['tax_id'] + '</span>');
						}

						if (error_fields['address_1']) {
							$('#address-form input[name=\'address_1\']').after('<span class="error">' + error_fields['address_1'] + '</span>');
						}

						if (error_fields['city']) {
							$('#address-form input[name=\'city\']').after('<span class="error">' + error_fields['city'] + '</span>');
						}

						if (error_fields['postcode']) {
							$('#address-form input[name=\'postcode\']').after('<span class="error">' + error_fields['postcode'] + '</span>');
						}

						if (error_fields['country']) {
							$('#address-form select[name=\'country_id\']').after('<span class="error">' + error_fields['country'] + '</span>');
						}

						if (error_fields['zone']) {
							$('#address-form select[name=\'zone_id\']').after('<span class="error">' + error_fields['zone'] + '</span>');
						}
					}
				}
			}
		});
	});

	// Country
	$('.country-selector').on('change', function() {
		if (this.value == '') return;

		var $this = $(this);

		return $.ajax({
			url: 'index.php?route=checkout/checkout/country&country_id=' + this.value,
			dataType: 'json',
			beforeSend: function() {
				$this.after(Kuler.waitHtml);
			},
			complete: function() {
				$('.wait').remove();
			},
			success: function(json) {
				if (json['postcode_required'] == '1') {
					$($this.data('postCodeRequired')).show();
				} else {
					$($this.data('postCodeRequired')).hide();
				}

				html = '<option value=""> --- Please Select --- </option>';

				if (json['zone'] != '') {
					for (i = 0; i < json['zone'].length; i++) {
						html += '<option value="' + json['zone'][i]['zone_id'] + '"';

						if (json['zone'][i]['zone_id'] == '') {
							html += ' selected="selected"';
						}

						html += '>' + json['zone'][i]['name'] + '</option>';
					}
				} else {
					html += '<option value="0" selected="selected"> --- None --- </option>';
				}

				var $zone = $($this.data('zone'));

				$zone
					.html(html)
					.val($zone.data('value'));
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	});

    // Refresh shipping method, payment method, order total
    var $checkoutForm = $('#checkout-form');
    $checkoutForm.on('change', '#details-country-selector, #zone, [name="shipping_address_id"], [name="payment_address_id"]', function () {
	    if ($(this).val() != 'new') {
        loadMethods('all', $checkoutForm.serialize());
	    }
    });

	$('#shipping-method-content').on('click', '[name="shipping_method"]', function () {
		loadMethods('shipping', $checkoutForm.serialize());
	});

	$('#payment-method-content').on('click', '[name="payment_method"]', function () {
		loadMethods('payment', $checkoutForm.serialize());
	});

	// Confirm order
	var $confirmOrderButton = $('#confirm-order');
	$checkoutForm.on('submit', function (evt) {
		evt.preventDefault();

		$.ajax({
			url: Kuler.order_confirm_url,
			type: 'POST',
			dataType: 'json',
			data: $checkoutForm.serialize(),
			beforeSend: function () {
				$confirmOrderButton.after(Kuler.waitHtml).prop('disabled', true);
				$checkoutForm.find('.error, .warning').remove();
			},
			success: function (data) {
				$checkoutForm.find('.wait').remove();
				$confirmOrderButton.prop('disabled', false);

				if (data.status) {
					$('#payment-form').html(data.payment);

					if ($('#payment-form').find('select, input[type="text"]').length || $('#payment-form').find('.warning').length) {
						$.magnificPopup.open({
							items: [
								{
									src: '#payment-form'
								}
							],
							type: 'inline',
							mainClass: 'mfp-fade'
						});
					} else {
						var $confirmButton = $('#payment-form').find('.buttons input[type="submit"], .buttons .button, #confirm-order, #button-confirm, #button-paypal, #button-confirm').first();

						if ($confirmButton[0]) {
							$confirmButton[0].click();
						}

						$confirmOrderButton.after(Kuler.waitHtml).prop('disabled', true);
					}
				} else if (data.error_fields) {
					var error_fields = data.error_fields;

					if (error_fields['warning'])
					{
						$checkoutForm.prepend('<span class="error">' + error_fields['warning'] + '</span>');
					}

					if (error_fields['firstname']) {
						$('#checkout-form input[name=\'firstname\']').after('<span class="error">' + error_fields['firstname'] + '</span>');
					}

					if (error_fields['lastname']) {
						$('#checkout-form input[name=\'lastname\']').after('<span class="error">' + error_fields['lastname'] + '</span>');
					}

					if (error_fields['email']) {
						$('#checkout-form input[name=\'email\'] + br').after('<span class="error">' + error_fields['email'] + '</span>');
					}

					if (error_fields['telephone']) {
						$('#checkout-form input[name=\'telephone\'] + br').after('<span class="error">' + error_fields['telephone'] + '</span>');
					}

					if (error_fields['company_id']) {
						$('#checkout-form input[name=\'company_id\']').after('<span class="error">' + error_fields['company_id'] + '</span>');
					}

					if (error_fields['tax_id']) {
						$('#checkout-form input[name=\'tax_id\']').after('<span class="error">' + error_fields['tax_id'] + '</span>');
					}

					if (error_fields['address_1']) {
						$('#checkout-form input[name=\'address_1\']').after('<span class="error">' + error_fields['address_1'] + '</span>');
					}

					if (error_fields['city']) {
						$('#checkout-form input[name=\'city\']').after('<span class="error">' + error_fields['city'] + '</span>');
					}

					if (error_fields['postcode']) {
						$('#checkout-form input[name=\'postcode\']').after('<span class="error">' + error_fields['postcode'] + '</span>');
					}

					if (error_fields['country']) {
						$('#checkout-form select[name=\'country_id\']').after('<span class="error">' + error_fields['country'] + '</span>');
					}

					if (error_fields['zone']) {
						$('#checkout-form input[name=\'zone_id\']').after('<span class="error">' + error_fields['zone'] + '</span>');
					}

					if (error_fields['shipping_address_id']) {
						$('#checkout-form select[name=\'shipping_address_id\']').after('<span class="error">' + error_fields['shipping_address_id'] + '</span>');
					}

					if (error_fields['payment_address_id']) {
						$('#checkout-form select[name=\'payment_address_id\']').after('<span class="error">' + error_fields['payment_address_id'] + '</span>');
					}

					if (error_fields['shipping_method']) {
						$('#checkout-form #shipping-method-content').prepend('<span class="error">' + error_fields['shipping_method'] + '</span>');
					}

					if (error_fields['payment_method']) {
						$('#checkout-form #payment-method-content').prepend('<span class="error">' + error_fields['payment_method'] + '</span>');
					}

					if (error_fields['order_agree']) {
						$('#checkout-form input[name=\'order_agree\']').parent().after('<span class="error">' + error_fields['order_agree'] + '</span>');
					}

                    if (error_fields['password']) {
                        $('#checkout-form input[name=\'password\']').after('<span class="error">' + error_fields['password'] + '</span>');
                    }

                    if (error_fields['confirm']) {
                        $('#checkout-form input[name=\'confirm\']').after('<span class="error">' + error_fields['confirm'] + '</span>');
                    }

                    if (error_fields['register_agree']) {
                        $('#checkout-form [name=\'register_agree\']').parent().after('<span class="error">' + error_fields['register_agree'] + '</span>');
                    }
				}
			}
		});
	});

	$('#details-country-selector').trigger('change');
});