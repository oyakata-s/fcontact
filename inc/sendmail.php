<?php

/*
 * メール送信
 */

function fcontact_sendmail() {

	require_once FCONTACT_DIR_PATH . '/inc/database.php';		// データベース操作関数

	if(isset($_POST)) $_POST = sanitize($_POST);

	// header定義
	header('Content-Type:application/json;charset=utf-8');

	// facebookからユーザー情報を取得
	$user_info = getUserInfo();
	if (is_null($user_info) || empty($user_info)) {
		echo json_encode(array('result' => __('Facebook Authentication Failed.', 'fcontact')));
		die();
	}

	// ユーザー情報チェック
	if ( ($chk_result = chkUserInfo($user_info, h($_POST['fbid']))) !== true ) {
		echo json_encode(array('result' => $chk_result));
		die();
	}

	// メッセージ本体取得
	$message = h($_POST['message']);

	/*
	 * 管理者宛メール
	 */
	$mail_to = get_fcontact_option('fcontact_mail_to');
	$mail_from = get_fcontact_option('fcontact_mail_from');
	$mail_from_name = get_fcontact_option('fcontact_mail_from_name');
	$mail_header = get_fcontact_option('fcontact_mail_header');
	$mail_subject = sprintf('[%s]', get_bloginfo('name')) . get_fcontact_option('fcontact_mail_subject');
	$mail_body = getMailBody($user_info, $message);
	$result = sendmail($mail_to, $mail_from, $mail_from_name, $mail_header, $mail_subject, $mail_body);
	// メール送信
	if ( !$result ) {
		echo json_encode(array('result' => __('Send Mail Error', 'fcontact')));
		die();
	}

	/*
	 * データベースバックアップ（オプション）
	 */
	if (get_option('fcontact_backup_enable')) {
		try {
			$db = new ContactDb(FCONTACT_DIR_PATH . '/database/contact.db', true);
			$result = $db->insert($user_info['fbid'], $user_info['name'], $user_info['mail'], $message);
			$db->close();
			if ($result != true) {
				error_log($result);
			}
		} catch (Exception $e) {
			error_log($e->getMessage());
		}
	}

	/*
	 * 自動返信メール（オプション）
	 */
	if (get_option('fcontact_reply_enable')) {
		$reply_to = $user_info['mail'];
		$reply_name = $user_info['name'];
		$reply_header = get_fcontact_option('fcontact_reply_header');
		$reply_subject = sprintf('[%s]', get_bloginfo('name')) . get_fcontact_option('fcontact_reply_subject');
		$reply_body = getReplyBody($message, $reply_name);
		$result = sendmail($reply_to, $mail_from, $mail_from_name, $reply_header, $reply_subject, $reply_body);
	}

	/*
	 * 結果をJSONで返す
	 */
	if ($result === true) {
		echo json_encode(array('result' => 'success'));
	} else {
		echo json_encode(array('result' => 'something error.'));
	}

	die();
}

/*
 * デバッグ用メール送信
 */
function debug_mail($to, $from, $from_name, $header, $subject, $message) {
	// フォルダ作成
	$dir_path = FCONTACT_DIR_PATH . 'debug';
	if ( !file_exists($dir_path) ) {
		if ( !mkdir($dir_path, 0755) ) {
			return false;
		}
	}

	$headers[] = "From: " . $from_name . " <" . $from . ">";
	$headers = array_merge($headers, explode("\n", $header));

	// ファイル出力
	$fileName = date_i18n('Ymd') . '.txt';
	$filePath = $dir_path . '/' . $fileName;
	$text = "\n▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼\n";
	$text .= 'To: ' . $to . "\n";
	foreach($headers as $tmp) {
		$text .= $tmp . "\n";
	}
	$text .= 'Subject: ' . $subject . "\n";
	$text .= "Message:\n";
	$text .= $message;
	$text .= "time: " . date_i18n( "Y/m/d (D) H:i:s");
	$text .= "\n▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲\n";

	if ( !file_put_contents($filePath, $text, FILE_APPEND) ) {
		return false;
	}

	return true;
}

