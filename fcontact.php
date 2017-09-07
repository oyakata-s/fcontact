<?php
/*
 * Plugin Name: FContactForm
 * Plugin URI: http://something-25.com
 * Description: Facebookアカウントを利用したコンタクトフォーム
 * Version: 0.1.2
 * Author: oyakata-s
 * Author URI: http://something-25.com
 *
 * All files, unless otherwise stated, are released under the GNU General Public License
 * version 3.0 (http://www.gnu.org/licenses/gpl-3.0.html)
 */

/*
 * 定数定義
 */
define( 'FCONTACT_DEBUG', true );		// デバッグモード
define( 'FCONTACT_DIR_PATH', plugin_dir_path( __FILE__ ) );		// 本プラグイディレクトリへのパス

require_once FCONTACT_DIR_PATH . 'inc/sendmail.php';		// お問い合わせページ関数
require_once FCONTACT_DIR_PATH . 'inc/download.php';		// CSVダウンロード用関数

/*
 * 初期化処理
 */
function fcontact_init() {

	// 多言語翻訳用
	load_plugin_textdomain( 'fcontact', false, 'fcontact/languages');

	// 管理メニューに追加
	add_action( 'admin_menu', 'add_menu_fcontactsetting' );

	// 管理画面用script
	add_action( 'admin_print_footer_scripts', 'add_fcontact_admin_script' );

	// ajax通信用
	add_action( 'wp_ajax_fcontact_sendmail', 'fcontact_sendmail' );
	add_action( 'wp_ajax_nopriv_fcontact_sendmail', 'fcontact_sendmail' );
	add_action( 'wp_ajax_fcontact_download', 'fcontact_download' );

	// ショートコード定義
	add_shortcode( 'fcontact_form', 'shortcode_fcontact_form' );

	// script出力
	add_action( 'wp_head', 'add_fcontact_header_script' );
	add_action( 'wp_footer', 'add_fcontact_footer_script' );
	add_action( 'wp_print_styles', 'fcontact_print_styles' );
	add_action( 'wp_enqueue_scripts', 'fcontact_enqueue_scripts' );
}
add_action( 'plugins_loaded', 'fcontact_init' );

/*
 * ショートコード
 */
function shortcode_fcontact_form() {
	ob_start();
	include FCONTACT_DIR_PATH . '/parts/fcontact-form.php';
	return ob_get_clean();
}

/*
 * blogurlからホスト名取得
 */
function get_fcontact_host() {
	$info = parse_url(get_bloginfo('url'));
	return $info['host'];
}

/*
 * プラグインバージョン
 */
function get_fcontact_version() {
	$data = get_file_data( __FILE__, array( 'version' => 'Version' ) );
	$version = $data['version' ];
	if ($version < '1.0') {
		return date('0.Ymd.Hi');
	} else {
		return $version;
	}
}

/*
 * script読込
 */
function fcontact_enqueue_scripts() {
	wp_enqueue_script('plugin-fcontact', plugin_dir_url( __FILE__ ) . 'js/fcontact.js', array(), get_fcontact_version(), true);
}

/*
 * style読み込み
 */
function fcontact_print_styles() {
	wp_enqueue_style('plugin-fcontact', plugin_dir_url( __FILE__ ) . 'css/style.css', array(), get_fcontact_version(), 'all');
}

/*
 * 管理画面のみ必要なJS読み込み
 */
function add_fcontact_admin_script() {
?>
<script src="<?php echo plugin_dir_url(__FILE__); ?>js/download.js"></script>
<script src="<?php echo plugin_dir_url(__FILE__); ?>js/encoding.min.js"></script>
<?php
}

/*
 * headタグにscriptタグを出力
 */
function add_fcontact_header_script() {
?>
<script>
var ajaxurl = '<?php echo admin_url( 'admin-ajax.php'); ?>';
var appId = '<?php echo get_option('fcontact_app_id') ?>';
var fb_activate = false;
</script>
<?php
}

/*
 * bodyタグの最後に出力
 */
