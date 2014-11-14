<?php $kuler = Kuler::getInstance();
$kuler->language->load('kuler/zoneshop');
?>
<div id="carousel<?php echo $module; ?>" class="carousel">
  <div class="box-heading"><span><?php echo $kuler->language->get('text_carousel_title'); ?></span></div>
  <ul class="jcarousel-skin-opencart">
    <?php foreach ($banners as $banner) { ?>
    <li><a href="<?php echo $banner['link']; ?>"><img src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" title="<?php echo $banner['title']; ?>" /></a></li>
    <?php } ?>
  </ul>
</div>
<script type="text/javascript"><!--
$('#carousel<?php echo $module; ?> ul').jcarousel({
	vertical: false,
	visible: <?php echo $limit; ?>,
	scroll: <?php echo $scroll; ?>
});
//--></script>