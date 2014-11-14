<div id="kbm-recent-article-<?php echo $module; ?>" class="kbm-recent-article">
    <div class="box kuler-module">
	    <?php if ($show_title) { ?>
        <div class="box-heading"><span><?php echo $title; ?></span></div>
	    <?php } ?>
        <div class="box-content">
            <ul class="articles row">
	            <?php foreach ($articles as $article) { ?>
                <li class="col-lg-12 wow fadeInUp" data-wow-delay="0.1s" data-wow-offset="100">
	                <div>
                    <?php if ($product_featured_image) { ?>
                      <div class="image">
	                      <img src="<?php echo $article['featured_image_thumb']; ?>" class="avatar" alt="" />
	                      <div class="link">
		                      <a href="<?php echo $article['link']; ?>" class="article-title"><i class="fa fa-link"></i></a>
	                      </div>
                      </div>
                    <?php } ?>
                    <a href="<?php echo $article['link']; ?>" class="article-title"><?php echo $article['name']; ?></a>
                    <span class="date"><?php echo $article['date_added_formatted']; ?></span>
                    <?php if ($product_description) { ?>
                      <p><?php echo $article['description']; ?></p>
                    <?php } ?>
	                </div>
                </li>
	            <?php } ?>
            </ul>
        </div>
    </div>
</div>