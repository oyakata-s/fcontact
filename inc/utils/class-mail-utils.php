<?php
/*
 * メールを扱うクラス
 */
require_once ABSPATH . 'wp-admin/includes/file.php';		// WP_Filesystem使用

if ( ! class_exists( 'MailUtils' ) ) {
class MailUtils {

	protected $from = null;
	protected $sender = null;
	protected $debugMode = false;
	private $debugDirPath = null;

	/*
	 * コンストラクタ
	 */
	public function __construct( $from, $from_name = null ) {
		if ( empty( $from )  ) {
			error_log( 'MailUtils construction failed.' );
			throw new Exception( 'MailUtils construction failed.' );
		}
		$this->from = $from;
		$this->sender = $from_name;
	}

	/*
	 * デバッグモード設定
	 */
	public function setDebugMode( $debug_mode, $debug_dir_path = null ) {
		$this->debugMode = $debug_mode;
		$this->debugDirPath = $debug_dir_path;
		if ( $debug_mode && empty( $debug_dir_path ) ) {
			$this->debugMode = false;
		}
	}

	/*
	 * メール送信
	 */
	public function sendmail( $to, $header, $subject, $message ) {
		$headers[] = 'From: ' . $this->sender . ' <' . $this->from . '>';
		$headers = array_merge( $headers, explode( "\n", $header ) );

		// debug
		if ( $this->debugMode ) {
			$this->debugmail( $to, $subject, $message, $headers );
			return true;
		}

		add_filter( 'wp_mail_from', array( $this, 'setMailFrom' ) );
		add_filter( 'wp_mail_from_name', array( $this, 'setMailFromName' ) );
		add_action( 'phpmailer_init', array( $this, 'setReturnPath' ) );

		$result = wp_mail( $to, $subject, $message, $headers );

		remove_action( 'phpmailer_init', array( $this, 'setReturnPath' ) );
		remove_filter( 'wp_mail_from', array( $this, 'setMailFrom' ) );
		remove_filter( 'wp_mail_from_name', array( $this, 'setMailFromName' ) );

		return $result;
	}

	/*
	 * デバッグ用出力
	 */
	protected function debugmail( $to, $subject, $message, $headers ) {
		$text = "\n▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼\n";
		$text .= 'To: ' . $to . "\n";
		foreach($headers as $tmp) {
			$text .= $tmp . "\n";
		}
		$text .= 'Subject: ' . $subject . "\n";
		$text .= "Message:\n";
		$text .= $message . "\n";
		$text .= "time: " . date_i18n( "Y/m/d (D) H:i:s");
		$text .= "\n▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲\n";

		$filename = date_i18n('Ymd') . '.txt';
		$filepath = $this->debugDirPath . $filename;

		if ( WP_Filesystem() ) {
			global $wp_filesystem;
			$data = $wp_filesystem->get_contents( $filepath );
			$data .= $text;
			$wp_filesystem->put_contents( $filepath, $data, FS_CHMOD_FILE );
		}
	}

	/*
	 * WPフィルター用
	 */
	public function setMailFrom( $email ) {
		return $this->from;
	}
	public function setMailFromName( $sender ) {
		return $this->sender;
	}
	public function setReturnPath( $phpmailer ) {
		return $phpmailer->Sender = $this->from;
	}

}
}

?>
