<?php

/*
 * SQLiteデータベースを操作するクラス
 */

if ( ! function_exists( 'ContactDb' ) ) {
class ContactDb extends SQLite3 {

	private static $instance = null;

	// private $db = null;

	/*
	 * コンストラクタ
	 */
	public function __construct( $db_path, $create = false ) {
		try {
			$this->open( $db_path );

			if ( $create ) {
				$this->create_table();
			}
		} catch ( Exception $e ) {
			error_log( $e->getMessage() );
			throw new Exception( 'ContactDb construction failed.' );
		}
	}

	/*
	 * インスタンスを取得
	 */
	public static function getInstance( $db_path, $create = false ) {
		if ( empty( $instance ) ) {
			try {
				$instance = new ContactDb( $db_path, $create );
			} catch ( Exception $e ) {
				error_log( $e->getMessage() );
				throw $e;
			}
		}
		return $instance;
	}

	/*
	 * insert
	 */
	public function insert( $fbid, $name, $mail, $message ) {
		$st = sprintf( "INSERT INTO contact(fbid,name,mail,message) VALUES(
					%d, '%s', '%s', '%s')", $fbid, $name, $mail, $message );

		$result = true;
		try {
			$result = $this->exec( $st );
		} catch ( Exception $e ) {
			error_log( $e->getMessage() );
			throw $e;
		}

		return $result;
	}

	/*
	 * select
	 */
	public function select( $where = null ) {
		$st;
		if ( is_null( $where ) ) {
			$st = 'SELECT * FROM contact';
		} else {
			$st = sprintf( "SELECT * FROM contact %s", $where );
		}

		try {
			$results = $this->query( $st );
			return $results;
		} catch ( Exception $e ) {
			error_log( $e->getMessage() );
			throw $e;
		}

		return false;
	}

	/*
	 * なければテーブル作成
	 */
	private function create_table() {
		try {
			$st = "CREATE TABLE IF NOT EXISTS contact(
				id  INTEGER PRIMARY KEY AUTOINCREMENT,
				create_at TIMESTAMP DEFAULT (DATETIME('now', 'localtime')),
				fbid INTEGER,
				name VARCHAR(255),
				mail VARCHAR(255),
				message VARCHAR(255))";
			$this->exec( $st );
		} catch ( Exception $e ) {
			error_log( $e->getMessage() );
			throw $e;
		}
	}
}
}

?>
