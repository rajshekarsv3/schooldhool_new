<div id="cart">
  <div class="heading">
    <h4><?php echo $heading_title; ?></h4>
    <a><span id="cart-total"><?php echo $text_items; ?></span></a></div>
  <div class="wrapper">
    <div class="content">
      <div class="container">
        <?php if ($products || $vouchers) { ?>
          <div class="mini-cart-info">
            <ul class="row">
              <?php foreach ($products as $product) { ?>
                <li class="col-sm-12">
                  <div class="row">
                    <div class="col-sm-3 image">
                      <?php if ($product['thumb']) { ?>
                        <a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" /></a>
                      <?php } ?>
                    </div>
                    <div class="col-sm-7 name">
                      <a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
                      <div>
                        <?php foreach ($product['option'] as $option) { ?>
                          - <small><?php echo $option['name']; ?> <?php echo $option['value']; ?></small><br />
                        <?php } ?>
                      </div>
                      <div class="quantity">x&nbsp;<?php echo $product['quantity']; ?></div>
                      <div class="total"><?php echo $product['total']; ?></div>
                    </div>
                    <div class="col-sm-2 remove"><img src="catalog/view/theme/default/image/remove-small.png" alt="<?php echo $button_remove; ?>" title="<?php echo $button_remove; ?>" onclick="(getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') ? location = 'index.php?route=checkout/cart&remove=<?php echo $product['key']; ?>' : $('#cart').load('index.php?route=module/cart&remove=<?php echo $product['key']; ?>' + ' #cart > *');" /></div>
                  </div>
                </li>
              <?php } ?>
            </ul>
            <?php foreach ($vouchers as $voucher) { ?>
              <ul>
                <li class="image"></li>
                <li class="name"><?php echo $voucher['description']; ?></li>
                <li class="quantity">x&nbsp;1</li>
                <li class="total"><?php echo $voucher['amount']; ?></li>
                <li class="remove"><img src="catalog/view/theme/default/image/remove-small.png" alt="<?php echo $button_remove; ?>" title="<?php echo $button_remove; ?>" onclick="(getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') ? location = 'index.php?route=checkout/cart&remove=<?php echo $voucher['key']; ?>' : $('#cart').load('index.php?route=module/cart&remove=<?php echo $voucher['key']; ?>' + ' #cart > *');" /></li>
              </ul>
            <?php } ?>
          </div>
          <div class="row mini-cart-total">
              <?php foreach ($totals as $total) { ?>
                <div class="col-sm-12">
                  <span class="right"><b><?php echo $total['title']; ?>:</b></span>
                  <span class="right"><?php echo $total['text']; ?></span>
                </div>
              <?php } ?>
          </div>
          <div class="checkout">
            <a class="view" href="<?php echo $cart; ?>"><?php echo $text_cart; ?></a>
            <a class="check" href="<?php echo $checkout; ?>"><?php echo $text_checkout; ?></a>
          </div>
        <?php } else { ?>
          <div class="empty"><?php echo $text_empty; ?></div>
        <?php } ?>
      </div>
    </div>
  </div>
</div>
