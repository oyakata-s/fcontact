<?php
/*
 * Plugin Name: FContactForm
 * Plugin URI: https://github.com/oyakata-s/fcontact
 * Description: Contact form using a Facebook account.
 * Version: 0.2
 * Author: oyakata-s
 * Author URI: https://something-25.com
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: fcontact
 */

/*
 * 定数定義
 */
define( 'FCONTACT_DIR_PATH', plugin_dir_path( __FILE__ ) );			// プラグインディレクトリへのパス
define( 'FCONTACT_DIR_URL', plugin_dir_url( __FILE__ ) );			// プラグインディレクトリへのURL
define( 'FCONTACT_TEXTDOMAIN', 'fcontact' );					// テキストドメイン

// define( 'FCONTACT_DEBUG', true );		// デバッグモード
define( 'FCONTACT_DATABASE_DIR_PATH', FCONTACT_DIR_PATH . 'database/' );	// データベースパス
define( 'FCONTACT_DEBUG_DIR_PATH', FCONTACT_DIR_PATH . 'debug/' );		// デバッグ用パス

/*
 * ライブラリ読み込み
 */
require_once ABSPATH . 'wp-admin/includes/file.php';		// WP_Filesystem使用
require_once FCONTACT_DIR_PATH . 'inc/init.php';			// 初期化関連
require_once FCONTACT_DIR_PATH . 'inc/admin.php';			// 管理画面関連
require_once FCONTACT_DIR_PATH . 'inc/shortcodes.php';		// ショートコード関連
require_once FCONTACT_DIR_PATH . 'inc/sendmail.php';		// お問い合わせページ関数
require_once FCONTACT_DIR_PATH . 'inc/download.php';		// CSVダウンロード用関数


require_once FCONTACT_DIR_PATH . 'inc/utils/class-ft-base.php';		// 
class FContact extends FtBase {

	/* 
	 * 初期化
	 */
	public function __construct() {

		/* 
		 * ベースクラスのコンストラクタ呼び出し
		 */
		try {
			parent::__construct( __FILE__ );
		} catch ( Exception $e ) {
			throw $e;
		}

		// 多言語翻訳用
		load_plugin_textdomain( 'fcontact', false, 'fcontact/languages' );

		/*
		 * プラグイン有効化時
		 */
		register_activation_hook( __FILE__, 'fcontact_activation' );

		/*
		 * プラグイン無効化時
		 */
		register_deactivation_hook( __FILE__, 'fcontact_deactivation' );

		/*
		 * プラグインロード
		 */
		add_action( 'plugins_loaded', 'fcontact_loaded' );

		/*
		 * CSS&JS出力
		 */
		add_action( 'wp_head', 'fcontact_header_script' );
		add_action( 'wp_footer', 'fcontact_footer_script' );
		add_action( 'wp_print_styles', 'fcontact_print_styles' );
		add_action( 'wp_enqueue_scripts', 'fcontact_enqueue_scripts' );

		/*
		 * ajax用
		 */
		add_action( 'wp_ajax_fcontact_sendmail', 'fcontact_sendmail' );
		add_action( 'wp_ajax_nopriv_fcontact_sendmail', 'fcontact_sendmail' );
		add_action( 'wp_ajax_fcontact_download', 'fcontact_download' );

		/*
		 * 設定メニュー追加
		 */
		add_action( 'admin_menu', 'add_menu_fcontact_setting' );

		/*
		 * 管理画面のみCSS&JS出力
		 */
		add_action( 'admin_print_styles-' . FCONTACT_HOOK_SUFFIX, 'fcontact_admin_print_style' );
		add_action( 'admin_enqueue_scripts', 'fcontact_admin_enqueue_script' );
	}

}

$fcontact = new FContact();

?>
