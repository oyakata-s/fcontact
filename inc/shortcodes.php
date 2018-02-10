<?php
/*
 * ショートコード関連
 */

/*
 * コンタクトフォーム表示
 */
function shortcode_fcontact_form() {
	ob_start();
	include FCONTACT_DIR_PATH . 'parts/fcontact-form.php';
	return ob_get_clean();
}
add_shortcode( 'fcontact_form', 'shortcode_fcontact_form' );

/* 
 * [mail_body]
 */
function shortcode_fcontact_message() {
	$message = apply_filters( 'apply_message', '' );
	return $message;
}
add_shortcode( 'mail_body', 'shortcode_fcontact_message' );

/* 
 * [mail_user]
 */
function shortcode_fcontact_user() {
	$username = apply_filters( 'apply_username', '' );
	return $username;
}
add_shortcode( 'mail_user', 'shortcode_fcontact_user' );

/* 
 * [mail_name]
 */
function shortcode_fcontact_mail() {
	$usermail = apply_filters( 'apply_usermail', '' );
	return $usermail;
}
add_shortcode( 'mail_addr', 'shortcode_fcontact_mail' );

?>
