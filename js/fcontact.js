// fcontact js

/*
 * DOM読み込み時
 */
jQuery(document).ready(function() {
	var form = jQuery('#fcontact .contact-form');

	/*
	 * 送信、確認ボタン
	 */
	jQuery('#fcontact .contact-form input[type=submit]').click(function() {
		if (!validate(form.get()[0])) {
			return true;
		}

		ref = jQuery(this);
		/*
		 * ログイン状態で処理を分ける
		 */
		FB.getLoginStatus(function(response) {
			if (response.status === 'connected') {
				// ログイン済み
				if (ref.hasClass('submit')) {
					var formdata = form.serialize();
					sendmail(formdata);
				} else {
					// 確認画面を表示
					showConfirm(form);
				}
			} else {
				// ログインしていない場合はログイン
				FB.login(function(response) {
					if (response.authResponse) {
						FB.api('/me?fields=id,name,email&locale=ja_JP', function(response) {
							console.log(response);
							setForm(response);
							if (ref.hasClass('submit')) {
								var formdata = form.serialize();
								sendmail(formdata);
							} else {
								showConfirm(form);
							}
						});
					}
				}, { scope : 'email' });
			}
		});

		/*
		 * facebook sdkを使用できない場合
		 */
		if (!fb_activate) {
			showOverlay('dialog', '<em>Facebook Application is not activated.</em>', false);
		}

		return false;
	});

	/*
	 * 確認エリアの送信ボタン
	 */
	jQuery('#fcontact .confirm-area input[type=submit]').click(function() {
		var formdata = form.serialize();
		sendmail(formdata);
		return false;
	});

	// キャンセルボタン
	jQuery('#fcontact .confirm-area input[type=reset]').click(function() {
		form.find('textarea').prop('readonly', false);
		form.find('input.confirm,input.clear,input.logout').prop('disabled', false);
		jQuery('html,body').animate({ scrollTop: form.offset().top }, 'fast');
		jQuery('#fcontact .confirm-area').hide();
		return false;
	});

	// ログアウトボタン
	jQuery('#fcontact .contact-form .logout').click(function() {
		fbLogout();
		return false;
	});

	// 戻るボタン
	jQuery('#fcontact .result-area .back').click(function() {
		window.location.reload();
		return false;
	});

	// エラー時ダイアログの閉じるボタン
	jQuery('#fcontact .overlay .close').click(function() {
		hideOverlay('dialog', false);
		return true;
	});

});

/*
 * 入力チェック
 */
function validate(form) {
	return form.checkValidity();
}

/*
 * オーバーレイを表示
 */
function showOverlay(cls, text, fade) {
	overlay = jQuery('#fcontact .overlay');
	target = jQuery('#fcontact .overlay .' + cls);
	target.find('.text').html(text);

	if (typeof(fade)==='undefined' || fade===true) {
		overlay.addClass(cls).fadeIn('fast');
	} else {
		overlay.addClass(cls).show();
	}

	target.css({
		height: target.outerHeight(),
		top: 0,
		bottom: 0,
	});
}

/*
 * オーバーレイを隠す
 */
function hideOverlay(cls, fade) {
	if (typeof(fade)==='undefined' || fade===true) {
		overlay.removeClass(cls).fadeOut('fast');
	} else {
		overlay.removeClass(cls).hide();
	}
}

/*
 * 確認を表示
 */
function showConfirm(form) {
	form.find('textarea').prop('readonly', true);
	form.find('input.confirm,input.clear,input.logout').prop('disabled', true);
	confirm = jQuery('#fcontact .confirm-area');
	confirm.find('.name').html(form.find('input.name').val());
	confirm.find('.mail').html(form.find('input.mail').val());
	confirm.find('.message').html(form.find('textarea').val().replace(/\r?\n/g,'<br>'));
	confirm.show();
	jQuery('html,body').animate({ scrollTop: confirm.offset().top }, 'fast');
}

/*
 * 結果を表示
 */
function showResult(result) {
	if (result) {
		jQuery('#fcontact .result-area .error').hide();
	} else {
		jQuery('#fcontact .result-area .success').hide();
	}
	jQuery('#fcontact .confirm-area').hide();
	jQuery('#fcontact .contact-form').hide();
	jQuery('#fcontact .result-area').show();
	jQuery('html,body').animate({ scrollTop: 0 }, 'fast');
}

/*
 * メール送信処理
 */
function sendmail(formdata) {
	showOverlay('message', 'Sending...');

	jQuery.ajax({
		type: 'POST',
		url: ajaxurl,
		data: formdata,
		dataType: 'json',
		success: function(data) {
			console.log(data);
			if (data.result === 'success') {
				showResult(true);
				hideOverlay('message');
			} else {
				jQuery('#fcontact .result-area .error .cause').text(data.result);
				showResult(false);
				hideOverlay('message');
			}
		},
		error: function() {
			jQuery('#fcontact .result-area .error .cause').text(__('Could not communicate with server.', 'fcontact'));
			showResult(false);
			hideOverlay('message');
		}
	});

}

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
		jQuery('html,body').animate({ scrollTop: 0 }, 'fast');
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
