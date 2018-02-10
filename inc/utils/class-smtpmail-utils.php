<?php
/*
 * SMTPを使用してメール送信を扱うクラス
 */
require_once 'class-mail-utils.php';

if ( ! class_exists( 'SmtpMailUtils' ) ) {
class SmtpMailUtils extends MailUtils {

	private $smtpHost = null;
	private $smtpPort = null;
	private $smtpSecure = 'none';
	private $smtpAuth = true;
	private $username = null;
	private $password = null;

	/*
	 * コンストラクタ
	 */
	public function __construct(
			$from, $from_name = null,
			$smtp_host = null,
			$smtp_port = null,
			$smtp_secure = 'none',
			$smtp_auth = true,
			$username = null,
			$password = null ) {

		try {
			parent::__construct( $from, $from_name );
		} catch ( Exception $e ) {
			throw $e;
		}

		if ( empty( $smtp_host ) ||
				empty( $smtp_port ) ||
				empty( $smtp_secure ) ||
				empty( $username ) ||
				empty( $password ) ) {
			error_log( 'SmtpMailUtils construction failed.' );
			throw new Exception( 'SmtpMailUtils construction failed.' );
		}
		$this->smtpHost = $smtp_host;
		$this->smtpPort = $smtp_port;
		$this->smtpSecure = $smtp_secure;
		$this->smtpAuth = $smtp_auth;
		$this->username = $username;
		$this->password = $password;
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

		add_action( 'phpmailer_init', array( $this, 'phpMailerInitSmtp' ) );

		$result = wp_mail( $to, $subject, $message, $headers );

		remove_action( 'phpmailer_init', array( $this, 'phpMailerInitSmtp' ) );

		return $result;
	}

	/*
	 * WPフィルター用
	 */
	public function phpMailerInitSmtp( $phpmailer ) {
		$phpmailer->Mailer = 'smtp';
		$phpmailer->From = $this->from;
		$phpmailer->FromName = mb_encode_mimeheader( $this->sender );
		$phpmailer->Sender = $phpmailer->From;
		$phpmailer->SMTPSecure = ( $this->smtpSecure === 'none' ) ? '' : $this->smtpSecure;
		$phpmailer->Host = $this->smtpHost;
		$phpmailer->Port = $this->smtpPort;
		$phpmailer->SMTPAuth = ( $this->smtpAuth ) ?  true : false;
		$phpmailer->Username = $this->username;
		$phpmailer->Password = $this->password;
	}

}
}

?>
