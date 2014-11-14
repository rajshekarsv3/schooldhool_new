<?php
$kuler = Kuler::getInstance();
?>
<div class="kis-module<?php if (!empty($settings['css_class'])) echo ' ' . $settings['css_class']; ?>" id="kis-module-<?php echo $module; ?>">
	<div class="box kuler-module">
		<?php if (!empty($settings['show_title'])) { ?>
			<div class="box-heading"><span><?php echo $kuler->translate($settings['title']); ?></span></div>
		<?php } ?>
		<div class="box-content">
			<iframe src="http://widget.stagram.com/in/<?php if(!empty($settings['username'])) { echo $settings['username']; } ?>/?s=<?php echo $settings['thumbsize'] ?>&w=<?php echo $settings['horizontal'] ?>&h=<?php echo $settings['vertical'] ?>&b=0" allowtransparency="true" frameborder="0" scrolling="no" style="border:none;overflow:hidden;width:<?php echo (($settings['horizontal']*$settings['thumbsize'])+($settings['horizontal']*15)); ?>px; height:<?php echo (($settings['vertical']*$settings['thumbsize'])+($settings['vertical']*15)); ?>px" ></iframe>
		</div>
	</div>
</div>