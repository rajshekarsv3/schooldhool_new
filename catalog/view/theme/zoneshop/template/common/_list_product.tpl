<?php $kuler = Kuler::getInstance(); ?>
<div>
	<div class="col-md-5 col-sm-6 left">
		<div class="thumb">
			<?php if ($product['thumb']) { ?>
				<div class="image"><a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" /></a></div>
			<?php } else { ?>
				<div class="image"><span class="no-image"><img src="image/no_image.jpg" alt="<?php echo $product['name']; ?>" /></span></div>
			<?php } ?>
			<div class="details">
				<?php if(isset($setting['add']) && $setting['add']) { ?>
					<div class="cart"><a onclick="addToCart('<?php echo $product['product_id']; ?>');"><span><?php echo $button_cart; ?></span></a></div>
				<?php } ?>
				<?php if(isset($setting['wishlist']) && $setting['wishlist']) { ?>
					<div class="wishlist"><a onclick="addToWishList('<?php echo $product['product_id']; ?>');"><span><?php echo $button_wishlist; ?></span></a></div>
				<?php } ?>
				<?php if(isset($setting['compare']) && $setting['compare']) { ?>
					<div class="compare"><a onclick="addToCompare('<?php echo $product['product_id']; ?>');"><span><?php echo $button_compare; ?></span></a></div>
				<?php } ?>
			</div>
		</div>
	</div>
	<div class="col-md-7 col-sm-6 right">
		<?php if(isset($setting['name']) && $setting['name']) { ?>
			<div class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></div>
		<?php } ?>
		<?php if(isset($setting['price']) && $setting['price']) { ?>
			<div class="price">
				<?php if (!$product['special']) { ?>
					<div><span class="price-fixed"><?php echo $product['price']; ?></span></div>
				<?php } else { ?>
					<div class="sale">-<?php echo $kuler->calculateSalePercent($product['special'], $product['price']); ?>%</div>
					<div class="special-price">
						<span class="price-fixed"><?php echo $product['special']; ?></span>
						<span class="price-old"><?php echo $product['price']; ?></span>
					</div>
				<?php } ?>
			</div>
		<?php } ?>
		<?php if(isset($setting['rating']) && $setting['rating']) { ?>
			<div class="rating"><img src="catalog/view/theme/<?php echo $kuler->getTheme() ?>/image/stars-<?php echo $product['rating']; ?>.png" alt="<?php echo $product['reviews']; ?>" /></div>
		<?php } ?>
		<?php if(isset($setting['description']) && $setting['description']) { ?>
			<div class="description"><?php echo $product['description']; ?></div>
		<?php } ?>
	</div>
</div>