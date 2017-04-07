<div id="fcontact" class="logout <?php if (get_option('fcontact_confirm_enable')) : echo 'confirm'; endif; ?>">

	<div class="result-area" style="display: none;">
		<h3 class="success"><?php _e('Thank you! ', 'fcontact'); ?></h3>
		<h3 class="error"><?php _e('Transmission Error.', 'fcontact'); ?></h3>
		<p class="success"><?php echo get_fcontact_option('fcontact_reply_subject'); ?></p>
		<p class="error"><?php echo get_fcontact_option('fcontact_error_message'); ?></p>
		<p class="error"><em class="cause"></em></p>
		<input class="back" type="button" value="<?php _e('Back', 'fcontact') ?>" />
	</div>

	<form class="contact-form" method="POST">
		<h3><?php _e('Contact Us', 'fcontact'); ?></h3>
		<input type="hidden" name="action" value="fcontact_sendmail">
		<input type="hidden" class="fbid" name="fbid" value="">
		<input type="hidden" class="name" name="name" value="" disabled>
		<input type="hidden" class="mail" name="mail" value="" disabled>
		<label for="message"><?php _e('Message', 'fcontact'); ?><span class="required">*</span></label>
		<textarea id="message" name="message" cols="35" rows="8" aria-required="true" required placeholder="<?php _e('Please enter a message', 'fcontact') ?>"></textarea>
		<input class="submit" type="submit" value="<?php _e('Send', 'fcontact') ?>" />
		<input class="confirm" type="submit" value="<?php _e('Confirm', 'fcontact') ?>" />
		<input class="clear" type="reset" value="<?php	_e('Clear', 'fcontact'); ?>" />
		<input class="logout" type="reset" value="<?php	_e('Logout', 'fcontact'); ?>" />
	</form>

	<div class="confirm-area" style="display: none;">
		<h3><?php _e('Please confirm', 'fcontact'); ?></h3>
		<div class="label"><?php _e('Name'); ?></div>
		<div class="name value"></div>
		<div class="label"><?php _e('Email'); ?></div>
		<div class="mail value"></div>
		<div class="label"><?php _e('Message', 'fcontact'); ?></div>
		<div class="message value"></div>
		<input class="submit" type="submit" value="<?php _e('Send', 'fcontact') ?>" />
		<input class="cancel" type="reset" value="<?php	_e('Cancel', 'fcontact'); ?>" />
	</div>

	<div class="overlay">
		<div class="message">
			<span class="text"><?php _e('Sending...', 'fcontact'); ?></span>
		</div>
		<div class="dialog">
			<p class="text"><?php _e('Facebook Application is not activated.', 'fcontact'); ?></p>
			<button type="button" class="close"><?php _e('Close', 'fcontact'); ?></button>
		</div>
	</div>

</div>
