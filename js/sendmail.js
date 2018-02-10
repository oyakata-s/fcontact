// fcontact js

/*
 * DOM読み込み時
 */
jQuery(document).ready(function($) {
	$.overlay.init();
	var form = $('#fcontact .contact-form');

	/*
	 * 送信ボタン
	 */
	$('#fcontact .contact-form input[type=submit]').click(function() {
		if (!validate(form.get()[0])) {
			return true;
		}

		ref = $(this);
		/*
		 * ログイン状態チェック
		 */
		FB.getLoginStatus(function(response) {
			if (response.status === 'connected') {
				/* 
				 * ログイン済み
				 */
				var name = $('#fcontact input.name').val();
				var mail = $('#fcontact input.mail').val();
				var text = cprintf(fcontact_sendmail.send_info, {
					'name' : name,
					'mail' : mail
				});
				$.overlay.show({
					dialog : true,
					title : fcontact_sendmail.send_title,
					text : '<p style="text-indent:1em;">' + text + '</p>',
					onlyOK : false,
					autoHide : false,
					okFunc : function() {
						sendmail(form);
					}
				});
			} else {
				/* 
				 * ログインしてない場合はログイン
				 */
				FB.login(function(response) {
					// ログイン成功
					if (response.authResponse) {
						FB.api('/me?fields=id,name,email&locale=ja_JP', function(response) {
							console.log(response);
							setForm(response);
							var text = cprintf(fcontact_sendmail.send_info, {
								'name' : response.name,
								'mail' : response.email
							});
							$.overlay.show({
								dialog : true,
								title : fcontact_sendmail.send_title,
								text : '<p style="text-indent:1em;">' + text + '</p>',
								onlyOK : false,
								autoHide : false,
								okFunc : function() {
									sendmail(form);
								}
							});
						});
					}
				}, { scope : 'email' });
			}
		});

		/*
		 * facebook sdkを使用できない場合
		 */
		if (!fb_activate) {
			$.overlay.show({
				dialog : true,
				text : '<em>' + fcontact_sendmail.fbapp_not_enable + '</em>',
				onlyOK : true
			});
		}

		return false;
	});

	// キャンセルボタン
	$('#fcontact .confirm-area input[type=reset]').click(function() {
		form.find('textarea').prop('readonly', false);
		form.find('input.confirm,input.clear,input.logout').prop('disabled', false);
		$('html,body').animate({ scrollTop: form.offset().top }, 'fast');
		$('#fcontact .confirm-area').hide();
		return false;
	});

	// ログアウトボタン
	$('#fcontact .contact-form .logout').click(function() {
		fbLogout();
		$.overlay.show({
			dialog : true,
			title : null,
			text : '<p">' + fcontact_sendmail.logout_msg + '</p>',
		});
		return false;
	});

	// 戻るボタン
	$('#fcontact .result-area .back').click(function() {
		window.location.reload();
		return false;
	});

	// エラー時ダイアログの閉じるボタン
	$('#fcontact-overlay .close').click(function() {
		hideOverlay('dialog', false);
		return true;
	});

	/*
	 * バリデート
	 */
	function validate(form) {
		return form.checkValidity();
	}

	/*
	 * 結果を表示
	 */
	function showResult(result) {
		if (result) {
			$('#fcontact .result-area').addClass('success');
		} else {
			$('#fcontact .result-area').addClass('error');
		}
		form.hide();
		$('#fcontact .result-area').show();
	}

	/*
	 * メール送信
	 */
	function sendmail(form) {
		formdata = form.serialize()

		// オーバーレイ表示
		$.overlay.update({
			dialog : false,
			text : '<i class="fa fa-paper-plane"></i>&emsp;' + fcontact_sendmail.sending_msg
		});

		/*
		 * 送信本体
		 */
		$.ajax({
			type: 'POST',
			url: ajaxurl,
			data: formdata,
			dataType: 'json'
		}).done(function(data) {
			if (data.result === 'success') {
				console.log(data);
				showResult(true);
				$.overlay.hide();
			} else {
				console.log('sendmail error: cause:' + data.cause);
				// $('#fcontact .result-area .error .cause').text(data.cause);
				showResult(false);
				$.overlay.hide();
			}
		}).fail(function() {
			$('#fcontact .result-area .error .cause').text('Server Error.');
			showResult(false);
			$.overlay.hide();
		});
	}

	function cprintf(fmt, params) {
		return fmt.replace(/%{(.*?)}/g, function($0, $1) {
			return ( params[$1] && typeof(params[$1]) != "object" ) ?
			params[$1].toString() : JSON.stringify(params[$1]);
		});
	}

});

/*
 * facebookからのレスポンスをもとに
 * formに入力
 */
function setForm(response) {
	jQuery('#fcontact').removeClass('logout');
	jQuery('#fcontact input.fbid').val(response.id);
	jQuery('#fcontact input.name').val(response.name);
	jQuery('#fcontact input.mail').val(response.email);
}

/*
 * formクリア
 */
function clearForm() {
	jQuery('#fcontact input.fbid').val('');
	jQuery('#fcontact input.name').val('');
	jQuery('#fcontact input.mail').val('');
}

/*
 * facebookからログアウトする
 */
function fbLogout() {
	FB.logout(function(response) {
		jQuery('#fcontact').addClass('logout');
		clearForm();
		// jQuery('html,body').animate({ scrollTop: 0 }, 'fast');
	});
}

/*
 * facebookからレスポンスを受け取る
 * /me?fields=id,name,email&locale=ja_JP
 */
function statusChangeCallback(response) {
	if (response.status === 'connected') {
		FB.api('/me?fields=id,name,email&locale=ja_JP', function(response) {
			console.log(response);
			setForm(response);
		});
	} else {
		console.log('user is not logged in.');
		clearForm();
	}
}
