<?php
/*
 * 設定関連
 */
define( 'FCONTACT_HOOK_SUFFIX', 'settings_page_plugin_fcontact_options' );

require_once FCONTACT_DIR_PATH . 'inc/base/class-ft-setting.php';			// 設定ベースクラス

class FcontactSetting extends  FtSetting {

	/*
	 * 初期化
	 */
	public function __construct() {

		$info = parse_url( get_bloginfo( 'url' ) );
		$host = $info[ 'host' ];

		try {
			parent::__construct(
				'fcontact',
				array(
					'fcontact_pageid' => false,
					'fcontact_app_id' => '',
					'fcontact_app_secret' => '',
					'fcontact_mail_from' => 'wordpress@' . $host,
					'fcontact_mail_from_name' => get_bloginfo( 'name' ),
					'fcontact_error_message' => __( 'Failed to send your message.', 'fcontact' ),
					'fcontact_success_message' => __( 'Thank you for contacting me.', 'fcontact' ),
					'fcontact_backup_enable' => false,
					'fcontact_mail_to' => get_option( 'admin_email' ),
					'fcontact_mail_header' => '',
					'fcontact_mail_body' => __( "I received a message on your website.\nThe message is as follow.\n\nFrom:[mail_user] <[mail_addr]>\nMessage:\n[mail_body]\n\nThis e-mail has been sent automatically from the program.\nReply to this email address is not possible.\n", 'fcontact' ),
					'fcontact_mail_subject' => __( 'You got a message.', 'fcontact' ),
					'fcontact_reply_enable' => false,
					'fcontact_reply_header' => 'Reply-To: ' . get_option( 'fcontact_mail_from' ),
					'fcontact_reply_subject' => __( 'Thank you for contacting me.', 'fcontact' ),
					'fcontact_reply_body' => __( "Dear, [mail_user]\n\nThank you for contacting me.\nYour message is as follow.\n\nMessage:\n[mail_body]\n\nThis e-mail has been sent automatically from the program.\nReply to this email address is not possible.\n", 'fcontact' ),
					'fcontact_smtp_enable' => false,
					'fcontact_smtp_host' => null,
					'fcontact_smtp_port' => null,
					'fcontact_smtp_secure' => false,
					'fcontact_smtp_auth' => 'none',
					'fcontact_smtp_user' => null,
					'fcontact_smtp_pass' => null
				) );
		} catch ( Exception $e ) {
			throw $e;
		}

		add_action( 'admin_menu', array( $this, 'addOptionsPage' ) );

		add_action( 'admin_print_styles-'.FCONTACT_HOOK_SUFFIX, array( $this, 'enqueueStyles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueueScripts' ) );

	}

	/* 
	 * オプションページ追加
	 */
	public function addOptionsPage() {
		$this->registerOptionsPage(
			__('FContact Setting', 'fcontact'),
			__('FContact Setting', 'fcontact'),
			'manage_options',
			'plugin_fcontact_options',
			FCONTACT_DIR_PATH . 'parts/admin-fcontact.php'
		);
	}

	/* 
	 * css追加
	 */
	public function enqueueStyles() {
		global $fcontact;
		wp_enqueue_style( 'fcontact_admin_style',
			FCONTACT_DIR_URL . 'css/admin-style.min.css',
			array(),
			$fcontact->getVersion(),
			'all' );
	}

	/* 
	 * js追加
	 */
	public function enqueueScripts( $hook_suffix ) {
		if ( $hook_suffix == FCONTACT_HOOK_SUFFIX ) {
			global $fcontact;
			wp_enqueue_script( 'fcontact_admin',
				FCONTACT_DIR_URL . 'js/admin_script.min.js',
				array( 'jquery' ),
				$fcontact->getVersion(),
				false );
			wp_enqueue_script( 'fcontact_encoding',
				FCONTACT_DIR_URL . 'js/encoding.min.js',
				array( 'jquery' ),
				$fcontact->getVersion(),
				true );		// in footer
			wp_enqueue_script( 'fcontact_download',
				FCONTACT_DIR_URL . 'js/download.min.js',
				array( 'jquery' ),
				$fcontact->getVersion(),
				true );		// in footer
		}
	}

}

?>
