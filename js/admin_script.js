/*
 * 管理画面用スクリプト
 */

jQuery(document).ready(function($) {

	/*
	 * 初期化
	 */

	/*
	 * タブ切替
	 */
	$('#settings-tab li').on('click', 'a', function() {
		var index = $('#settings-tab li a').index(this);
		console.log(index);

		$('#settings-tab li').each(function() {
			$(this).removeClass('active');
		});
		$('#tab-contents .tab-content').each(function() {
			$(this).removeClass('active');
		});

		$(this).parent().addClass('active');
		$('#tab-contents .tab-content').eq(index).addClass('active')

		return false;
	});

});
