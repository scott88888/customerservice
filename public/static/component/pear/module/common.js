layui.define(['jquery', 'element','table'], function(exports) {
	"use strict";

	/**
	 * 常用封装類
	 * */
	var MOD_NAME = 'common',
		$ = layui.jquery,
		table = layui.table,
		element = layui.element;

	var common = new function() {
		
		/**
		 * 取得當前表格选中字段
		 * @param obj 表格回调参數
		 * @param field 要取得的字段
		 * */
		this.checkField = function(obj, field) {
			let data = table.checkStatus(obj.config.id).data;
			if (data.length === 0) {
				return "";
			}
			let ids = "";
			for (let i = 0; i < data.length; i++) {
				ids += data[i][field] + ",";
			}
			ids = ids.substr(0, ids.length - 1);
			return ids;
		}
		
		/**
		 * 當前是否為与移動端
		 * */
		this.isModile = function(){
			if ($(window).width() <= 768) {
				return true;
			}
			return false;
		}
		
		
		/**
		 * 送出 json 資料
		 * @param data 送出資料
		 * @param href 送出接口
		 * @param table 刷新父级表
		 * 
		 * */
		this.submit = function(data,href,table,callback){
			$.ajax({
			    url:href,
			    data:JSON.stringify(data),
			    dataType:'json',
			    contentType:'application/json',
			    type:'post',
			    success:callback !=null?callback(result):function(result){
			        if(result.success){
			            layer.msg(result.msg,{icon:1,time:1000},function(){
			                parent.layer.close(parent.layer.getFrameIndex(window.name));//關閉當前頁
			                parent.layui.table.reload(table);
			            });
			        }else{
			            layer.msg(result.msg,{icon:2,time:1000});
			        }
			    }
			})
		}
	}
	exports(MOD_NAME, common);
});
