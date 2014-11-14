<?php echo $header; ?>
	<div class="breadcrumb">
		<div class="container">
			<div class="row">
				<div class="col-md-8">
					<h2><?php echo $heading_title; ?></h2>
				</div>
				<div class="col-md-6">
					<ul>
						<?php foreach ($breadcrumbs as $breadcrumb) { ?>
							<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
						<?php } ?>
					</ul>
				</div>
			</div>
		</div>
	</div><!--/.breadcrumb-->
	<div class="container">
		<div class="row">
			<?php echo $column_left; ?>
			<?php if ($column_left && $column_right) { ?>
				<?php $class = 'col-md-6'; ?>
			<?php } elseif ($column_left || $column_right) { ?>
				<?php $class = 'col-md-9'; ?>
			<?php } else { ?>
				<?php $class = 'col-md-12'; ?>
			<?php } ?>
			<div id="content" class="<?php echo $class; ?> blog-column-<?php echo $column; ?>">
				<?php echo $content_top; ?>
				<?php if ($articles) { ?>
					<div class="article-list">
						<?php foreach ($articles as $article) { ?>
							<div class="article">
								<div class="article-header">
									<h3><a href="<?php echo $article['link']; ?>"><?php echo $article['name']; ?></a></h3>
									<div class="article-extra-info">
										<?php if ($article['display_author']) { ?>
											<?php echo '<span class="author vcard">'; echo _t('text_by_x', '<a rel="author">'. $article['author_name'] .'</a></span>'); ?>
										<?php } ?>

										<?php if ($article['display_category'] && $article['categories']) { ?>
											<?php echo '<span class="category">'; ?>
											<?php echo _t('text_in'); ?>
											<?php $article_links = array(); ?>
											<?php foreach ($article['categories'] as $article_category) {
												$article_links[] = sprintf('<a href="%s">%s</a>', $article_category['link'], $article_category['name']);
											} ?>
											<?php echo implode(', ', $article_links); ?>
											<?php echo '</span>'; ?>
										<?php } ?>

										<?php if ($article['display_date']) { ?>
											<?php echo '<span class="entry-date">' ; echo _t('text_on'); echo '<time>'; ?>  <?php echo $article['date_added_formatted']; echo '</time></span>'; ?>.
										<?php } ?>
									</div>
								</div>
								<div class="article-content">
									<a href="<?php echo $article['link']; ?>" class="article-image"><img src="<?php echo $article['featured_image_thumb']; ?>" /></a>
									<p>
										<?php echo $article['description']; ?>
									</p>
									<div class="article-read-more">
										<a class="read-more"  href="<?php echo $article['link']; ?>"><?php echo _t('text_read_more'); ?></a>
									</div>
								</div>
							</div>
						<?php } ?>
					</div>
					<div class="pagination">
						<?php echo $pagination; ?>
					</div>
				<?php } else { ?>
					<p><?php echo _t('text_there_is_no_article_that_match_the_search_criteria'); ?></p>
				<?php } ?>
			</div>
			<?php echo $column_right; ?>
			<?php echo $content_bottom; ?>
		</div>
	</div>
<?php echo $footer; ?>