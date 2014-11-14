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
	<?php if ($success) { ?>
		<div class="success"><?php echo $success; ?></div>
	<?php } ?>
  	<div class="box">

		<div class="heading">
	  		<h1><img src="view/image/module.png" alt="" /> <?php echo $heading_title; ?></h1>
	  		<div class="buttons">
	  			<a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a>
	  			<a onclick="$('#continue').val('1');$('#form').submit();" class="button"><?php echo $button_save_continue; ?></a>
	  			<a href="<?php echo $cancel; ?>" class="button"><?php echo $button_cancel; ?></a>
	  		</div>
		</div>

		<div class="content">

	      	<div id="htabs" class="htabs">
	        	<a id="tab-general-link" href="#tab-general"><?php echo $tab_general; ?></a>
	        	<a id="tab-templates-link" href="#tab-templates"><?php echo $tab_templates; ?></a>
	        	<a id="tab-triggers-link" href="#tab-triggers"><?php echo $tab_triggers; ?></a>
	        	<a id="tab-sendmessage-link" href="#tab-sendmessage"><?php echo $tab_sendmessage; ?></a>
	        	<a id="tab-logs-link" href="#tab-logs"><?php echo $tab_logs; ?></a>
	      	</div>

	  		<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<div id="tab-general">
					<h2><?php echo $heading_account; ?></h2>
		        	<table class="form">
			          	<tr>
			            	<td><?php echo $text_credits; ?></td>
			            	<td>
								<span style="font-size:28px">
									<?php echo $entry_credits; ?>
								</span>
			            	</td>
			          	</tr>
			          <tr>
			            <td><?php echo $text_apikey; ?></td>
			            <td><input type="text" name="textplode_apikey" value="<?php echo $textplode_apikey; ?>" /></td>
			          </tr>
			        </table>
			        <h2><?php echo $heading_general; ?></h2>
			       	<table class="form">
			          	<tr>
			            	<td><?php echo $text_status; ?></td>
			            	<td>
			            		<select name="textplode_status">
			                		<?php if ($textplode_status) { ?>
			                			<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
			                			<option value="0"><?php echo $text_disabled; ?></option>
			                		<?php } else { ?>
			                			<option value="1"><?php echo $text_enabled; ?></option>
			                			<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
			                		<?php } ?>
			              		</select>
			              	</td>
			          	</tr>
			          	<tr>
			            	<td><?php echo $text_from_name; ?></td>
			            	<td>
			            		<input type="text" maxlength="11" name="textplode_from_name" value="<?php echo $textplode_from_name; ?>" />
			            		<?php echo $text_from_name_limit; ?>
			            	</td>
			          	</tr>
			          	<tr>
			            	<td><?php echo $text_admin_number; ?></td>
			            	<td>
			            		<input type="text" maxlength="11" name="textplode_admin_number" value="<?php echo $textplode_admin_number; ?>" />
			            	</td>
			          	</tr>
						<?php if($hasApiKey){ ?>
			          	<tr>
			            	<td><?php echo $text_sync_group; ?></td>
			            	<td>
			            		<select name="textplode_sync_group">
			            			<?php if($sync_group == 0){ ?>
			            				<option value="-1">New Group (<?=$store_name;?> Customers)</option>
			            			<?php } ?>
									<?php if(!empty($array_groups)){ ?>
										<?php foreach($array_groups as $group) { ?>
											<?php $contacts = ($group['count'] != 1) ? ' contacts)' : ' contact)'; ?>
											<?php if($group['group_id'] == $textplode_sync_group){ ?>
												<option selected="SELECTED" value="<?php echo $group['group_id']; ?>"><?php echo $group['group_name']; ?> <?php echo '(' . $group['count'] . $contacts;?></option>
											<? }else{ ?>
												<option value="<?php echo $group['group_id']; ?>"><?php echo $group['group_name']; ?> <?php echo '(' . $group['count'] . $contacts;?></option>
											<? } ?>
										<?php } ?>
									<? } ?>
								</select>
								<a id="sync" class="button" style="margin-left: 5px;" href="javascript:;"><?php echo $text_sync_now;?></a>
			            	</td>
			          	</tr>
						<?php } ?>
			          	<tr>
			            	<td><?php echo $text_opt_in_out; ?></td>
			            	<td>
			            		<select name="textplode_opt_in_out">
			                		<?php if ($textplode_opt_in_out == 1) { ?>
			                			<option value="1" selected="selected"><?php echo $text_opt_in; ?></option>
			                			<option value="-1"><?php echo $text_opt_out; ?></option>
			                			<option value="0"><?php echo $text_opt_disabled; ?></option>
			                		<?php } else if ($textplode_opt_in_out == -1){ ?>
			                			<option value="1"><?php echo $text_opt_in; ?></option>
			                			<option value="-1" selected="selected"><?php echo $text_opt_out; ?></option>
			                			<option value="0"><?php echo $text_opt_disabled; ?></option>
			                		<?php }else{ ?>
			                			<option value="1"><?php echo $text_opt_in; ?></option>
			                			<option value="-1"><?php echo $text_opt_out; ?></option>
			                			<option value="0" selected="selected"><?php echo $text_opt_disabled; ?></option>
			                		<?php } ?>
			              		</select>
			              	</td>
			          	</tr>
			        </table>
			        <h2><?php echo $heading_statistics; ?></h2>
			       	<table class="form">
		        		<tr>
			            	<td><?php echo $text_mobile_users; ?></td>
			            	<td>
								<span style="font-size:18px">
									<?php echo sprintf($text_mobile_users_format, $array_mobile_customers['mobile'], $array_mobile_customers['customers'], $array_mobile_customers['percentage']) ?>
								</span>
			            	</td>
			          	</tr>
			          	<?php if($textplode_opt_in_out != 0){ ?>
		        		<tr>
			            	<td><?php echo $text_enabled_users; ?></td>
			            	<td>
								<span style="font-size:18px">
									<?php echo sprintf($text_mobile_users_format, $array_mobile_customers['enabled'], $array_mobile_customers['customers'], ($array_mobile_customers['enabled']/$array_mobile_customers['customers'])*100) ?>
								</span>
			            	</td>
			          	</tr>
			          	<?php } ?>
			      	</table>
			    </div>
				<div id="tab-templates">
					<div id="template-languages" class="htabs">
						<?php foreach($array_languages as $language){ ?>
						<a href="#template-<?php echo $language['code'];?>">
							<img src="view/image/flags/<?php echo $language['image'];?>" title="<?php echo $language['name'];?>">
							<?php echo $language['name'];?>
						</a>
						<?php } ?>
					</div>
					<?php foreach($array_languages as $language){ ?>
						<div id="template-<?php echo $language['code']; ?>">
				        	<table id="module" class="list">
				        		<thead>
						          	<tr>
						            	<td class="left"><?php echo $text_template_name; ?></td>
						            	<td class="left"><?php echo $text_template_content; ?></td>
						            	<td>&nbsp;</td>
						          	</tr>
						          </thead>
						          <tbody>
						          	<?php foreach($array_templates as $template){ ?>
						          		<?php if($language['language_id'] == $template['language_id']){ ?>
											<tr>
					 							<td class="left"><?php echo $template['template_name']; ?></td>
												<td class="left"><?php echo $template['template_content']; ?></td>
												<td class="right">
													<a class="button" href="<?php echo $this->url->link('module/textplode/edit/&id=' . $template['template_id'], 'token=' . $this->session->data['token'], 'SSL'); ?>">Edit</a>
													<a class="button" href="<?php echo $this->url->link('module/textplode/delete/&id=' . $template['template_id'], 'token=' . $this->session->data['token'], 'SSL'); ?>">Delete</a>
												</td>
											</tr>
										<?php } ?>
						          	<?php } ?>
					          	</tbody>
					          	<tfoot>
					          		<td class="right" colspan="2">
					          			&nbsp;
					          		</td>
									<td class="right">
										<a class="button" href="<?php echo $this->url->link('module/textplode/create/', 'language=' . $language['language_id'] . '&token=' . $this->session->data['token'], 'SSL'); ?>">New</a>
									</td>
					          	</tfoot>
					      	</table>
				      	</div>
				    <?php } ?>
			    </div>
				<div id="tab-triggers">

							<div class="admin-notifications" style="display:none">
								<h2><?php echo $heading_admin; ?></h2>
					        	<table class="list">
					        		<thead>
					        			<tr>
					        				<td class="left"><?php echo $table_heading_status; ?></td>
					        				<td class="left"><?php echo $table_heading_template; ?></td>
					        				<td class="left"><?php echo $table_heading_active; ?></td>
					        			</tr>
					        		</thead>
					        		<tbody>
						        		<tr>
											<td class="left"><?php echo $text_new_order; ?></td>
											<td>
												<select name="textplode_template_new_order">
													<option value="-1"><?php echo $text_select;?></option>
													<?php
													foreach($array_templates as $template){
														if($template['template_id'] == $this->config->get('textplode_template_new_order')){
															echo '<option selected="SELECTED" value=' . $template['template_id'] . '>' . $template['template_name'] . '</option>';
														}else{
															echo '<option value=' . $template['template_id'] . '>' . $template['template_name'] . '</option>';
														}
													}
													?>
												</select>
											</td>
											<td>
												<input type="hidden" name="textplode_active_new_order" value="off"/>
												<?php if(isset($active_array["textplode_active_new_order"])){ ?>
													<?php if($active_array["textplode_active_new_order"] == "on"){ ?>
														<input checked="CHECKED" type="checkbox" name="textplode_active_new_order" />
													<?php }else{ ?>
														<input type="checkbox" name="textplode_active_new_order" />
													<?php } ?>
												<?php }else{ ?>
														<input type="checkbox" name="textplode_active_new_order" />
												<?php } ?>
											</td>
						        		</tr>
						        		<tr>
											<td class="left"><?php echo $text_new_customer; ?></td>
											<td>
												<select name="textplode_template_new_customer">
													<option value="-1"><?php echo $text_select;?></option>
													<?php
													foreach($array_templates as $template){
														if($template['template_id'] == $this->config->get('textplode_template_new_customer')){
															echo '<option selected="SELECTED" value=' . $template['template_id'] . '>' . $template['template_name'] . '</option>';
														}else{
															echo '<option value=' . $template['template_id'] . '>' . $template['template_name'] . '</option>';
														}
													}
													?>
												</select>
											</td>
											<td>
												<input type="hidden" name="textplode_active_new_customer" value="off"/>
												<?php if(isset($active_array["textplode_active_new_customer"])){ ?>
													<?php if($active_array["textplode_active_new_customer"] == "on"){ ?>
														<input checked="CHECKED" type="checkbox" name="textplode_active_new_customer" />
													<?php }else{ ?>
														<input type="checkbox" name="textplode_active_new_customer" />
													<?php } ?>
												<?php }else{ ?>
														<input type="checkbox" name="textplode_active_new_customer" />
												<?php } ?>
											</td>
						        		</tr>
						        	</tbody>
						      	</table>
						    </div>
					<h2><?php echo $heading_user; ?></h2>
					<div id="languages" class="htabs">
						<?php foreach($array_languages as $language){ ?>
						<a href="#<?php echo $language['code'];?>">
							<img src="view/image/flags/<?php echo $language['image'];?>" title="<?php echo $language['name'];?>">
							<?php echo $language['name'];?>
						</a>
						<?php } ?>
					</div>
					<? foreach($array_languages as $language){ ?>
						<div id="<?php echo $language['code'];?>">

				        	<table class="list">
				        		<thead>
				        			<tr>
					        			<td class="left"><?php echo $table_heading_status; ?></td>
					        			<td class="left"><?php echo $table_heading_template; ?></td>
					        			<td class="left"><?php echo $table_heading_active; ?></td>
				        			</tr>
				        		</thead>
				        		<tbody>
				        			<? foreach($array_statuses as $status){ ?>
					        			<? if($status['language_id'] == $language['language_id']){ ?>
						        			<tr>
												<td class="left"><? echo $status['name']; ?></td>
												<td>
													<select name="textplode_template_<?php echo str_replace(' ', '_', strtolower($status['name'])); ?>_<?php echo $language['code'];?>">
														<option value="-1"><?php echo $text_select;?></option>
														<?php
														foreach($array_templates as $template){
															if($template['template_id'] == str_replace(' ', '_', $this->config->get(strtolower('textplode_template_'.str_replace(' ', '_', strtolower($status['name'])) . '_' . $language['code'])))){
																echo '<option selected="SELECTED" value=' . $template['template_id'] . '>' . $template['template_name'] . '</option>';
															}else{
																echo '<option value=' . $template['template_id'] . '>' . $template['template_name'] . '</option>';
															}
														}
														?>
													</select>
												</td>
												<td>
													<input type="hidden" name="textplode_active_<?php echo str_replace(' ', '_', strtolower($status['name'])); ?>_<?php echo $language['code'];?>" value="off"/>
													<?php if(isset($active_array["textplode_active_" . str_replace(' ', '_', strtolower($status['name'])) . '_' . $language['code']])){ ?>
														<?php if($active_array["textplode_active_" . str_replace(' ', '_', strtolower($status['name'])) . '_' . $language['code']] == "on"){ ?>
															<input checked="CHECKED" type="checkbox" name="textplode_active_<?php echo strtolower($status['name']); ?>_<?php echo $language['code'];?>" />
														<?php }else{ ?>
															<input type="checkbox" name="textplode_active_<?php echo str_replace(' ', '_', strtolower($status['name'])); ?>_<?php echo $language['code'];?>" />
														<?php } ?>
													<?php }else{ ?>
															<input type="checkbox" name="textplode_active_<?php echo str_replace(' ', '_', strtolower($status['name'])); ?>_<?php echo $language['code'];?>" />
													<?php } ?>
												</td>
							        		</tr>
						        		<? } ?>
						        	<? } ?>
					        	</tbody>
					      	</table>
						</div>
				      	<?php } ?>
			    	</div>
				<div id="tab-sendmessage">
		        	<table class="form" style="max-width: 500px;">
		        		<?php if($sync_group == -1){ ?>
		        		<tr>
		        			<td style="border-bottom: none;">
		        				&nbsp;
		        			</td>
		        			<td class="help" style="text-align: center;font-size: 1em;border-bottom: none;">
		        				<?php echo $text_sync_first; ?>
		        			</td>
		        		</tr>
		        		<?php } ?>
		        		<tr>
		        			<td><? echo $text_recipient;?></td>
		        			<td>
								<select name="groups">
									<option data-count="1" value="-1"> <?php echo $text_single;?> </option>
									<?php if(!empty($array_groups)){ ?>

										<?php foreach($array_groups as $group) { ?>
											<option data-count="<?php echo $group['count']; ?>" value="<?php echo $group['group_id']; ?>"><?php echo $group['group_name']; ?> <?php echo '(' . $group['count'] . ' contacts)';?></option>
										<?php } ?>
									<? } ?>
								</select>
								<input id="single-recipient" type="text" placeholder="<?php echo $text_phone;?>" name="recipient" value='<?php echo $textplode_customer_number; ?>' />
							</td>
						</tr>
						<tr>
							<td>
								<? echo $text_message;?>
							</td>
							<td>
								<textarea id="message" name="message" maxlength="459" cols="75" rows="6"></textarea>
								<br/>
								<span style="text-align:right;display:inline-block;float:right;" id="characters"><?php echo $text_characters; ?></span>
							</td>
						</tr>
						<tr>
							<td colspan="2" class="right">
								<a class="button" style="float:right;margin-left:5px;" id="send" href="javascript:;"><?php echo $text_send; ?></a>
							</td>
						</tr>
			      	</table>
			    </div>
				<div id="tab-logs">
		        	<table>
		        		<tr>
		        			<td>
								<textarea style="min-width: 800px; min-height: 400px"><?php echo $error_log; ?></textarea><br/>
							</td>
						</tr>
						<tr>
							<td class="right">
								<a class="button" style="float: right;" href="<?php echo $this->url->link('module/textplode/clearerrors/', 'token=' . $this->session->data['token'], 'SSL'); ?>"><?php echo $text_clear; ?></a>
							</td>
						</tr>
			      	</table>
			    </div>
			    <input id="continue" type="hidden" name="continue" value="0" />
			    <input id="continue" type="hidden" name="current_tab" value="tab-general" />
	  		</form>
		</div>

  	</div>
