<?php
	global $fcontact;
?>
<div id="fcontact" class="logout">

	<div class="result-area">
		<h3 class="title success"><?php _e( 'Thank you! ', 'fcontact' ); ?></h3>
		<h3 class="title error"><?php _e( 'Sorry, sendmail error occurred.', 'fcontact' ); ?></h3>
		<p class="description success"><?php echo nl2br( $fcontact->getOption( 'fcontact_success_message' ) ); ?></p>
		<p class="description error"><?php echo nl2br( $fcontact->getOption( 'fcontact_error_message' ) ); ?></p>
		<p class="cause error"><em></em></p>
		<input class="back" type="button" value="<?php _e( 'Back', 'fcontact' ); ?>" />
	</div>

	<form class="contact-form" method="POST">
		<input type="hidden" name="action" value="fcontact_sendmail">
		<input type="hidden" class="fbid" name="fbid" value="">
		<input type="hidden" class="name" name="name" value="" disabled>
		<input type="hidden" class="mail" name="mail" value="" disabled>
		<label for="message"><?php _e( 'Message', 'fcontact' ); ?></label>
		<textarea id="message" name="message" cols="35" rows="8" aria-required="true" required placeholder="<?php _e( 'Please enter a message', 'fcontact' ); ?>"></textarea>
		<input class="submit" type="submit" value="<?php _e( 'Send', 'fcontact' ); ?>" />
		<input class="clear" type="reset" value="<?php	_e( 'Clear', 'fcontact' ); ?>" />
		<input class="logout" type="reset" value="<?php	_e( 'Logout', 'fcontact' ); ?>" />
	</form>

</div>
