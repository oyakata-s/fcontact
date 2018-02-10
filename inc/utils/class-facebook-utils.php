<?php
/*
 * facebookを扱うクラス
 */
require_once FCONTACT_DIR_PATH . 'inc/facebook-sdk-v5/autoload.php';

class FacebookUtils {

	static private $instance = null;	// インスタンス

	private $facebook = null;

	/*
	 * コンストラクタ
	 */
	public function __construct( $app_id, $app_secret ) {
		try {
			if ( empty( $app_id ) || empty( $app_secret ) ) {
				error_log( 'FacebookUtils construction failed.' );
				throw new Exception( 'FacebookUtils construction failed.' );
			}
			$config = array(
				'app_id' => $app_id,
				'app_secret' => $app_secret
			);
			$this->facebook = new Facebook\Facebook( $config );
		} catch ( Exception $e ) {
			throw $e;
		}
	}

	/*
	 * インスタンスを取得
	 */
	public static function getInstance( $app_id, $app_secret ) {
		if ( empty( $instance ) ) {
			try {
				$instance = new FacebookUtils( $app_id, $app_secret );
			} catch ( Exception $e ) {
				throw $e;
			}
		}
		return $instance;
	}

	/*
	 * ユーザー情報を取得
	 */
	public function getUserInfo() {
		$helper = $this->facebook->getJavaScriptHelper();
		try {
			$accessToken = $helper->getAccessToken();
		} catch ( Facebook\Exceptions\FacebookResponseException $e ) {
			error_log( $e->getType() . '::' . $e->getMessage() );
			throw $e;
		} catch ( Facebook\Exceptions\FacebookSDKException $e ) {
			error_log( $e->getType() . '::' . $e->getMessage() );
			throw $e;
		}

		if ( isset( $accessToken ) ) {
			$this->facebook->setDefaultAccessToken( $accessToken );
			$response = $this->facebook->get( '/me?fields=id,name,email&locale=ja_JP' );
			$user = $response->getGraphUser();
			$userinfo = array(
				'fbid' => $user['id'],
				'name' => $user['name'],
				'mail' => $user['email'],
			);
			return $userinfo;
		}
	}

}

?>
