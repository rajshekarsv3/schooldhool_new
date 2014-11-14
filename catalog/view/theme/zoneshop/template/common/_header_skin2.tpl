<?php
$kuler->language->load('kuler/zoneshop');
?>
<div id="container">
	<div class="header-top">
		<?php
		$modules = Kuler::getInstance()->getModules('header_top');
		if ($modules) {
			echo implode('', $modules);
		}
		?>
	</div>
	<div id="top-bar">
		<div class="container">
			<div class="row">
				<div id="welcome" class="col-lg-7">
					<?php if (!$logged) { ?>
						<?php echo $text_welcome; ?>
					<?php } else { ?>
						<?php echo $text_logged; ?>
					<?php } ?>
				</div>
				<div class="col-lg-5">
					<div class="extra">
						<?php echo $language; ?>
						<?php echo $currency; ?>
					</div><!--/.extra-->
					<div class="col-sm-12 links">
						<span><?php echo $kuler->language->get('text_my_accounts'); ?></span>
						<div>
							<a href="<?php echo $wishlist; ?>" id="wishlist-total"><?php echo $text_wishlist; ?></a>
							<a href="<?php echo $account; ?>"><?php echo $text_account; ?></a>
							<a href="<?php echo $checkout; ?>"><?php echo $text_checkout; ?></a>
						</div>
					</div>
				</div>
			</div>
		</div><!--/.container-->
	</div><!--/#top-bar-->
	<div id="header">
		<div class="container">
			<div class="row">
				<div class="col-md-2">
					<?php if ($logo) { ?>
						<div id="logo">
							<a href="<?php echo $home; ?>">
								<img src="<?php echo $logo; ?>" title="<?php echo $name; ?>" alt="<?php echo $name; ?>" />
							</a>
						</div>
					<?php } ?>
				</div>
				<div class="col-md-10">
					<div class="col-lg-12 second-row">
						<div class="row">
							<div class="col-lg-9 header-extra-info">
								<div class="col-lg-9 header-extra-info">
									<?php
									$modules = Kuler::getInstance()->getModules('header_extra_info');
									if ($modules) {
										echo implode('', $modules);
									}
									?>
								</div>
							</div>
							<div class="col-lg-3 shop">
								<?php echo $cart; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="navigation">
			<div class="container">
				<div class="row">
					<div class="col-lg-9">
			      <span id="btn-mobile-toggle">
				      <?php echo $kuler->translate($kuler->getSkinOption('mobile_menu_title')); ?>
				    </span>
						<?php
						$modules = Kuler::getInstance()->getModules('menu');
						if ($modules) {
							echo implode('', $modules);
						}else{
							?>
							<?php if ($kuler->getSkinOption('multi_level_default_menu')) { $categories = $kuler->getRecursiveCategories(); } ?>
							<div id="menu" class="container">
								<div class="row">
									<ul class="mainmenu">
										<li style="display: none;"><a><?php echo $kuler->translate($kuler->getSkinOption('mobile_menu_title')); ?></a></li>
										<li class="item"><a href="<?php echo $base; ?>" <?php if ($kuler->getSkinOption('home_icon_type') == 'icon') { ?> class="home-icon" <?php } ?>><?php echo $kuler->language->get('text_home') ?></a></li>
										<?php foreach ($categories as $category) { ?>
											<li><a href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a>
												<?php if ($category['children']) { ?>
													<div>
														<?php for ($i = 0; $i < count($category['children']);) { ?>
															<ul>
																<?php $j = $i + ceil(count($category['children']) / $category['column']); ?>
																<?php for (; $i < $j; $i++) { ?>
																	<?php if (isset($category['children'][$i])) { ?>
																		<li><a href="<?php echo $category['children'][$i]['href']; ?>"><?php echo $category['children'][$i]['name']; ?></a>
																			<?php if (!empty($category['children'][$i]['children'])) { ?>
																				<?php echo renderSubMenuRecursive($category['children'][$i]['children']); ?>
																			<?php } ?>
																		</li>
																	<?php } ?>
																<?php } ?>
															</ul>
														<?php } ?>
													</div>
												<?php } ?>
											</li>
										<?php } ?>
									</ul>
								</div><!--/.container-->
							</div><!--/#menu-->
						<?php } ?>
					</div><!-- .navigation-->
					<div class="col-lg-3">
						<?php if ($kuler->getSkinOption('live_search_status')) { ?>
							<?php include(DIR_TEMPLATE . Kuler::getInstance()->getTheme() . '/template/common/_live_search.tpl'); ?>
						<?php } else { ?>
							<div id="search">
								<div class="container">
									<input type="text" name="search" placeholder="<?php echo $text_search; ?>" value="<?php echo $search; ?>" />
								</div>
								<div class="button-search"></div>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div><!--.navigation-->
	</div>
	<?php
	function renderSubMenuRecursive($categories) {
		$html = '<ul class="sublevel">';

		foreach ($categories as $category)
		{
			$parent = !empty($category['children']) ? ' parent' : '';
			$active = !empty($category['active']) ? ' active' : '';
			$html .= sprintf("<li class=\"item$parent $active\"><a href=\"%s\">%s</a>", $category['href'], $category['name']);

			if (!empty($category['children']))
			{
				$html .= '<span class="btn-expand-menu"></span>';
				$html .= renderSubMenuRecursive($category['children']);
			}

			$html .= '</li>';
		}

		$html .= '</ul>';

		return $html;
	}
	?>
	<?php if($modules = Kuler::getInstance()->getModules('vertical_menu')||$modules = Kuler::getInstance()->getModules('promo')){ ?>
		<div class="container top">
			<div class="row">
				<?php
				$promo_col_class = 'col-md-12';

				$modules = Kuler::getInstance()->getModules('vertical_menu');
				if ($modules) {
					echo '<div class="col-md-3">' . implode('', $modules) . '</div>';
					$promo_col_class = 'col-md-9';
				}
				?>
				<?php
				$modules = Kuler::getInstance()->getModules('promo');
				if ($modules) {
					echo '<div class="' . $promo_col_class . '">' . implode('', $modules) . '</div>';
				}
				?>
			</div>
		</div>
	<?php } ?>