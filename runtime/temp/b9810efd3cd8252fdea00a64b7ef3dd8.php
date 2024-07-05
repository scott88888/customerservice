<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:71:"/www/wwwroot/web/public/../application/service/view/services/index.html";i:1634650162;}*/ ?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <link rel="stylesheet" href="/static/component/pear/css/pear.css" />
</head>
<body class="pear-container">
		<div class="layui-card">
			<div class="layui-card-body">
				<form class="layui-form" action="">
					<div class="layui-form-item">
						<div class="layui-form-item layui-inline">
							<label class="layui-form-label">用户名</label>
							<div class="layui-input-inline">
								<input type="text" name="user_name" placeholder="请输入客服用户名" class="layui-input">
							</div>
						</div>
						<div class="layui-form-item layui-inline">
							<label class="layui-form-label">客服分组</label>
							<div class="layui-input-inline">
								<select name="groupid">
									<option value="">请选择客服分组</option>
									<?php foreach($group as $v): ?>
									<option value="<?php echo $v['id']; ?>"><?php echo (isset($v['groupname']) && ($v['groupname'] !== '')?$v['groupname']:''); ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="layui-form-item layui-inline">
							<button class="pear-btn pear-btn-md pear-btn-primary" lay-submit lay-filter="query">
								<i class="layui-icon layui-icon-search"></i>
								查询
							</button>
							<button type="reset" class="pear-btn pear-btn-md">
								<i class="layui-icon layui-icon-refresh"></i>
								重置
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>
		<div class="layui-card">
			<div class="layui-card-body">
				<table id="dataTable" lay-filter="dataTable"></table>
			</div>
		</div>

		<script type="text/html" id="toolbar">
		    <button class="pear-btn pear-btn-primary pear-btn-md" lay-event="add">
		        <i class="layui-icon layui-icon-addition"></i>
		        添加客服
			</button>
		</script>

		<script type="text/html" id="user-status">
			{{#if (d.state == 'online') { }}
			<span>在线</span>
			{{# }else{ }}
			<span>离线</span>
			{{# } }}
		</script>

		<script type="text/html" id="user-enable">
			<input type="checkbox" name="enable" value="{{d.service_id}}" lay-skin="switch" lay-text="开启|关闭" lay-filter="user-enable" {{ d.offline_first == 1 ? 'checked' : '' }}>
		</script>

		<script type="text/html" id="tool-bar">
			<button class="pear-btn pear-btn-primary pear-btn-xs" lay-event="edit"><i class="layui-icon layui-icon-edit"></i>编辑</button>
			<button class="pear-btn pear-btn-success pear-btn-xs" lay-event="pass"><i class="layui-icon layui-icon-password"></i>密码</button>
			<button class="pear-btn pear-btn-danger pear-btn-xs" lay-event="remove"><i class="layui-icon layui-icon-delete"></i>删除</button>
			<button class="pear-btn pear-btn-xs copy" lay-event="copy" data-clipboard-text="{{d.personal}}"><i class="layui-icon layui-icon-link"></i>复制链接</button>
			<button class="pear-btn pear-btn-xs copy" lay-event="copy" data-clipboard-text="{{d.personalwechat}}"><i class="layui-icon layui-icon-link"></i>微信链接</button>
		</script>

        <script src="/static/component/layui/layui.js"></script>
        <script src="/static/component/pear/pear.js"></script>
		<script src="/assets/js/platform/clipboard.min.js?v=AI_KF"></script>
        <script>
			layui.use(['table', 'form', 'jquery','common'], function() {
				let table = layui.table;
				let form = layui.form;
				let $ = layui.jquery;
				let common = layui.common;

				let MODULE_PATH = "/service/";

                let cols = [
                        [{
                                field: 'service_id',
                                title: 'ID',
                                unresize: true,
                                align: 'left',
                                width: 80
                            },{
                                field: 'user_name',
                                title: '用户名',
                                unresize: true,
                                align: 'left'
                            }, {
                                field: 'nick_name',
                                title: '昵称',
                                unresize: true,
                                align: 'left',
                            },  {
                                field: 'group_name',
                                title: '客服分组',
                                unresize: true,
                                align: 'left',
                            },
							{
                                title: '离线优先',
                                field: 'offline_first',
                                align: 'left',
                                templet: '#user-enable'
							},
                            {
                                field: 'state',
                                title: '在线状态',
                                unresize: true,
                                align: 'left',
                                templet: '#user-status'
                            },
                            {
                                title: '操作',
                                toolbar: '#tool-bar',
                                align: 'center',
                                width: 500
                            }
                        ]
                    ];

				table.render({
					elem: '#dataTable',
					url: MODULE_PATH + 'services/index',
					page: true,
					cols: cols,
                    cellMinWidth: 100,
					skin: 'line',
					toolbar: '#toolbar',
					defaultToolbar: [{
						title: '刷新',
						layEvent: 'refresh',
						icon: 'layui-icon-refresh',
					}, 'filter', 'print', 'exports']
				});

				table.on('toolbar(dataTable)', function(obj) {
					if (obj.event === 'refresh') {
						window.refresh();
					} else if (obj.event === 'add') {
                        window.add(obj);
                    }
				});

                table.on('tool(dataTable)', function(obj) {
					if (obj.event === 'edit') {
                        window.edit(obj);
                    }else if (obj.event === 'pass') {
                        window.pass(obj);
                    }else if (obj.event === 'copy') {
                        window.copy(obj);
                    }else if (obj.event === 'remove') {
                        window.remove(obj);
                    }
                });

                form.on('submit(query)', function(data) {
                    table.reload('dataTable', {
                        where: data.field,
                        page:{curr: 1}
                    });
                    return false;
                });

                form.on('switch(user-enable)', function(obj) {
                    let offline_first = obj.elem.checked?'1':0;
                    $.ajax({
                        type: "POST",
                        url: MODULE_PATH + "services/offline_first",
                        dataType: 'json',
						data: {'service_id':this.value,'offline_first':offline_first},
                        success: function(result) {
                            if (result.code === 1) {
                                layer.tips(result.msg, obj.othis);
                            } else {
                                layer.tips(result.msg, obj.othis);
                            }
                        }
                    });
                });

				window.add = function(obj) {
                    layer.open({
                        type: 2,
                        title: '添加客服',
                        shade: 0.1,
                        area: ['500px', '500px'],
                        content: MODULE_PATH + 'services/add'
                    });
                };

                window.edit = function(obj) {
                    layer.open({
                        type: 2,
                        title: '编辑客服',
                        shade: 0.1,
                        area: ['500px', '500px'],
                        content: MODULE_PATH + 'services/edit?id='+obj.data.service_id
                    });
                };

                window.pass = function(obj) {
                    layer.open({
                        type: 2,
                        title: '修改密码',
                        shade: 0.1,
                        area: ['500px', '250px'],
                        content: MODULE_PATH + 'services/pass?id='+obj.data.service_id
                    });
                };

                window.copy = function(obj) {
                    var clipboard = new ClipboardJS('.copy');
                    clipboard.on('success', function(e) {
                        layer.msg('复制成功', {icon: 1});
                        e.clearSelection();
                    });
                    clipboard.on('error', function(e) {
                        layer.msg('复制成功，请关闭兼容模式或者升级浏览器');
                    });
				};

                window.remove = function(obj) {
                    layer.confirm('确定要删除该用户', {
                        icon: 3,
                        title: '提示'
                    }, function(index) {
                        layer.close(index);
                        let loading = layer.load();
                        $.ajax({
                            url: MODULE_PATH + "services/remove?service_id="+obj.data.service_id,
                            dataType: 'json',
                            type: 'delete',
                            success: function(result) {
                                layer.close(loading);
                                if (result.code === 1) {
                                    layer.msg(result.msg, {
                                        icon: 1,
                                        time: 1000
                                    }, function() {
                                        obj.del();
                                    });
                                } else {
                                    layer.msg(result.msg, {
                                        icon: 2,
                                        time: 1000
                                    });
                                }
                            }
                        })
                    });
                };
                
				window.refresh = function(param) {
					table.reload('dataTable');
				}
			})
		</script>
</body>
</html>
