/**
 * Created with JetBrains PhpStorm.
 * User: taoqili
 * Date: 12-6-12
 * Time: 下午5:02
 * To change this template use File | Settings | File Templates.
 */
UE.I18N['zh-cn'] = {
    'labelMap':{
        'anchor':'锚點', 'undo':'撤销', 'redo':'重做', 'bold':'加粗', 'indent':'首行缩进', 'snapscreen':'截圖',
        'italic':'斜体', 'underline':'下划线', 'strikethrough':'刪除线', 'subscript':'下标','fontborder':'字符边框',
        'superscript':'上标', 'formatmatch':'格式刷', 'source':'源程式碼', 'blockquote':'引用',
        'pasteplain':'纯文字粘贴模式', 'selectall':'全选', 'print':'列印', 'preview':'預覽',
        'horizontal':'分隔线', 'removeformat':'清除格式', 'time':'時間', 'date':'日期',
        'unlink':'取消連結', 'insertrow':'前插入行', 'insertcol':'前插入列', 'mergeright':'右合併單元格', 'mergedown':'下合併單元格',
        'deleterow':'刪除行', 'deletecol':'刪除列', 'splittorows':'拆分成行',
        'splittocols':'拆分成列', 'splittocells':'完全拆分單元格','deletecaption':'刪除表格標題','inserttitle':'插入標題',
        'mergecells':'合併多个單元格', 'deletetable':'刪除表格', 'cleardoc':'清空文档','insertparagraphbeforetable':"表格前插入行",'insertcode':'程式碼語言',
        'fontfamily':'字体', 'fontsize':'字号', 'paragraph':'段落格式', 'simpleupload':'單圖上傳', 'insertimage':'多圖上傳','edittable':'表格属性','edittd':'單元格属性', 'link':'超連結',
        'emotion':'表情', 'spechars':'特殊字符', 'searchreplace':'查詢替换', 'map':'Baidu地圖', 'gmap':'Google地圖',
        'insertvideo':'视频', 'help':'帮助', 'justifyleft':'居左對齐', 'justifyright':'居右對齐', 'justifycenter':'居中對齐',
        'justifyjustify':'两端對齐', 'forecolor':'字体颜色', 'backcolor':'背景色', 'insertorderedlist':'有序列表',
        'insertunorderedlist':'無序列表', 'fullscreen':'全屏', 'directionalityltr':'从左向右输入', 'directionalityrtl':'从右向左输入',
        'rowspacingtop':'段前距', 'rowspacingbottom':'段後距',  'pagebreak':'分頁', 'insertframe':'插入Iframe', 'imagenone':'默認',
        'imageleft':'左浮動', 'imageright':'右浮動', 'attachment':'附件', 'imagecenter':'居中', 'wordimage':'圖片转存',
        'lineheight':'行间距','edittip' :'編輯提示','customstyle':'自訂標題', 'autotypeset':'自動排版',
        'webapp':'百度應用','touppercase':'字母大寫', 'tolowercase':'字母小寫','background':'背景','template':'模板','scrawl':'涂鸦',
        'music':'音乐','inserttable':'插入表格','drafts': '从草稿箱載入', 'charts': '圖表'
    },
    'insertorderedlist':{
        'num':'1,2,3...',
        'num1':'1),2),3)...',
        'num2':'(1),(2),(3)...',
        'cn':'一,二,三....',
        'cn1':'一),二),三)....',
        'cn2':'(一),(二),(三)....',
        'decimal':'1,2,3...',
        'lower-alpha':'a,b,c...',
        'lower-roman':'i,ii,iii...',
        'upper-alpha':'A,B,C...',
        'upper-roman':'I,II,III...'
    },
    'insertunorderedlist':{
        'circle':'○ 大圆圈',
        'disc':'● 小黑點',
        'square':'■ 小方块 ',
        'dash' :'— 破折号',
        'dot':' 。 小圆圈'
    },
    'paragraph':{'p':'段落', 'h1':'標題 1', 'h2':'標題 2', 'h3':'標題 3', 'h4':'標題 4', 'h5':'標題 5', 'h6':'標題 6'},
    'fontfamily':{
        'songti':'宋体',
        'kaiti':'楷体',
        'heiti':'黑体',
        'lishu':'隶书',
        'yahei':'微软雅黑',
        'andaleMono':'andale mono',
        'arial': 'arial',
        'arialBlack':'arial black',
        'comicSansMs':'comic sans ms',
        'impact':'impact',
        'timesNewRoman':'times new roman'
    },
    'customstyle':{
        'tc':'標題居中',
        'tl':'標題居左',
        'im':'强调',
        'hi':'明显强调'
    },
    'autoupload': {
        'exceedSizeError': '文件大小超出限制',
        'exceedTypeError': '文件格式不允许',
        'jsonEncodeError': '服务器返回格式錯誤',
        'loading':"正在上傳...",
        'loadError':"上傳錯誤",
        'errorLoadConfig': '後端配置项没有正常載入，上傳插件不能正常使用！'
    },
    'simpleupload':{
        'exceedSizeError': '文件大小超出限制',
        'exceedTypeError': '文件格式不允许',
        'jsonEncodeError': '服务器返回格式錯誤',
        'loading':"正在上傳...",
        'loadError':"上傳錯誤",
        'errorLoadConfig': '後端配置项没有正常載入，上傳插件不能正常使用！'
    },
    'elementPathTip':"元素路徑",
    'wordCountTip':"字數统计",
    'wordCountMsg':'當前已输入{#count}个字符, 您还可以输入{#leave}个字符。 ',
    'wordOverFlowMsg':'<span style="color:red;">字數超出最大允许值，服务器可能拒绝保存！</span>',
    'ok':"确认",
    'cancel':"取消",
    'closeDialog':"關閉對話框",
    'tableDrag':"表格拖動必须引入uiUtils.js文件！",
    'autofloatMsg':"工具栏浮動依赖編輯器UI，您首先需要引入UI文件!",
    'loadconfigError': '取得後台配置项請求出错，上傳功能将不能正常使用！',
    'loadconfigFormatError': '後台配置项返回格式出错，上傳功能将不能正常使用！',
    'loadconfigHttpError': '請求後台配置项http錯誤，上傳功能将不能正常使用！',
    'snapScreen_plugin':{
        'browserMsg':"仅支援IE浏览器！",
        'callBackErrorMsg':"服务器返回資料有误，請檢查配置项之後重试。",
        'uploadErrorMsg':"截圖上傳失敗，請檢查服务器端环境! "
    },
    'insertcode':{
        'as3':'ActionScript 3',
        'bash':'Bash/Shell',
        'cpp':'C/C++',
        'css':'CSS',
        'cf':'ColdFusion',
        'c#':'C#',
        'delphi':'Delphi',
        'diff':'Diff',
        'erlang':'Erlang',
        'groovy':'Groovy',
        'html':'HTML',
        'java':'Java',
        'jfx':'JavaFX',
        'js':'JavaScript',
        'pl':'Perl',
        'php':'PHP',
        'plain':'Plain Text',
        'ps':'PowerShell',
        'python':'Python',
        'ruby':'Ruby',
        'scala':'Scala',
        'sql':'SQL',
        'vb':'Visual Basic',
        'xml':'XML'
    },
    'confirmClear':"確定清空當前文档么？",
    'contextMenu':{
        'delete':"刪除",
        'selectall':"全选",
        'deletecode':"刪除程式碼",
        'cleardoc':"清空文档",
        'confirmclear':"確定清空當前文档么？",
        'unlink':"刪除超連結",
        'paragraph':"段落格式",
        'edittable':"表格属性",
        'aligntd':"單元格對齐方式",
        'aligntable':'表格對齐方式',
        'tableleft':'左浮動',
        'tablecenter':'居中显示',
        'tableright':'右浮動',
        'edittd':"單元格属性",
        'setbordervisible':'設定表格边线可见',
        'justifyleft':'左對齐',
        'justifyright':'右對齐',
        'justifycenter':'居中對齐',
        'justifyjustify':'两端對齐',
        'table':"表格",
        'inserttable':'插入表格',
        'deletetable':"刪除表格",
        'insertparagraphbefore':"前插入段落",
        'insertparagraphafter':'後插入段落',
        'deleterow':"刪除當前行",
        'deletecol':"刪除當前列",
        'insertrow':"前插入行",
        'insertcol':"左插入列",
        'insertrownext':'後插入行',
        'insertcolnext':'右插入列',
        'insertcaption':'插入表格名稱',
        'deletecaption':'刪除表格名稱',
        'inserttitle':'插入表格標題行',
        'deletetitle':'刪除表格標題行',
        'inserttitlecol':'插入表格標題列',
        'deletetitlecol':'刪除表格標題列',
        'averageDiseRow':'平均分布各行',
        'averageDisCol':'平均分布各列',
        'mergeright':"向右合併",
        'mergeleft':"向左合併",
        'mergedown':"向下合併",
        'mergecells':"合併單元格",
        'splittocells':"完全拆分單元格",
        'splittocols':"拆分成列",
        'splittorows':"拆分成行",
        'tablesort':'表格排序',
        'enablesort':'設定表格可排序',
        'disablesort':'取消表格可排序',
        'reversecurrent':'逆序當前',
        'orderbyasc':'按ASCII字符升序',
        'reversebyasc':'按ASCII字符降序',
        'orderbynum':'按數值大小升序',
        'reversebynum':'按數值大小降序',
        'borderbk':'边框底纹',
        'setcolor':'表格隔行变色',
        'unsetcolor':'取消表格隔行变色',
        'setbackground':'选区背景隔行',
        'unsetbackground':'取消选区背景',
        'redandblue':'红蓝相间',
        'threecolorgradient':'三色渐变',
        'copy':"複製(Ctrl + c)",
        'copymsg': "浏览器不支援,請使用 'Ctrl + c'",
        'paste':"粘贴(Ctrl + v)",
         'pastemsg': "浏览器不支援,請使用 'Ctrl + v'"
    },
    'copymsg': "浏览器不支援,請使用 'Ctrl + c'",
    'pastemsg': "浏览器不支援,請使用 'Ctrl + v'",
    'anthorMsg':"連結",
    'clearColor':'清空颜色',
    'standardColor':'标准颜色',
    'themeColor':'主題顏色',
    'property':'属性',
    'default':'默認',
    'modify':'修改',
    'justifyleft':'左對齐',
    'justifyright':'右對齐',
    'justifycenter':'居中',
    'justify':'默認',
    'clear':'清除',
    'anchorMsg':'锚點',
    'delete':'刪除',
    'clickToUpload':"點擊上傳",
    'unset':'尚未設定語言文件',
    't_row':'行',
    't_col':'列',
    'more':'更多',
    'pasteOpt':'粘贴选项',
    'pasteSourceFormat':"保留源格式",
    'tagFormat':'只保留标签',
    'pasteTextFormat':'只保留文字',
    'autoTypeSet':{
        'mergeLine':"合併空行",
        'delLine':"清除空行",
        'removeFormat':"清除格式",
        'indent':"首行缩进",
        'alignment':"對齐方式",
        'imageFloat':"圖片浮動",
        'removeFontsize':"清除字号",
        'removeFontFamily':"清除字体",
        'removeHtml':"清除冗余HTML程式碼",
        'pasteFilter':"粘贴过滤",
        'run':"執行",
        'symbol':'符号轉換',
        'bdc2sb':'全角转半角',
        'tobdc':'半角转全角'
    },

    'background':{
        'static':{
            'lang_background_normal':'背景設定',
            'lang_background_local':'線上圖片',
            'lang_background_set':'选项',
            'lang_background_none':'無背景色',
            'lang_background_colored':'有背景色',
            'lang_background_color':'颜色設定',
            'lang_background_netimg':'網络圖片',
            'lang_background_align':'對齐方式',
            'lang_background_position':'精確定位',
            'repeatType':{'options':["居中", "横向重复", "纵向重复", "平铺","自訂"]}

        },
        'noUploadImage':"當前未上傳过任何圖片！",
        'toggleSelect':"單击可切换选中狀態\n原圖尺寸: "
    },
    //===============dialog i18N=======================
    'insertimage':{
        'static':{
            'lang_tab_remote':"插入圖片", //节點
            'lang_tab_upload':"本地上傳",
            'lang_tab_online':"線上管理",
            'lang_tab_search':"圖片搜索",
            'lang_input_url':"地 址：",
            'lang_input_size':"大 小：",
            'lang_input_width':"宽度",
            'lang_input_height':"高度",
            'lang_input_border':"边 框：",
            'lang_input_vhspace':"边 距：",
            'lang_input_title':"描 述：",
            'lang_input_align':'圖片浮動方式：',
            'lang_imgLoading':"　圖片載入中……",
            'lang_start_upload':"開始上傳",
            'lock':{'title':"锁定宽高比例"}, //属性
            'searchType':{'title':"圖片類型", 'options':["新闻", "壁纸", "表情", "圖示"]}, //select的option
            'searchTxt':{'value':"請輸入搜索關鍵字"},
            'searchBtn':{'value':"百度一下"},
            'searchReset':{'value':"清空搜索"},
            'noneAlign':{'title':'無浮動'},
            'leftAlign':{'title':'左浮動'},
            'rightAlign':{'title':'右浮動'},
            'centerAlign':{'title':'居中独占一行'}
        },
        'uploadSelectFile':'點擊選擇圖片',
        'uploadAddFile':'继续新增',
        'uploadStart':'開始上傳',
        'uploadPause':'暫停上傳',
        'uploadContinue':'继续上傳',
        'uploadRetry':'重试上傳',
        'uploadDelete':'刪除',
        'uploadTurnLeft':'向左旋转',
        'uploadTurnRight':'向右旋转',
        'uploadPreview':'預覽中',
        'uploadNoPreview':'不能預覽',
        'updateStatusReady': '选中_张圖片，共_KB。',
        'updateStatusConfirm': '已成功上傳_张照片，_张照片上傳失敗',
        'updateStatusFinish': '共_张（_KB），_张上傳成功',
        'updateStatusError': '，_张上傳失敗。',
        'errorNotSupport': 'WebUploader 不支援您的浏览器！如果你使用的是IE浏览器，請尝试升级 flash 播放器。',
        'errorLoadConfig': '後端配置项没有正常載入，上傳插件不能正常使用！',
        'errorExceedSize':'文件大小超出',
        'errorFileType':'文件格式不允许',
        'errorInterrupt':'文件傳输中斷',
        'errorUploadRetry':'上傳失敗，請重试',
        'errorHttp':'http請求錯誤',
        'errorServerUpload':'服务器返回出错',
        'remoteLockError':"宽高不正确,不能所定比例",
        'numError':"請輸入正确的長度或者宽度值！例如：123，400",
        'imageUrlError':"不允许的圖片格式或者圖片域！",
        'imageLoadError':"圖片載入失敗！請檢查連結地址或網络狀態！",
        'searchRemind':"請輸入搜索關鍵字",
        'searchLoading':"圖片載入中，請稍後……",
        'searchRetry':" :( ，抱歉，没有找到圖片！請重试一次！"
    },
    'attachment':{
        'static':{
            'lang_tab_upload': '上傳附件',
            'lang_tab_online': '線上附件',
            'lang_start_upload':"開始上傳",
            'lang_drop_remind':"可以将文件拖到这里，單次最多可选100个文件"
        },
        'uploadSelectFile':'點擊選擇文件',
        'uploadAddFile':'继续新增',
        'uploadStart':'開始上傳',
        'uploadPause':'暫停上傳',
        'uploadContinue':'继续上傳',
        'uploadRetry':'重试上傳',
        'uploadDelete':'刪除',
        'uploadTurnLeft':'向左旋转',
        'uploadTurnRight':'向右旋转',
        'uploadPreview':'預覽中',
        'updateStatusReady': '选中_个文件，共_KB。',
        'updateStatusConfirm': '已成功上傳_个文件，_个文件上傳失敗',
        'updateStatusFinish': '共_个（_KB），_个上傳成功',
        'updateStatusError': '，_张上傳失敗。',
        'errorNotSupport': 'WebUploader 不支援您的浏览器！如果你使用的是IE浏览器，請尝试升级 flash 播放器。',
        'errorLoadConfig': '後端配置项没有正常載入，上傳插件不能正常使用！',
        'errorExceedSize':'文件大小超出',
        'errorFileType':'文件格式不允许',
        'errorInterrupt':'文件傳输中斷',
        'errorUploadRetry':'上傳失敗，請重试',
        'errorHttp':'http請求錯誤',
        'errorServerUpload':'服务器返回出错'
    },
    'insertvideo':{
        'static':{
            'lang_tab_insertV':"插入视频",
            'lang_tab_searchV':"搜索视频",
            'lang_tab_uploadV':"上傳视频",
            'lang_video_url':"视频網址",
            'lang_video_size':"视频尺寸",
            'lang_videoW':"宽度",
            'lang_videoH':"高度",
            'lang_alignment':"對齐方式",
            'videoSearchTxt':{'value':"請輸入搜索關鍵字！"},
            'videoType':{'options':["全部", "热门", "娱乐", "搞笑", "体育", "科技", "综艺"]},
            'videoSearchBtn':{'value':"百度一下"},
            'videoSearchReset':{'value':"清空结果"},

            'lang_input_fileStatus':' 當前未上傳文件',
            'startUpload':{'style':"background:url(upload.png) no-repeat;"},

            'lang_upload_size':"视频尺寸",
            'lang_upload_width':"宽度",
            'lang_upload_height':"高度",
            'lang_upload_alignment':"對齐方式",
            'lang_format_advice':"建议使用mp4格式."

        },
        'numError':"請輸入正确的數值，如123,400",
        'floatLeft':"左浮動",
        'floatRight':"右浮動",
        '"default"':"默認",
        'block':"独占一行",
        'urlError':"输入的视频地址有误，請檢查後再试！",
        'loading':" &nbsp;视频載入中，請等待……",
        'clickToSelect':"點擊选中",
        'goToSource':'訪問源视频',
        'noVideo':" &nbsp; &nbsp;抱歉，找不到對应的视频，請重试！",

        'browseFiles':'浏览文件',
        'uploadSuccess':'上傳成功!',
        'delSuccessFile':'从成功對列中移除',
        'delFailSaveFile':'移除保存失敗文件',
        'statusPrompt':' 个文件已上傳！ ',
        'flashVersionError':'當前Flash版本过低，請更新FlashPlayer後重试！',
        'flashLoadingError':'Flash載入失敗!請檢查路徑或網络狀態',
        'fileUploadReady':'等待上傳……',
        'delUploadQueue':'从上傳對列中移除',
        'limitPrompt1':'單次不能選擇超过',
        'limitPrompt2':'个文件！請重新選擇！',
        'delFailFile':'移除失敗文件',
        'fileSizeLimit':'文件大小超出限制！',
        'emptyFile':'空文件無法上傳！',
        'fileTypeError':'文件類型不允许！',
        'unknownError':'未知錯誤！',
        'fileUploading':'上傳中，請等待……',
        'cancelUpload':'取消上傳',
        'netError':'網络錯誤',
        'failUpload':'上傳失敗!',
        'serverIOError':'服务器IO錯誤！',
        'noAuthority':'無权限！',
        'fileNumLimit':'上傳个數限制',
        'failCheck':'驗證失敗，本次上傳被跳过！',
        'fileCanceling':'取消中，請等待……',
        'stopUploading':'上傳已停止……',

        'uploadSelectFile':'點擊選擇文件',
        'uploadAddFile':'继续新增',
        'uploadStart':'開始上傳',
        'uploadPause':'暫停上傳',
        'uploadContinue':'继续上傳',
        'uploadRetry':'重试上傳',
        'uploadDelete':'刪除',
        'uploadTurnLeft':'向左旋转',
        'uploadTurnRight':'向右旋转',
        'uploadPreview':'預覽中',
        'updateStatusReady': '选中_个文件，共_KB。',
        'updateStatusConfirm': '成功上傳_个，_个失敗',
        'updateStatusFinish': '共_个(_KB)，_个成功上傳',
        'updateStatusError': '，_张上傳失敗。',
        'errorNotSupport': 'WebUploader 不支援您的浏览器！如果你使用的是IE浏览器，請尝试升级 flash 播放器。',
        'errorLoadConfig': '後端配置项没有正常載入，上傳插件不能正常使用！',
        'errorExceedSize':'文件大小超出',
        'errorFileType':'文件格式不允许',
        'errorInterrupt':'文件傳输中斷',
        'errorUploadRetry':'上傳失敗，請重试',
        'errorHttp':'http請求錯誤',
        'errorServerUpload':'服务器返回出错'
    },
    'webapp':{
        'tip1':"本功能由百度APP提供，如看到此頁面，請各位站長首先申請百度APPKey!",
        'tip2':"申請完成之後請至ueditor.config.js中配置获得的appkey! ",
        'applyFor':"點此申請",
        'anthorApi':"百度API"
    },
    'template':{
        'static':{
            'lang_template_bkcolor':'背景颜色',
            'lang_template_clear' : '保留原有内容',
            'lang_template_select' : '選擇模板'
        },
        'blank':"空白文档",
        'blog':"博客文章",
        'resume':"个人简历",
        'richText':"圖文混排",
        'sciPapers':"科技论文"


    },
    'scrawl':{
        'static':{
            'lang_input_previousStep':"上一步",
            'lang_input_nextsStep':"下一步",
            'lang_input_clear':'清空',
            'lang_input_addPic':'新增背景',
            'lang_input_ScalePic':'缩放背景',
            'lang_input_removePic':'刪除背景',
            'J_imgTxt':{title:'新增背景圖片'}
        },
        'noScarwl':"尚未作画，白纸一张~",
        'scrawlUpLoading':"涂鸦上傳中,别急哦~",
        'continueBtn':"继续",
        'imageError':"糟糕，圖片读取失敗了！",
        'backgroundUploading':'背景圖片上傳中,别急哦~'
    },
    'music':{
        'static':{
            'lang_input_tips':"输入歌手/歌曲/专辑，搜索您感兴趣的音乐！",
            'J_searchBtn':{value:'搜索歌曲'}
        },
        'emptyTxt':'未搜索到相關音乐结果，請换一个關鍵字试试。',
        'chapter':'歌曲',
        'singer':'歌手',
        'special':'专辑',
        'listenTest':'试听'
    },
    'anchor':{
        'static':{
            'lang_input_anchorName':'锚點名字：'
        }
    },
    'charts':{
        'static':{
            'lang_data_source':'資料源：',
            'lang_chart_format': '圖表格式：',
            'lang_data_align': '資料對齐方式',
            'lang_chart_align_same': '資料源与圖表X轴Y轴一致',
            'lang_chart_align_reverse': '資料源与圖表X轴Y轴相反',
            'lang_chart_title': '圖表標題',
            'lang_chart_main_title': '主標題：',
            'lang_chart_sub_title': '子標題：',
            'lang_chart_x_title': 'X轴標題：',
            'lang_chart_y_title': 'Y轴標題：',
            'lang_chart_tip': '提示文字',
            'lang_cahrt_tip_prefix': '提示文字前缀：',
            'lang_cahrt_tip_description': '仅饼圖有效， 当鼠标移動到饼圖中相应的块上时，提示框内的文字的前缀',
            'lang_chart_data_unit': '資料單位',
            'lang_chart_data_unit_title': '單位：',
            'lang_chart_data_unit_description': '显示在每个資料點上的資料的單位， 比如： 温度的單位 ℃',
            'lang_chart_type': '圖表類型：',
            'lang_prev_btn': '上一个',
            'lang_next_btn': '下一个'
        }
    },
    'emotion':{
        'static':{
            'lang_input_choice':'精选',
            'lang_input_Tuzki':'兔斯基',
            'lang_input_BOBO':'BOBO',
            'lang_input_lvdouwa':'绿豆蛙',
            'lang_input_babyCat':'baby猫',
            'lang_input_bubble':'泡泡',
            'lang_input_youa':'有啊'
        }
    },
    'gmap':{
        'static':{
            'lang_input_address':'地址',
            'lang_input_search':'搜索',
            'address':{value:"北京"}
        },
        searchError:'無法定位到该地址!'
    },
    'help':{
        'static':{
            'lang_input_about':'关于UEditor',
            'lang_input_shortcuts':'快速键',
            'lang_input_introduction':'UEditor是由百度web前端研发部開发的所见即所得富文字web編輯器，具有轻量，可定制，注重使用者体验等特點。開源基于BSD协议，允许自由使用和修改程式碼。',
            'lang_Txt_shortcuts':'快速键',
            'lang_Txt_func':'功能',
            'lang_Txt_bold':'给选中字設定為加粗',
            'lang_Txt_copy':'複製选中内容',
            'lang_Txt_cut':'剪切选中内容',
            'lang_Txt_Paste':'粘贴',
            'lang_Txt_undo':'重新執行上次操作',
            'lang_Txt_redo':'撤销上一次操作',
            'lang_Txt_italic':'给选中字設定為斜体',
            'lang_Txt_underline':'给选中字加下划线',
            'lang_Txt_selectAll':'全部选中',
            'lang_Txt_visualEnter':'软回车',
            'lang_Txt_fullscreen':'全屏'
        }
    },
    'insertframe':{
        'static':{
            'lang_input_address':'地址：',
            'lang_input_width':'宽度：',
            'lang_input_height':'高度：',
            'lang_input_isScroll':'允许滚動條：',
            'lang_input_frameborder':'显示框架边框：',
            'lang_input_alignMode':'對齐方式：',
            'align':{title:"對齐方式", options:["默認", "左對齐", "右對齐", "居中"]}
        },
        'enterAddress':'請輸入地址!'
    },
    'link':{
        'static':{
            'lang_input_text':'文字内容：',
            'lang_input_url':'連結地址：',
            'lang_input_title':'標題：',
            'lang_input_target':'是否在新視窗打開：'
        },
        'validLink':'只支援选中一个連結时生效',
        'httpPrompt':'您输入的超連結中不包含http等协议名稱，默認将為您新增http://前缀'
    },
    'map':{
        'static':{
            lang_city:"城市",
            lang_address:"地址",
            city:{value:"北京"},
            lang_search:"搜索",
            lang_dynamicmap:"插入動态地圖"
        },
        cityMsg:"請選擇城市",
        errorMsg:"抱歉，找不到该位置！"
    },
    'searchreplace':{
        'static':{
            lang_tab_search:"查找",
            lang_tab_replace:"替换",
            lang_search1:"查找",
            lang_search2:"查找",
            lang_replace:"替换",
            lang_searchReg:'支援正則表达式，新增前後斜杠标示為正則表达式，例如“/表达式/”',
            lang_searchReg1:'支援正則表达式，新增前後斜杠标示為正則表达式，例如“/表达式/”',
            lang_case_sensitive1:"区分大小寫",
            lang_case_sensitive2:"区分大小寫",
            nextFindBtn:{value:"下一个"},
            preFindBtn:{value:"上一个"},
            nextReplaceBtn:{value:"下一个"},
            preReplaceBtn:{value:"上一个"},
            repalceBtn:{value:"替换"},
            repalceAllBtn:{value:"全部替换"}
        },
        getEnd:"已经搜索到文章末尾！",
        getStart:"已经搜索到文章头部",
        countMsg:"总共替换了{#count}处！"
    },
    'snapscreen':{
        'static':{
            lang_showMsg:"截圖功能需要首先安装UEditor截圖插件！ ",
            lang_download:"點此下载",
            lang_step1:"第一步，下载UEditor截圖插件並运行安装。",
            lang_step2:"第二步，插件安装完成後即可使用，如不生效，請重启浏览器後再试！"
        }
    },
    'spechars':{
        'static':{},
        tsfh:"特殊字符",
        lmsz:"罗马字符",
        szfh:"數学字符",
        rwfh:"日文字符",
        xlzm:"希腊字母",
        ewzm:"俄文字符",
        pyzm:"拼音字母",
        yyyb:"英語音标",
        zyzf:"其他"
    },
    'edittable':{
        'static':{
            'lang_tableStyle':'表格樣式',
            'lang_insertCaption':'新增表格名稱行',
            'lang_insertTitle':'新增表格標題行',
            'lang_insertTitleCol':'新增表格標題列',
            'lang_orderbycontent':"使表格内容可排序",
            'lang_tableSize':'自動调整表格尺寸',
            'lang_autoSizeContent':'按表格文字自适应',
            'lang_autoSizePage':'按頁面宽度自适应',
            'lang_example':'示例',
            'lang_borderStyle':'表格边框',
            'lang_color':'颜色:'
        },
        captionName:'表格名稱',
        titleName:'標題',
        cellsName:'内容',
        errorMsg:'有合併單元格，不可排序'
    },
    'edittip':{
        'static':{
            lang_delRow:'刪除整行',
            lang_delCol:'刪除整列'
        }
    },
    'edittd':{
        'static':{
            lang_tdBkColor:'背景颜色:'
        }
    },
    'formula':{
        'static':{
        }
    },
    'wordimage':{
        'static':{
            lang_resave:"转存步骤",
            uploadBtn:{src:"upload.png",alt:"上傳"},
            clipboard:{style:"background: url(copy.png) -153px -1px no-repeat;"},
            lang_step:"1、點擊頂部複製按钮，将地址複製到剪贴板；2、點擊新增照片按钮，在弹出的對話框中使用Ctrl+V粘贴地址；3、點擊打開後選擇圖片上傳流程。"
        },
        'fileType':"圖片",
        'flashError':"FLASH初始化失敗，請檢查FLASH插件是否正确安装！",
        'netError':"網络連結錯誤，請重试！",
        'copySuccess':"圖片地址已经複製！",
        'flashI18n':{} //留空默認中文
    },
    'autosave': {
        'saving':'保存中...',
        'success':'本地保存成功'
    }
};
