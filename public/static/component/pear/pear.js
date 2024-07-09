window.rootPath = (function(src) {
	src = document.scripts[document.scripts.length - 1].src;
	return src.substring(0, src.lastIndexOf("/") + 1);
})();

layui.config({
	base: rootPath + "module/",
	version: "3.8.9"
}).extend({
	admin: "admin", 	// 框架布局组件
	menu: "menu",		// 資料菜单组件
	frame: "frame", 	// 内容頁面组件
	tab: "tab",			// 多选项卡组件
	echarts: "echarts", // 資料图表组件
	echartsTheme: "echartsTheme", // 資料图表主题
	hash: "hash",		// 資料加密组件
	select: "select",	// 下拉多选组件
	drawer: "drawer",	// 抽屉弹层组件
	notice: "notice",	// 消息提示组件
	step:"step",		// 分布表單组件
	tag:"tag",			// 多标签頁组件
	popup:"popup",      // 弹层封装
	iconPicker:"iconPicker", // 图表選擇
	treetable:"treetable",   // 树状表格
	dtree:"dtree",			// 树结构
	tinymce:"tinymce/tinymce", // 編輯器
	area:"area",			// 省市级联
	count:"count",			// 数字滚动组件
	topBar: "topBar",		// 置顶组件
	button: "button",		// 加载按钮
	design: "design",		// 表單设计
	card: "card",			// 資料卡片组件
	loading: "loading",		// 加载组件
	cropper:"cropper",		// 裁剪组件
	convert:"convert",		// 資料转换
	yaml:"yaml",			// yaml 解析组件
	context: "context",		// 上下文组件
	http: "http",			// ajax请求组件
	theme: "theme",			// 主题转换
	message: "message",     // 通知组件
	uploads: "uploads"		// 上传组件
}).use(['layer', 'theme'], function () {
	layui.theme.changeTheme(window, false);
});