</div>
<script type="text/javascript"><!--
    $('#htabs a').tabs();
    $('#languages a').tabs();
    $('#template-languages a').tabs();

    $(document).ready(function(){

    	$('#message').on('keyup', function(){
    		var text = $(this).val();
    		var c = (text.length != 1) ? ' characters' : ' character';
    		$('#characters').text(text.length + c + ' used');
    	});

    	$('#sync').on('click', function(){
    		var sync_group = $('select[name="textplode_sync_group"]').val();
    		$('#sync').replaceWith( "Please Wait..." );
    		window.location = "<?php echo str_replace('amp;', '', $this->url->link('module/textplode/sync/', 'token=' . $this->session->data['token'], 'SSL')); ?>&group=" + sync_group;
    	});

    	$('#send').on('click', function(){
    		var contacts_val = $('select[name="groups"] option:selected').attr('data-count');
    		var message_val = $('#message').val();
    		$.post('https://www.textplode.com/apirequest.php', {
    			action: 'calculate',
    			contacts: contacts_val,
    			message: message_val,
    			apikey: '<?php echo $textplode_apikey; ?>'
    		}, function(response){
    			response = $.parseJSON(response);
    			if(response['credits'] == 1){
    				var credit = 'credit';
    			}else{
    				var credit = 'credits';
    			}
    			if(window.confirm("Sending this message will cost " + response['credits'] + " " + credit + "\n\nContinue?") === true){
    				send();
    			}
    		});
    	});

    	function send(){

    		if($('select[name="groups"] option:selected').val() == -1){
    			var to = 'single:' + $('#single-recipient').val();
    		}else{
    			var to = 'group:' + $('select[name="groups"] option:selected').val()
    		}

    		$.post('https://www.textplode.com/apirequest.php', {
    			action: 'send',
    			recipients: to,
    			message: $('#message').val(),
    			from: '<?php echo $textplode_from_name; ?>',
    			apikey: '<?php echo $textplode_apikey; ?>'
    		}, function(response){
    			response = $.parseJSON(response);
    			var url = "<?php echo str_replace('amp;', '', $this->url->link('module/textplode/send/', 'token=' . $this->session->data['token'], 'SSL')); ?>";
    			url += "&success=" + response['return_code'];
    			if(response['errors'][0]['code']){
    				url += "&error_code=" + response['errors'][0]['code'];
    			}
    			//console.log(url);
    			window.location = url;
    		});
    	};

    	if('<? echo $textplode_admin_number;?>' != ''){ /* Witchcraft */
    		$('.admin-notifications').show();
    	}

    	<?php if(isset($textplode_tab)){ ?>
	    	<?php if($textplode_tab != ''){ ?>
	    		$('#<?php echo $textplode_tab; ?>-link').trigger('click');
	    		$('input[name="current_tab"]').val('<?php echo $textplode_tab; ?>');
	    	<?php } ?>
    	<?php } ?>

    	$('a[id^="tab"]').on('click', function(){
    		$('input[name="current_tab"]').val($(this).attr('href').replace('#', ''));
    	});

		<?php if(!empty($array_groups)){ ?>
    	$('select[name="groups"]').on('change', function(){
    		if($(this).val() == -1){
    			$('#single-recipient').show();
    		}else{
    			$('#single-recipient').hide();
    		}
    	});
    	<?php } ?>

    });
//--></script>
<?php echo $footer; ?>