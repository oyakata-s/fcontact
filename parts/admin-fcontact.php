<div class="wrap">
	<h2><?php _e( 'FContact Setting', 'fcontact' ); ?></h2>
	<form method="POST" action="options.php">
<?php
		settings_fields( 'fcontact_settings_group' );
		do_settings_sections( 'fcontact_settings_group' );
		global $fcontact;
?>

		<!-- tab control -->
		<ul id="settings-tab">
			<li class="active"><a href="#"><?php _e( 'Setup', 'fcontact' ); ?></a></li>
			<li><a href="#"><?php _e( 'Admin Mail Settings', 'fcontact' ); ?></a></li>
			<li><a href="#"><?php _e( 'Reply Mail Settings', 'fcontact' ); ?></a></li>
			<li><a href="#"><?php _e( 'SMTP Settings', 'fcontact' ); ?></a></li>
		</ul>

		<div id="tab-contents">

		<!-- Setup -->
		<table class="form-table tab-content active">
			<tr>
				<th scope="row"><?php _e( 'Facebook Setting', 'fcontact' ); ?></th>
				<td><fieldset>
					<label for="fcontact_app_id"><?php _e( 'Application ID', 'fcontact' ); ?></label>
					<input type="text" name="fcontact_app_id" id="fcontact_app_id" placeholder="<?php _e( 'Please enter application id.', 'fcontact' ); ?>" value="<?php echo esc_attr( $fcontact->getOption( 'fcontact_app_id' ) ); ?>"><br>
					<label for="fcontact_app_secret"><?php _e( 'Application Secret', 'fcontact' ); ?></label>
					<input type="text" name="fcontact_app_secret" id="fcontact_app_secret" placeholder="<?php _e( 'Please enter application secret.', 'fcontact' ); ?>" value="<?php echo esc_attr( $fcontact->getOption( 'fcontact_app_secret' ) ); ?>">
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e( 'Mail From', 'fcontact' ); ?></th>
				<td><fieldset>
					<label for="fcontact_mail_from"><?php _e( 'From Address', 'fcontact' ); ?></label>
					<input type="text" name="fcontact_mail_from" id="fcontact_mail_from" placeholder="<?php _e( 'Please enter a sender mail address. ', 'fcontact' ); ?>" value="<?php echo esc_attr( $fcontact->getOption( 'fcontact_mail_from' ) ); ?>"><br>
					<label for="fcontact_mail_from_name"><?php _e( 'From Name', 'fcontact' ); ?></label>
					<input type="text" name="fcontact_mail_from_name" id="fcontact_mail_from_name" placeholder="<?php _e( 'Please enter a sender name.', 'fcontact' ); ?>" value="<?php echo esc_attr( $fcontact->getOption( 'fcontact_mail_from_name' ) ); ?>">
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e( 'Display message', 'fcontact' ); ?></th>
				<td><fieldset>
					<label for="fcontact_success_message"><?php _e( 'On success', 'fcontact' ); ?></label>
					<textarea name="fcontact_success_message" id="fcontact_success_message" placeholder="<?php _e( 'Please enter the sentences to be displayed when mail transmission is completed.', 'fcontact' ); ?>" rows="4"><?php echo esc_textarea( $fcontact->getOption( 'fcontact_success_message' ) ); ?></textarea><br>
					<label for="fcontact_error_message"><?php _e( 'On error', 'fcontact' ); ?></label>
					<textarea name="fcontact_error_message" id="fcontact_error_message" placeholder="<?php _e( 'Please enter the sentences to display when mail transmission fails.', 'fcontact' ); ?>" rows="4"><?php echo esc_textarea( $fcontact->getOption( 'fcontact_error_message' ) ); ?></textarea>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e( 'Database Backup', 'fcontact' ); ?></th>
				<td><fieldset>
					<input type="checkbox" name="fcontact_backup_enable" id="fcontact_backup_enable" value="1" <?php checked( $fcontact->getOption( 'fcontact_backup_enable' ), 1 ); ?> />
					<label style="width:auto;" for="fcontact_backup_enable"><?php _e( 'Enable Backup', 'fcontact' ); ?></label>&emsp;
					<input type="button" class="button-primary" name="fcontact_download" id="fcontact_download" value="<?php _e( 'CSV Export', 'fcontact' )?>" />
					</fieldset>
				</td>
			</tr>
		</table>

		<!-- Admin Mail Settings -->
		<table class="form-table tab-content">
			<tr>
				<th scope="row"><label for="fcontact_mail_to"><?php _e( 'Mail To', 'fcontact' ); ?></label></th>
				<td><fieldset>
					<input type="text" name="fcontact_mail_to" id="fcontact_mail_to" placeholder="<?php _e( 'Please enter the mail address to receive.', 'fcontact' ); ?>" value="<?php echo esc_attr( $fcontact->getOption( 'fcontact_mail_to' ) ); ?>">
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="fcontact_mail_header"><?php _e( 'Mail Header', 'fcontact' ); ?></label></th>
				<td><fieldset>
					<textarea name="fcontact_mail_header" id="fcontact_mail_header" placeholder="<?php _e( 'Please enter mail header.', 'fcontact' ); ?>" rows="3"><?php echo esc_textarea( $fcontact->getOption( 'fcontact_mail_header' ) ); ?></textarea>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="fcontact_mail_subject"><?php _e( 'Mail Subject', 'fcontact' ); ?></label></th>
				<td><fieldset>
					<input type="text" name="fcontact_mail_subject" id="fcontact_mail_subject" placeholder="<?php _e( 'Please enter mail subject.', 'fcontact' ); ?>" value="<?php echo esc_attr( $fcontact->getOption( 'fcontact_mail_subject' ) ); ?>">
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="fcontact_mail_body"><?php _e( 'Mail Body', 'fcontact' ); ?></label></th>
				<td><fieldset>
					<textarea name="fcontact_mail_body" id="fcontact_mail_body" placeholder="<?php _e( 'Please enter mail body.', 'fcontact' ); ?>" rows="10"><?php echo esc_textarea( $fcontact->getOption( 'fcontact_mail_body' ) ); ?></textarea>
					</fieldset>
				</td>
			</tr>
		</table>

		<!-- Reply Mail Settings -->
		<table class="form-table tab-content">
			<tr>
				<th scope="row"><?php _e( 'Auto Reply', 'fcontact' ); ?></th>
				<td><fieldset>
					<input type="checkbox" name="fcontact_reply_enable" id="fcontact_reply_enable" value="1" <?php checked( $fcontact->getOption( 'fcontact_reply_enable' ), 1 ); ?> />
					<label for="fcontact_reply_enable"><?php _e( 'Enable Reply', 'fcontact' ); ?></label>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="fcontact_reply_header"><?php _e( 'Mail Header', 'fcontact' ); ?></label></th>
				<td><fieldset>
					<textarea name="fcontact_reply_header" id="fcontact_reply_header" placeholder="<?php _e( 'Please enter mail header.', 'fcontact' ); ?>" rows="3"><?php echo esc_textarea( $fcontact->getOption( 'fcontact_reply_header' ) ); ?></textarea>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="fcontact_reply_subject"><?php _e( 'Mail Subject', 'fcontact' ); ?></label></th>
				<td><fieldset>
					<input type="text" name="fcontact_reply_subject" id="fcontact_reply_subject" placeholder="<?php _e( 'Please enter mail subject.', 'fcontact' ); ?>" value="<?php echo esc_attr( $fcontact->getOption( 'fcontact_reply_subject' ) ); ?>">
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="fcontact_reply_body"><?php _e( 'Mail Body', 'fcontact' ); ?></label></th>
				<td><fieldset>
					<textarea name="fcontact_reply_body" id="fcontact_reply_body" placeholder="<?php _e( 'Please enter mail body.', 'fcontact' ); ?>" rows="10"><?php echo esc_textarea( $fcontact->getOption( 'fcontact_reply_body' ) ); ?></textarea>
					</fieldset>
				</td>
			</tr>
		</table>

		<!-- SMTP Settings -->
		<table class="form-table tab-content">
			<tr>
				<th scope="row"><?php _e( 'Use SMTP', 'fcontact' ); ?></th>
				<td><fieldset>
					<input type="checkbox" name="fcontact_smtp_enable" id="fcontact_smtp_enable" value="1" <?php checked( $fcontact->getOption( 'fcontact_smtp_enable' ), 1 ); ?> />
					<label for="fcontact_smtp_enable"><?php _e( 'Send email via SMTP', 'fcontact' ); ?></label>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e( 'SMTP Server', 'fcontact' ); ?></th>
				<td><fieldset>
					<label for="fcontact_smtp_host"><?php _e( 'SMTP Host', 'fcontact' ); ?></label>
					<input type="text" name="fcontact_smtp_host" id="fcontact_smtp_host" placeholder="<?php _e( 'Please enter smtp host', 'fcontact' ); ?>" value="<?php echo esc_attr( $fcontact->getOption( 'fcontact_smtp_host' ) ); ?>"><br>
					<label for="fcontact_smtp_port"><?php _e( 'SMTP Port', 'fcontact' ); ?></label>
					<input type="text" name="fcontact_smtp_port" id="fcontact_smtp_port" placeholder="<?php _e( 'Please enter smtp port', 'fcontact' ); ?>" value="<?php echo esc_attr( $fcontact->getOption( 'fcontact_smtp_port' ) ); ?>">
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e( 'Encryption', 'fcontact' ); ?></th>
				<td><fieldset>
					<input type="radio" name="fcontact_smtp_secure" id="fcontact_smtp_secure_none" value="none" <?php checked( $fcontact->getOption( 'fcontact_smtp_secure' ) === 'none', 1 ); ?> /><label for="fcontact_smtp_secure_none"><?php _e( 'No encryption', 'fcontact' ); ?></label>
					<input type="radio" name="fcontact_smtp_secure" id="fcontact_smtp_secure_ssl" value="ssl" <?php checked( $fcontact->getOption( 'fcontact_smtp_secure' ) === 'ssl', 1 ); ?> /><label for="fcontact_smtp_secure_ssl"><?php _e( 'Use SSL encryption', 'fcontact' ); ?></label>
					<input type="radio" name="fcontact_smtp_secure" id="fcontact_smtp_secure_tls" value="tls" <?php checked( $fcontact->getOption( 'fcontact_smtp_secure' ) === 'tls', 1 ); ?> /><label for="fcontact_smtp_secure_tls"><?php _e( 'Use TLS encryption', 'fcontact' ); ?></label>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e( 'Authentication', 'fcontact' ); ?></th>
				<td><fieldset>
					<input type="checkbox" name="fcontact_smtp_auth" id="fcontact_smtp_auth" value="1" <?php checked( $fcontact->getOption( 'fcontact_smtp_auth' ), 1 ); ?> />
					<label for="fcontact_smtp_auth"><?php _e( 'Use SMTP Authentication', 'fcontact' ); ?></label><br>
					<label for="fcontact_smtp_user"><?php _e( 'Username', 'fcontact' ); ?></label>
					<input type="text" name="fcontact_smtp_user" id="fcontact_smtp_user" placeholder="<?php _e( 'Please enter username', 'fcontact' ); ?>" value="<?php echo esc_attr( $fcontact->getOption( 'fcontact_smtp_user' ) ); ?>"><br>
					<label for="fcontact_smtp_pass"><?php _e( 'Password', 'fcontact' ); ?></label>
					<input type="password" name="fcontact_smtp_pass" id="fcontact_smtp_pass" placeholder="<?php _e( 'Please enter password', 'fcontact' ); ?>" value="<?php echo esc_attr( $fcontact->getOption(' fcontact_smtp_pass' ) ); ?>">
					</fieldset>
				</td>
			</tr>
		</table>

		</div>
		<!-- /.tab-content -->

		<?php submit_button(); ?>
	</form>
</div>
