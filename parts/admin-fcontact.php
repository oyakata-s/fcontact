<div class="wrap">
	<h2><?php _e('Contact Setting', 'fcontact'); ?></h2>
	<form method="POST" action="options.php">
<?php
		settings_fields( 'fcontact_settings_group' );
		do_settings_sections( 'fcontact_settings_group' );
?>

		<h3><?php _e('Setting: Facebook', 'fcontact'); ?></h3>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="fcontact_app_id"><?php _e('Application ID', 'fcontact'); ?></label></th>
				<td><fieldset>
					<input type="text" name="fcontact_app_id" id="fcontact_app_id" placeholder="<?php _e('input application id', 'fcontact'); ?>" value="<?php echo get_option('fcontact_app_id'); ?>" style="width: 100%;">
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="fcontact_app_secret"><?php _e('Application Secret', 'fcontact'); ?></label></th>
				<td><fieldset>
					<input type="text" name="fcontact_app_secret" id="fcontact_app_secret" placeholder="<?php _e('input application secret', 'fcontact'); ?>" value="<?php echo get_option('fcontact_app_secret'); ?>" style="width: 100%;">
					</fieldset>
				</td>
			</tr>
		</table>

		<h3><?php _e('Setting: Mail Common', 'fcontact'); ?></h3>
		<table class="form-table">
			<tr>
				<th scope="row"><?php _e('Add Confirm', 'fcontact'); ?></th>
				<td><fieldset>
					<input type="checkbox" name="fcontact_confirm_enable" id="fcontact_confirm_enable" <?php if (get_option('fcontact_confirm_enable')) : echo 'checked'; endif; ?> />
					<label for="fcontact_confirm_enable"><?php _e('Enable Confirm', 'fcontact'); ?></label>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e('Mail From', 'fcontact'); ?></th>
				<td><fieldset>
					<label for="fcontact_mail_from" style="width:100px;"><?php _e('From Address', 'fcontact'); ?></label>
					<input type="text" name="fcontact_mail_from" id="fcontact_mail_from" placeholder="<?php echo esc_attr(get_fcontact_default('fcontact_mail_from')); ?>" value="<?php echo esc_attr(get_option('fcontact_mail_from')); ?>" style="width: 50%;"><br>
					<label for="fcontact_mail_from_name" style="width:100px;"><?php _e('From Name', 'fcontact'); ?></label>
					<input type="text" name="fcontact_mail_from_name" id="fcontact_mail_from_name" placeholder="<?php echo esc_attr(get_fcontact_default('fcontact_mail_from_name')); ?>" value="<?php echo esc_attr(get_option('fcontact_mail_from_name')); ?>" style="width: 50%;">
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="fcontact_mail_footer"><?php _e('Mail Footer', 'fcontact'); ?></label></th>
				<td><fieldset>
					<textarea name="fcontact_mail_footer" id="fcontact_mail_footer" placeholder="<?php echo esc_attr(get_fcontact_default('fcontact_mail_footer')); ?>" style="width: 100%;" rows="3"><?php echo esc_attr(get_option('fcontact_mail_footer')); ?></textarea>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="fcontact_error_message"><?php _e('Error Message', 'fcontact'); ?></label></th>
				<td><fieldset>
					<textarea name="fcontact_error_message" id="fcontact_error_message" placeholder="<?php echo esc_attr(get_fcontact_default('fcontact_error_message')); ?>" style="width: 100%;" rows="4"><?php echo esc_attr(get_option('fcontact_reply_message')); ?></textarea>
					</fieldset>
				</td>
			</tr>
		</table>

		<h3><?php _e('Setting: Admin Mail', 'fcontact'); ?></h3>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="fcontact_mail_to"><?php _e('Mail To', 'fcontact'); ?></label></th>
				<td><fieldset>
					<input type="text" name="fcontact_mail_to" id="fcontact_mail_to" placeholder="<?php echo esc_attr(get_fcontact_default('fcontact_mail_to')); ?>" value="<?php echo esc_attr(get_option('fcontact_mail_to')); ?>" style="width: 100%;">
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="fcontact_mail_header"><?php _e('Mail Header', 'fcontact'); ?></label></th>
				<td><fieldset>
					<textarea name="fcontact_mail_header" id="fcontact_mail_header" placeholder="<?php echo esc_attr(get_fcontact_default('fcontact_mail_header')); ?>" style="width: 100%;" rows="3"><?php echo esc_attr(get_option('fcontact_mail_header')); ?></textarea>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="fcontact_mail_subject"><?php _e('Mail Subject', 'fcontact'); ?></label></th>
				<td><fieldset>
					<input type="text" name="fcontact_mail_subject" id="fcontact_mail_subject" placeholder="<?php echo esc_attr(get_fcontact_default('fcontact_mail_subject')); ?>" value="<?php echo esc_attr(get_option('fcontact_mail_subject')); ?>" style="width: 100%;">
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="fcontact_mail_body"><?php _e('Mail Body', 'fcontact'); ?></label></th>
				<td><fieldset>
					<textarea name="fcontact_mail_body" id="fcontact_mail_body" placeholder="<?php echo esc_attr(get_fcontact_default('fcontact_mail_body')); ?>" style="width: 100%;" rows="4"><?php echo esc_attr(get_option('fcontact_mail_body')); ?></textarea>
					</fieldset>
				</td>
			</tr>
		</table>

		<h3><?php _e('Setting: Reply Mail', 'fcontact'); ?></h3>
		<table class="form-table">
			<tr>
				<th scope="row"><?php _e('Auto Reply', 'fcontact'); ?></th>
				<td><fieldset>
					<input type="checkbox" name="fcontact_reply_enable" id="fcontact_reply_enable" <?php if (get_option('fcontact_reply_enable')) : echo 'checked'; endif; ?> />
					<label for="fcontact_reply_enable"><?php _e('Enable Reply', 'fcontact'); ?></label>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="fcontact_reply_header"><?php _e('Mail Header', 'fcontact'); ?></label></th>
				<td><fieldset>
					<textarea name="fcontact_reply_header" id="fcontact_reply_header" placeholder="<?php echo esc_attr(get_fcontact_default('fcontact_reply_header')); ?>" style="width: 100%;" rows="3"><?php echo esc_attr(get_option('fcontact_reply_header')); ?></textarea>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="fcontact_reply_subject"><?php _e('Mail Subject', 'fcontact'); ?></label></th>
				<td><fieldset>
					<input type="text" name="fcontact_reply_subject" id="fcontact_reply_subject" placeholder="<?php echo esc_attr(get_fcontact_default('fcontact_reply_subject')); ?>" value="<?php echo esc_attr(get_option('fcontact_reply_subject')); ?>" style="width: 100%;">
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="fcontact_reply_message"><?php _e('Mail Body', 'fcontact'); ?></label></th>
				<td><fieldset>
					<textarea name="fcontact_reply_message" id="fcontact_reply_message" placeholder="<?php echo esc_attr(get_fcontact_default('fcontact_reply_message')); ?>" style="width: 100%;" rows="4"><?php echo esc_attr(get_option('fcontact_reply_message')); ?></textarea>
					</fieldset>
				</td>
			</tr>
		</table>

		<h3><?php _e('Setting: SMTP', 'fcontact'); ?></h3>
		<table class="form-table">
			<tr>
				<th scope="row"><?php _e('Use SMTP', 'fcontact'); ?></th>
				<td><fieldset>
					<input type="checkbox" name="fcontact_smtp_enable" id="fcontact_smtp_enable" <?php if (get_option('fcontact_smtp_enable')) : echo 'checked'; endif; ?> />
					<label for="fcontact_smtp_enable"><?php _e('Send email via SMTP', 'fcontact'); ?></label>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e('SMTP Server', 'fcontact'); ?></th>
				<td><fieldset>
					<label for="fcontact_smtp_host" style="width:100px;"><?php _e('SMTP Host', 'fcontact'); ?></label>
					<input type="text" name="fcontact_smtp_host" id="fcontact_smtp_host" placeholder="<?php _e('input smtp host', 'fcontact'); ?>" value="<?php echo get_option('fcontact_smtp_host'); ?>" style="width: 50%;"><br>
					<label for="fcontact_smtp_port" style="width:100px;"><?php _e('SMTP Port', 'fcontact'); ?></label>
					<input type="text" name="fcontact_smtp_port" id="fcontact_smtp_port" placeholder="<?php _e('input smtp port', 'fcontact'); ?>" value="<?php echo get_option('fcontact_smtp_port'); ?>" style="width: 50%;">
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e('Encryption', 'fcontact'); ?></th>
				<td><fieldset>
					<input type="radio" name="fcontact_smtp_secure" id="fcontact_smtp_secure_none" value="none" <?php if (get_option('fcontact_smtp_secure')==='none') : echo 'checked'; endif; ?> /><label for="fcontact_smtp_secure_none"><?php _e('No encryption', 'fcontact'); ?></label>
					<input type="radio" name="fcontact_smtp_secure" id="fcontact_smtp_secure_ssl" value="ssl" <?php if (get_option('fcontact_smtp_secure')==='ssl') : echo 'checked'; endif; ?> /><label for="fcontact_smtp_secure_ssl"><?php _e('Use SSL encryption', 'fcontact'); ?></label>
					<input type="radio" name="fcontact_smtp_secure" id="fcontact_smtp_secure_tls" value="tls" <?php if (get_option('fcontact_smtp_secure')==='tls') : echo 'checked'; endif; ?> /><label for="fcontact_smtp_secure_tls"><?php _e('Use TLS encryption', 'fcontact'); ?></label>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e('Authentication', 'fcontact'); ?></th>
				<td><fieldset>
					<input type="checkbox" name="fcontact_smtp_auth" id="fcontact_smtp_auth" <?php if (get_option('fcontact_smtp_auth')) : echo 'checked'; endif; ?> />
					<label for="fcontact_smtp_auth"><?php _e('Use SMTP Authentication', 'fcontact'); ?></label><br>
					<label for="fcontact_smtp_user" style="width:100px;"><?php _e('Username', 'fcontact'); ?></label>
					<input type="text" name="fcontact_smtp_user" id="fcontact_smtp_user" placeholder="<?php _e('input username', 'fcontact'); ?>" value="<?php echo get_option('fcontact_smtp_user'); ?>" style="width: 50%;"><br>
					<label for="fcontact_smtp_pass" style="width:100px;"><?php _e('Password', 'fcontact'); ?></label>
					<input type="password" name="fcontact_smtp_pass" id="fcontact_smtp_pass" placeholder="<?php _e('input password', 'fcontact'); ?>" value="<?php echo get_option('fcontact_smtp_pass'); ?>" style="width: 50%;">
					</fieldset>
				</td>
			</tr>
		</table>

		<h3><?php _e('Setting: Other', 'fcontact'); ?></h3>
		<table class="form-table">
			<tr>
				<th scope="row"><?php _e('Database Backup', 'fcontact'); ?></th>
				<td><fieldset>
					<input type="checkbox" name="fcontact_backup_enable" id="fcontact_backup_enable" <?php if (get_option('fcontact_backup_enable')) : echo 'checked'; endif; ?> />
					<label for="fcontact_backup_enable"><?php _e('Enable Backup', 'fcontact'); ?></label>&emsp;
					<input type="button" class="button-primary" name="fcontact_download" id="fcontact_download" value="<?php _e('CSV Export', 'fcontact')?>" />
					</fieldset>
				</td>
			</tr>
		</table>

		<?php submit_button(); ?>
	</form>
</div>
