/**

 @Name：layuiAdmin iframe版全局配置
 @Author：贤心
 @Site：http://www.layui.com/admin/
 @License：LPPL（layui付费产品协议）
    
 */
 
layui.define(['laytpl', 'layer', 'element', 'util'], function(exports){
  exports('setter', {
    container: 'LAY_app' //容器ID
    ,base: layui.cache.base //记录静态資源所在路徑
    ,views: layui.cache.base + 'tpl/' //動态模板所在目錄
    ,entry: 'index' //默認视圖文件名
    ,engine: '.html' //视圖文件後缀名
    ,pageTabs: true //是否開啟頁面选项卡功能。iframe版推荐開啟
    
    ,name: 'layuiAdmin'
    ,tableName: 'layuiAdmin' //本地存储表名
    ,MOD_NAME: 'admin' //模組事件名
    
    ,debug: true //是否開啟调试模式。如開啟，接口异常时会抛出异常 URL 等訊息

    //自訂請求字段
    ,request: {
      tokenName: false //自動携带 token 的字段名（如：access_token）。可設定 false 不携带。
    }
    
    //自訂响应字段
    ,response: {
      statusName: 'code' //資料狀態的字段名稱
      ,statusCode: {
        ok: 0 //資料狀態一切正常的狀態碼
        ,logout: 1001 //登入狀態失效的狀態碼
      }
      ,msgName: 'msg' //狀態訊息的字段名稱
      ,dataName: 'data' //資料详情的字段名稱
    }
    
    //扩展的第三方模組
    ,extend: [
      'echarts', //echarts 核心包
      'echartsTheme' //echarts 主题
    ]
    
    //主题配置
    ,theme: {
      //内置主题配色方案
      color: [{
        main: '#20222A' //主题色
        ,selected: '#009688' //选中色
        ,alias: 'default' //默認别名
      },{
        main: '#03152A'
        ,selected: '#3B91FF'
        ,alias: 'dark-blue' //藏蓝
      },{
        main: '#2E241B'
        ,selected: '#A48566'
        ,alias: 'coffee' //咖啡
      },{
        main: '#50314F'
        ,selected: '#7A4D7B'
        ,alias: 'purple-red' //紫红
      },{
        main: '#344058'
        ,logo: '#1E9FFF'
        ,selected: '#1E9FFF'
        ,alias: 'ocean' //海洋
      },{
        main: '#3A3D49'
        ,logo: '#2F9688'
        ,selected: '#5FB878'
        ,alias: 'green' //墨绿
      },{
        main: '#20222A'
        ,logo: '#F78400'
        ,selected: '#F78400'
        ,alias: 'red' //橙色
      },{
        main: '#28333E'
        ,logo: '#AA3130'
        ,selected: '#AA3130'
        ,alias: 'fashion-red' //时尚红
      },{
        main: '#24262F'
        ,logo: '#3A3D49'
        ,selected: '#009688'
        ,alias: 'classic-black' //经典黑
      },{
        logo: '#226A62'
        ,header: '#2F9688'
        ,alias: 'green-header' //墨绿头
      },{
        main: '#344058'
        ,logo: '#0085E8'
        ,selected: '#1E9FFF'
        ,header: '#1E9FFF'
        ,alias: 'ocean-header' //海洋头
      },{
        header: '#393D49'
        ,alias: 'classic-black-header' //经典黑头
      },{
        main: '#50314F'
        ,logo: '#50314F'
        ,selected: '#7A4D7B'
        ,header: '#50314F'
        ,alias: 'purple-red-header' //紫红头
      },{
        main: '#28333E'
        ,logo: '#28333E'
        ,selected: '#AA3130'
        ,header: '#AA3130'
        ,alias: 'fashion-red-header' //时尚红头
      },{
        main: '#28333E'
        ,logo: '#009688'
        ,selected: '#009688'
        ,header: '#009688'
        ,alias: 'green-header' //墨绿头
      }]
      
      //初始的颜色索引，對应上面的配色方案數组索引
      //如果本地已经有主题色记录，則以本地记录為優先，除非請求本地資料（localStorage）
      ,initColorIndex: 0
    }
  });
});
