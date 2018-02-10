<?php
/*
 * 初期化関連
 */

/*
 * プラグイン有効化
 */
function fcontact_activation() {
	// ディレクトリの準備
	fcontact_check_dir( FCONTACT_DATABASE_DIR_PATH );
	fcontact_check_dir( FCONTACT_DEBUG_DIR_PATH );

	// 固定ページ作成
	fcontact_create_contact_page();
}

/*
 * プラグイン無効化
 */
function fcontact_deactivation() {
	// ディレクトリの削除
	fcontact_remove_dir( FCONTACT_DATABASE_DIR_PATH );
	fcontact_remove_dir( FCONTACT_DEBUG_DIR_PATH );

	// 固定ページ削除
	fcontact_delete_contact_page();
}

/*
 * プラグインロード時
 */
function fcontact_loaded() {
	$info = parse_url( get_bloginfo( 'url' ) );
	$host = $info[ 'host' ];

	/*
	 * オプション定義
	 */
	$options = array();
	$options[ 'fcontact_pageid' ] = false;
	$options[ 'fcontact_app_id' ] = '';
	$options[ 'fcontact_app_secret' ] = '';
	$options[ 'fcontact_mail_from' ] = 'wordpress@' . $host;
	$options[ 'fcontact_mail_from_name' ] = get_bloginfo( 'name' );
	$options[ 'fcontact_error_message' ] = __( 'Failed to send your message.', 'fcontact' );
	$options[ 'fcontact_success_message' ] = __( 'Thank you for contacting me.', 'fcontact' );
	$options[ 'fcontact_backup_enable' ] = false;
	$options[ 'fcontact_mail_to' ] = get_option( 'admin_email' );
	$options[ 'fcontact_mail_header' ] = '';
	$options[ 'fcontact_mail_body' ] = __( "I received a message on your website.\nThe message is as follow.\n\nFrom:[mail_user] <[mail_addr]>\nMessage:\n[mail_body]\n\nThis e-mail has been sent automatically from the program.\nReply to this email address is not possible.\n", 'fcontact' );
	$options[ 'fcontact_mail_subject' ] = __( 'You got a message.', 'fcontact' );
	$options[ 'fcontact_reply_enable' ] = false;
	$options[ 'fcontact_reply_header' ] = 'Reply-To: ' . get_option( 'fcontact_mail_from' );
	$options[ 'fcontact_reply_subject' ] = __( 'Thank you for contacting me.', 'fcontact' );
	$options[ 'fcontact_reply_body' ] = __( "Dear, [mail_user]\n\nThank you for contacting me.\nYour message is as follow.\n\nMessage:\n[mail_body]\n\nThis e-mail has been sent automatically from the program.\nReply to this email address is not possible.\n", 'fcontact' );
	$options[ 'fcontact_smtp_enable' ] = false;
	$options[ 'fcontact_smtp_host' ] = null;
	$options[ 'fcontact_smtp_port' ] = null;
	$options[ 'fcontact_smtp_secure' ] = false;
	$options[ 'fcontact_smtp_auth' ] = 'none';
	$options[ 'fcontact_smtp_user' ] = null;
	$options[ 'fcontact_smtp_pass' ] = null;

	global $fcontact;
	$fcontact->setOptions( $options );

	// 固定ページチェック
	fcontact_check_contact_page();
}

/*
 * headタグにscriptタグを出力
 */
function fcontact_header_script() {
?>
<script>
var ajaxurl = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
</script>
<?php
}

/*
 * footer出力
 */
function fcontact_footer_script() {
	global $fcontact;
	$fb_appid = $fcontact->getOption( 'fcontact_app_id' );
	if ( empty( $fb_appid ) ) {
		$fb_appid = -1;
	}
?>
<div id="fb-root"></div>
<script>
var fb_activate = false;
window.fbAsyncInit = function() {
	FB.init({
		appId      : <?php echo $fb_appid; ?>,
		cookie     : true,  // enable cookies to allow the server to access
							// the session
		xfbml      : true,  // parse social plugins on this page
		version    : 'v2.8' // use version 2.8
	});
	FB.getLoginStatus(function(response) {
		fb_activate = true;
		statusChangeCallback(response);
	});
};
(function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) return;
	js = d.createElement(s); js.id = id;
	js.src = "//connect.facebook.net/ja_JP/sdk.js";
	fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
</script>
<?php
}

/*
 * スタイル出力
 */
