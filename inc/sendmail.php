<?php

/*
 * メール送信
 */
require_once FCONTACT_DIR_PATH . 'inc/utils/class-mail-utils.php';
require_once FCONTACT_DIR_PATH . 'inc/utils/class-smtpmail-utils.php';
require_once FCONTACT_DIR_PATH . 'inc/utils/class-facebook-utils.php';
require_once FCONTACT_DIR_PATH . 'inc/utils/class-contactdb.php';

function fcontact_sendmail() {
	global $fcontact;

	// 受信データ取得とサニタイズ
	$fbid = esc_attr( $_POST['fbid'] );
	$message = esc_textarea( $_POST['message'] );

	try {
		// facebook準備
		$facebook = FacebookUtils::getInstance(
			$fcontact->getOption( 'fcontact_app_id' ),
			$fcontact->getOption( 'fcontact_app_secret' )
		);

		// ユーザー情報取得
		$user_info = $facebook->getUserInfo();
	} catch ( Exception $e ) {
		return_result( false, $e->getMessage() );
	}

	// ユーザー情報チェック
	if ( ( $chk_result = chk_user_info( $user_info, $fbid ) ) !== true ) {
		return_result( false, $chk_result );
	}

	/*
	 * メール送信オブジェクト生成
	 */
	try {
		if ( $fcontact->getOption( 'fcontact_smtp_enable' ) ) {
			$mailer = new SmtpMailUtils(
				$fcontact->getOption( 'fcontact_mail_from' ),
				$fcontact->getOption( 'fcontact_mail_from_name' ),
				$fcontact->getOption( 'fcontact_smtp_host' ),
				$fcontact->getOption( 'fcontact_smtp_port' ),
				$fcontact->getOption( 'fcontact_smtp_secure' ),
				( $fcontact->getOption( 'fcontact_smtp_auth' ) ) ? true : false,
				$fcontact->getOption( 'fcontact_smtp_user' ),
				$fcontact->getOption( 'fcontact_smtp_pass' )
			);
		} else {
			$mailer = new MailUtils(
				$fcontact->getOption( 'fcontact_mail_from' ),
				$fcontact->getOption( 'fcontact_mail_from_name' )
			);
		}
	} catch ( Exception $e ) {
		return_result( false, $e->getMessage() );
	}

	// デバッグモードにする
	if ( defined( 'FCONTACT_DEBUG' ) && FCONTACT_DEBUG == true ) {
		$mailer->setDebugMode( true, FCONTACT_DIR_PATH . 'debug/' );
	}

	/*
	 * 送信者名、メッセージを取得するためのフィルター適用
	 */
	$username = $user_info['name'];
	$usermail = $user_info['mail'];
	// フィルター用関数定義
	$apply_msg = function( $str ) use ( $message ) {
		return $message;
	};
	$apply_user = function( $str ) use ( $username ) {
		return $username;
	};
	$apply_mail = function( $str ) use ( $usermail ) {
		return $usermail;
	};
	// フィルター適用
	add_filter( 'apply_message', $apply_msg );
	add_filter( 'apply_username', $apply_user );
	add_filter( 'apply_usermail', $apply_mail );

	/*
	 * 管理者宛メール送信
	 */
	$result = $mailer->sendmail(
		$fcontact->getOption( 'fcontact_mail_to' ),
		$fcontact->getOption( 'fcontact_mail_header' ),
		$fcontact->getOption( 'fcontact_mail_subject' ),
		do_shortcode( $fcontact->getOption( 'fcontact_mail_body' ) )
	);
	if ( ! $result ) {
		return_result( false, 'Send Mail Error' );
	}

	/*
	 * データベースバックアップ（オプション）
	 */
	if ( $fcontact->getOption( 'fcontact_backup_enable') ) {
		try {
			$db = new ContactDb( FCONTACT_DIR_PATH . 'database/contact.db', true );
			$db->insert( $user_info['fbid'], $user_info['name'], $user_info['mail'], $message );
			$db->close();
		} catch ( Exception $e ) {
			error_log( 'DB Backup Error: ' . $e->getMessage() );
		}
	}

	/*
	 * 自動返信メール（オプション）
	 */
	if ( $fcontact->getOption( 'fcontact_reply_enable' ) ) {
		$result = $mailer->sendmail(
			$user_info['mail'],
			$fcontact->getOption( 'fcontact_reply_header' ),
			$fcontact->getOption( 'fcontact_reply_subject' ),
			do_shortcode( $fcontact->getOption( 'fcontact_reply_body' ) )
		);
	}

	/*
	 * 適用したフィルター削除
	 */
	remove_filter( 'apply_message', $apply_msg );
	remove_filter( 'apply_username', $apply_user );
	remove_filter( 'apply_usermail', $apply_mail );

	return_result( true );
}

/*
 * 結果をJSONで出力
 */
function return_result( $result, $cause = null ) {
	header( 'Content-Type:application/json;charset=utf-8' );
	if ( $result ) {
		echo json_encode( array( 'result' => 'success' ) );
	} else {
		echo json_encode( array(
			'result' => 'error',
			'cause' => $cause
		) );
	}
	die();
}

/*
 * ユーザー情報チェック
 * メールアドレス形式チェック
 */
function chk_user_info( $user, $fbid ) {
	// FBIDチェック
	if ( $fbid !== $user['fbid'] ) {
		return 'Facebook ID is different.';
	}

	// メールアドレスチェック
	// if ( ! preg_match( '|^[0-9a-z_./?-]+@([0-9a-z-]+\.)+[0-9a-z-]+$|', $user['mail'], $m ) ) {
	if ( ! is_email( $user['mail'] ) ) {
		return 'Invalid e-mail address format.';
	}

	return true;
}

?>
