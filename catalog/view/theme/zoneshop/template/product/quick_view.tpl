<?php $kuler = Kuler::getInstance(); $theme = $kuler->getTheme(); ?>
<!doctype html>
<html>
<head>
	<title><?php echo $heading_title; ?></title>
	<base href="<?php echo $base; ?>" />
	<?php
	$kuler->addStyle(array(
		"catalog/view/theme/$theme/stylesheet/stylesheet.css"
	));
	?>
	<!-- {STYLES} -->
	<?php
	$kuler->addScript(array(
		'catalog/view/javascript/jquery/jquery-1.7.1.min.js',
		'catalog/view/javascript/jquery/ui/jquery-ui-1.8.16.custom.min.js',
		'catalog/view/javascript/common.js',
		"catalog/view/theme/$theme/js/utils.js"
	));
	?>
	<!-- {SCRIPTS} -->
</head>
<body class="<?php echo $kuler->getBodyClass(); ?>">
<div id="content" class="quickview">
	<div class="product-info">
            <div class="row">
                <?php if ($thumb || $images) { ?>
                <div class="left col-sm-4">
                    <?php if ($thumb) { ?>
                    <div class="image"><a id="quickviw-first-a" href="<?php echo $product_url; ?>" target="_top" title="<?php echo $heading_title; ?>" ><img src="<?php echo $thumb; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" id="main-image" style="max-width: 240px;" /></a></div>
                    <?php } ?>

                    <?php if ($images) { ?>
                    <div class="image-additional">
                        <?php foreach ($images as $image) { ?>
                        <a href="<?php echo $image['popup']; ?>" title="<?php echo $heading_title; ?>"><img src="<?php echo $image['thumb']; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" /></a>
                        <?php } ?>
                    </div>
                    <?php } ?>
                </div>
                <?php } ?>
                <div class="right col-sm-8">
                    <h1><?php echo $heading_title; ?></h1>
                    <div class="description">
                        <?php if ($manufacturer) { ?>
                        <span><?php echo $text_manufacturer; ?></span> <a href="<?php echo $manufacturers; ?>" target="_top"><?php echo $manufacturer; ?></a><br />
                        <?php } ?>
                        <span><?php echo $text_model; ?></span> <?php echo $model; ?><br />
                        <?php if ($reward) { ?>
                        <span><?php echo $text_reward; ?></span> <?php echo $reward; ?><br />
                        <?php } ?>
                        <span><?php echo $text_stock; ?></span> <?php echo $stock; ?></div>
                    <?php if ($price) { ?>
                    <div class="price">
                        <?php if (!$special) { ?>
                        <?php echo $price; ?>
                        <?php } else { ?>
                        <span class="price-old"><?php echo $price; ?></span> <br /><span class="price-new"><?php echo $special; ?></span>
                        <?php } ?>
                        <br />
                        <?php if ($tax) { ?>
                        <div class="price-tax"><?php echo $text_tax; ?> <?php echo $tax; ?></div>
                        <?php } ?>
                        <?php if ($points) { ?>
                        <span class="reward"><?php echo $text_points; ?> <?php echo $points; ?></span>
                        <?php } ?>
                        <?php if ($discounts) { ?>
                        <div class="discount">
                            <?php foreach ($discounts as $discount) { ?>
                            <?php echo sprintf($text_discount, $discount['quantity'], $discount['price']); ?><br />
                            <?php } ?>
                        </div>
                        <?php } ?>
                    </div>
                    <?php } ?>

                    <?php if ($review_status) { ?>
                    <div class="review">
                        <div><img src="catalog/view/theme/<?php echo $theme; ?>/image/stars-<?php echo $rating; ?>.png" alt="<?php echo $reviews; ?>" /></div>
                    </div>
                    <?php } ?>

                    <div class="cart">
                        <div>
                            <a id="add_to_cart" class="button"><?php echo $button_cart; ?></a>
                            <a href="<?php echo $product_url; ?>" target="_top" class="button"><?php echo $kuler->translate($kuler->getSkinOption('more_details_button_text')); ?></a>
                        </div>
                    </div>
                    <div class="tab-content">
                        <?php if ($limit = $kuler->getSkinOption('product_description_limit')) { ?>
                        <?php echo substr(strip_tags($description), 0, $limit) . "..."; ?>
                        <?php } else { ?>
                        <?php echo $description; ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
	</div>

</div>
<script>
	$('#add_to_cart').on('click', function (evt) {
		evt.preventDefault();

		parent.window.addToCart(<?php echo $product_id; ?>)
	});

	$('.image-additional a').on('click', function () {
		$('#main-image').attr('src', this.href);

		return false;
	});
</script>
<!-- {BODY_SCRIPTS} -->
</body>
</html>