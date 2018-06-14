<?php
/*
 * Plugin Name: FContactForm
 * Plugin URI: https://github.com/oyakata-s/fcontact
 * Description: Contact form using a Facebook account.
 * Version: 0.2.2
 * Author: oyakata-s
 * Author URI: https://something-25.com
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: fcontact
 */

/*
 * 定数定義
 */
define( 'FCONTACT_FILE', __FILE__ );								// プラグインファイルへのパス
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
require_once FCONTACT_DIR_PATH . 'inc/setting.php';			// 設定関連
require_once FCONTACT_DIR_PATH . 'inc/shortcodes.php';		// ショートコード関連

require_once FCONTACT_DIR_PATH . 'inc/ajax/class-download-ajax.php';	// メール送信用
require_once FCONTACT_DIR_PATH . 'inc/ajax/class-sendmail-ajax.php';	// CSVダウンロード用

require_once FCONTACT_DIR_PATH . 'inc/base/class-ft-base.php';		// ベースクラス
require_once FCONTACT_DIR_PATH . 'inc/base/class-ft-utils.php';			// ユーティリティ関連

class FContact extends FtBase {

	/* 
	 * 初期化
	 */
	public function __construct() {

		/* 
		 * ベースクラスのコンストラクタ呼び出し
		 */
		try {
			parent::__construct( FCONTACT_FILE );
		} catch ( Exception $e ) {
			throw $e;
		}

		// 多言語翻訳用
		load_plugin_textdomain( 'fcontact', false, 'fcontact/languages' );

		// 設定
		$this->setting = new FcontactSetting();

		register_activation_hook( FCONTACT_FILE, array( $this, 'activation' ) );
		register_deactivation_hook( FCONTACT_FILE, array( $this, 'deactivation' ) );
		add_action( 'plugins_loaded', array( $this, 'pluginLoaded' ) );

		add_action( 'wp_head', array( $this, 'addHead' ) );
		add_action( 'wp_footer', array( $this, 'addFooter' ) );
		add_action( 'wp_print_styles', array( $this, 'enqueueStyles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueueScripts' ) );
	}

	/* 
	 * プラグインロード時
	 */
	public function pluginLoaded() {
		$this->checkContactPage();
	}

	/* 
	 * プラグイン有効化
	 */
	public function activation() {
		// ディレクトリの準備
		FtUtils::checkDirectory( FCONTACT_DATABASE_DIR_PATH );
		FtUtils::checkDirectory( FCONTACT_DEBUG_DIR_PATH );

		$this->createContactPage();
	}

	/* 
	 * プラグイン無効化
	 */
	public function deactivation() {
		// ディレクトリの削除
		FtUtils::removeDirectory( FCONTACT_DATABASE_DIR_PATH );
		FtUtils::removeDirectory( FCONTACT_DEBUG_DIR_PATH );

		$this->deleteContactPage();
	}

	/* 
	 * head追加
	 */
	public function addHead() {
		echo '<script>';
		echo 'var ajaxurl = "' . admin_url( 'admin-ajax.php' ) . '";';
		echo '</script>';
	}

	/* 
	 * footer追加
	 */
	public function addFooter() {
		$fb_appid = $this->getOption( 'fcontact_app_id' );
		if ( empty( $fb_appid ) ) {
			$fb_appid = -1;
		}
		$fcontact_pageid = $this->getOption( 'fcontact_pageid' );
		if ( empty( $fcontact_pageid ) || is_page( $fcontact_pageid ) ) :
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
		endif;
	}

	/* 
	 * style追加
	 */
	public function enqueueStyles() {
		$fcontact_pageid = $this->getOption( 'fcontact_pageid' );
		if ( empty( $fcontact_pageid ) || is_page( $fcontact_pageid ) ) {
			wp_enqueue_style( 'icomoon',
				FCONTACT_DIR_URL . 'css/icomoon.css',
				array(),
				'5.0.6',
				'all' );
			wp_enqueue_style( 'plugin-fcontact',
				FCONTACT_DIR_URL . 'css/style.min.css',
				array(),
				$this->getVersion(),
				'all' );
			wp_enqueue_style( 'simple-overlay',
				FCONTACT_DIR_URL . 'js/overlay/overlay.min.css',
				array(),
				$this->getVersion(),
				'all' );
		}
	}

	/* 
	 * js追加
	 */
	public function enqueueScripts() {
		$fcontact_pageid = $this->getOption( 'fcontact_pageid' );
		if ( empty( $fcontact_pageid ) || is_page( $fcontact_pageid ) ) {
			wp_enqueue_script( 'simple-overlay',
				FCONTACT_DIR_URL . 'js/overlay/jquery.overlay.min.js',
				array( 'jquery' ),
				$this->getVersion(),
			true );	// footer
			wp_enqueue_script( 'fcontact-sendmail',
				FCONTACT_DIR_URL . 'js/sendmail.min.js',
				array( 'simple-overlay' ),
				$this->getVersion(),
				true );	// footer
			wp_localize_script( 'fcontact-sendmail',
				'fcontact_sendmail',
				array(
					// 'fb_appid' => $this->getOption( 'fcontact_app_id' ),
					'send_title' => __( 'Send Message', 'fcontact' ),
					'send_info' => __( '%{name}[%{mail}] send a message.', 'fcontact' ),
					'sending_msg' => __( 'Sending...', 'fcontact' ),
					'logout_msg' => __( 'You logged out from Facebook.', 'fcontact' ),
					'fbapp_not_enable' => __( 'Facebook Application is not activated.', 'fcontact' )
				) );
		}
	}

	/* 
	 * お問い合わせ固定ページ生成
	 */
	private function createContactPage() {
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
	 * お問い合わせ固定ページ削除
	 */
	private function deleteContactPage( $force = false ) {
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
	 * お問い合わせ固定ページ存在チェック
	 */
	private function checkContactPage() {
		$page_check = get_page_by_path( 'fcontact' );
		if ( ! isset( $page_check->ID ) ) {
			delete_option( 'fcontact_pageid' );
		} else {
			update_option( 'fcontact_pageid', $page_check->ID );
		}
	}

}

$fcontact = new FContact();

$download = new DownloadRunner( 'fcontact_download' );
$sendmail = new SendmailRunner( 'fcontact_sendmail', true );

?>
