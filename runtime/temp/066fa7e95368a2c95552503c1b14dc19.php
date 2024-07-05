<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:70:"/www/wwwroot/web/public/../application/service/view/visitors/lang.html";i:1632933632;}*/ ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="/static/component/pear/css/pear.css" />
</head>
<body>
<form class="layui-form" action="">
	<div class="mainBox">
		<div class="main-container">
			<div class="layui-form-item">
				<label class="layui-form-label">用户语言</label>
				<div class="layui-input-inline">
					<select name="lang" lay-verify="required">
						<option value="">请选择用户语言</option>
						<?php $_667a94b451bf2=config('lang'); if(is_array($_667a94b451bf2) || $_667a94b451bf2 instanceof \think\Collection || $_667a94b451bf2 instanceof \think\Paginator): if( count($_667a94b451bf2)==0 ) : echo "" ;else: foreach($_667a94b451bf2 as $key=>$vo): ?>
						<option value="<?php echo $key; ?>" <?php if($visiter['lang'] == $key): ?>selected<?php endif; ?>><?php echo $vo; ?></option>
						<?php endforeach; endif; else: echo "" ;endif; ?>
					</select>
				</div>
			</div>
		</div>
	</div>
	<div class="bottom">
		<div class="button-container">
			<button type="submit" class="layui-btn layui-btn-normal layui-btn-sm" lay-submit="" lay-filter="save">
				<i class="layui-icon layui-icon-ok"></i>
				提交
			</button>
			<button type="reset" class="layui-btn layui-btn-primary layui-btn-sm">
				<i class="layui-icon layui-icon-refresh"></i>
				重置
			</button>
		</div>
	</div>
</form>
<script src="/static/component/layui/layui.js"></script>
<script src="/static/component/pear/pear.js"></script>
<script>
    layui.use(['form','jquery'],function(){
        let form = layui.form;
        let $ = layui.jquery;

        form.on('submit(save)', function(data){
            $.ajax({
                data:JSON.stringify(data.field),
                dataType:'json',
                contentType:'application/json',
                type:'post',
                success:function(res){
                    if (res.code==1) {
                        layer.msg(res.msg, {
                            icon: 1
                        });
                        setTimeout(function() {
                            var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
                            parent.layer.close(index);
                            parent.layui.table.reload("dataTable");
                        }, 2000)
                    }else {
                        layer.msg(res.msg,{icon:2,time:1500})
                    }
                }
            });
            return false;
        });
    })
</script>
</body>
</html>