/*
 * メール送信本体
 */
function sendmail($to, $from, $from_name, $header, $subject, $message) {
	$result = false;
	if (defined('FCONTACT_DEBUG') && FCONTACT_DEBUG) {
		// デバッグ用
		$result = debug_mail($to, $from, $from_name, $header, $subject, $message);
	} elseif (get_option('fcontact_smtp_enable')) {
		/*
		 * SMTPを使ってメール送信
		 */
		$headers[] = 'From: ' . $from_name . ' <' . $from . '>';
		$headers = array_merge($headers, explode("\n", $header));
		add_action( 'phpmailer_init', 'phpmailer_init_smtp' );		// SMTP設定
		$result = wp_mail($to, $subject, $message, $headers);
		remove_action( 'phpmailer_init', 'phpmailer_init_smtp' );
	} else {
		/*
		 * メール送信（デフォルト）
		 */
		$headers[] = 'From: ' . $from_name . ' <' . $from . '>';
		$headers = array_merge($headers, explode("\n", $header));
		add_filter( 'wp_mail_from', 'set_mail_from' );				// from指定はfilterを使う
		add_filter( 'wp_mail_from_name', 'set_mail_from_name' );
		add_action( 'phpmailer_init', 'set_return_path' );			// return-path指定
		$result = wp_mail($to, $subject, $message, $headers);
		remove_filter( 'wp_mail_from', 'set_mail_from' );			// filter解除
		remove_filter( 'wp_mail_from_name', 'set_mail_from_name' );
		remove_action( 'phpmailer_init', 'set_return_path' );
	}

	return $result;
}

/*
 * phpmailer設定変更
 * 通常用
 */
function set_mail_from( $email ) {
	return get_fcontact_option('fcontact_mail_from');
}
function set_mail_from_name( $sender ) {
	return get_fcontact_option('fcontact_mail_from_name');
}
function set_return_path( $phpmailer ) {
	return $phpmailer->Sender = get_fcontact_option('fcontact_mail_from');
}
function phpmailer_init_default( $phpmailer ) {
	$phpmailer->From = get_fcontact_option('fcontact_mail_from');
	$phpmailer->FromName = mb_encode_mimeheader(get_fcontact_option('fcontact_mail_from_name'));
	$phpmailer->Sender = $phpmailer->From;
}
/*SMTP用*/
function phpmailer_init_smtp( $phpmailer ) {
	$phpmailer->Mailer = 'smtp';
	$phpmailer->From = get_fcontact_option('fcontact_mail_from');
	$phpmailer->FromName = mb_encode_mimeheader(get_fcontact_option('fcontact_mail_from_name'));
	$phpmailer->Sender = $phpmailer->From;
	$phpmailer->SMTPSecure = (get_fcontact_option('fcontact_smtp_secure')==='none') ? '' : get_fcontact_option('fcontact_smtp_secure');
	$phpmailer->Host = get_fcontact_option('fcontact_smtp_host');
	$phpmailer->Port = get_fcontact_option('fcontact_smtp_port');
	$phpmailer->SMTPAuth = (get_fcontact_option('fcontact_smtp_auth')) ?  true : false;
	$phpmailer->Username = get_fcontact_option('fcontact_smtp_user');
	$phpmailer->Password = get_fcontact_option('fcontact_smtp_pass');
}

/*
 * ユーザー情報チェック
 * メールアドレス形式チェック
 */
function chkUserInfo( $user, $fbid ) {
	// FBIDチェック
	if ($fbid !== $user['fbid']) {
		return __('Facebook ID is different.', 'fcontact');
	}

	// メールアドレスチェック
	if (!preg_match('|^[0-9a-z_./?-]+@([0-9a-z-]+\.)+[0-9a-z-]+$|', $user['mail'], $m)) {
		return __('Invalid e-mail address format.', 'fcontact');
	}

	return true;
}

