<?php $kuler = Kuler::getInstance(); ?>
<?php echo $header; ?>
  <div class="container">
	  <div class="breadcrumb">
		  <div class="container">
			  <div class="row">
				  <div class="col-md-8">
					  <h2><?php echo $heading_title; ?></h2>
				  </div>
				  <div class="col-md-4">
					  <ul>
						  <?php foreach ($breadcrumbs as $breadcrumb) { ?>
							  <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
						  <?php } ?>
					  </ul>
				  </div>
			  </div>
		  </div>
	  </div><!--/.breadcrumb-->
    <div class="row">
      <?php echo $column_left; ?>
      <?php if ($column_left && $column_right) { ?>
        <?php $class = 'col-md-6'; ?>
      <?php } elseif ($column_left || $column_right) { ?>
        <?php $class = 'col-md-9'; ?>
      <?php } else { ?>
        <?php $class = 'col-md-12'; ?>
      <?php } ?>
      <div id="content" class="<?php echo $class; ?>">
        <?php echo $content_top; ?>
        <?php if ($products) { ?>
          <div class="container product-filter">
            <div class="row">
              <div class="col-lg-3 col-sm-6 display">
                <?php echo $text_list; ?>
                <a onclick="display('grid');"><?php echo $text_grid; ?></a>
              </div>
              <div class="col-lg-3 col-sm-6 product-compare">
                <a href="<?php echo $compare; ?>" id="compare-total"><?php echo $text_compare; ?></a>
              </div>
              <div class="col-lg-2 col-sm-6 limit"><b><?php echo $text_limit; ?></b>
                <select onchange="location = this.value;">
                  <?php foreach ($limits as $limits) { ?>
                    <?php if ($limits['value'] == $limit) { ?>
                      <option value="<?php echo $limits['href']; ?>" selected="selected"><?php echo $limits['text']; ?></option>
                    <?php } else { ?>
                      <option value="<?php echo $limits['href']; ?>"><?php echo $limits['text']; ?></option>
                    <?php } ?>
                  <?php } ?>
                </select>
              </div>
              <div class="col-lg-4 col-sm-6 sort"><b><?php echo $text_sort; ?></b>
                <select onchange="location = this.value;">
                  <?php foreach ($sorts as $sorts) { ?>
                    <?php if ($sorts['value'] == $sort . '-' . $order) { ?>
                      <option value="<?php echo $sorts['href']; ?>" selected="selected"><?php echo $sorts['text']; ?></option>
                    <?php } else { ?>
                      <option value="<?php echo $sorts['href']; ?>"><?php echo $sorts['text']; ?></option>
                    <?php } ?>
                  <?php } ?>
                </select>
              </div>
            </div>
          </div>
          <div class="product-list category-product-list">
            <?php foreach ($products as $product) { ?>
              <?php if ($column_left && $column_right) { ?>
                <?php $class = 'col-md-6 col-sm-6'; ?>
              <?php } elseif ($column_left || $column_right) { ?>
                <?php $class = 'col-md-4 col-sm-6'; ?>
              <?php } else { ?>
                <?php $class = 'col-lg-20 col-md-6 col-sm-6'; ?>
              <?php } ?>
              <div class="<?php echo $class; ?>">
	              <div class="<?php echo $class; ?>">
		              <?php if ($product['thumb']) { ?>
			              <div class="image"><a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" title="<?php echo $product['name']; ?>" alt="<?php echo $product['name']; ?>" /></a></div>
		              <?php } ?>
		              <?php if ($images = $kuler->getProductImages($product['product_id'])) { ?>
			              <?php if(!$kuler->mobile->isMobile() && $kuler->getSkinOption('enable_swap_image')){ ?>
				              <?php $size = $kuler->getImageSizeByPath($product['thumb']); ?>
				              <span class="hover"><img src="<?php echo $kuler->resizeImage($images[0], $size['width'], $size['height']); ?>" alt="<?php echo $product['name']; ?>"/></span>
			              <?php } ?>
		              <?php } ?>
		              <?php if ($product['special']) { ?>
			              <div class="sale">-<?php echo $kuler->calculateSalePercent($product['special'], $product['price']); ?>%</div>
		              <?php } ?>
		              <div class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></div>
		              <div class="description"><?php echo $product['description']; ?></div>
		              <?php if ($product['price']) { ?>
			              <div class="price">
				              <?php if (!$product['special']) { ?>
					              <?php echo $product['price']; ?>
				              <?php } else { ?>
					              <span class="price-new"><?php echo $product['special']; ?></span> <span class="price-old"><?php echo $product['price']; ?></span>
				              <?php } ?>
			              </div>
		              <?php } ?>
		              <?php if ($product['rating']) { ?>
			              <div class="rating"><img src="catalog/view/theme/default/image/stars-<?php echo $product['rating']; ?>.png" alt="<?php echo $product['reviews']; ?>" /></div>
		              <?php } ?>
		              <div class="cart">
			              <a onclick="addToCart('<?php echo $product['product_id']; ?>');"><span><?php echo $button_cart; ?></span></a>
		              </div>
		              <div class="wishlist"><a onclick="addToWishList('<?php echo $product['product_id']; ?>');"><?php echo $button_wishlist; ?></a></div>
		              <div class="compare"><a onclick="addToCompare('<?php echo $product['product_id']; ?>');"><?php echo $button_compare; ?></a></div>
		              <?php if (Kuler::getInstance()->getSkinOption('show_quick_view')) { ?>
			              <div class="quick-view"><a href="<?php echo Kuler::getInstance()->getQuickViewUrl($product); ?>" class="quick_view"><?php echo $kuler->translate($kuler->getSkinOption('view_button_text')); ?></a> </div>
		              <?php } ?>
	              </div>
              </div>
            <?php } ?>
          </div>
          <div class="pagination"><?php echo $pagination; ?></div>
        <?php } else { ?>
          <div class="content"><?php echo $text_empty; ?></div>
          <div class="buttons">
            <div class="right"><a href="<?php echo $continue; ?>" class="button"><?php echo $button_continue; ?></a></div>
          </div>
        <?php }?>
        <?php echo $content_bottom; ?></div>
      <?php echo $column_right; ?>
    </div>
  </div>

