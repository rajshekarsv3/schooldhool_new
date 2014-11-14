<?php
$kuler = Kuler::getInstance();

$kuler->addScript($kuler->getThemeResource('catalog/view/javascript/kuler/swiper/idangerous.swiper-2.1.min.js'), true);
if ($has_deal_date) {
	$kuler->addScript($kuler->getThemeResource('catalog/view/javascript/kuler/countdown/jquery.plugin.min.js'), true);
	$kuler->addScript($kuler->getThemeResource('catalog/view/javascript/kuler/countdown/jquery.countdown.min.js'), true);
}
?>

<div class="kuler-showcase-module<?php if (!empty($settings['css_class'])) echo ' ' . $settings['css_class']; ?>" id="kuler-showcase-module-<?php echo $module; ?>">
	<div class="box kuler-module">
		<?php if (!empty($settings['show_title'])) { ?>
			<div class="box-heading"><span><?php echo $kuler->translate($settings['title']); ?></span></div>
		<?php } ?>
		<?php if ($settings['type'] == 'tab') { ?>
			<div class="box-heading tab-heading">
				<?php foreach ($showcases as $showcase) { ?>
					<?php if (!empty($showcase['show_title'])) { ?>
					<span><?php echo $kuler->translate($showcase['title']); ?></span>
					<?php } ?>
				<?php } ?>
			</div>
		<?php } ?>
		<div dir="ltr" class="box-content swiper-container">
			<div class="swiper-wrapper">
				<?php foreach ($showcases as $showcase) { ?>
				<div class="showcase swiper-slide<?php if (!empty($showcase['css_class'])) echo ' ' . $showcase['css_class']; ?>">
					<div class="container">
						<div <?php if($direction == "rtl") { ?> dir="rtl" <?php } ?> class="row">
							<?php if (!empty($showcase['items'])) { ?>
								<?php foreach ($showcase['items'] as $item) { ?>
									<div class="item <?php echo $item['type']; ?><?php if (!empty($item['css_class'])) echo ' ' . $item['css_class']; ?>">
										<?php if ($item['type'] == 'html') { ?>
											<?php echo $kuler->translate($item['html_content']); ?>
										<?php } else if ($item['type'] == 'product') { ?>
											<div class="box-product product-grid">
												<?php foreach ($item['products'] as $product) { ?>
													<?php echo $this->common->loadProductTemplate($item, $product, 'grid'); ?>
												<?php } ?>
											</div>
										<?php } ?>
									</div>
								<?php } ?>
							<?php } ?>
						</div>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
		<?php if ($settings['type'] == 'slide') { ?>
		<div class="nav">
			<span class="prev">prev</span>
			<span class="next">next</span>
		</div>
		<?php } ?>
	</div>
</div>
<script>
	$(document).ready(function () {
		<?php if ($settings['type'] == 'slide') { ?>
		var showcaseSwiper = new Swiper('#kuler-showcase-module-<?php echo $module; ?> .swiper-container',{
			mode:'horizontal',
			calculateHeight: true,
			cssWidthAndHeight: false,
			loop: true
		});

		$('#kuler-showcase-module-<?php echo $module; ?> .next').on('click', function () {
			showcaseSwiper.swipeNext();
		});

		$('#kuler-showcase-module-<?php echo $module; ?> .prev').on('click', function () {
			showcaseSwiper.swipePrev();
		});
		<?php } else if ($settings['type'] == 'tab') { ?>
		var $tabHeading = $("#kuler-showcase-module-<?php echo $module; ?> .tab-heading span"),
			tabLength = $tabHeading.length;

		$tabHeading.first().addClass('active');

		var showcaseSwiper = new Swiper('#kuler-showcase-module-<?php echo $module; ?> .swiper-container',{
			mode:'horizontal',
			calculateHeight: true,
			cssWidthAndHeight: true,
			loop: true,
			onSlideChangeStart: function () {
				$tabHeading.filter('.active').removeClass('active');
				$tabHeading.eq((showcaseSwiper.activeIndex % tabLength) - 1).addClass('active')
			}
		});

		$tabHeading.on('touchstart mousedown', function(e){
			e.preventDefault();
			showcaseSwiper.swipeTo($(this).index());
		})
		<?php } ?>
			if ($.fn.countdown) {
			$.countdown.setDefaults({
				labels: ['<?php echo $__['text_years'];  ?>', '<?php echo $__['text_months']; ?>', '<?php echo $__['text_weeks']; ?>', '<?php echo $__['text_days']; ?>', '<?php echo $__['text_hours']; ?>', '<?php echo $__['text_minutes']; ?>', '<?php echo $__['text_seconds']; ?>'],
				labels1: ['<?php echo $__['text_year']; ?>', '<?php echo $__['text_month']; ?>', '<?php echo $__['text_week']; ?>', '<?php echo $__['text_day']; ?>', '<?php echo $__['text_hour']; ?>', '<?php echo $__['text_minute']; ?>', '<?php echo $__['text_second']; ?>'],
				compactLabels: ['y', 'm', 'w', 'd'],
				whichLabels: null,
				digits: ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
				timeSeparator: ':',
				isRTL: <?php echo $direction == 'rtl' ? 'true': 'false'; ?>
			});

			$('.product-deal-countdown').each(function () {
				var $dealEl = $(this);

				if ($dealEl.data('isDeal')) {
					$dealEl.countdown({
						until: new Date($dealEl.data('year'), $dealEl.data('month') - 1, $dealEl.data('day')),
						compact: false
					});
				}
			});
			}
	});
</script>