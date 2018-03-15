<?php
/*
 * CSVダウンロード
 */
require_once FCONTACT_DIR_PATH . 'inc/utils/class-contactdb.php';		// データベース操作関数
require_once FCONTACT_DIR_PATH . 'inc/utils/class-csv-utils.php';		// CSV操作関数
require_once FCONTACT_DIR_PATH . 'inc/base/class-ft-ajax.php';			// ajax用

if ( ! class_exists( 'DownloadRunner' ) ) {
class DownloadRunner extends FtAjaxRunner {

	protected function run() {
		/* 
		 * CSV操作用オブジェクト生成
		 */
		$csv = new CsvUtils( array(
			'id',
			'receive_time',
			'facebook_id',
			'name',
			'mail',
			'message'
		) );

		/* 
		 * データベースに接続し、全行を取得
		 * CSVに追加
		 */
		$db = new ContactDb( FCONTACT_DIR_PATH . 'database/contact.db' );
		$results = $db->select();
		if ( $results != false ) {
			while( $row = $results->fetchArray() ) {
				$csv->putLine( array(
					$row[ 'id' ],
					$row[ 'create_at' ],
					$row[ 'fbid' ],
					$row[ 'name' ],
					$row[ 'mail' ],
					$row[ 'message' ]
				) );
			}
		}
		$db->close();

		/* 
		 * 一時ファイルにCSVを出力
		 */
		$file_path = FCONTACT_DIR_PATH . 'database/export.csv';
		try {
			$csv->outputFile( $file_path );
		} catch ( Exception $e ) {
			error_log( $e->getMessage() );
		}

		// ダウンロード用
		header( 'Pragma: public' ); // required
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		header( 'Cache-Control: private', false ); // required for certain browsers
		header( 'Content-Type: application/force-download' );
		header( 'Content-Length: ' . filesize( $file_path ) );
		header( 'Content-Disposition: attachment; filename="' . basename( $file_path ) . '"' );
		header( 'Content-Transfer-Encoding: binary' );
		readfile( $file_path);
}

}
}

?>
