/**
	* @title 			數字输入法键盘插件【仿微信】wcKeyBoard-v1.0 beta (UTF-8)
	* @Create		hison
	* @Timer		2018/04/27 15:30:45 GMT+0800 (中國标准時間)
*/
!function (win) {
	var _doc = win.document, _docEle = _doc.documentElement,
		util = {
			$: function (id) {
				return _doc.getElementById(id);
			},
			touch: function (o, fn) {
				o.addEventListener("click", function (e) {
					fn.call(this, e);
				}, !1);
			}
		},
		wcKeyBoard = function (options) {
			var that = this,
				config = {
					id: 'wcKeyBoard',				//彈窗ID標識 (不同ID對应不同彈窗)
					resId: '#wcKeyBoardRes'
				};
			if (!(that instanceof wcKeyBoard)) {
				return new wcKeyBoard(options);
			}

			that.opts = options || {};
			for (var i in config) {
				if (!(i in that.opts)) {
					that.opts[i] = config[i];
				}
			}
			that.init();
		};
	wcKeyBoard.prototype = {
		init: function () {
			var that = this, opt = that.opts, keyboradBox = null;
			if(util.$(opt.id)) return;
			keyboradBox = _doc.createElement("div"); keyboradBox.id = opt.id; keyboradBox.className = "wckeyboard";
			keyboradBox.innerHTML = [
				'<div class="keyboardPanel">\
							<div class="keyboard-tmpl">\
								<div class="keyboard-result" id="wcKeyBoardRes" style="display: none;"></div>\
								<div class="keyboard-xclose"></div>\
								<ul class="clearfix">\
									<li class="number">1</li>\
									<li class="number">2</li>\
									<li class="number">3</li>\
									<li class="number">4</li>\
									<li class="number">5</li>\
									<li class="number">6</li>\
									<li class="number">7</li>\
									<li class="number">8</li>\
									<li class="number">9</li>\
									<li class="float">.</li>\
									<li class="zero">0</li>\
									<li class="del"></li>\
								</ul>\
							</div>\
						</div>'
			].join('');
			_doc.body.appendChild(keyboradBox);

			that.callback();
		},
		callback: function () {
			var that = this, opt = that.opts, resObj = $(opt.resId);
			// 處理數字
			$("#" + opt.id).on("click", ".number", function () {
				if (resObj.text().indexOf(".") != -1 && resObj.text().substring(resObj.text().indexOf(".") + 1, resObj.text().length).length == 2) {
					return;
				}
				if ($.trim(resObj.text()) == "0") {
					return;
				}
				if (parseInt(resObj.text()) >= 10000 && resObj.text().indexOf(".") == -1) {
					return;
				}
				resObj.text(resObj.text() + $(this).text());
				resObj.val(resObj.text());
			});
			// 處理小數點
			$("#" + opt.id).on("click", ".float", function () {
				if ($.trim(resObj.text()) == "" || resObj.text().indexOf(".") != -1) {
					return;
				}
				resObj.text(resObj.text() + $(this).text());
				resObj.val(resObj.text());
			});
			// 處理數字0
			$("#" + opt.id).on("click", ".zero", function () {
				if (resObj.text().indexOf(".") != -1 && resObj.text().substring(resObj.text().indexOf(".") + 1, resObj.text().length).length == 2) {
					return;
				}
				if ($.trim(resObj.text()) == "0") {
					return;
				}
				if (parseInt(resObj.text()) >= 10000 && resObj.text().indexOf(".") == -1) {
					return;
				}
				resObj.text(resObj.text() + $(this).text());
				resObj.val(resObj.text());
			});
			// 處理刪除
			$("#" + opt.id).on("click", ".del", function () {
				resObj.text(resObj.text().substring(0, resObj.text().length - 1));
				resObj.val(resObj.text());
			});
			// 關閉键盘
			$("#" + opt.id).on("click", ".keyboard-xclose", function () {
				$("#" + opt.id).remove();
			});
		}
	};
	win.wcKeyBoard = wcKeyBoard;
}(window);
