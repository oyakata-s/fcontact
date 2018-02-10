(function($) {
	$.overlay = {
		// 変数
		overlay : null,
		setting : null,

		// 初期化
		init : function() {
			var selector = 'simple-overlay';

			// DOMエレメントの追加
			var element = $('<div />', {
				'id' : selector
			});
			var html = '<div class="message"><span class="text"></span></div><div class="dialog"><p class="title"></p><p class="text"></p><div class="control"><button type="button" class="OK">OK</button><button type="button" class="Cancel">Cancel</button></div></div>';
			element.html(html);
			$('body').append(element);

			overlay = $('#' + selector);

			return this;
		},

		// 破棄
		destroy : function() {
			overlay.remove();
		},

		// 表示
		show : function(option) {

			// パラメータとデフォルト値のマージ
			var defaults = {
				dialog: false,
				title: null,
				text: 'よろしいですか？',
				okFunc: null,
				cancelFunc: null,
				onlyOK: true,
				fade: true,
				autoHide: true
			};
			setting = $.extend(defaults, option);

			if (!setting.dialog) {
				autoHide = false;
			}

			// パーツ表示設定
			if (setting.title !== null) {
				overlay.find('.title').html(setting.title).show();
			} else {
				overlay.find('.title').hide();
			}
			if (setting.onlyOK) {
				overlay.find('button.Cancel').hide();
			} else {
				overlay.find('button.Cancel').show();
			}

			// ボタン動作設定
			overlay.find('.control')
				.on('click', 'button.OK', function() {
					if ($.isFunction(setting.okFunc)) {
						setting.okFunc.call(this);
					}
					if (setting.autoHide) {
						$.overlay.hide();
					}
					return false;
				})
				.on('click', 'button.Cancel', function() {
					if ($.isFunction(setting.cancelFunc)) {
						setting.cancelFunc.call(this);
					}
					$.overlay.hide();
					return false;
				})

			// 表示
			var cls = (setting.dialog) ? 'dialog' : 'message';
			var target = overlay.find('.' + cls);
			target.find('.text').html(setting.text);

			if (setting.fade===true) {
				overlay.addClass(cls).fadeIn('fast');
			} else {
				overlay.addClass(cls).show();
			}

			target.css({
				height: target.outerHeight(),
				top: 0,
				bottom: 0,
			});
		},

		// 非表示
		hide : function() {
			var cls = (setting.dialog) ? 'dialog' : 'message';
			if (setting.fade) {
				overlay.removeClass(cls).fadeOut('fast');
			} else {
				overlay.removeClass(cls).hide();
			}
			overlay.find('.' + cls).attr('style', '');
		},

		// 更新
		update : function(option) {
			var old = $.extend(true, {}, setting);
			setting = $.extend(setting, option);

			// パーツ表示設定
			if (setting.title !== null) {
				overlay.find('.title').html(setting.title).show();
			}
			if (setting.onlyOK) {
				overlay.find('button.Cancel').hide();
			} else {
				overlay.find('button.Cancel').show();
			}

			// ボタン動作設定
			overlay.find('.control').off()
				.on('click', 'button.OK', function() {
					if ($.isFunction(setting.okFunc)) {
						setting.okFunc.call(this);
					}
					if (setting.autoHide) {
						$.overlay.hide();
					}
					return false;
				})
				.on('click', 'button.Cancel', function() {
					if ($.isFunction(setting.cancelFunc)) {
						setting.cancelFunc.call(this);
					}
					if (setting.autoHide) {
						$.overlay.hide();
					}
					return false;
				})

			if (old.dialog !== setting.dialog) {
				overlay.removeClass('dialog message').hide();
			}

			// 表示
			var cls = (setting.dialog) ? 'dialog' : 'message';
			var target = overlay.find('.' + cls);
			target.attr('style', '');

			target.find('.text').html(setting.text);
			overlay.addClass(cls).show();

			target.css({
				height: target.outerHeight(),
				top: 0,
				bottom: 0,
			});
		}
	};

})(jQuery);
