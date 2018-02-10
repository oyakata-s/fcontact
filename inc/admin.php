<?php
/*
 * 管理画面関連
 */
define( 'FCONTACT_HOOK_SUFFIX', 'settings_page_plugin_fcontact_options' );

/*
 * 設定メニューに追加
 */
function add_menu_fcontact_setting() {
	add_options_page(
		__('FContact Setting', 'fcontact'),
		__('FContact Setting', 'fcontact'),
		'manage_options',
		'plugin_fcontact_options',
		'create_fcontact_options');
	add_action('admin_init', 'register_fcontact_settings');
}
function register_fcontact_settings() {
	global $fcontact;
	$options = $fcontact->getOptions();
	foreach ( $options as $key => $value ) {
		register_setting( 'fcontact_settings_group', $key );
	}
}
function create_fcontact_options() {
	if ( ! current_user_can('manage_options') ) {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}

	include FCONTACT_DIR_PATH . '/parts/admin-fcontact.php';
}

/*
 * 管理画面のみ必要なstyle読み込み
 */
function fcontact_admin_print_style() {
	if ( defined( 'FCONTACT_DEBUG' ) && FCONTACT_DEBUG == true ) {
		$style_css = FCONTACT_DIR_URL . 'css/admin-style.css';
	} else {
		$style_css = FCONTACT_DIR_URL . 'css/admin-style.min.css';
	}

	global $fcontact;
	wp_enqueue_style( 'fcontact_admin_style',
		$style_css,
		array(),
		$fcontact->getVersion(),
		'all' );
}

/*
 * 管理画面のみ必要なJS読み込み
 */
function fcontact_admin_enqueue_script( $hook_suffix ) {
	if ( defined( 'FCONTACT_DEBUG' ) && FCONTACT_DEBUG == true ) {
		$admin_js = FCONTACT_DIR_URL . 'js/admin_script.js';
	} else {
		$admin_js = FCONTACT_DIR_URL . 'js/admin_script.min.js';
	}

	if ( $hook_suffix == FCONTACT_HOOK_SUFFIX ) {
		global $fcontact;
		wp_enqueue_script( 'fcontact_admin',
			$admin_js,
			array( 'jquery' ),
			$fcontact->getVersion(),
			false );
		wp_enqueue_script( 'fcontact_encoding',
			FCONTACT_DIR_URL.'js/encoding.min.js',
			array( 'jquery' ),
			$fcontact->getVersion(),
			true );		// in footer
		wp_enqueue_script( 'fcontact_download',
			FCONTACT_DIR_URL.'js/download.min.js',
			array( 'jquery' ),
			$fcontact->getVersion(),
			true );		// in footer
	}
}

?>
