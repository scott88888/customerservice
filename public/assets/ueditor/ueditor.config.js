/**
 * ueditor配置
 */

(function () {

    var URL = window.UEDITOR_HOME_URL || getUEBasePath();

    window.UEDITOR_CONFIG = {

        UEDITOR_HOME_URL: URL
        ,is_mobile: 0 // 是否来自移動端
        ,UEDITOR_ROOT_URL: '/assets/ueditor/'
        , serverUrl: URL + "php/controller.php"


        //,zIndex : 900     //編輯器層级的基數,默認是900

        //若實例化編輯器的頁面手動修改的domain，此处需要設定為true
        //,customDomain:false

        //,focus:false //初始化时，是否让編輯器获得焦點true或false

        //如果自訂，最好给p标签如下的行高，要不输入中文时，会有跳動感
        //,initialStyle:'p{line-height:1em}'//編輯器層级的基數,可以用来改变字体等

        //,iframeCssUrl: URL + '/themes/iframe.css' //给編輯区域的iframe引入一个css文件

        //indentValue
        //首行缩进距离,默認是2em
        //,indentValue:'2em'

        //,initialFrameWidth:1000  //初始化編輯器宽度,默認1000
        //,initialFrameHeight:320  //初始化編輯器高度,默認320

        //,readonly : false //編輯器初始化结束後,編輯区域是否是只读的，默認是false

        //,autoClearEmptyNode : true //getContent时，是否刪除空的inlineElement节點（包括嵌套的情况）

        //,fullscreen : false //是否開啟初始化时即全屏，默認關閉

        //,imagePopup:true      //圖片操作的浮層開关，默認打開

        //,autoSyncData:true //自動同步編輯器要送出的資料
        //,emotionLocalization:false //是否開啟表情本地化，默認關閉。若要開啟請确保emotion文件夹下包含官網提供的images表情文件夹

        //粘贴只保留标签，去除标签所有属性
        //,retainOnlyLabelPasted: false

        //,pasteplain:false  //是否默認為纯文字粘贴。false為不使用纯文字粘贴，true為使用纯文字粘贴
        //纯文字粘贴模式下的过滤規則
        //'filterTxtRules' : function(){
        //    function transP(node){
        //        node.tagName = 'p';
        //        node.setStyle();
        //    }
        //    return {
        //        //直接刪除及其字节點内容
        //        '-' : 'script style object iframe embed input select',
        //        'p': {$:{}},
        //        'br':{$:{}},
        //        'div':{'$':{}},
        //        'li':{'$':{}},
        //        'caption':transP,
        //        'th':transP,
        //        'tr':transP,
        //        'h1':transP,'h2':transP,'h3':transP,'h4':transP,'h5':transP,'h6':transP,
        //        'td':function(node){
        //            //没有内容的td直接删掉
        //            var txt = !!node.innerText();
        //            if(txt){
        //                node.parentNode.insertAfter(UE.uNode.createText(' &nbsp; &nbsp;'),node);
        //            }
        //            node.parentNode.removeChild(node,node.innerText())
        //        }
        //    }
        //}()

        //,allHtmlEnabled:false //送出到後台的資料是否包含整个html字符串

        //insertorderedlist
        //有序列表的下拉配置,值留空时支援多語言自動识别，若配置值，則以此值為准
        //,'insertorderedlist':{
        //      //自定的樣式
        //        'num':'1,2,3...',
        //        'num1':'1),2),3)...',
        //        'num2':'(1),(2),(3)...',
        //        'cn':'一,二,三....',
        //        'cn1':'一),二),三)....',
        //        'cn2':'(一),(二),(三)....',
        //     //系统自带
        //     'decimal' : '' ,         //'1,2,3...'
        //     'lower-alpha' : '' ,    // 'a,b,c...'
        //     'lower-roman' : '' ,    //'i,ii,iii...'
        //     'upper-alpha' : '' , lang   //'A,B,C'
        //     'upper-roman' : ''      //'I,II,III...'
        //}

        //insertunorderedlist
        //無序列表的下拉配置，值留空时支援多語言自動识别，若配置值，則以此值為准
        //,insertunorderedlist : { //自定的樣式
        //    'dash' :'— 破折号', //-破折号
        //    'dot':' 。 小圆圈', //系统自带
        //    'circle' : '',  // '○ 小圆圈'
        //    'disc' : '',    // '● 小圆點'
        //    'square' : ''   //'■ 小方块'
        //}
        //,listDefaultPaddingLeft : '30'//默認的左边缩进的基數倍
        //,listiconpath : 'http://bs.baidu.com/listicon/'//自訂标号的路徑
        //,maxListLevel : 3 //限制可以tab的级數, 設定-1為不限制

        //,autoTransWordToList:false  //禁止word中粘贴进来的列表自動变成列表标签

        //fontfamily
        //字体設定 label留空支援多語言自動切换，若配置，則以配置值為准
        //,'fontfamily':[
        //    { label:'',name:'songti',val:'宋体,SimSun'},
        //    { label:'',name:'kaiti',val:'楷体,楷体_GB2312, SimKai'},
        //    { label:'',name:'yahei',val:'微软雅黑,Microsoft YaHei'},
        //    { label:'',name:'heiti',val:'黑体, SimHei'},
        //    { label:'',name:'lishu',val:'隶书, SimLi'},
        //    { label:'',name:'andaleMono',val:'andale mono'},
        //    { label:'',name:'arial',val:'arial, helvetica,sans-serif'},
        //    { label:'',name:'arialBlack',val:'arial black,avant garde'},
        //    { label:'',name:'comicSansMs',val:'comic sans ms'},
        //    { label:'',name:'impact',val:'impact,chicago'},
        //    { label:'',name:'timesNewRoman',val:'times new roman'}
        //]

        //fontsize
        //字号
        //,'fontsize':[10, 11, 12, 14, 16, 18, 20, 24, 36]

        //paragraph
        //段落格式 值留空时支援多語言自動识别，若配置，則以配置值為准
        //,'paragraph':{'p':'', 'h1':'', 'h2':'', 'h3':'', 'h4':'', 'h5':'', 'h6':''}

        //rowspacingtop
        //段间距 值和显示的名字相同
        //,'rowspacingtop':['5', '10', '15', '20', '25']

        //rowspacingBottom
        //段间距 值和显示的名字相同
        //,'rowspacingbottom':['5', '10', '15', '20', '25']

        //lineheight
        //行内间距 值和显示的名字相同
        //,'lineheight':['1', '1.5','1.75','2', '3', '4', '5']

        //customstyle
        //自訂樣式，不支援国际化，此处配置值即可最後显示值
        //block的元素是依据設定段落的逻辑設定的，inline的元素依据BIU的逻辑設定
        //尽量使用一些常用的标签
        //参數说明
        //tag 使用的标签名字
        //label 显示的名字也是用来標識不同類型的標識符，注意这个值每个要不同，
        //style 新增的樣式
        //每一个對象就是一个自訂的樣式
        //,'customstyle':[
        //    {tag:'h1', name:'tc', label:'', style:'border-bottom:#ccc 2px solid;padding:0 4px 0 0;text-align:center;margin:0 0 20px 0;'},
        //    {tag:'h1', name:'tl',label:'', style:'border-bottom:#ccc 2px solid;padding:0 4px 0 0;margin:0 0 10px 0;'},
        //    {tag:'span',name:'im', label:'', style:'font-style:italic;font-weight:bold'},
        //    {tag:'span',name:'hi', label:'', style:'font-style:italic;font-weight:bold;color:rgb(51, 153, 204)'}
        //]

        //打開右键選單功能
        //,enableContextMenu: true
        //右键選單的内容，可以参考plugins/contextmenu.js里边的默認選單的例子，label留空支援国际化，否則以此配置為准
        //,contextMenu:[
        //    {
        //        label:'',       //显示的名稱
        //        cmdName:'selectall',//執行的command命令，当點擊这个右键選單时
        //        //exec可选，有了exec就会在點擊时執行这个function，優先级高于cmdName
        //        exec:function () {
        //            //this是當前編輯器的實例
        //            //this.ui._dialogs['inserttableDialog'].open();
        //        }
        //    }
        //]

        //快速選單
        //,shortcutMenu:["fontfamily", "fontsize", "bold", "italic", "underline", "forecolor", "backcolor", "insertorderedlist", "insertunorderedlist"]

        //elementPathEnabled
        //是否启用元素路徑，默認是显示
       ,elementPathEnabled : false

        //wordCount
        ,wordCount:false          //是否開啟字數统计
        //,maximumWords:10000       //允许的最大字符數
        //字數统计提示，{#count}代表當前字數，{#leave}代表还可以输入多少字符數,留空支援多語言自動切换，否則按此配置显示
        //,wordCountMsg:''   //當前已输入 {#count} 个字符，您还可以输入{#leave} 个字符
        //超出字數限制提示  留空支援多語言自動切换，否則按此配置显示
        //,wordOverFlowMsg:''    //<span style="color:red;">你输入的字符个數已经超出最大允许值，服务器可能会拒绝保存！</span>

        //tab
        //點擊tab键时移動的距离,tabSize倍數，tabNode什么字符做為單位
        //,tabSize:4
        //,tabNode:'&nbsp;'

        //removeFormat
        //清除格式时可以刪除的标签和属性
        //removeForamtTags标签
        //,removeFormatTags:'b,big,code,del,dfn,em,font,i,ins,kbd,q,samp,small,span,strike,strong,sub,sup,tt,u,var'
        //removeFormatAttributes属性
        //,removeFormatAttributes:'class,style,lang,width,height,align,hspace,valign'

        //undo
        //可以最多回退的次數,默認20
        //,maxUndoCount:20
        //当输入的字符數超过该值时，保存一次现场
        //,maxInputCount:1


        //scaleEnabled
        //是否可以拉伸長高,默認true(当開啟时，自動長高失效)
        //,scaleEnabled:false
        //,minFrameWidth:800    //編輯器拖動时最小宽度,默認800
        //,minFrameHeight:220  //編輯器拖動时最小高度,默認220


        //,topOffset:30
        //編輯器底部距离工具栏高度(如果参數大于等于編輯器高度，則設定無效)
        //,toolbarTopOffset:400

        //設定远程圖片是否抓取到本地保存
        //,catchRemoteImageEnable: true //設定是否抓取远程圖片

        //autotypeset
        //自動排版参數
        //,autotypeset: {
        //    mergeEmptyline: true,           //合併空行
        //    removeClass: true,              //去掉冗余的class
        //    removeEmptyline: false,         //去掉空行
        //    textAlign:"left",               //段落的排版方式，可以是 left,right,center,justify 去掉这个属性表示不執行排版
        //    imageBlockLine: 'center',       //圖片的浮動方式，独占一行剧中,左右浮動，默認: center,left,right,none 去掉这个属性表示不執行排版
        //    pasteFilter: false,             //根據規則过滤没事粘贴进来的内容
        //    clearFontSize: false,           //去掉所有的内嵌字号，使用編輯器默認的字号
        //    clearFontFamily: false,         //去掉所有的内嵌字体，使用編輯器默認的字体
        //    removeEmptyNode: false,         // 去掉空节點
        //    //可以去掉的标签
        //    removeTagNames: {标签名字:1},
        //    indent: false,                  // 行首缩进
        //    indentValue : '2em',            //行首缩进的大小
        //    bdc2sb: false,
        //    tobdc: false
        //}

        //tableDragable
        //表格是否可以拖拽
        //,tableDragable: true


        //sourceEditor
        //源碼的查看方式,codemirror 是程式碼高亮，textarea是文字框,默認是codemirror
        //注意默認codemirror只能在ie8+和非ie中使用
        //,sourceEditor:"codemirror"
        //如果sourceEditor是codemirror，还用配置一下两个参數
        //codeMirrorJsUrl js載入的路徑，默認是 URL + "third-party/codemirror/codemirror.js"
        //,codeMirrorJsUrl:URL + "third-party/codemirror/codemirror.js"
        //codeMirrorCssUrl css載入的路徑，默認是 URL + "third-party/codemirror/codemirror.css"
        //,codeMirrorCssUrl:URL + "third-party/codemirror/codemirror.css"
        //編輯器初始化完成後是否进入源碼模式，默認為否。
        //,sourceEditorFirst:false

        //iframeUrlMap
        //dialog内容的路徑 ～会被替换成URL,垓属性一旦打開，将覆盖所有的dialog的默認路徑
        //,iframeUrlMap:{
        //    'anchor':'~/dialogs/anchor/anchor.html',
        //}

        //allowLinkProtocol 允许的連結地址，有这些前缀的連結地址不会自動新增http
        //, allowLinkProtocols: ['http:', 'https:', '#', '/', 'ftp:', 'mailto:', 'tel:', 'git:', 'svn:']

        //webAppKey 百度應用的APIkey，每个站長必须首先去百度官網註冊一个key後方能正常使用app功能，註冊介绍，http://app.baidu.com/static/cms/getapikey.html
        //, webAppKey: ""

        //默認过滤規則相關配置项目
        //,disabledTableInTable:true  //禁止表格嵌套
        //,rgb2Hex:true               //默認产出的資料中的color自動从rgb格式变成16进制格式

		// xss 过滤是否開啟,inserthtml等操作
		,xssFilterRules: true
		//input xss过滤
		,inputXssFilter: true
		//output xss过滤
		,outputXssFilter: true
		// xss过滤白名單 名單来源: https://raw.githubusercontent.com/leizongmin/js-xss/master/lib/default.js
		,whitList: {
			a:      ['target', 'href', 'title', 'class', 'style'],
			abbr:   ['title', 'class', 'style'],
			address: ['class', 'style'],
			area:   ['shape', 'coords', 'href', 'alt'],
			article: [],
			aside:  [],
			audio:  ['autoplay', 'controls', 'loop', 'preload', 'src', 'class', 'style'],
			b:      ['class', 'style'],
			bdi:    ['dir'],
			bdo:    ['dir'],
			big:    [],
			blockquote: ['cite', 'class', 'style'],
			br:     [],
			caption: ['class', 'style'],
			center: [],
			cite:   [],
			code:   ['class', 'style'],
			col:    ['align', 'valign', 'span', 'width', 'class', 'style'],
			colgroup: ['align', 'valign', 'span', 'width', 'class', 'style'],
			dd:     ['class', 'style'],
			del:    ['datetime'],
			details: ['open'],
			dl:     ['class', 'style'],
			dt:     ['class', 'style'],
			em:     ['class', 'style'],
			font:   ['color', 'size', 'face'],
			footer: [],
			h1:     ['class', 'style'],
			h2:     ['class', 'style'],
			h3:     ['class', 'style'],
			h4:     ['class', 'style'],
			h5:     ['class', 'style'],
			h6:     ['class', 'style'],
			header: [],
			hr:     ['class', 'style'],
			i:      ['class', 'style'],
			ins:    ['datetime'],
			li:     ['class', 'style'],
			mark:   [],
			nav:    [],
			ol:     ['class', 'style'],
            div:    ['class'], // 最好过滤, 'style'标签
			p:      ['class', 'style'],
			pre:    ['class', 'style'],
			s:      [],
			section:['class', 'style'],
			small:  [],
			span:   ['class', 'style'],
			sub:    ['class', 'style'],
			sup:    ['class', 'style'],
			strong: ['class', 'style'],
			table:  ['width', 'border', 'align', 'valign', 'class', 'style'],
			tbody:  ['align', 'valign', 'class', 'style'],
			td:     ['width', 'rowspan', 'colspan', 'align', 'valign', 'class', 'style'],
			tfoot:  ['align', 'valign', 'class', 'style'],
			th:     ['width', 'rowspan', 'colspan', 'align', 'valign', 'class', 'style'],
			thead:  ['align', 'valign', 'class', 'style'],
			tr:     ['rowspan', 'align', 'valign', 'class', 'style'],
			tt:     [],
			u:      [],
			ul:     ['class', 'style'],
            img:    ['src', 'alt', 'title', 'width', 'height', 'id', '_src', '_url', 'loadingclass', 'class', 'style', 'word_img'],
            source: ['src', 'type'],
            video: ['autoplay', 'controls', 'url', 'class', 'style', 'type', 'class', 'pluginspage', 'src', 'width', 'height', 'align', 'style', 'wmode', 'play','autoplay','loop', 'menu', 'allowscriptaccess', 'allowfullscreen', 'controls', 'preload'],
            embed: ['type', 'class', 'pluginspage', 'src', 'width', 'height', 'align', 'style', 'wmode', 'play','autoplay','loop', 'menu', 'allowscriptaccess', 'allowfullscreen', 'controls', 'preload'],
            iframe: ['src', 'class', 'height', 'width', 'max-width', 'max-height', 'align', 'frameborder', 'allowfullscreen']
		}
    };

    function getUEBasePath(docUrl, confUrl) {

        return getBasePath(docUrl || self.document.URL || self.location.href, confUrl || getConfigFilePath());

    }

    function getConfigFilePath() {

        var configPath = document.getElementsByTagName('script');

        return configPath[ configPath.length - 1 ].src;

    }

    function getBasePath(docUrl, confUrl) {

        var basePath = confUrl;


        if (/^(\/|\\\\)/.test(confUrl)) {

            basePath = /^.+?\w(\/|\\\\)/.exec(docUrl)[0] + confUrl.replace(/^(\/|\\\\)/, '');

        } else if (!/^[a-z]+:/i.test(confUrl)) {

            docUrl = docUrl.split("#")[0].split("?")[0].replace(/[^\\\/]+$/, '');

            basePath = docUrl + "" + confUrl;

        }

        return optimizationPath(basePath);

    }

    function optimizationPath(path) {

        var protocol = /^[a-z]+:\/\//.exec(path)[ 0 ],
            tmp = null,
            res = [];

        path = path.replace(protocol, "").split("?")[0].split("#")[0];

        path = path.replace(/\\/g, '/').split(/\//);

        path[ path.length - 1 ] = "";

        while (path.length) {

            if (( tmp = path.shift() ) === "..") {
                res.pop();
            } else if (tmp !== ".") {
                res.push(tmp);
            }

        }

        return protocol + res.join("/");

    }

    window.UE = {
        getUEBasePath: getUEBasePath
    };

})();
