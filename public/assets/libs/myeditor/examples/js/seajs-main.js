define(function(require){
	var $ = require("jquery");
	var editormd = require("editormd");
    
    require("../../src/js/languages/en"); // 載入英語語言包
    
    console.log($, editormd);
                
    $.get("./test.md", function(md){
        testEditor = editormd("test-editormd", {
            width: "90%",
            height: 640,
            path : '../lib/',
            markdown : md,
            //toolbar  : false,             //關閉工具栏
            htmlDecode : true,            // 開啟HTML标签解析，為了安全性，默認不開啟
            tex : true,                   // 開啟科学公式TeX語言支援，默認關閉
            //previewCodeHighlight : false,  // 關閉預覽視窗的程式碼高亮，默認開啟
            flowChart : true,              // 疑似Sea.js与Raphael.js有冲突，必须先載入Raphael.js，Editor.md才能在Sea.js下正常进行；
            sequenceDiagram : true,        // 同上
            onload : function() {
                console.log('onload', this);
                //this.fullscreen();
                //this.unwatch();
                //this.watch().fullscreen();

                //this.setMarkdown("#PHP");
                //this.width("100%");
                //this.height(480);
                //this.resize("100%", 640);
            }
        });
    });

    $("#show-btn").bind('click', function(){
        testEditor.show();
    });

    $("#hide-btn").bind('click', function(){
        testEditor.hide();
    });

    $("#get-md-btn").bind('click', function(){
        alert(testEditor.getMarkdown());
    });

    $("#get-html-btn").bind('click', function() {
        alert(testEditor.getHTML());
    });                

    $("#watch-btn").bind('click', function() {
        testEditor.watch();
    });                 

    $("#unwatch-btn").bind('click', function() {
        testEditor.unwatch();
    });              

    $("#preview-btn").bind('click', function() {
        testEditor.previewing();
    });

    $("#fullscreen-btn").bind('click', function() {
        testEditor.fullscreen();
    });

    $("#show-toolbar-btn").bind('click', function() {
        testEditor.showToolbar();
    });

    $("#close-toolbar-btn").bind('click', function() {
        testEditor.hideToolbar();
    });
});