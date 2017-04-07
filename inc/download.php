<?php

/*
 * CSVダウンロード
 */

function fcontact_download() {

	require_once FCONTACT_DIR_PATH . '/inc/database.php';		// データベース操作関数

	// ファイル名
	$file_path = FCONTACT_DIR_PATH . 'database/export.csv';

	// CSVに出力するタイトル行
	$export_csv_title = array(
		add_quote('id'),
		add_quote('受信日時'),
		add_quote('FacebookID'),
		add_quote('名前'),
		add_quote('メール'),
		add_quote('メッセージ') );

	if (touch($file_path)) {

		// オブジェクト生成
		$file = new SplFileObject( $file_path, 'w' );

		// タイトル行出力
		$file->fputcsv($export_csv_title);

		// データベースから取得して出力
		$db = new ContactDb(FCONTACT_DIR_PATH . 'database/contact.db');
		$results = $db->select();
		if ($results != false) {
			while($row = $results->fetchArray()) {
				$output = array(
					add_quote($row['id']),
					add_quote($row['create_at']),
					add_quote($row['fbid']),
					add_quote($row['name']),
					add_quote($row['mail']),
					add_quote($row['message']));
				$file->fputcsv($output);
			}
		}
		$db->close();
	}

	// ダウンロード用
	header('Pragma: public'); // required
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Cache-Control: private', false); // required for certain browsers
	header('Content-Type: application/force-download');
	header('Content-Length: ' . filesize($file_path));
	header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
	header('Content-Transfer-Encoding: binary');
	readfile($file_path);

	die();
}

function add_quote( $str ) {
	// return '"' . $str . '"';
	return $str;
}

?>
