<?php echo $header; ?>
<div id="content">

  	<div class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
			<?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
		<?php } ?>
  	</div>

  	<?php if ($error_warning) { ?>
  		<div class="warning"><?php echo $error_warning; ?></div>
  	<?php } ?>

  	<div class="box">

		<div class="heading">
	  		<h1><img src="view/image/module.png" alt="" /> <?php echo $heading_title; ?></h1>
	  		<div class="buttons">
	  			<a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a>
	  			<a href="<?php echo $cancel; ?>" class="button"><?php echo $button_cancel; ?></a>
	  		</div>
		</div>

		<div class="content">
	  		<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
	        	<table class="form">
					<tr>
						<td><?php echo $template_name;?></td>
						<td>
							<input type="text" name="template_name" style="width: 400px;" value="<?php echo (isset($entry_name)) ? $entry_name : ''; ?>"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $template_content;?></td>
						<td>
							<textarea name="template_content" style="width: 400px;height: 120px;" /><?php echo (isset($entry_content)) ? $entry_content : ''; ?></textarea>
							<input type="hidden" name="language_id" value="<?php echo (isset($_GET['language'])) ? $_GET['language'] : $entry_language; ?>"/>
						</td>
					</tr>
		      	</table>
	  		</form>
		</div>

  	</div>
</div>
<script type="text/javascript"><!--
    $('#htabs a').tabs();
//--></script>
<?php echo $footer; ?>