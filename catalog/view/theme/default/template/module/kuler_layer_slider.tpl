<?php if ($sliders) { ?>
<?php
$class = $sliderParams['fullwidth'] ? $sliderParams['fullwidth'] : 'boxed';
?>
<?php if (!$sliderParams['fullwidth']) { ?>
<style>
	.kls-container {
		margin: 0 auto;
		max-width: <?php echo $sliderParams['width']; ?>px;
	}
</style>
<?php } ?>
<div id="kls-<?php echo $module ?>" class="kls-container kls-<?php echo $sliderParams['fullwidth'] ?>">
	<div class="kls-banner">
		<ul>
			<?php foreach ($sliders as $slider) { ?>
			<?php if ($slider['status']) { ?>
			<li
				data-transition="<?php echo $slider['params']['slider_transition']; ?>"
				data-masterspeed="<?php echo $slider['params']['slider_duration']; ?>"
				data-slotamound="<?php echo $slider['params']['slider_slot']; ?>"
				<?php if ($slider['params']['slider_delay']) { ?> data-delay="<?php echo $slider['params']['slider_delay']; ?>"<?php } ?>
				<?php if ($slider['params']['slider_enable_link']) { ?>
				data-link="<?php echo $slider['params']['slider_link']; ?>" data-target="_blank"<?php } ?>
				<?php if ($slider['params']['slider_thumbnail']) { ?> data-thumb="<?php echo $slider['thumbnail']; ?>"<?php } ?>>
				<?php if( $slider['params']['slider_usevideo'] == 'youtube' || $slider['params']['slider_usevideo'] == 'vimeo' ) { ?>
					<?php 
						$vurl = '//player.vimeo.com/video/'.$slider['params']['slider_videoid'].'/';

						if ($slider['params']['slider_usevideo'] == 'youtube') {
						 	$vurl  = '//www.youtube.com/embed/'.$slider['params']['slider_videoid'].'/';
						}
					?>
					<div class="caption fade fullscreenvideo"
						data-autoplay="<?php echo $slider['params']['slider_videoplay'] ? 'true' : 'false' ?>"
						data-nextslideatend="true"
						data-x="0"
						data-y="0"
						data-speed="500"
						data-start="10"
						data-easing="easeOutBack">
						<iframe src="<?php echo $vurl;?>?enablejsapi=1&title=0&byline=0&portrait=0;api=1&origina=<?php echo $url; ?>" width="100%" height="100%"></iframe>
					</div>
				<?php } else if ($slider['main_image']) { ?>
					<img src="<?php echo $slider['main_image']; ?>" alt="<?php echo $slider['slider_title']; ?>" />
				<?php } ?>
				<?php foreach ($slider['layersparams']->layers as $i => $layer)  { ?>
				<?php 
					$type = $layer['layer_type'];
				?>
					<div class="caption <?php echo $layer['layer_class']; ?> <?php echo $layer['layer_animation'];?> <?php echo $layer['layer_easing'];?><?php if ($layer['layer_endanimation'] != 'auto') echo ' ' . $layer['layer_endanimation']; ?>"
						data-x="<?php echo $layer['layer_left']; ?>"
						data-y="<?php echo $layer['layer_top']; ?>"
						<?php if ($layer['layer_endtime']) { ?>
							data-end="<?php echo $layer['layer_endtime']; ?>"
						<?php } ?>
						<?php if ($layer['layer_endeasing'] != 'nothing') { ?>
							data-endeasing="<?php echo $layer['layer_endeasing']; ?>"
						<?php } ?>
						<?php if ($layer['layer_endeasing'] != 'nothing' && $layer['layer_endspeed']) { ?>
							data-endspeed="<?php echo $layer['layer_endspeed']; ?>"
						<?php } ?>
						data-speed="<?php echo $layer['layer_speed']; ?>"
						data-start="<?php echo $layer['time_start']; ?>"
						data-easing="<?php echo $layer['layer_easing']; ?>">
							<?php if( $type =='image') { ?>
								<img src="<?php echo $url."image/".$layer['layer_content']; ?>" alt="Layer">
							<?php } else if($type == 'video') { ?>
								<?php if( $layer['layer_video_type'] == 'vimeo')  { ?>
								<iframe src="//player.vimeo.com/video/<?php echo $layer['layer_video_id'];?>?title=0&amp;byline=0&amp;portrait=0;api=1" width="<?php echo $layer['layer_video_width'];?>" height="<?php echo $layer['layer_video_height'];?>"></iframe>
								<?php } else { ?>
								<iframe width="<?php echo $layer['layer_video_width'];?>" height="<?php echo $layer['layer_video_height'];?>" src="//www.youtube.com/embed/<?php echo $layer['layer_video_id'];?>" frameborder="0" allowfullscreen></iframe>
								<?php } ?>
							<?php } else { ?>
								<?php echo html_entity_decode(str_replace('_ASM_', '&', $layer['layer_caption']) , ENT_QUOTES, 'UTF-8'); ?>
							<?php } ?>
					</div>
				<?php } ?>
			</li>
			<?php } ?>
			<?php } ?>
		</ul>
		<?php if ($sliderParams['show_time_line']) { ?>
		<div class="tp-bannertimer tp-<?php echo $sliderParams['time_line_position']; ?>"></div>
		<?php } ?>
	</div>
</div>
<script>
	$(function () {
		jQuery('#kls-<?php echo $module ?> .kls-banner').revolution({
			delay:<?php echo $sliderParams['delay'];?>,
			startheight:<?php echo $sliderParams['height'];?>,
			startwidth:<?php echo $sliderParams['width'];?>,


			hideThumbs:<?php echo (int)$sliderParams['hide_navigator_after'];?>,

			thumbWidth:<?php echo (int)$sliderParams['thumbnail_width'];?>,
			thumbHeight:<?php echo (int)$sliderParams['thumbnail_height'];?>,
			thumbAmount:<?php echo (int)$sliderParams['thumbnail_amount'];?>,

			navigationType:"<?php echo $sliderParams['navigator_type'];?>",
			navigationArrows:"<?php echo $sliderParams['navigator_arrows'];?>",
			<?php if( $sliderParams['navigation_style'] != 'none' ) {   ?>
			navigationStyle:"<?php echo $sliderParams['navigation_style'];?>",
			<?php } ?>

			navOffsetHorizontal:<?php echo (int)$sliderParams['offset_horizontal'];?>,
			navOffsetVertical:<?php echo (int)$sliderParams['offset_vertical'];?>,

			touchenabled:"<?php echo ($sliderParams['touch_mobile']?'on':'off') ?>",
			onHoverStop:"<?php echo ($sliderParams['stop_on_hover']?'on':'off') ?>",
			shuffle:"<?php echo ($sliderParams['shuffle_mode']?'on':'off') ?>",
			stopAtSlide:-1,
			stopAfterLoops:-1,

			hideCaptionAtLimit:0,
			hideAllCaptionAtLilmit:0,
			hideSliderAtLimit: <?php echo intval($sliderParams['hide_screen_width']); ?>,
			<?php if ($sliderParams['fullwidth'] == 'fullwidth') { ?>
			fullWidth: "on",
			<?php } ?>
			<?php if ($sliderParams['fullwidth'] == 'fullscreen') { ?>
			fullScreen: 'on',
			fullScreenAlignForce:"on",
			<?php } ?>
			shadow: <?php echo (int)$sliderParams['shadow_type']; ?>

		});
	});
</script>
<?php } ?>