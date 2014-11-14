<?php $kuler = Kuler::getInstance(); ?>
<?php echo $header; ?>
  <?php if ($kuler->getSkinOption('show_google_map')) { ?>
    <div id="google-map">
      <div id="map" style="width: 100%; height: 400px;"></div>
      <script src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
      <script>
        Kuler.latitude = <?php echo json_encode($kuler->getSkinOption('latitude')); ?>;
        Kuler.longitude = <?php echo json_encode($kuler->getSkinOption('longitude')); ?>;
        Kuler.map_type = <?php echo json_encode($kuler->getSkinOption('map_type')); ?>;

        (function () {
          jQuery(document).ready(function ($) {
            var map_canvas = document.getElementById('map');
            var map_options = {
              center: new google.maps.LatLng(Kuler.latitude, Kuler.longitude),
              zoom: 8,
              mapTypeId: google.maps.MapTypeId[Kuler.map_type]
            }
            var map = new google.maps.Map(map_canvas, map_options);
            map.setZoom(15);

            var latLng = new google.maps.LatLng(Kuler.latitude, Kuler.longitude);
            var image = new google.maps.MarkerImage(
              'catalog/view/theme/' + Kuler.theme + '/image/icon/map-marker.png',
              new google.maps.Size(57, 76),
              new google.maps.Point(0,0),
              new google.maps.Point(30, 76)
            );

            var marker = new google.maps.Marker({
              position: latLng,
              map: map,
              icon: image
            });
          });

        })();
      </script>
    </div>
  <?php } ?>
  <div class="contact-page">
    <div class="container">
	    <div id="content" >
		    <div class="row">
			    <?php echo $column_left; ?>
			    <?php if ($column_left && $column_right) { ?>
				    <?php $class = 'col-md-6'; ?>
			    <?php } elseif ($column_left || $column_right) { ?>
				    <?php $class = 'col-md-9'; ?>
			    <?php } else { ?>
				    <?php $class = 'col-md-12'; ?>
			    <?php } ?>
			    <div class="<?php echo $class; ?>">
				    <?php echo $content_top; ?>
				    <h2 class="box-heading"><?php echo $heading_title; ?></h2>
				    <?php if ($kuler->getSkinOption('show_custom_information')) { ?>
					    <div class="custom">
						    <?php echo $kuler->translate($kuler->getSkinOption('custom_information')); ?>
					    </div>
				    <?php } ?>
				    <div class="row">
					    <div class="col-md-3">
						    <h3><span><?php echo $text_location; ?></span></h3>
						    <div class="contact-info">
							    <div class="content"><div class="left"><b><?php echo $text_address; ?></b><br />
									    <?php echo $store; ?><br />
									    <?php echo $address; ?></div>
								    <div class="right">
									    <?php if ($telephone) { ?>
										    <b><?php echo $text_telephone; ?></b><br />
										    <?php echo $telephone; ?><br />
										    <br />
									    <?php } ?>
									    <?php if ($fax) { ?>
										    <b><?php echo $text_fax; ?></b><br />
										    <?php echo $fax; ?>
									    <?php } ?>
								    </div>
							    </div>
						    </div>
					    </div>
					    <div class="col-md-9">
						    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
							    <h3><span><?php echo $text_contact; ?></span></h3>
							    <div class="content">
								    <div class="row">
									    <div class="col-md-6">
										    <input type="text" name="name" placeholder="<?php echo $entry_name; ?>" value="<?php echo $name; ?>" />
										    <br />
										    <?php if ($error_name) { ?>
											    <span class="error"><?php echo $error_name; ?></span>
										    <?php } ?>
									    </div>
									    <div class="col-md-6">
										    <input type="text" name="email" placeholder="<?php echo $entry_email; ?>" value="<?php echo $email; ?>" />
										    <br />
										    <?php if ($error_email) { ?>
											    <span class="error"><?php echo $error_email; ?></span>
										    <?php } ?>
									    </div>
								    </div>
								    <textarea name="enquiry" placeholder="<?php echo $entry_enquiry; ?>" cols="40" rows="10"><?php echo $enquiry; ?></textarea>
								    <br />
								    <?php if ($error_enquiry) { ?>
									    <span class="error"><?php echo $error_enquiry; ?></span>
								    <?php } ?>
								    <input type="text" placeholder="<?php echo $entry_captcha; ?>" name="captcha" value="<?php echo $captcha; ?>" />
								    <br />
								    <img src="index.php?route=information/contact/captcha" alt="" />
								    <?php if ($error_captcha) { ?>
									    <span class="error"><?php echo $error_captcha; ?></span>
								    <?php } ?>
							    </div>
							    <div class="buttons">
								    <div class="right"><input type="submit" value="<?php echo $button_continue; ?>" class="button" /></div>
							    </div>
						    </form>
					    </div>
				    </div>
				    <?php echo $content_bottom; ?></div>
			    <?php echo $column_right; ?>
		    </div>
	    </div>
    </div>
  </div>

<?php echo $footer; ?>