/*
 * facebookからユーザー情報を取得
 */
function getUserInfo() {
	// require_once FCONTACT_DIR_PATH . '/inc/facebook-sdk/facebook.php';
	require_once('facebook-sdk-v5/autoload.php');

	$config = array(
		'app_id' => get_option('fcontact_app_id'),
		'app_secret' => get_option('fcontact_app_secret'),
	);
	$facebook = new Facebook\Facebook($config);

	$helper = $facebook->getJavaScriptHelper();
	try {
		$accessToken = $helper->getAccessToken();
	} catch (Facebook\Exceptions\FacebookResponseException $e) {
		error_log($e->getType() . '::' . $e->getMessage());
		return null;
	} catch (Facebook\Exceptions\FacebookSDKException $e) {
		error_log($e->getType() . '::' . $e->getMessage());
		return null;
	}

	if (isset($accessToken)) {
		$facebook->setDefaultAccessToken($accessToken);
		$response = $facebook->get('/me?fields=id,name,email&locale=ja_JP');
		$user = $response->getGraphUser();
		$userinfo = array(
			'fbid' => $user['id'],
			'name' => $user['name'],
			'mail' => $user['email'],
		);
		return $userinfo;
	}

	return null;
}

// 送信メールヘッダ（未使用）
function getMailHeader($from,$bcc,$to){
	$header = array(
		'From: ', $from,
		'Bcc: ' . $bcc,
	);
	return $header;
}

/*
 * 送信メール本文生成
 */
function getMailBody($user, $message) {
	$body = get_fcontact_option('fcontact_mail_body') . "\n";
	$body .= "=============================================\n";
	$body .= getPostData($user);
	$body .= "[message]\n" . $message . "\n";
	$body .= "=============================================\n";
	$body .= "sent date and time: ".date_i18n( "Y/m/d (D) H:i:s")."\n";
	$body .= "IP address: ".@$_SERVER["REMOTE_ADDR"]."\n";
	$body .= "Host name: ".getHostByAddr(getenv('REMOTE_ADDR'))."\n\n";
	$body .= get_fcontact_option('fcontact_mail_footer') . "\n";
	return $body;
}

/*
 * 返信メール本文作成
 */
function getReplyBody($message, $to) {
	$body = sprintf(get_fcontact_option('fcontact_reply_message'), $to) . "\n";
	$body .= "=============================================\n";
	$body .= $message . "\n";
	$body .= "=============================================\n";
	$body .= "sent date and time: ".date_i18n( "Y/m/d (D) H:i:s")."\n";
	$body .= "IP address: ".@$_SERVER["REMOTE_ADDR"]."\n";
	$body .= "Host name: ".getHostByAddr(getenv('REMOTE_ADDR'))."\n\n";
	$body .= get_fcontact_option('fcontact_mail_footer') . "\n";
	return $body;
}

/*
 * postデータを整形して返す
 */
function getPostData($post) {
	$res = '';
	foreach($post as $key => $val) {
		$res .= '[' . $key . ']' . $val . "\n";
	}
	return $res;
}

// 配列連結の処理
function connect2val($arr){
	$out = '';
	foreach($arr as $key => $val){
		if($key === 0 || $val == ''){//配列が未記入（0）、または内容が空のの場合には連結文字を付加しない（型まで調べる必要あり）
			$key = '';
		}elseif(strpos($key,"円") !== false && $val != '' && preg_match("/^[0-9]+$/",$val)){
			$val = number_format($val);//金額の場合には3桁ごとにカンマを追加
		}
		$out .= $val . $key;
	}
	return $out;
}

function sanitize($arr){
	if(is_array($arr)){
		return array_map('sanitize',$arr);
	}
	return str_replace("\0","",$arr);
}

function h($string) {
	// encode
	$encode = get_bloginfo( 'charset' );

	return htmlspecialchars($string, ENT_QUOTES, $encode);
}

?>