<script type="text/javascript"><!--
	function display(view) {
		if (view == 'list') {
			$('.category-product-list.product-grid').attr('class', 'product-list category-product-list');
			$('.category-product-list.product-list > div').each(function(index, element) {
				html = '<div class="row">';
				html += '<div class="col-md-8 col-sm-6 right">';
				html += '<div class="row">';
				html += '<div class="col-md-4">';
				var image = $(element).find('.image').html();
				html += '<div class="thumb">';
				if (image != null) {
					html += '<div class="image">' + image + '</div>';
				}
				<?php if ($kuler->getSkinOption('enable_swap_image')) { ?>
				var $hoverImage;
				if (($hoverImage = $(element).find('.hover')) && $hoverImage.length) {
					html += '<div class="hover">' + $hoverImage.html() + '</div>';
				}
				<?php } ?>
				html += '<div class="details">';
				<?php if ($kuler->getSkinOption('show_quick_view')) { ?>
				html += '<div class="quick-view">' + $(element).find('.quick-view').html() + '</div>';
				<?php } ?>
				html += '</div>';
				html += '</div>';
				html += '</div>';
				html += '<div class="col-md-8">';
				html += '  <div class="name">' + $(element).find('.name').html() + '</div>';

				html += '  <div class="description">' + $(element).find('.description').html() + '</div>';
				html += '</div>';
				html += '</div>';
				html += '</div>';
				html += '<div class="col-md-4 col-sm-6 left">';
				html += '<div>';
				var price = $(element).find('.price').html();
				if (price != null) {
					html += '<div class="price">' + price  + '</div>';
				}
				var rating = $(element).find('.rating').html();
				if (rating != null) {
					html += '<div class="rating">' + rating + '</div>';
				}
				html += '</div>';
				html += '<div class="inf">';
				html += '<div class="row">';
				html += '<div class="col-md-8">';
				html += '<div class="cart">' + $(element).find('.cart').html() + '</div>';
				html += '</div>';
				html += '<div class="col-md-2">';
				html += '<div class="wishlist">' + $(element).find('.wishlist').html() + '</div>';
				html += '</div>';
				html += '<div class="col-md-2">';
				html += '<div class="compare">' + $(element).find('.compare').html() + '</div>';
				html += '</div>';
				html += '</div>';
				html += '</div>';
				html += '</div>';
				html += '</div>';
				$(element).html(html);
			});
			$('.display').html('<span class="list"><?php echo $text_list; ?></span> <a class="grid" onclick="display(\'grid\');"><?php echo $text_grid; ?></a>');
			$.totalStorage('display', 'list');
		} else {
			$('.category-product-list.product-list').attr('class', 'product-grid category-product-list');
			$('.category-product-list.product-grid > div').each(function(index, element) {
				html = '';
				var image = $(element).find('.image').html();
				html += '<div class="wrapper">';
				html += '<div class="thumb">';
				if (image != null) {
					html += '<div class="image">' + image + '</div>';
				}
				<?php if ($kuler->getSkinOption('enable_swap_image')) { ?>
				var $hoverImage;
				if (($hoverImage = $(element).find('.hover')) && $hoverImage.length) {
					html += '<span class="hover">' + $hoverImage.html() + '</span>';
				}
				<?php } ?>
				html += '<div class="details">';
				<?php if ($kuler->getSkinOption('show_quick_view')) { ?>
				html += '<div class="quick-view">' + $(element).find('.quick-view').html() + '</div>';
				<?php } ?>
				html += '</div>';
				html += '</div>';
				html += '<div class="row">';
				html += '<div class="col-md-8">';
				html += '<div class="name">' + $(element).find('.name').html() + '</div>';
				var rating = $(element).find('.rating').html();
				if (rating != null) {
					html += '<div class="rating">' + rating + '</div>';
				}
				html += '</div>';
				html += '<div class="col-md-4">';
				var price = $(element).find('.price').html();
				if (price != null) {
					html += '<div class="price">' + price  + '</div>';
				}
				html += '</div>';
				html += '</div>';
				html += '<div class="inf row">';
				html += '<div class="col-md-8">';
				html += '<div class="cart">' + $(element).find('.cart').html() + '</div>';
				html += '</div>';
				html += '<div class="col-md-2">';
				html += '<div class="wishlist">' + $(element).find('.wishlist').html() + '</div>';
				html += '</div>';
				html += '<div class="col-md-2">';
				html += '<div class="compare">' + $(element).find('.compare').html() + '</div>';
				html += '</div>';
				html += '</div>';
				html += '<div class="description">' + $(element).find('.description').html() + '</div>';
				html += '</div>';
				$(element).html(html);
			});
			$('.display').html('<a class="list" onclick="display(\'list\');"><?php echo $text_list; ?></a> <span class="grid"><?php echo $text_grid; ?></span>');

			$.totalStorage('display', 'grid');
		}

		initQuickView('.category-page .quick-view a');
	}

view = $.totalStorage('display');

if (view) {
	display(view);
} else {
	display('list');
}
//--></script> 
<?php echo $footer; ?>