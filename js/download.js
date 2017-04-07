// download js

/*
 * DOM読み込み時
 */
jQuery(document).ready(function($) {
	/*
	 * ダウンロードボタン
	 */
	$('#fcontact_download').click(function() {
		download();

		return false;
	});

	function download() {
		var query = 'action=fcontact_download';

		$.post(ajaxurl, query, function(data) {
//			console.log(data);
			if(data != ''){
				// SJISに変換
				var convdata = convert_str(data, 'SJIS');

				// let downloadData = new Blob([uint8_array], {type: 'application/octet-binary'});
				var downloadData = new Blob([convdata], {type: 'text/csv'});
				var filename = 'export.csv';

				// 出力
				if (window.navigator.msSaveBlob) {
					window.navigator.msSaveBlob(downloadData, filename);
				} else {
					var downloadUrl  = (window.URL || window.webkitURL).createObjectURL(downloadData);
					var link = document.createElement('a');
					link.href = downloadUrl;
					link.download = filename;
					link.click();
					(window.URL || window.webkitURL).revokeObjectURL(downloadUrl);
				}
			}
		});
	}

	/*
	 * 文字コードを変換
	 */
	function convert_str( str, code ) {
		// Unicodeコードポイントの配列に変換する
		var unicode_array = str_to_unicode_array( str );

		// 指定されたコードポイントの配列に変換
		var conv_code_array = Encoding.convert(
			unicode_array, // ※文字列を直接渡すのではない点に注意
			code,  // to
			'UNICODE' // from
		);

		// 文字コード配列をTypedArrayに変換する
		var uint8_array = new Uint8Array( conv_code_array );
		return uint8_array;
	}

	// 文字列から，Unicodeコードポイントの配列を作る
	function str_to_unicode_array( str ){
		var arr = [];
		for( var i = 0; i < str.length; i ++ ){
			arr.push( str.charCodeAt( i ) );
		}
		return arr;
	};

});