function add_fcontact_footer_script() {
?>
<div id="fb-root"></div>
<script>
var fb_activate = false;
window.fbAsyncInit = function() {
	FB.init({
		appId      : appId,
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
 * 管理画面関連
 */
function add_menu_fcontactsetting() {
	add_options_page(
		__('FContact Setting', 'fcontact'),
		__('FContact Setting', 'fcontact'),
		'manage_options',
		'create_fcontact_options',
		'create_fcontact_options');
	add_action('admin_init', 'register_fcontact_settings');
}
function register_fcontact_settings() {
	register_setting('fcontact_settings_group', 'fcontact_confirm_enable');
	register_setting('fcontact_settings_group', 'fcontact_backup_enable');
	register_setting('fcontact_settings_group', 'fcontact_app_id');
	register_setting('fcontact_settings_group', 'fcontact_app_secret');
	register_setting('fcontact_settings_group', 'fcontact_smtp_enable');
	register_setting('fcontact_settings_group', 'fcontact_smtp_host');
	register_setting('fcontact_settings_group', 'fcontact_smtp_port');
	register_setting('fcontact_settings_group', 'fcontact_smtp_secure');
	register_setting('fcontact_settings_group', 'fcontact_smtp_auth');
	register_setting('fcontact_settings_group', 'fcontact_smtp_user');
	register_setting('fcontact_settings_group', 'fcontact_smtp_pass');
	register_setting('fcontact_settings_group', 'fcontact_mail_to');
	register_setting('fcontact_settings_group', 'fcontact_mail_from');
	register_setting('fcontact_settings_group', 'fcontact_mail_from_name');
	register_setting('fcontact_settings_group', 'fcontact_mail_header');
	register_setting('fcontact_settings_group', 'fcontact_mail_body');
	register_setting('fcontact_settings_group', 'fcontact_mail_subject');
	register_setting('fcontact_settings_group', 'fcontact_mail_footer');
	register_setting('fcontact_settings_group', 'fcontact_reply_enable');
	register_setting('fcontact_settings_group', 'fcontact_reply_header');
	register_setting('fcontact_settings_group', 'fcontact_reply_subject');
	register_setting('fcontact_settings_group', 'fcontact_reply_message');
	register_setting('fcontact_settings_group', 'fcontact_error_message');
}
function create_fcontact_options() {
	if ( !current_user_can('manage_options') ) {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}

	require FCONTACT_DIR_PATH . '/parts/admin-fcontact.php';
}

/*
 * 各オプション既定値を取得
 */
function get_fcontact_default( $key ) {
	switch ($key) {
		case 'fcontact_smtp_secure':
			return 'none';
		case 'fcontact_mail_to':
			return get_option('admin_email');
		case 'fcontact_mail_from':
			return 'wordpress@' . get_fcontact_host(); // get_option('admin_email');
		case 'fcontact_mail_from_name':
			return get_bloginfo('name');
		case 'fcontact_mail_subject':
			$output = __('You got a message.', 'fcontact');
			return $output;
		case 'fcontact_mail_body':
			$output = __("I received a message on your website.\nThe message is as follow.\n", 'fcontact');
			return $output;
		case 'fcontact_mail_footer':
			$output = __("This e-mail has been sent automatically from the program.\nReply to this email address is not possible.\n", 'fcontact');
			return $output;
		case 'fcontact_reply_header':
			return 'Reply-To: ' . get_fcontact_option('fcontact_mail_from');
		case 'fcontact_reply_subject':
			$output = __('Thank you for contacting me.', 'fcontact');
			return $output;
		case 'fcontact_reply_message':
			$output = __("Dear, %s\n\nThank you for contacting me.\nYour message is as follow.\n", 'fcontact');
			return $output;
		case 'fcontact_error_message':
			$output = __('Failed to send your message.', 'fcontact');
			return $output;
		default:
			return '';
	}
}

/*
 * 各オプションの値を出力
 * 未定義の場合は規定値を返す
 */
function get_fcontact_option( $key ) {
	$value;
	if ( $value = get_option($key) ) {
		return $value;
	} else {
		return get_fcontact_default($key);
	}
}

?>
