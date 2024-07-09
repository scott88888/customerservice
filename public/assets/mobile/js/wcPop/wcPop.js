/**
	* @title 			彈窗插件【仿微信】wcPop-v1.0 beta (UTF-8)
	* @Create		hison
	* @Timer		2018/03/30 11:30:45 GMT+0800 (中國标准時間)
*/
!function(win){
	var _doc = win.document, _docEle = _doc.documentElement, index = 0,
	util = {
		$: function(id){
			return _doc.getElementById(id);
		},
		touch: function(o, fn){
			o.addEventListener("click", function(e){
				fn.call(this, e);
			}, !1);
		},
		//取得插件js路徑
		jspath: function(){
			for(var s = _doc.getElementsByTagName("script"), i = s.length; i > 0; i--)
				if(s[i-1].src && s[i-1].src.match(/wcPop[\w\-\.]*\.js/) != null)
					return s[i-1].src.substring(0, s[i-1].src.lastIndexOf("/")+1);
		},
		timer: {}
	},
	wcPop = function(options){
		var _this = this,
			config = {
				id: 'wcPop',				//彈窗ID標識 (不同ID對应不同彈窗)
				title: '',					//標題
				content: '',				//内容
				style: '',					//自定彈窗樣式
				skin: '',					//自定彈窗显示風格 ->目前支援配置  toast(仿微信toast風格)、footer(底部對話框風格)、actionsheet(底部弹出式選單)、ios|android(仿微信樣式)
				icon: '',					//彈窗小圖标(success | info | error | loading)
				
				shade: true,				//是否显示遮罩層
				shadeClose: true,			//是否點擊遮罩时關閉層
				anim: 'scaleIn',			//scaleIn：缩放打開(默認)  fadeIn：渐变打開  fadeInUpBig：由上向下打開 fadeInDownBig：由下向上打開  rollIn：左側翻转打開  shake：震動  footer：底部向上弹出
				time: 0,					//設定彈窗自動關閉秒數1、 2、 3
				zIndex: 9999,				//設定元素層叠
				
				btns: null,					//不設定則不显示按钮，btn参數: [{按钮1配置}, {按钮2配置}]
				end: null					//層销毁後的回调函數
			};
		
		_this.opts = options;
		for(var i in config){
			if(!(i in _this.opts)){
				_this.opts[i] = config[i];
			}
		}
		_this.init();
	};
	
	wcPop.prototype = {
		init: function(){
			var _this = this, opt = _this.opts, xwbox = null,
			ftBtns = function(){
				if(!opt.btns) return;
				var btnTpl = "";
				for(var i in opt.btns){
					btnTpl += '<span class="btn" data-index="'+i+'" style="'+(opt.btns[i].style ? opt.btns[i].style : '')+'">'+opt.btns[i].text+'</span>';
				}
				return btnTpl;
			}();
			
			util.$(opt.id) ? (xwbox = util.$(opt.id)) : (xwbox = _doc.createElement("div"), xwbox.id = opt.id);
			opt.skin && (xwbox.setAttribute("type", opt.skin));
			xwbox.setAttribute("index", index);
			xwbox.setAttribute("class", "wcPop wcPop"+index);
			xwbox.innerHTML = [
				'<div class="popui__modal-panel">',
					/**遮罩*/
					opt.shade ? ('<div class="popui__modal-mask" style="z-index:'+(_this.maxIndex()+1)+'"></div>') : '',
					/**窗体*/
					'<div class="popui__panel-main" style="z-index:'+(_this.maxIndex()+2)+'">\
						<div class="popui__panel-section">\
							<div class="popui__panel-child '+(opt.anim ? 'anim-'+opt.anim : '')+' '+(opt.skin ? 'popui__'+opt.skin : '')+'" style="'+opt.style+'">',
								opt.title ? ('<div class="popui__panel-tit">'+opt.title+'</div>') : '',
								opt.content ? ('<div class="popui__panel-cnt">'+(opt.skin == "toast" && opt.icon ? ('<div class="popui__toast-icon"><img class="'+(opt.icon == "loading" ? "anim-loading" : '')+'" src="'+util.jspath()+'skin/'+opt.icon+'.png" /></div>') : '') + opt.content +'</div>') : '',
								opt.btns ? '<div class="popui__panel-btn">'+ftBtns+'</div>' : '',
							'</div>\
						</div>\
					</div>\
				</div>'
			].join('');
			//_doc.body.insertBefore(xwbox, _doc.body.childNodes[0]);
			_doc.body.appendChild(xwbox);
			
			this.index = index++;
			_this.callback();
		},
		callback: function(){
			var _this = this, opt = _this.opts;
			//自動關閉彈窗
			if(opt.time){
				util.timer[_this.index] = setTimeout(function(){
					exports.close(_this.index);
					typeof opt.end == "function" && opt.end.call(_this);
				}, opt.time * 1000);
			}
			
			//按钮事件
			if(opt.btns){
				for (var o = util.$(opt.id).getElementsByClassName("popui__panel-btn")[0].children, len = o.length, i = 0; i < len; i++)
					util.touch(o[i], function(e){
						var idx = this.getAttribute("data-index"), btn = opt.btns[idx];
						typeof btn.onTap === "function" && btn.onTap(e);
					});
			}
			//點擊遮罩層關閉
			if(opt.shade && opt.shadeClose){
				var c = util.$(opt.id).getElementsByClassName("popui__modal-mask")[0];
				util.touch(c, function () {
					exports.close(_this.index)
				});
			}
		},
		//取得彈窗最大層级
		maxIndex: function(){
			for(var idx = this.opts.zIndex, elem = _doc.getElementsByTagName("*"), i = 0, len = elem.length; i < len; i++)
				idx = Math.max(idx, elem[i].style.zIndex);
			return idx;
		}
	};
	
	var exports = (function(){
		//實例化彈窗(返回 彈窗索引值)
		fn = function(args){
			var o = new wcPop(args);
			return o.index;
		};
		
		//關閉彈窗
		fn.close = function(index){
			var index = index ? index : "";
			var o = _doc.getElementsByClassName("wcPop"+index)[0];

			if(o){
				o.setAttribute("class", "wcPop-close");
				setTimeout(function(){
					_doc.body.removeChild(o);
					clearTimeout(util.timer[index]);
					delete util.timer[index];
				}, 200)
			}
		}
		
		//載入css
		fn.load = function(path){
			for(var ck = _doc.createElement("link"), lk = _doc.getElementsByTagName("link"), i = lk.length; i > 0; i--)
				if(lk[i-1].href == path) return;
			ck.type="text/css";
			ck.rel = "stylesheet";
			ck.href = util.jspath() + path;
			_doc.getElementsByTagName("head")[0].appendChild(ck);
		};
		
		//更多接口
		fn.moreAPI = function(title, content, time){
			var opts = {
				title: title, content: content, time: time
			}
			fn(opts);
		};
		
		return fn;
	}());
	
	//載入css
	exports.load("skin/wcPop.css");
	
	win.wcPop = exports;
}(window);