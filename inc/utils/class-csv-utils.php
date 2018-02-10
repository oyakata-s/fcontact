<?php
/*
 * CSV操作クラス
 */
require_once ABSPATH . 'wp-admin/includes/file.php';		// WP_Filesystem使用

if ( ! class_exists( 'CsvUtils' ) ) {
class CsvUtils {

	private $header = null;
	private $data = array();

	/*
	 * コンストラクタ
	 */
	public function __construct( $header= null ) {
		if ( ! is_null( $header ) && is_array( $header ) ) {
			$this->header = $header;
			$this->data[] = $header;
		}
	}

	/* 
	 * 見出し行セット
	 */
	public function setHeader( $header ) {
		if ( is_null( $header ) || ! is_array( $header ) ) {
			throw new Exception( 'header is allowed only array.' );
		}
		$this->header = $header;
		$this->data[] = $header;
	}

	/* 
	 * 一行追加
	 */
	public function putLine( $data ) {
		$this->data[] = $data;
	}

	/* 
	 * ファイル出力
	 */
	public function outputFile( $file ) {
		$ressult = false;
		if ( WP_Filesystem() ) {
			global $wp_filesystem;
			if ( $wp_filesystem->touch( $file ) ) {
				// $data = $this->getCsvLine( $this->header );
				foreach ( $this->data as $value ) {
					$data .= $this->getLine( $value );
				}
				$result = $wp_filesystem->put_contents( $file, $data, FS_CHMOD_FILE);
			} else {
				throw new Exception( 'file create error.' );
			}
		} else {
			throw new Exception( 'filesystem access error.' );
		}
		return $result;
	}

	/* 
	 * 一行取得
	 */
	private function getLine( $data ) {
		if ( is_array( $data ) ) {
			$line = '';
			foreach ( $data as $value ) {
				$line .= $this->add_quote( $value ) . ',';
			}
			$line .= "\n";
			return $line;
		}
		return null;
	}

	private function add_quote( $str ) {
		return '"' . $str . '"';
		// return $str;
	}

}
}

?>
