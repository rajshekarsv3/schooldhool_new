<div class="box special">
  <div class="box-heading"><?php echo $heading_title; ?></div>
  <div class="box-content">
    <div class="product-grid">
      <?php foreach ($products as $product) { ?>
        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
          <?php if ($product['thumb']) { ?>
            <div class="thumb">
              <a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>"/></a>
              <span class="hover"></span>
              <div class="details">
                <div class="cart"><a onclick="addToCart('<?php echo $product['product_id']; ?>');"><span><?php echo $button_cart; ?></span></a></div>
                <div class="wishlist"><a onclick="addToWishList('<?php echo $product['product_id']; ?>');"><span><?php echo $button_wishlist; ?></span></a></div>
                <div class="compare"><a onclick="addToCompare('<?php echo $product['product_id']; ?>');"><span><?php echo $button_compare; ?></span></a></div>
              </div>
            </div>
          <?php } else { ?>
            <div class="thumb no-image">
              <img src="image/no_image.jpg" alt="<?php echo $product['name']; ?>" />
            </div>
          <?php } ?>
          <div class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></div>
          <div class="description"><?php echo $product['description']; ?></div>
          <div class="price">
            <?php if (!$product['special']) { ?>
              <div><span class="price-fixed"><?php echo $product['price']; ?></span></div>
            <?php } else { ?>
              <div class="special-price">
                <div class="sale">-<?php echo $kuler->calculateSalePercent($product['special'], $product['price']); ?>%</div>
                <span class="price-fixed"><?php echo $product['special']; ?></span><span class="price-old"><?php echo $product['price']; ?></span>
              </div>
            <?php } ?>
          </div>
          <div class="rating"><img src="catalog/view/theme/<?php echo $kuler->getTheme() ?>/image/stars-<?php echo $product['rating']; ?>.png" alt="<?php echo $product['reviews']; ?>" /></div>
        </div>
      <?php } ?>
    </div>
  </div>
</div>
