<?php
$kuler = Kuler::getInstance();
$theme = $kuler->getTheme();

$kuler->addStyle(array(
  "catalog/view/javascript/jquery/ui/themes/ui-lightness/jquery-ui-1.8.16.custom.css",
	"catalog/view/theme/$theme/stylesheet/stylesheet.css",
	"catalog/view/theme/$theme/stylesheet/animate.min.css",
	"catalog/view/theme/$theme/stylesheet/font-awesome.min.css"
));

$kuler->addScript(array(
	'catalog/view/javascript/jquery/jquery-1.7.1.min.js',
	'catalog/view/javascript/jquery/ui/jquery-ui-1.8.16.custom.min.js',
	'catalog/view/javascript/common.js',
	"catalog/view/theme/$theme/js/lib/jquery.magnific-popup.min.js",
	"catalog/view/theme/$theme/js/utils.js"
));

?>
<!DOCTYPE html>
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<head>
<meta charset="UTF-8" />
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="apple-mobile-web-app-title" content="Kuler Themes">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no, minimal-ui">
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>" />
<?php if ($description) { ?>
<meta name="description" content="<?php echo $description; ?>" />
<?php } ?>
<?php if ($keywords) { ?>
<meta name="keywords" content="<?php echo $keywords; ?>" />
<?php } ?>
<?php if ($icon) { ?>
<link href="<?php echo $icon; ?>" rel="icon" />
<?php } ?>
<?php foreach ($links as $link) { ?>
<link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
<?php } ?>
<!-- {STYLES} -->
<!-- {SCRIPTS} -->
<?php if ($stores) { ?>
<script type="text/javascript"><!--
$(document).ready(function() {
<?php foreach ($stores as $store) { ?>
$('body').prepend('<iframe src="<?php echo $store; ?>" style="display: none;"></iframe>');
<?php } ?>
});
//--></script>
<?php } ?>
<?php echo $google_analytics; ?>
<?php if($direction == "rtl") { ?>
  <link rel="stylesheet" type="text/css" href="catalog/view/theme/<?php echo $kuler->getTheme() ?>/stylesheet/rtl.css" media="screen">
<?php } ?>
</head>
<body class="<?php echo $kuler->getBodyClass(); ?><?php echo ((empty($_GET['_route_']) && empty($_GET['route'])) || (isset($_GET['route']) && $_GET['route'] == 'common/home')) ? ' home' : '' ?> wow fadeInUp" data-wow-delay="50">
  <div id="notification">
  </div><!--/#notificaton-->
<?php if ($kuler->getSkinOption('show_facebook')) { ?>
<?php echo $kuler->getFacebookScriptCode(); ?>
<?php } ?>
<?php if ($kuler->getRootSkin() == 'skin1' || $kuler->getRootSkin() == 'skin3') { ?>
  <?php include(DIR_TEMPLATE . Kuler::getInstance()->getTheme() . '/template/common/_header_skin1.tpl'); ?>
<?php } elseif ($kuler->getRootSkin() == 'skin2'){ ?>
  <?php include(DIR_TEMPLATE . Kuler::getInstance()->getTheme() . '/template/common/_header_skin2.tpl'); ?>
<?php } ?>