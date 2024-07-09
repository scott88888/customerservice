## 更新日志

### v1.0.x

##### v1.0.0 beta

预览版：基本功能完成；

##### v1.0.0 releases

发布 v1.0.0 正式版。

主要更新：

- 新建分支 `mathjax-version`，但不打算继续對此分支进行開发；

- 移除 MathJax，改用 KaTeX [#2](https://github.com/pandao/editor.md/issues/2)，解析和预览响应速度大幅度提高 [#3](https://github.com/pandao/editor.md/issues/3)；
    - 移除 `mathjax` 配置项；
    - 移除 `mathjaxURL` 属性；
    - 移除 `setMathJaxConfig()` 方法；
    - 移除 `loadMathJax()` 方法；
    - 移除MathJax的所有示例；
    - 新增 `tex` 配置项，表示是否開啟支持科学公式 TeX ，基于 KaTeX；
    - 新增 `katexURL` 属性；
    - 新增 `loadKaTeX` 方法；
    - 新增 KaTeX 的示例；
    
- `setCodeEditor()` 方法更名為 `setCodeMirror()`；

- 合并 CodeMirror 使用到的多个 JS 模块文件，大幅减少 HTTP 請求，加快下载速度；
    - 新增合并后的两个模块文件：`./lib/codemirror/modes.min.js`、`./lib/codemirror/addons.min.js` ；
    - `Gulpfile.js` 新增合并 CodeMirror 模块文件的任务方法 `codemirror-mode` 和 `codemirror-addon` ；
    - 另外在使用 Require.js 时，因為 CodeMirror 的严格模块依赖的限制，不能使用上述合并的模块文件，仍然采用動态加载多个模块文件；
    
- 更新 `README.md` 等相關文档和示例；

- 解决 Sea.js 环境下 Raphael.js 無法运行导致不支持流程图和时序图的問題，即必须先加载 Raphael.js ，后加载 Sea.js ；

### v1.1.x

##### v1.1.0

主要更新：

- 设计并更换了 Logo；
- 新增新增图片、連結、锚點連結、程式碼块、预格式文本等操作弹出對話框层及示例；
- 新增支持图片(跨域)上传；
- 改用 `<textarea>` 来存放 Markdown 源文档；
- 新增支持自訂工具栏；
- 新增支持多語言；
- 新增支持 Zepto.js；
- 新增支持多个 Editor.md 并存和動态加载 Editor.md 及示例；
- 新增支持智能识别和解析 HTML 标签及示例；
- 新增多个外部操作方法接口及示例；
- 修复了一些大大小小的 Bug；

具体更新如下：

- 更换 Logo，建立基础 VI；
    - 建立了全系列 WebFont 字体 `dist/fonts/editormd-logo.*` ；
    - 新增樣式类 `editormd-logo` 等；

- 改用 `<textarea>` 来存放 Markdown 源文档；
    - 原先使用 `<script type="text/markdown"></script>` 来存放 Markdown 源文档；
    - 建立 Editor.md 只需要写一个 `<div id="xxxx"></div>` ，如果没有新增 `class="editormd"` 属性会自動新增，另外如果不存在 `<textarea>` 标签，则也会自動新增 `<textarea>` ；

- 新增支持智能识别和解析 HTML 标签，增强了 Markdown 语法的扩展性，几乎無限，例如：插入视频等等；
    - 新增配置项 `htmlDecode` ，表示是否開啟 HTML 标签识别和解析，但是為了安全性，默认不開啟；
    - 新增识别和解析 HTML 标签的示例；
    
- 新增插入連結、锚點連結、预格式文本和程式碼块的弹出對話框层；
    - 弹出层改為使用固定定位；
    - 新增動态建立對話框的方法 `createDialog()`；
    - 新增静态属性 `editormd.codeLanguages` ，用于存放程式碼語言列表；

- 開始支持图片上传；
    - 新增新增图片（上传）弹出對話框层；
    - 支持基于 iframe 的跨域上传，并新增相应的示例（ PHP 版）；
    
- 開始支持自訂工具栏图标及操作处理；
    - 配置项 `toolbarIcons` 类型由數组更改為函數，返回一个图标按钮列表數组；
    - 新增配置项 `toolbarHandlers` 和 `toolbarIconsTexts` ，分别用于自訂按钮操作处理和按钮内容文本；
    - 新增方法 `getToolbarHandles()` ，用于可在外部使用默认的操作方法；
    - 新增成员属性 `activeIcon` ，可取得当前或上次點擊的工具栏图标的 jQuery 实例對象；
    
- 新增表單取值、自訂工具栏、图片上传、多个 Editor.md 并存和動态加载 Editor.md 等多个示例；

- 新增插入锚點按钮和操作处理；

- 新增预览 HTML 内容視窗的關閉按钮，之前只能按 ESC 才能退出 HTML 全視窗预览；

- 新增多語言（ l18n ）及動态加载語言包支持；
    - 新增英语 `en` 和繁体中文 `zh-tw` 語言包模块；
    - 修改一些方法的内部實現以支持動态語言加载:
        - `toolbarHandler()` 更為 `setToolbarHandler()` ；
        - `setToolbar()` 方法包含 `setToolbarHandler()` ；
        - 新建 `createInfoDialog()` 方法；
	    - 修改 `showInfoDialog()` 和 `hideInfoDialog()` 方法的内部實現等；

- 修改多次 Bug ，并优化触摸事件，改进對 iPad 的支持；

- 工具栏新增清空按钮和清空方法 `clear()` ，解决工具栏文本会被选中出现蓝底的問題;

- 配置项 `tocStartLevel` 的默认值由 2 改為 1，表示默认从 H1 開始產生 ToC；

- 解决 IE8 下加载出错的問題；
    - 新增两个静态成员属性 `isIE` 和 `isIE8` ，用于判断 IE8；
    - 由于 IE8 不支持 FlowChart 和 SequenceDiagram，默认在 IE8 下不加载这两个组件，無论是否開啟；

- 新增 Zepto.js 的支持；
	- 為了兼容 Zepto.js ，某些元素在操作处理上不再使用 `outerWidth()` 、 `outerHeight()` 、`hover()` 、`is()` 等方法；
	- 為了避免修改 flowChart.js 和 sequence-diagram.js 的源碼，所以想支持 flowChart 或 sequenceDiagram 得加上这一句： `var jQuery = Zepto;`；

- 新增 `editormd.$name` 属性，修改 `editormd.homePage` 属性的新地址；

- `editormd.markdownToHTML()` 新增方法返回一个 jQuery 实例對象；
    - 该实例對象定义了一个 `getMarkdown()`方法，用于取得 Markdown 源程式碼；
    - 该实例對象定义了一个 `tocContainer` 成员属性，即 ToC 列表的父层的 jQuery 实例對象；

- 新增只读模式；
    - 新增配置项 `readOnly` ，默认值為 `false` ，即可編輯模式；
    - 其他相關改動；

- 新增方法 `focus()` 、 `setCursor()` 、 `getCursor()` 、`setSelection()` 、`getSelection()` 、 `replaceSelection()` 和 `insertValue()` 方法，并增加對应的示例；

- 新增配置项 `saveHTMLToTextarea` ，用于将解析后的 HTML 保存到 Textarea，以供送出到后台程序；
    - `getHTML()` 方法必须在 `saveHTMLToTextarea == true` 的情况下才能使用；
    - 新增 `getHTML()` 方法的别名 `getTextareaSavedHTML()` 方法；
    - 新增方法 `getPreviewedHTML()` ，用于取得预览視窗的 HTML ；

- 修复了一些大大小小的 Bugs；

##### v1.1.1

- 接受一个 pull 請求，修复了 `getHTML ()` 和 `getPreviewedHTML()` 方法中的 ３ 处錯誤；

##### v1.1.2

- 修复 Bug [＃10](https://github.com/pandao/editor.md/issues/10)；
- 修复 Bug [＃12](https://github.com/pandao/editor.md/issues/12)；

##### v1.1.3

- 修复 Bug [＃14](https://github.com/pandao/editor.md/issues/14)；
- 修复 Bug [＃15](https://github.com/pandao/editor.md/issues/15)；

##### v1.1.4

- 修复 Bug [＃17](https://github.com/pandao/editor.md/issues/17)；
    - 修改了 `getToolbarHandles()` 和 `setToolbarHandler()` 方法；
- 从 `editormd.scss` 中分离出 `editormd.logo.scss` ，并產生 `editormd.logo.css` ，以便單独使用；
    - 同时修改了 `Gulpfile.js` 的相应任务；
    
##### v1.1.5

- 修复 Bug [＃18](https://github.com/pandao/editor.md/issues/18)；
    - 修改了 `showInfoDialog()` 和 `createInfoDialog()` 方法；
    - 新增 `infoDialogPosition()` 方法；
    
- 修复 Bug [＃20](https://github.com/pandao/editor.md/issues/20)；
    - 修改了引用的处理函數；
    - 插入的 headers 的 `#` 号後面都加上了一个空格；

##### v1.1.6

修复多处 Bug，具体如下：
    
- 修复 Bug [#23](https://github.com/pandao/editor.md/issues/23)，即 Headers 的 id 属性的重复及中文問題；
    - 修改了 `editormd.markedRenderer()` 方法；

- 修复 Bug [#24](https://github.com/pandao/editor.md/issues/24)；
    - 修改了 `setMarkdown()` 、 `clear()` 和 `loadedDisplay()` 方法的内部實現；
    - 新增了 `katexRender()` 、 `flowChartAndSequenceDiagramRender()` 、 `previewCodeHighlight()` 方法；
    
- 修复有些情况下無法保存 Markdown 源文档到 textarea 的問題；
    - 修改了 `setCodeMirror()` 、 `recreateEditor()` 等方法；

- 修改了以上 Bug 及部分相關示例文件；

##### v1.1.7

修复多处 Bug，具体如下：

- 修复 Bug [#25](https://github.com/pandao/editor.md/issues/25)；
    - 修改了 `loadedDisplay()` 方法，将 `settings.onload` 移動了 `CodeMirror.on("change")` 事件注册后再触发；

- 修复 Bug [#26](https://github.com/pandao/editor.md/issues/26)；
    - 修改了 `saveToTextareas()` 方法；
    - 新增 `state.loaded` 和 `state.watching` 两个属性；

- 修改了以上 Bug 相關示例文件；

##### v1.1.8

改进功能，具体如下：

- 改进 [#27](https://github.com/pandao/editor.md/issues/27)；
    - 新增配置项 `matchWordHighlight` ，可选值有： `true, false, "onselected"` ，默认值為 `true` ，即開啟自動匹配和标示相同單词；

- 改进 [#28](https://github.com/pandao/editor.md/issues/28)；
    - 将 `jquery.min.js` 、 `font-awesome.min.css` 、 `github-markdown.css` 移除（这是一个疏忽，它们不是動态加载的依赖模块或者不需要的，避免不必要的硬盘空間占用）；

- 修改了所有相關的示例文件；

##### v1.1.9

- 修复無法解析 heading link 的 Bug [#29](https://github.com/pandao/editor.md/issues/29)；

    - 修改了 `editormd.markedRenderer()` 方法的内部實現；
    - 新增了 `editormd.trim()` ，用于清除字符串两边的空格；
    - 修改了所有相關的示例文件和测试用例 `marked-heading-link-test.html` ；
    
- 修改了 `README.md` ，新增了 `Shields.io` 图标；

### v1.2

##### v1.2.0

v1.2.0 主要更新：

- 新增程式碼折叠、搜索替换、自訂樣式主题和自訂快捷键等功能；
- 新增 Emoji 表情、@Link 、GFM Task Lists 支持；
- 新增表格插入、Emoji 表情插入、HTML 实体字符插入、使用帮助等對話框；
- 新增插件扩展机制；
- 新增手動加载依赖模块方式；
- 改用 `Prefixes.css` 作 CSS 前缀预处理；
- 改进和增强工具栏自訂功能，完善事件监听和处理方法；
- 部分功能改进（更加方便的预格式文本/程式碼插入、自動闭合标签等）、新增多个方法、改进 Require.js 支持和修复多个 Bug 等等；

**具体更新如下：**

- 新建 v1.1.x 分支；
    - v1.2 文件结构变動较大；

- 新增程式碼折叠、自動闭合标签和搜索替换功能；
    - 搜索快捷键 `Ctrl + F / Command + F` ；
    - 替换快捷键 `Ctrl + Shift + F / Command + Option + F` ；
    - 折叠快捷键 `Ctrl + Q / Command + Q` ；

- 新增自訂主题支持；
    - 新增 3 个成员方法 `setTheme()` 、 `setCodeMirrorOption()` 和 `getCodeMirrorOption()` ；

- 新增 @Link 支持；

- 新增 GFM Task Lists 支持；

- 新增 Emoji 表情支持；
    - 支持 Github emoji `:emoji-name:` 、FontAwesome icons（`:fa-xxx:`）、Twitter emoji (twemoji) （ `:tw-xxxx:` ）、Editor.md logo icons（ `:editormd-logo:` ）形式的 Emoji；
    - 新增属性 `editormd.emoji` 、 `editormd.twemoji` 、 `editormd.urls` 和 `editormd.regex`；
    
- 新增 HTML 实体字符插入、插入表格和使用帮助對話框；
    - 修改了 `createDialog()` 等方法；
    - 新增 `mask` 成员属性和锁屏方法 `editormd.lockScreen()` 、 `editormd.fn.lockScreen()` ；

- 改进插入预格式文本和程式碼對話框；
    - 将 `<textarea>` 改為 `CodeMirror` ，输入更加方便和直观；

- 新增自訂键盘快捷键功能；
    - 新增 2 个方法： `addKeyMap()` 和 `removeKayMap()`；

- 改用 `Prefixes.css` 作CSS前缀预处理；
    - SCSS前缀预处理mixins改用 [Prefixes.scss](https://github.com/pandao/prefixes.scss "Prefixes.scss")；

- 改进和增强工具栏自訂功能；
	- 新增配置项 `toolbarCustomIcons` ，用于增加自訂工具栏的功能，可以直接插入 HTML 标签，不使用默认的元素建立图标；
    - 新增工具栏列表预设值属性 `editormd.toolbarModes` ；
    - 移除成员属性 `toolbarIconHandlers` ；

- 完善和新增事件处理方法；
	- 新增事件回调注册方法 `on()` ；
	- 新增事件回调移除方法 `off()` ；
	- 新增事件回调处理配置项： `onresize` 、 `onscroll` 、`onpreviewscroll` 、 `onpreviewing` 、 `onpreviewed` 、`onwatch` 和 `onunwatch` ；

- 新增手動加载依赖模块方式，以便可同步使用成员方法；
    - 新增属性 `autoLoadModules` ，默认值為 `true` ；

- 新增插件及扩展机制；
    
    - 新增插件自訂机制，改变整体结构(包括文件结构)，以便更加方便地實現插件扩展；
	- 新增對象扩展方法 `extends()` 、 `set()` ；

- 新增成员方法和属性：

    - 新增两个方法： `setValue()` 、`getValue()`；
	- 新增 `config()` 方法，用于加载后重新配置；
	- 增加两个属性 `cm` ，是 `codeEditor` 的简写， `cmElement` 是 `codeMirror` 的别名;

- 成员方法的改进：

	- 改进： `showToolbar()` 和 `hideToolbar()` 方法增加一个 `callback` 函數，用于直接回调操作；
	- 改进：修改了 `previewCodeHighlight()` 方法；
	- 更名： `recreateEditor()` 更名為 `recreate()` ；
    - 移除 `setMarked()` 方法；
    
- 新增 HTML 标签解析过滤机制；
    - 通過設定 `settings.htmlDecode = "style,script,iframe"` 来實現过滤指定标签的解析；

- 改进 Require.js 支持；
    - 修复 Require.js 下 CodeMirror 編輯器的程式碼無法高亮的問題；
    - 更新 `underscore` 版本至 `1.8.2` ；
    - 移除 `editormd.requirejsInit()` 和 `editormd.requireModules()` 方法；
    - 新增 `Require.js/AMD` 专用版本文件 `editormd.amd.js` ；
    - 新建 Gulp 任务 `amd` ；

- 修改和新增以上改进等相關示例；

### v1.3

#### v1.3.0

主要更新：

- 预设键盘快捷键处理（粗体等），插入 Markdown 更加方便；
- 更新 CodeMirror 版本為 `5.0` ；
- 更新 Marked 版本為 `0.3.3`；
- 新增自動高度和工具栏固定定位功能；
- 改进表格插入對話框；
- 工具栏新增三个按钮，分别是将所选文本首字母转成大写、转成小写、转成大写；
- 修改使用帮助文档；
- 修复多个 Bug；

具体更新如下：

- 新增常用键盘快捷键预设处理；
    - 新增属性 `editormd.keyMaps` ，预设一些常用操作，例如插入粗体等；
    - 新增成员方法 `registerKeyMaps()` ；
    - 退出HTML全屏预览快捷键更改為 `Shift + ESC`；
    - 新增配置项 `disabledKeyMaps` ，用于屏蔽一些快捷键操作；
- 更新 CodeMirror 版本為 `5.0`；
    - 修改無法输入 `/` 的問題；
- 更新 Marked 版本為 `0.3.3`；
- 新增自動高度和工具栏固定定位（滚動條拖動时）模式；
    - 新增配置项 `settings.autoHeight` ；
    - 新增配置项 `settings.toolbarAutoFixed` ；
    - 新增方法 `setToolbarAutoFixed(true|false)` ；
- 新增信箱地址自動新增連結功能；
    - 新增配置项 `emailLink` ，默认為 `true` ; 
- 改进表格插入對話框；
- 工具栏新增三个按钮，分别是将所选文本首字母转成大写、转成小写、转成大写；
    - 新增方法 `editormd.ucwords()` ，别名 `editormd.wordsFirstUpperCase()` ；
    - 新增方法 `editormd.ucfirst()` ，别名 `editormd.firstUpperCase()` ；
    - 新增两个成员方法 `getSelections()` 和 `getSelections()` ；

- 修复 Font awesome 图标 emoji 部分無法解析的 Bug，[#39](https://github.com/pandao/editor.md/issues/39)
- 改进 @link 功能 [#40](https://github.com/pandao/editor.md/issues/40)；
    - 新增配置项 `atLink` ，默认為 `true` ; 
- 修复無法输入 `/` 的問題 [#42](https://github.com/pandao/editor.md/issues/42)；
- 修改使用帮助说明的錯誤 [#43](https://github.com/pandao/editor.md/issues/43)；
- 新增配置项 `pluginPath`，默认為空时，等于 `settings.path + "../plugins/"` ；

### v1.4

#### v1.4.0

主要更新：

- 新增延迟解析机制，预览更即时；
- 新增跳转到指定行的功能和對話框；
- 新增 ToC 下拉選單、自訂 ToC 容器的功能；
- 新增跳转到行、搜索的工具栏按钮；
- 新增支持插入和解析（列印）分頁符；
- 改进快捷键功能和自動高度模式等；
- 改进：将锚點連結改名為引用連結；
- 改进編輯器重建和重配置功能；
- 修复多个 Bug；

具体更新：

- 新增延迟解析预览的机制，解决输入太多太快出现的 “延迟卡顿” 問題；
    - 新增配置项 `delay` ，默认值為 `300`；
    - 修复当输入速度太快时，解析Flowchart会抛出錯誤的問題；
- 修改 iPad 等移動终端的浏览器無法上传图片的問題 [#48](https://github.com/pandao/editor.md/issues/48)；
- 修复單独引用 `editormd.preview.css` 时無法显示 Font Awesome 和 Editor.md logo 字体的問題；
- 更新和修改 Gulp 构建；
    - 修改了 `Gulpfile.js` ，并且 `gulp-ruby-sass` 升级到最新版本 `1.0.0-alpha.3` ; 
    - 編輯 SCSS 时，不再產生 CSS 的 Source map 文件；
- 执行 jshint 和更正一些 JS 写法的不规范，精简了程式碼；
- 新增配置项 `appendMarkdown` 和 `appendMarkdown()` 方法，用于(初始化前后)追加 Markdown 到 Textarea ；
- 改进部分预设快捷键功能，包括 F9 (watch)、F10 (preview)、F11 (fullscreen)等;
- 修复自動高度模式下出现的几个問題；
    - 全屏退出时高度不正确的問題：修改了 `fullscreenExit()` 方法的内部實現；
    - 当解析预览后的 HTML 内容高度高于 Markdown 源碼編輯器高度时，無法正确预览的問題 [#49](https://github.com/pandao/editor.md/issues/49)；
- 修改 `onscroll` 和 `onpreviewscroll` 無法訪問 `this` 的問題；
- 修改 `init()` 方法，可以只設定一个参數；
- 新增插入 TeX (KaTeX) 公式的快捷键 `Ctrl + Shift + K` 和插入方法 `tex()` ；
- 将锚點連結改為引用連結，引用的連結改為插入到頁尾；
    - 工具栏的名稱 `anchor` 改為 `reference-link`；
    - 工具栏的名稱 `htmlEntities` 改名為 `html-entities`；
- 改进編輯器重建和重配置功能；
    - 修改了 `loadedDisplay()` 方法；
    - 修改了 `config()` 和 `recreate()` 方法；
- 新增跳转到指定行的功能；
    - 新增方法 `gotoLine()` ；
    - 新增跳转到行對話框插件 `goto-line-dialog` ；
    - 新增快捷键 `Ctrl + Alt + G` ；
    - 改进 `executePlugin()` 方法；
    - 修改了 `help-dialog/help.md` ；
- 新增搜索工具栏按钮；
    - 新增方法 `search()` 、`searchReplace()` 和 `searchReplaceAll()` ；
    - 原全屏预览 HTML 按钮的图标改為 `fa-desktop`；
    - 改為默认開啟搜索替换功能；
- 更换了关于 Editor.md 的标语（ slogan ）；
- 标题按钮 `h` 改為大写的 `H`；
- `saveToTextareas()` 方法更名為 `save()`；
- 新增 ToC 下拉選單、自訂 ToC 容器的功能；
    - 新增 Markdown 扩展语法 `[TOCM]` ，自動產生 ToC 下拉選單；
    - 新增配置项 `tocm` ，默认為 `true`，即可以使用 `[TOCM]` ；
    - 新增配置项 `tocDropdown` 和 `tocTitle` ；
    - 新增方法 `editormd.tocDropdownMenu()` ；
    - 新增配置项 `tocContainer` ，值為 jQuery 選擇器，默认為空；
- 修改了配置项 `placeholder` 的默认值；
- 改进對 IE8 的兼容支持；
- 修复 Firefox 下因為 `Object.watch()` 而出现的問題；
- 新增支持插入和解析（列印）分頁符；
    - 新增配置项 `pageBreak` ，默认值為 `true`；
    - 新增语法 `[========]` ，即括号内至少 8 个等号；
    - 新增插入分頁符的工具栏图标和方法 `pagebreak()` ；
    - 新增插入分頁符的快捷键 `Shift + Alt + P`；
- 修复一些 Bug，包括 [#51](https://github.com/pandao/editor.md/issues/51) 等；
- 新增和修改以上更新的相關示例；

#### v1.4.1

- 新增配置项 `syncScrolling`，即是否開啟同步滚動预览，默认值為 `false` ； 
- 修复 Bug [＃64](https://github.com/pandao/editor.md/issues/64)；
    - 更新 `editormd.katexURL` 资源地址的默认值，即更新版本為 `0.3.0` ； 
    - 新增测试用例`tests/katex-tests.html`；
    - 修改示例文件`examples/katex.html`； 
- 修复 Bug [＃66](https://github.com/pandao/editor.md/issues/66)；
- 修复編輯器工具栏按钮 `:hover` CSS3 transition 無效的問題； 
- 修改了 `README.md`；

#### v1.4.2

- 改进和增强自訂工具栏功能，支持图标按钮右對齐 [#69](https://github.com/pandao/editor.md/issues/69)；
- 改进和增强 HTML 标签的解析过滤功能，支持过滤指定的属性等 [#70](https://github.com/pandao/editor.md/issues/70)；
- 刪除分支 `mathjax-version` 和 `v1.1.9`；

#### v1.4.3

- 改进：可配置是否自動聚焦編輯器 [#74](https://github.com/pandao/editor.md/issues/74)；
	- 新增配置项 `autoFocus`，默认值為 `true`; 
- 修复 Bug [#77](https://github.com/pandao/editor.md/issues/77)；
- 改进：帮助對話框里的連結改為新視窗打開，避免直接跳转到連結，导致編輯内容丢失的問題 [#79](https://github.com/pandao/editor.md/issues/79)；
- 改进和完善編輯器配置项；
	- 新增配置项 `tabSize`、`indentUnit` 和 `lineWrapping`；
	- 新增配置项 `autoCloseBrackets` 和 `showTrailingSpace` ；
	- 新增配置项 `matchBrackets`、`indentWithTabs` 和 `styleSelectedText`；
- 改进：修改 CSS `font-family`，改进跨平台中英文字体显示；
- 修改了 `README.md`；

#### v1.4.4

- 修复 Bug [#81](https://github.com/pandao/editor.md/issues/81)，即不支持 `:+1:` 的問題；
- 修复 Bug [#85](https://github.com/pandao/editor.md/issues/85)，即图片上传返回结果不支持 `Content-Type=application/json` 的問題；
- 修复图片上传無法显示 loading 的問題；

#### v1.4.5

- 规范项目的中英文混排；
- 新增配置项 `name`，用于指定 Markdown textarea 的 `name="xxxx"` 属性；
- 修复 Bug，即無法正确解析公式的 `<` 和 `>` 的問題 [#87](https://github.com/pandao/editor.md/issues/87);
- 修复 Bug，即 `getHTML()` 無效的問題 [#95](https://github.com/pandao/editor.md/issues/95);
- 修复 Bug，即火狐上传图片后無法返回值的問題 [#96](https://github.com/pandao/editor.md/issues/96);
    - 修改了图片上传插件；
    - 修改 PHP 上传类及示例；
- 方法更名：`extends()` 更名為 `extend()`，以兼容 IE8；
- 修复 IE8 下 Emoji 正则表达式字符集越界的問題；
- 更新了 `README.md` 和 `CHANGE.md` 等相關文档文件；


### v1.5

#### v1.5.0

主要更新：

- 新增：編輯器黑色主题 Dark，改进自訂主题功能（即工具栏、編輯区、预览区可分别設定主题樣式）；
- 新增：多行公式支持；
- 新增：支持非編輯狀態下的 ToC 自訂容器；
- 新增：支持設定為單向同步滚動；
- 改进：編輯器樣式美化，更换了滚動條樣式; 
- 改进：提高同步滚動定位的精确度；
- 改进：修复和改进 HTML 标签及属性过滤功能；
- 改进：修复在 Bootstrap 下的兼容性問題；
- 修复多处 Bug；

具体更新：

- 新增：解析后的程式碼块自動换行；

- 新增：支持多行公式；
    - 新增：新增语法：\`\`\`math | latex | katex；
    - 改进：美化 KaTeX 公式，即加大字号等；

- 新增：支持設定為單向同步滚動，即只是編輯区單向同步滚動，配置项 `syncScrolling : "single"`；
    - 新增：配置同步滚動示例文件 `sync-scrolling.html`；

- 新增：增加了編輯器樣式主题 Dark，即工具栏和预览区各自有一个暗黑色主题；
    - 变更：自 `v1.5.0` 開始，配置项 `theme` 改為指定 Editor.md 本身的主题；
    - 新增配置项 `editorTheme` ，用于指定編輯区的主题，即 CodeMirror 的主题；
    - 新增配置项 `previewTheme` ，用于指定预览区的主题；
    - 新增方法 `setEditorTheme()`，别名： `setCodeMirror()`；
    - 新增方法 `setPreviewTheme()`；
    - 修改了方法 `setTheme()` ；
    - 更换了滚動條樣式，Only Webkit；
    - 改进全屏狀態下的樣式显示，去掉 JS 操作的部分，改為通過 CSS 樣式类 `.editormd-fullscreen` 控制；
    - 修改和增加相關的方法、SCSS 文件及示例文件 `themes.html`；

- 新增：非編輯狀態下 ToC 自訂容器支持；
    - 新增配置项 `markdownSourceCode`，即解析后是否保留源碼，默认為不保留 `false`；
    - 新增配置项 `tocContainer`，值為自訂 ToC 容器的 ID 選擇器 `#xxxxx`，默认為空；
    - 新增和修改了相關示例文件；

- 新增：新增加了 CSS 樣式类 `editormd-preview-active`，可以控制全屏HTML预览时的内容层樣式；
    - 修改了 `previewing()` 和 `previewed()` 方法；
    - 相關 issues [#103](https://github.com/pandao/editor.md/issues/103)；
    - 另外也调整了關閉按钮的位置；

- 改进：修复插入 Emoji `:moon:` 無法显示的問題，修改為其是 `:waxing_gibbous_moon:` 的别名 [#94](https://github.com/pandao/editor.md/pull/94)；

- 改进：修改了 CodeMirror 程式碼行的左右内间距，使其不会挨着左边的行号层；
    - 相關 issues [#97](https://github.com/pandao/editor.md/issues/97)；

- 改进：修改了同步滚動的定位算法，提高精确度；
    - 修正問題 [#99](https://github.com/pandao/editor.md/issues/99)；
    - 修改了 `bindScrollEvent()` 方法；

- 改进：完善 HTML 标签过滤功能，即程式碼块、`<pre>` 预格式文本和行内程式碼里的标签及属性不会被过滤；
    - 修复 Bug [#105](https://github.com/pandao/editor.md/issues/105)；
- 改进：当不显示行号时 `settings.lineNumbers == false`，CodeMirror 行号层去掉右边框； 
- 改进：根據指針在当前行的位置更合理插入标题和水平线 [#104](https://github.com/pandao/editor.md/pull/104)；
- 改进：调整了字体，優先显示 `"YaHei Consolas Hybrid", Consolas`；
- 改进：修复在 Bootstrap 下的兼容性問題，即因為 box-sizing 写错位置导致的弹出层宽度等错位問題 [#107](https://github.com/pandao/editor.md/issues/107)；