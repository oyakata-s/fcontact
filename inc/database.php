<?php

/*
 * SQLiteデータベースを操作するクラス
 */

class ContactDb {
	protected $db = null;

	/*
	 * コンストラクタ
	 */
	public function __construct( $db_path, $create_table = false ) {
		// ディレクトリチェック
		if (!check_database_dir( $db_path )) {
			return false;
		}

		// DB接続およびなければテーブル作成
		try {
			$this->db = new SQLite3($db_path);
			if ($create_table) {
				$this->create_table();
			}
		} catch (Exception $e) {
			error_log($e->getMessage());
			// return $e->getMessage();
		}

		// return true;
	}

	/*
	 * insert
	 */
	public function insert( $fbid, $name, $mail, $message ) {
		$st = sprintf("INSERT INTO contact(fbid,name,mail,message) VALUES(
					%d, '%s', '%s', '%s')", $fbid, $name, $mail, $message);
		try {
			$this->db->exec($st);
		} catch (Exception $e) {
			error_log($e->getMessage());
			return $e->getMessage();
		}

		return true;
	}

	/*
	 * select
	 */
	public function select( $where = null ) {
		$st;
		if ( is_null($where) ) {
			$st = 'SELECT * FROM contact';
		} else {
			$st = sprintf("SELECT * FROM contact %s", $where);
		}

		try {
			$results = $this->db->query($st);
			return $results;
		} catch (Exception $e) {
			error_log($e->getMessage());
			return false;
		}

		return false;
	}

	/*
	 * DB切断
	 */
	public function close() {
		try {
			$this->db->close();
		} catch (Exception $e) {
			error_log($e->getMessage());
		}
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
			$this->db->exec($st);
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}
}

/*
 * データベースを保存するディレクトリの存在をチェックする
 * 存在しなかったら作成する
 */
function check_database_dir( $filepath ) {
	// $directory_path = FCONTACT_DIR_PATH . '/database';
	$path = dirname($filepath);

	if ( !file_exists($path) ) {
		if ( mkdir($path, 0755) ) {
			return true;
		} else {
			return false;
		}
	}

	return true;
}

?>
