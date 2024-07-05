<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:72:"/www/wwwroot/web/public/../application/service/view/questions/index.html";i:1632912024;}*/ ?>

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
							<label class="layui-form-label">关键词</label>
							<div class="layui-input-inline">
								<input type="text" name="keyword" placeholder="请输入关键词" class="layui-input">
							</div>
						</div>
						<div class="layui-form-item layui-inline">
							<label class="layui-form-label">语言</label>
							<div class="layui-input-inline">
								<select name="lang">
									<option value="">选择语言</option>
									<?php $_667aa10c64870=config('lang'); if(is_array($_667aa10c64870) || $_667aa10c64870 instanceof \think\Collection || $_667aa10c64870 instanceof \think\Paginator): if( count($_667aa10c64870)==0 ) : echo "" ;else: foreach($_667aa10c64870 as $key=>$vo): ?>
									<option value="<?php echo $key; ?>"><?php echo $vo; ?></option>
									<?php endforeach; endif; else: echo "" ;endif; ?>
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
		        添加常见问题
			</button>
		</script>

		<script type="text/html" id="status">
			{{#if (d.status == 1) { }}
			<span>显示</span>
			{{# }else{ }}
			<span>不显示</span>
			{{# } }}
		</script>

		<script type="text/html" id="tool-bar">
			<button class="pear-btn pear-btn-primary pear-btn-xs" lay-event="edit"><i class="layui-icon layui-icon-edit"></i>编辑</button>
			<button class="pear-btn pear-btn-danger pear-btn-xs" lay-event="remove"><i class="layui-icon layui-icon-delete"></i>删除</button>
		</script>

        <script src="/static/component/layui/layui.js"></script>
        <script src="/static/component/pear/pear.js"></script>
        <script>
			layui.use(['table', 'form', 'jquery','common'], function() {
				let table = layui.table;
				let form = layui.form;
				let $ = layui.jquery;
				let common = layui.common;

				let MODULE_PATH = "/service/";

                let cols = [
                        [{
                                field: 'question',
                                title: '问题',
                                unresize: true,
                                align: 'left'
                            }, {
                                field: 'keyword',
                                title: '关键词',
                                unresize: true,
                                align: 'left',
                            },  {
                                field: 'answer',
                                title: '回答',
                                unresize: true,
                                align: 'left',
								width: 300,
                            },{
								field: 'sort',
								title: '排序',
								unresize: true,
								align: 'left',
							},{
								field: 'lang',
								title: '语言',
								unresize: true,
								align: 'left',
							},
                            {
                                field: 'status',
                                title: '是否展示',
                                unresize: true,
                                align: 'left',
                            	templet: '#status'
                            },
                            {
                                title: '操作',
                                toolbar: '#tool-bar',
                                align: 'center',
                                width: 200
                            }
                        ]
                    ];

				table.render({
					elem: '#dataTable',
					url: MODULE_PATH + 'questions/index',
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

				window.add = function(obj) {
                    layer.open({
                        type: 2,
                        title: '添加常见问题',
                        shade: 0.1,
                        area: ['700px', '500px'],
                        content: MODULE_PATH + 'questions/add'
                    });
                };

                window.edit = function(obj) {
                    layer.open({
                        type: 2,
                        title: '编辑常见问题',
                        shade: 0.1,
                        area: ['700px', '500px'],
                        content: MODULE_PATH + 'questions/edit?id='+obj.data.qid
                    });
                };

                window.remove = function(obj) {
                    layer.confirm('确定要删除该常见问题吗？', {
                        icon: 3,
                        title: '提示'
                    }, function(index) {
                        layer.close(index);
                        let loading = layer.load();
                        $.ajax({
                            url: MODULE_PATH + "questions/remove?id="+obj.data.qid,
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
