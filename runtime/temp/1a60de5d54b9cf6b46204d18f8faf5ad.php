<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:71:"/www/wwwroot/web/public/../application/service/view/visitors/index.html";i:1632946290;}*/ ?>

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
							<label class="layui-form-label">用户状态</label>
							<div class="layui-input-inline">
								<select name="state">
									<option value="">请选择用户状态</option>
									<option value="normal">正常</option>
									<option value="in_black_list">黑名单</option>
								</select>
							</div>
						</div>
						<div class="layui-form-item layui-inline">
							<label class="layui-form-label">用户分组</label>
							<div class="layui-input-inline">
								<select name="groupid">
									<option value="">请选择用户分组</option>
									<?php foreach($group as $v): ?>
									<option value="<?php echo $v['id']; ?>"><?php echo (isset($v['group_name']) && ($v['group_name'] !== '')?$v['group_name']:''); ?></option>
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

		<script type="text/html" id="user-status">
			{{#if (d.state == 'normal') { }}
			<span>正常</span>
			{{# }else{ }}
			<span>黑名单</span>
			{{# } }}
		</script>

		<script type="text/html" id="os-enable">
			<span>{{d.extends.os}}{{d.extends.browserName}}</span>
		</script>

		<script type="text/html" id="tool-bar">
			<button class="pear-btn pear-btn-success pear-btn-xs" lay-event="lang"><i class="layui-icon layui-icon-website"></i>语言</button>
			<button class="pear-btn pear-btn-primary pear-btn-xs" lay-event="edit"><i class="layui-icon layui-icon-edit"></i>分组</button>
			{{#if (d.state == 'normal') { }}
			<button class="pear-btn pear-btn-danger pear-btn-xs" lay-event="blacklist"><i class="layui-icon layui-icon-group"></i>拉黑</button>
			{{# }else{ }}
			<button class="pear-btn pear-btn-warn pear-btn-xs" lay-event="white"><i class="layui-icon layui-icon-user"></i>取消拉黑</button>
			{{# } }}

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
                                field: 'visiter_name',
                                title: '用户名',
                                unresize: true,
                                align: 'left',
                            },{
                                field: 'group_name',
                                title: '客户分组',
                                unresize: true,
                                align: 'left',
                            }, {
                                field: 'ip',
                                title: 'IP信息',
                                unresize: true,
                                align: 'left',
                            },  {
								field: 'lang',
								title: '语言',
								unresize: true,
								align: 'left',
							},{
                            	field: 'os',
                            	title: '操作系统',
                                unresize: true,
                                align: 'left',
                            	templet: '#os-enable'
                            },{
								field: 'timestamp',
								title: '最近访问时间',
								unresize: true,
								align: 'left',
							},
                            {
                                field: 'state',
                                title: '在线状态',
                                unresize: true,
                                align: 'left',
                                templet: '#user-status',
                            },
                            {
                                title: '操作',
                                toolbar: '#tool-bar',
                                align: 'center',
                                width: 250
                            }
                        ]
                    ];

				table.render({
					elem: '#dataTable',
					url: MODULE_PATH + 'visitors/index',
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
                    }else if (obj.event === 'blacklist') {
                        window.blacklist(obj);
                    }else if (obj.event === 'white') {
                        window.white(obj);
                    }else if (obj.event === 'lang') {
                        window.lang(obj);
                    }
                });

                form.on('submit(query)', function(data) {
                    table.reload('dataTable', {
                        where: data.field,
                        page:{curr: 1}
                    });
                    return false;
                });

                window.edit = function(obj) {
                    layer.open({
                        type: 2,
                        title: '用户分组',
                        shade: 0.1,
                        area: ['500px', '300px'],
                        content: MODULE_PATH + 'visitors/edit?id='+obj.data.visiter_id
                    });
                };

                window.lang = function(obj) {
                    layer.open({
                        type: 2,
                        title: '用户语言',
                        shade: 0.1,
                        area: ['500px', '300px'],
                        content: MODULE_PATH + 'visitors/lang?id='+obj.data.visiter_id
                    });
                };

                window.blacklist = function(obj) {
                    layer.confirm('确定要拉黑该用户', {
                        icon: 3,
                        title: '提示'
                    }, function(index) {
                        layer.close(index);
                        let loading = layer.load();
                        $.ajax({
                            url: MODULE_PATH + "visitors/blacklist?id="+obj.data.visiter_id,
                            dataType: 'json',
                            type: 'delete',
                            success: function(result) {
                                layer.close(loading);
                                if (result.code === 1) {
                                    layer.msg(result.msg, {
                                        icon: 1,
                                        time: 1000
                                    }, function() {
                                        window.refresh();
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

                window.white = function(obj) {
                    layer.confirm('确定要取消拉黑该用户吗', {
                        icon: 3,
                        title: '提示'
                    }, function(index) {
                        layer.close(index);
                        let loading = layer.load();
                        $.ajax({
                            url: MODULE_PATH + "visitors/white?id="+obj.data.visiter_id,
                            dataType: 'json',
                            type: 'delete',
                            success: function(result) {
                                layer.close(loading);
                                if (result.code === 1) {
                                    layer.msg(result.msg, {
                                        icon: 1,
                                        time: 1000
                                    }, function() {
                                        window.refresh();
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