function fcontact_print_styles() {
	if ( defined( 'FCONTACT_DEBUG' ) && FCONTACT_DEBUG == true ) {
		$style_css = FCONTACT_DIR_URL . 'css/style.css';
	} else {
		$style_css = FCONTACT_DIR_URL . 'css/style.min.css';
	}

	global $fcontact;
	$fcontact_pageid = $fcontact->getOption( 'fcontact_pageid' );
	if ( empty( $fcontact_pageid ) || is_page( $fcontact_pageid ) ) {
		wp_enqueue_style( 'icomoon',
			FCONTACT_DIR_URL . 'css/icomoon.css',
			array(),
			'5.0.6',
			'all' );
		wp_enqueue_style( 'plugin-fcontact',
			$style_css,
			array(),
			$fcontact->getVersion(),
			'all' );
		wp_enqueue_style( 'simple-overlay',
			FCONTACT_DIR_URL . 'js/overlay/overlay.css',
			array(),
			$fcontact->getVersion(),
			'all' );
	}
}

/*
 * JS出力
 */
function fcontact_enqueue_scripts() {
	if ( defined( 'FCONTACT_DEBUG' ) && FCONTACT_DEBUG == true ) {
		$sendmail_js = FCONTACT_DIR_URL . 'js/sendmail.js';
	} else {
		$sendmail_js = FCONTACT_DIR_URL . 'js/sendmail.min.js';
	}

	global $fcontact;
	$fcontact_pageid = $fcontact->getOption( 'fcontact_pageid' );
	if ( empty( $fcontact_pageid ) || is_page( $fcontact_pageid ) ) {
		wp_enqueue_script( 'simple-overlay',
			FCONTACT_DIR_URL . 'js/overlay/jquery.overlay.min.js',
			array( 'jquery' ),
			$fcontact->getVersion(),
			true );	// footer
		wp_enqueue_script( 'fcontact-sendmail',
			$sendmail_js,
			array( 'simple-overlay' ),
			$fcontact->getVersion(),
			true );	// footer
		wp_localize_script( 'fcontact-sendmail',
			'fcontact_sendmail',
			array(
				// 'fb_appid' => $fcontact->getOption( 'fcontact_app_id' ),
				'send_title' => __( 'Send Message', 'fcontact' ),
				'send_info' => __( '%{name}[%{mail}] send a message.', 'fcontact' ),
				'sending_msg' => __( 'Sending...', 'fcontact' ),
				'logout_msg' => __( 'You logged out from Facebook.', 'fcontact' ),
				'fbapp_not_enable' => __( 'Facebook Application is not activated.', 'fcontact' )
			) );
	}
}

/*
 * ディレクトリの存在をチェックする
 * 存在しなかったら作成する
 */
function fcontact_check_dir( $dir ) {
	if ( ! file_exists( $dir ) ) {
		return wp_mkdir_p( $dir );
	}

	return false;
}

/*
 * 指定したディレクトリが存在したら削除する
 */
function fcontact_remove_dir( $dir ) {
	if ( file_exists( $dir ) ) {
		if ( WP_Filesystem() ) {
			global $wp_filesystem;
			return $wp_filesystem->delete( $dir, true );
		}
	}

	return false;
}

/*
 * slug: fcontact　の固定ページを生成する
 */
function fcontact_create_contact_page() {
	$slug = 'fcontact';
	$title = __( 'Contact', 'fcontact' );
	$content = '[fcontact_form]';

	$newpage_opt = array(
		'post_type' => 'page',
		'post_name' => $slug,
		'post_title' => $title,
		'post_content' => $content,
		'post_status' => 'publish',
	);

	$page_check = get_page_by_path( $slug );
	if ( ! isset( $page_check->ID ) ) {
		$page_id = wp_insert_post( $newpage_opt );
	} else {
		$page_id = $page_check->ID;
	}

	update_option( 'fcontact_pageid', $page_id );
}

/*
 * slug: fcontactの固定ページを削除
 */
function fcontact_delete_contact_page( $force = false ) {
	delete_option( 'fcontact_pageid' );
	if ( $force ) {
		$page_check = get_page_by_path( 'fcontact' );
		if ( isset( $page_check->ID ) ) {
			wp_delete_post( $page_check->ID );
			update_option( 'fcontact_pageid', $page_check->ID );
		}
	}
}

/*
 * slug: fcontactの固定ページ存在チェック
 */
function fcontact_check_contact_page() {
	$page_check = get_page_by_path( 'fcontact' );
	if ( ! isset( $page_check->ID ) ) {
		delete_option( 'fcontact_pageid' );
	} else {
		update_option( 'fcontact_pageid', $page_check->ID );
	}
}

?>
