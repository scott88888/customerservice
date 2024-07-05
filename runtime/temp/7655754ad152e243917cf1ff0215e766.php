<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:67:"/www/wwwroot/web/public/../application/service/view/chat/index.html";i:1639672868;}*/ ?>
<link href="/assets/libs/layui/css/layui.css" rel="stylesheet">
<link href="/assets/libs/amaze/css/amazeui.min.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="/assets/libs/jquery/jquery.min.js"></script>
<script type="text/javascript" src="/assets/libs/jquery/jquery.form.min.js"></script>
<script src="/assets/libs/amaze/js/amazeui.min.js" type="text/javascript"></script>
<script src="/assets/libs/push/pusher.min.js?v=AI_KF" type="text/javascript"></script>
<script src="/assets/js/admin/functions.js?v=AI_KF" type="text/javascript"></script>
<link href="/assets/css/admin/index.css" type="text/css" rel="stylesheet" />
<link href="/assets/css/admin/index_me.css" type="text/css" rel="stylesheet" />
<script src="/static/component/layui/layui.js"></script>
<script src="/assets/libs/jquery/jquery.cookie.js" type="text/javascript"></script>
<script type="text/javascript" src="/assets/libs/webrtc/recorder.js"></script>
<link href="/assets/css/admin/ymwl_common.css" type="text/css" rel="stylesheet" />
<script type="application/javascript">
    var mediaStreamTrack;
    var WEB_SOCKET_SWF_LOCATION = "/assets/libs/web_socket/WebSocketMain.swf";
    var WEB_SOCKET_DEBUG = true;
    var WEB_SOCKET_SUPPRESS_CROSS_DOMAIN_SWF_ERROR = true;
    var chat_data =Array();
    var record;
    var choose_lock = false;
    var myTitle = document.title;
    var msgreminder = <?php echo config('setting.msgreminder'); ?>;
    var config ={
        'app_key':'<?php echo $app_key; ?>',
        'whost':'<?php echo $whost; ?>',
        'value':<?php echo $value; ?>,
        'wport':<?php echo $wport; ?>
    };
    function titleBlink(){
        record++;

        if(record === 3){
            record =1;
        }

        if(record === 1){
            document.title='【 】'+myTitle;
        }

        if(record === 2){
            document.title='【消息】'+myTitle;
        }

        if(record > 3){
            getwaitnum();
            return;
        }

        setTimeout("titleBlink()",500);//调节时间，单位毫秒。
    }

    layui.use('element', function () {
        var element = layui.element;
    });
    var wolive_connect =function () {
        pusher = new Pusher('<?php echo $app_key; ?>', {
            encrypted: <?php echo $value; ?>
            , enabledTransports: ['ws']
            , wsHost: '<?php echo $whost; ?>'
            , <?php echo $port; ?>: <?php echo $wport; ?>
    , authEndpoint:  '/admin/login/auth'
            ,disableStats: true
    });

        var web = "<?php echo $arr['business_id']; ?>";
        var value ="<?php echo $arr['service_id']; ?>";
        // 私人频道
        var channelme = pusher.subscribe("ud" + value);
        channelme.bind("on_notice", function (data) {
            if(data.message.type == 'change'){
                layer.msg(data.message.msg);
            }
            getchat();
            getwait();
        });

        channelme.bind("on_chat", function (data) {
            $.cookie("cu_com",'');
            layer.msg('该访客被删除');
            getchat();
        });

        // 公共平道
        var channelall = pusher.subscribe("all" + web);
        channelall.bind("on_notice", function (data) {
            if(<?php echo $arr['groupid']; ?> == 0 || <?php echo $arr['groupid']; ?> == data.message.groupid){
                layer.msg(data.message.msg, {offset: "20px"});
            }
            if(<?php echo $arr['groupid']; ?> != data.message.groupid){

                layer.msg('该用户向其他分组咨询！', {offset: "20px"});
            }

            getwait();
            getchat();

        });

        var channel =pusher.subscribe("kefu" + value);
        // 发送一个推送
        channel.bind("callbackpusher",function(data){
            $.post("<?php echo url('admin/set/callback','',true,true); ?>",data,function(res){
            })
        });

        // 接受视频请求
        channel.bind("video",function (data) {
            getchat();
            var msg = data.message;
            var cha = data.channel;
            var cid = data.cid;
            var avatar =data.avatar;
            var username =data.username;
            layer.open({
                type: 1,
                title: '申请框',
                area: ['260px', '160px'],
                shade: 0.01,
                fixed: true,
                btn: ['接受', '拒绝'],
                content: "<div style='position: absolute;left:20px;top:15px;'><img src='"+avatar+"' width='40px' height='40px' style='border-radius:40px;position:absolute;left:5px;top:5px;'><span style='width:100px;position:absolute;left:70px;top:5px;font-size:13px;overflow-x: hidden;'>"+username+"</span><div style='width:90px;height:20px;position:absolute;left:70px;top:26px;'>"+msg+"</div></div>",
                yes: function () {
                    layer.closeAll('page');
                    var str='';
                    str+='<div class="videos">';
                    str+='<video id="localVideo" autoplay></video>';
                    str+='<video id="remoteVideo" autoplay class="hidden"></video></div>';


                    layer.open({
                        type:1
                        ,title: '视频'
                        ,shade:0
                        ,closeBtn:1
                        ,area: ['440px', '378px']
                        ,content:str
                        ,end:function(){


                            mediaStreamTrack.getTracks().forEach(function (track) {
                                track.stop();
                            });

                        }
                    });
                    try{
                        connenctVide(cid);
                    }catch(e){
                        console.log(e);
                        return;
                    }

                },
                btn2:function(){
                    var sid = $('#channel').text();
                    $.ajax({
                        url:'/admin/set/refuse',
                        type:'post',
                        data:{channel:cha}
                    });

                    layer.closeAll('page');
                }
            });
        });

        channel.bind('bind-wechat',function(data){
            layer.open({
                content: data.message
                ,btn: ['确定']
                ,yes: function(index, layero){
                    location.reload();
                }
                ,cancel: function(){
                    return false;
                }
            });
        });


        channel.bind('getswitch',function(data){
            layer.alert(data.message);
            getchat();
        });

        // 接受拒绝视频请求
        channel.bind("video-refuse",function (data) {
            layer.alert(data.message);
            layer.closeAll('page');
        });
        // 接受消息
        channel.bind("cu-event", function (data) {
            if("<?php echo $voice; ?>" == 'open'){
                audioElementHovertree = document.createElement('audio');
                audioElementHovertree.setAttribute('src', "<?php echo $voice_address; ?>");
                audioElementHovertree.setAttribute('autoplay', 'autoplay');
            }
            var debug, portrait,showtime;
            var cdata = $.cookie("cu_com");
            if (cdata) {
                var json = $.parseJSON(cdata);
                debug = json.visiter_id;
                portrait = json.avatar;
            } else {
                debug = "";

            }
            if($.cookie("time") == ""){
                time =data.message.timestamp;
                $.cookie("time",time);
                var mydate =new Date(time*1000);
                showtime =mydate.getHours()+":"+mydate.getMinutes();
            }else{
                time =$.cookie("time");
                if((data.message.timestamp - time) >60){
                    var mydate =new Date(data.message.timestamp*1000);
                    showtime =mydate.getHours()+":"+mydate.getMinutes();
                }else{
                    showtime ="";
                }
                $.cookie("time",data.message.timestamp);
            }
            var msg = '';
            msg += '<li class="chatmsg"><div class="showtime">' +showtime+ '</div><div style="position: absolute;left:3px;">';
            msg += '<img class="my-circle  se_pic" src="' + portrait + '" width="50px" height="50px"></div>';
            msg += "<div class='outer-left'><div class='customer'>";

            if(data.message.content.indexOf('<img')>= 0||data.message.content.indexOf('<a')>= 0||!isNaN(data.message.content)||data.message.content.indexOf('<video')>= 0){
                msg += "<pre>" + data.message.content + "</pre>";
            }else{
                if(data.message.content_trans !=''){
                    msg += "<pre>" + data.message.content + "<p class='trans-data'>译文："+data.message.content_trans+"</p></pre>";
                }else{
                    msg += "<pre>" + data.message.content + "<span class='trans' data-cid='"+data.message.cid+"'>翻 译</span></pre>";
                }
            }
            msg += "</div></div>";
            msg += "</li>";
            var str = data.message.content;
            if (data.message.visiter_id == debug) {
                console.log(msg);
                $(".conversation").append(msg);
                getwatch(data.message.visiter_id);
                str.replace(/<img [^>]*src=['"]([^'"]+)[^>]*>/gi, function (match, capture) {
                    var pos = capture.lastIndexOf("/");
                    var value = capture.substring(pos + 1);
                    if (value.indexOf("emo") == 0) {
                        str = data.message.content;
                    } else {
                        str = '[图片]';
                    }
                });
                str = str.replace(/<div><a[^<>]+><i>.+?<\/i>.+?<\/a><\/div>/,'[文件]');
                str = str.replace(/<a[^<>]+>.+?<\/a>/,'[超链接]');
                str =str.replace(/<img src=['"]([^'"]+)[^>]*>/gi,'[图片]');
                $("#msg" + data.message.channel).html(str);
                console.log(data);
                var div = document.getElementById("wrap");
            }
            getnow(data.message);
            if(div){
                div.scrollTop = div.scrollHeight;
            }
            $("#notices-icon").removeClass('hide');
            notify(data.message.visiter_name || '新访客', {
                body: str,
                icon: data.message.avatar
            }, function(notification) {
                //可直接打开通知notification相关联的tab窗口
                window.focus();
                notification.close();
                console.log('#v'+data.message.channel+' .visit_content');
                $('#v'+data.message.channel+' .visit_content').trigger('click');
            });
        });


        // 通知 游客离线
        channel.bind("logout", function (data) {

            //表示访客离线
            var cdata = $.cookie("cu_com");
            var chas;
            if (cdata) {
                var jsondata = $.parseJSON(cdata);
                chas = jsondata.channel;
            }

            if (chas == data.message.chas) {
                //头像变灰
                $("#v_state").text("离线");
            }

            $("#img" + data.message.chas).addClass("icon_gray");
            getchat();

        });

        channel.bind("geton", function (data) {
            //表示访客在线
            var cdata = $.cookie("cu_com");
            var chas;
            if (cdata) {
                var jsondata = $.parseJSON(cdata);
                chas = jsondata.channel;
            }
            if (chas == data.message.chas) {
                //头像变亮
                $("#img" + data.message.chas).removeClass("icon_gray");
                $("#v_state").text("在线");
            }
            $("#img" + data.message.chas).removeClass("icon_gray");
            getchat();
        });

        pusher.connection.bind('state_change', function(states) {
            if(states.current == 'unavailable' || states.current == "disconnected" || states.current == "failed" ){

                pusher.unsubscribe("kefu" + value);
                pusher.unsubscribe("all" + web);
                pusher.unsubscribe("ud" + value);
                if (typeof pusher.isdisconnect == 'undefined') {
                    pusher.isdisconnect = true;
                    pusher.disconnect();
                    delete pusher;
                    window.setTimeout(function(){
                        wolive_connect();
                    },1000);
                }
                $(".profile").text('离线');
            }
        });

        pusher.connection.bind('connected', function() {
            $(".profile").text('在线');
        });
    };


    function showpage(obj){
        var value =$(obj).attr("name");
        var key =$(obj).attr("id");
        layer.tips(value, '#'+key,{tips: [4, '#2F4050']});
    }

    wolive_connect();

</script>

<script type="text/javascript" src="/assets/libs/web_socket/swfobject.js"></script>
<script type="text/javascript" src="/assets/libs/web_socket/web_socket.js"></script>
<script type="text/javascript" src="/assets/js/admin/online.js?v=2021914"></script>
<div id="container" style="overflow: hidden">
    <img src="<?php echo $service['avatar']; ?>" id="se_avatar" style="display: none">
    <span id="channel" style="display: none;"></span>
    <span id="customer" style="display: none;"></span>
    <div class="all_content" style="overflow-y: hidden;">
        <section class="" style="width:20%;height:100%;position:absolute;left:0px;background: #F7F7F7;min-width: 240px;border-right: 1px solid whitesmoke;">
            <div class="layui-tab layui-tab-brief" lay-filter="docDemoTabBrief"
                 style="margin:0px;position: absolute;top:0px;width: 100%;height: 100%">
                <ul class="layui-tab-title" style="height: 50px;border: 0;">
                    <li class="layui-this" style="width: 50%;color: #555555;height: 50px;line-height: 55px;">当前对话<span class="line"></span></li>
                    <li style="width: 50%;color: #555555;height: 50px;line-height: 55px;">排队列表 <div id="waitnum" class="notice-icon hide" style="position: absolute;top:0px;line-height: 18px;font-size: 8px;" ></div><span class="line"></span></li>
                </ul>
                <div class="layui-tab-content" style="padding: 0px;height: 100%;">
                    <div class="layui-tab-item  layui-show" id="chat_list" style="width: 100%;overflow-y: auto;">
                    </div>
                    <div class="layui-tab-item" id="wait_list" style="width: 100%;overflow-y: auto;">
                    </div>
                </div>
            </div>
        </section>


        <section style="width:52%;height:100%;position: absolute;left: 20%;background: #F7F7F7;min-width: 600px;">
            <div class="no_chats">
                <i class="no_chats-pic"></i>
            </div>
            <div class="chatbox hide" style="width: 100%;height: 100%;padding-bottom: 242px">

                <div id="wrap" style="width: 100%;height:100%;overflow-y: auto;background-color: #fff">

                    <ul class="conversation">

                    </ul>
                </div>
                <script type="text/javascript">

                    window.onresize = function(){
                        var height =document.body.clientHeight;
                        $("#chat_list").css("height",(height -110)+"px");
                        $("#wait_list").css("height",(height-110)+"px");
                    };

                    document.getElementById("wrap").onscroll = function(){
                        var t =  document.getElementById("wrap").scrollTop;
                        if( t == 0 ) {
                            var sdata = $.cookie("cu_com");
                            var jsondata = $.parseJSON(sdata);
                            var chas = jsondata.visiter_id;
                            if($.cookie("hid") != ""){
                                getdata(chas);
                            }
                        }
                    };
                    function info(){
                        layer.tips("将您剪切好的图片粘贴到输入框即可", "#paste", {tips: [1, '#9EC6EA']});
                    }
                </script>
                <div class="footer">
                    <div class="tool_box">

                        <div class="wl_faces_content">

                            <div class="wl_faces_main">
                                <ul>
                                    <li><a href="javascript:;" ><img title="emoji1f600" src="/upload/emoji/1f600.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f601" src="/upload/emoji/1f601.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f602" src="/upload/emoji/1f602.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f603" src="/upload/emoji/1f603.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f604" src="/upload/emoji/1f604.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f605" src="/upload/emoji/1f605.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f606" src="/upload/emoji/1f606.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f607" src="/upload/emoji/1f607.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f608" src="/upload/emoji/1f608.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f609" src="/upload/emoji/1f609.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f610" src="/upload/emoji/1f610.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f611" src="/upload/emoji/1f611.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f612" src="/upload/emoji/1f612.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f613" src="/upload/emoji/1f613.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f614" src="/upload/emoji/1f614.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f615" src="/upload/emoji/1f615.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f616" src="/upload/emoji/1f616.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f617" src="/upload/emoji/1f617.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f618" src="/upload/emoji/1f618.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f619" src="/upload/emoji/1f619.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f620" src="/upload/emoji/1f620.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f621" src="/upload/emoji/1f621.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f622" src="/upload/emoji/1f622.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f623" src="/upload/emoji/1f623.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f624" src="/upload/emoji/1f624.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f625" src="/upload/emoji/1f625.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f626" src="/upload/emoji/1f626.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f627" src="/upload/emoji/1f627.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f628" src="/upload/emoji/1f628.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f629" src="/upload/emoji/1f629.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f630" src="/upload/emoji/1f630.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f631" src="/upload/emoji/1f631.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f632" src="/upload/emoji/1f632.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f633" src="/upload/emoji/1f633.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f634" src="/upload/emoji/1f634.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f635" src="/upload/emoji/1f635.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f636" src="/upload/emoji/1f636.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f637" src="/upload/emoji/1f637.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f638" src="/upload/emoji/1f638.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f639" src="/upload/emoji/1f639.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f640" src="/upload/emoji/1f640.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f641" src="/upload/emoji/1f641.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f642" src="/upload/emoji/1f642.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f643" src="/upload/emoji/1f643.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f644" src="/upload/emoji/1f644.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f910" src="/upload/emoji/1f910.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f911" src="/upload/emoji/1f911.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f912" src="/upload/emoji/1f912.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f913" src="/upload/emoji/1f913.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f914" src="/upload/emoji/1f914.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f915" src="/upload/emoji/1f915.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f916" src="/upload/emoji/1f916.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f917" src="/upload/emoji/1f917.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f923" src="/upload/emoji/1f923.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f924" src="/upload/emoji/1f924.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f925" src="/upload/emoji/1f925.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f926" src="/upload/emoji/1f926.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f927" src="/upload/emoji/1f927.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f928" src="/upload/emoji/1f928.png"/></a></li>
                                    <li><a href="javascript:;" ><img title="emoji1f929" src="/upload/emoji/1f929.png"/></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="msg-input">
                        <div class="input-box">
                            <textarea id="text_in" class="edit-ipt" style="overflow-y: auto; font-weight: normal; font-size: 14px; overflow-x: hidden; word-break: break-all; font-style: normal; outline: none;padding: 5px;border:none;" contenteditable="true" hidefocus="true" tabindex="0" placeholder="请输入..."></textarea>
                        </div>
                        <div class="msg-toolbar-footer grey12" >
                            <a onclick="send()" class="layui-btn msg-send-btn">
                                发送
                            </a>
                            <a id="showinfo" class="showinfo">
                                <div style="height: 24px;border-left: 1px solid #FFF;margin-top: 8px;padding: 7px 15px">
                                    <img src="/assets/images/admin/B/up-menu.png" alt="">
                                </div>
                                <!-- <i class='triangle'  style="margin-top: 21px;"></i> -->
                            </a>
                        </div>
                    </div>
                    <div class="msg-toolbar" style="background: #fff;border: none;">
                        <a id="face_icon" onclick="faceon()"><img src="/assets/images/admin/B/smile.png" alt="表情" title="表情"></a>
                        <a>
                            <form id="picture" enctype="multipart/form-data">
                                <div class="am-form-group am-form-file">
                                    <img src="/assets/images/admin/B/photo.png" alt="">
                                    <input type="file" name="upload" onchange="put()"/>
                                </div>
                            </form>
                        </a>
                        <a>
                            <form id="file" enctype="multipart/form-data">
                                <div class="am-form-group am-form-file">
                                    <img src="/assets/images/admin/B/file.png" alt="">
                                    <input type="file" name="folder" onchange="putfile()"/>
                                </div>
                            </form>
                        </a>
                        <?php if($type == 'open'): ?>
                        <a onclick="getvideo()"><img src="/assets/images/admin/B/blacklist.png" alt=""></a>
                        <?php endif; if($atype == 'open'): ?>
                        <a onclick="getaudio()"><i class="layui-icon" style="font-size: 22px;cursor: pointer;">&#xe688;</i></a>
                        <?php endif; ?>
                        <a href="javascript:getblack()"><img src="/assets/images/admin/B/blacklist.png" alt="移入黑名单" title="移入黑名单"></a>
                        <a onclick="getswitch()"><img src="/assets/images/admin/B/transfer.png" alt="客服转接" title="客服转接"></a>
                        <a onclick="gethistory()"><img src="/assets/images/admin/B/record.png" alt="历史记录" title="历史记录"></a>
                        <a onclick="toEvaluate()"><img src="/assets/images/admin/B/toEvaluate.png" alt="推送评价" title="推送评价"></a>
                        <a onclick="toTrans()"><img src="/assets/images/admin/B/fanyi.png" alt="翻译" title="翻译"></a>
                        <a onmouseover="info()" id="paste" style="position:absolute; right:134px;bottom:30px;width: 120px;font-size: 12px;"><img src="/assets/images/admin/B/screen.png" alt=""> 怎样发截图？</a>
                    </div>
                </div>
            </div>

            <!-- 浮层 -->
            <div id='fuceng' class="hide" style="background: #f7f7f7;height: 68px;position: absolute;bottom: 70px;right: 20px;z-index: 9999;border-radius: 8px;padding: 8px 0">
                <ul style="width: 100%;height: 60px;">
                    <li class="fuceng-li" onclick="choosetype(this)" name='1'><img id='type1' class="layui-icon selecte-icon" src="/assets/images/admin/B/selected.png" alt=""><span>按Enter键发送消息，Ctrl+Enter换行</span></li>
                    <li class="fuceng-li selected-li" onclick="choosetype(this)" name='2'><img id='type2' class="layui-icon selecte-icon" src="/assets/images/admin/B/selected.png" alt=""><span>按Ctrl+Enter键发送消息，Enter换行</span></li>
                </ul>
            </div>
        </section>
        <section class="chatinfo">

            <div class="layui-tab layui-tab-brief" lay-filter="docDemoTabBrief" style="margin: 0px;height: 100%;background-color: #fff;position: relative;">
                <ul class="layui-tab-title" style="height: 50px;border-bottom: 0;background-color: #f7f7f7">
                    <li class="layui-this" style="width: 33%;height: 50px;line-height: 50px;color: #555555">访客信息<span class="line"></span></li>
                    <li style="width: 33%;height: 50px;line-height: 50px;color: #555555">黑名单<span class="line"></span></li>
                    <li style="width: 34%;height: 50px;line-height: 50px;color: #555555">快捷回复<span class="line"></span></li>
                </ul>

                <div class="layui-tab-content" style="padding: 16px;height: 100%;overflow-y: auto;padding-bottom: 20vh">
                    <div class="layui-tab-item layui-show">
                        <div class="" style="color: #555555;">

                            <div style="font-size: 14px;border-left: 5px solid #1E9FFF;height: 40px;line-height: 40px;background: #f2f2f2;    padding-left: 10px;">访问信息</div>

                            <div style="margin-top: 12px;">
                                来源：<span class="record"></span>
                            </div>
                            <div style="margin-top: 14px;">
                                地区：<span class="ipdizhi" style="font-size: 10px;"></span> 【<span class="iparea" style="font-size: 10px;"></span>】
                            </div>

                            <div style="margin-top: 14px;">
                                语言：<span class="lang-name"></span><span id="lang" style="display: none"></span>
                            </div>

                            <div style="margin-top: 14px;">
                                状态：<span id="v_state" style="font-size: 10px;"></span>
                            </div>

                            <div style="margin-top: 14px;">
                                上次登录时间：<span
                                    id="last_login_time" style="font-size: 10px;"></span>
                            </div>

                            <div style="margin-top: 14px;">
                                登录次数：<span
                                    id="login_times" style="font-size: 10px;"></span>
                            </div>
                            <div style="margin-top: 14px;">
                                登录设备：<span
                                    id="login_device" style="font-size: 10px;"></span>
                            </div>

                            <div style="font-size: 14px;border-left: 5px solid #1E9FFF;height: 40px;line-height: 40px;background: #f2f2f2;    padding-left: 10px;margin-top: 14px;">备注信息</div>

                            <div style="margin-top: 14px;">
                                姓名：<input type="text" id="name" placeholder="姓名填写后自动保存" class="layui-input" onblur="saveinfo()" />
                            </div>

                            <div style="margin-top: 14px;">
                                电话：<input type="text" id="tel" placeholder="电话填写后自动保存" class="layui-input" onblur="saveinfo()" />
                            </div>
                            <div style="margin-top: 14px;">
                                备注：<textarea  id="comment" placeholder="备注信息，填写后自动保存" class="layui-input"  onblur="saveinfo()" style="height: 50px;"></textarea>
                            </div>





                        </div>
                    </div>

                    <div class="layui-tab-item" id='black_list' style="width: 100%;overflow-y: auto;padding: 0px;">
                    </div>

                    <div class="layui-tab-item" id='word_list' style="width: 100%;height: 100%; overflow-y: auto;">
                        <div id='quit_reply' >


                        </div>


                    </div>

                </div>
            </div>

        </section>

    </div>

</div>
<script type="text/javascript">
    function toEvaluate() {
        var data = $.cookie("cu_com");
        var jsondata = $.parseJSON(data);
        $.ajax({
            url: '/admin/set/pushComment',
            type:'post',
            data:{visiter_id:jsondata.visiter_id},
            success:function(res){
                if(res.code == 0){
                    var str = '';
                    str += "<div class='push-evaluation'>已推送评价</div>";
                    $(".conversation").append(str);
                    var div = document.getElementById("wrap");
                    div.scrollTop = div.scrollHeight;
                } else {
                    layer.msg(res.msg, {icon: 2});
                }
            }
        });
    }

    function toTrans() {
        var text = $('#text_in').val();
        var to = $('#lang').text();
        if(text == ''){
            layer.msg("请输入内容", {icon: 2});
        }else{
            $.ajax({
                url: '/service/index/trans',
                type:'post',
                data:{text:text,to:to},
                success:function(res){
                    if(res.code ===1){
                        $('#text_in').val(res.data);
                    }else{
                        layer.msg(res.msg, {icon: 2});
                    }
                }
            });
        }
    }

    function saveinfo(){
        var data = $.cookie("cu_com");
        var jsondata = $.parseJSON(data);
        var name=$("#name").val();
        var tel=$('#tel').val();
        var comment=$("#comment").val();
        $.ajax({
            url:'/admin/manager/saveVisiter',
            type:'post',
            data:{name:name,tel:tel,comment:comment,visiter_id:jsondata.visiter_id},
            success:function(res){
                if(res.code == 0){
                    getchat();
                }
            }
        });

    }


    function show(){
        let text = $('.manager-reply').text();
        if(text == '管理快捷回复') {
            $('.manager-reply').text('退出管理')
        }else {
            $('.manager-reply').text('管理快捷回复')
        }
        $('.del-reply').toggle();
    }

    function clearList() {
        layer.open({
            type: 1,
            area: ['360px', '180'],
            title:'',
            content: '<div style="text-align:center;margin: 50px 0 30px;font-size:14px;">确认清空当前会话列表？</div>',
            btn: ['确定', '取消'],
            yes:function(res){
                $.ajax({
                    url:"/admin/set/clear",
                    type: "post",
                    data: {
                        id: "<?php echo $arr['service_id']; ?>"
                    },
                    success: function (res) {
                        if (res.code ==0) {
                            layer.msg(res.msg,{icon:2,offset:'20px'});
                            layer.closeAll();
                            $('.clear-btn').hide();
                            location.reload();
                        }
                    }
                });
            }
        });
    }


    function addreply(){
        $('.del-reply').hide();
        $('.manager-reply').text('管理快捷回复');
        var html='<form class="layui-form reply-form" style="margin-top:20px;">';
        html+='<div class="layui-form-item"><label class="layui-form-label" for="tag">标签</label>';
        html+='<div class="layui-input-block"><input id="tag" type="text" class="layui-input" style="width:552px" /></div></div>';
        html+='<div class="layui-form-item layui-form-text"><label class="layui-form-label" for="word">快捷用语</label>';
        html+='<div class="layui-input-block"><textarea id="word" name="content" class="layui-textarea" style="height:160px;width:552px"></textarea></div></div>'
        html+='</form>';

        layer.open({
            type:1,
            title:'添加快捷回复',
            area: ['600px', '415px'],
            content: html,
            btn: ['保存', '取消'],
            yes:function(res){
                $.ajax({
                    url:"/admin/manager/addword",
                    type: "post",
                    data: {word: $("#word").val(),tag:$("#tag").val()},
                    success: function (res) {
                        if (res.code ==0) {
                            layer.msg(res.msg, {icon: 1,time:2000,end:function () {
                                    var tag=$("#tag").val();

                                    var str ='<div style="position:relative" id="reply'+res.data.id+'">';
                                    str+='<a class="del-reply" style="display:none;" href="javascript:close('+res.data.id+')"><img src="'+'/assets/images/admin/B/delete.png" /></a>';
                                    str+='<a class="reply-text" href="javascript:showon('+"'"+res.data.word.replace("'", "\\'")+"'"+')">'+tag+'</a>';
                                    str+='<span class="reply-border"></span><span class="reply-about">'+res.data.word+'</span></div>';
                                    $("#quit_reply").prepend(str);
                                    layer.closeAll();
                                }});
                        }
                    }
                });
            }

        });
    }

    function close(id){
        $.ajax({
            url:'/admin/manager/delreply',
            type:'post',
            data:{id:id},
            success:function(res){
                if(res.code ==0){
                    layer.msg(res.msg,{icon:1,end:function(){

                            $("#reply"+id).remove();
                        }});
                }
            }
        })
    }


    function showon(str){

        $("#text_in").val(str);
        $("#text_in").focus();
    }



    function getOs() {
        var OsObject = "";

        if (isFirefox = navigator.userAgent.indexOf("Firefox") > 0) {
            return "Firefox";
        }
    }

    function showDiv(){

        $("#fuceng").toggleClass('hide');
    }


    $(function (){

        $("#showinfo").on('click',function(){

            showDiv();

            $(document).one("click", function () {

                $("#fuceng").addClass('hide');

            });
            event.stopPropagation();//阻止事件向上冒泡
        });

        $("#fuceng").click(function (event)
        {
            event.stopPropagation();//阻止事件向上冒泡

        });
    });





    function choosetype(obj){
        $(obj).addClass('selected-li');
        $(obj).siblings().removeClass('selected-li')
        var type =$(obj).attr('name');
        $.cookie('type',type);
        $("#fuceng").addClass('hide');

        types();
    }


    //获取qq截图的图片
    (function () {
        var imgReader = function (item) {
            var blob = item.getAsFile(),
                reader = new FileReader();
            // 读取文件后将其显示在网页中
            reader.onload = function (e) {
                var msg = '';
                msg += "<img   src='" + e.target.result + "'>";


                var sdata = $.cookie('cu_com');
                if (sdata) {
                    var json = $.parseJSON(sdata);
                    var img = json.avater;
                }

                var sid = $('#channel').text();
                var se = $("#chatmsg_submit").attr('name');
                var customer = $("#customer").text();
                var pic = $("#se_avatar").attr('src');
                var time;

                if($.cookie("time") == ""){
                    var myDate = new Date();
                    let hours = myDate.getHours();
                    let minutes = myDate.getMinutes();
                    if(hours < 10 ) {
                        minutes = '0'+minutes.toString();
                    }
                    if(minutes < 10 ) {
                        minutes = '0'+minutes.toString();
                    }
                    time = hours+":"+minutes;
                    var timestamp = Date.parse(new Date());
                    $.cookie("time",timestamp/1000);

                }else{

                    var timestamp = Date.parse(new Date());

                    var lasttime =$.cookie("time");
                    if((timestamp/1000 - lasttime) >30){
                        var myDate =new Date(timestamp);
                        let hours = myDate.getHours();
                        let minutes = myDate.getMinutes();
                        if(hours < 10 ) {
                            minutes = '0'+minutes.toString();
                        }
                        if(minutes < 10 ) {
                            minutes = '0'+minutes.toString();
                        }
                        time = hours+":"+minutes;
                    }else{
                        time ="";
                    }

                    $.cookie("time",timestamp/1000);

                }
                var str = '';
                str += '<li class="chatmsg""><div class="showtime">' + time + '</div>';
                str += '<div style="position: absolute;top: 26px;right: 2px;"><img  class="my-circle se_pic" src="' + pic + '" width="50px" height="50px"></div>';
                str += "<div class='outer-right'><div class='service' style='padding:0;border-radius:0;max-height:100px'>";
                str += "<pre>" + msg + "</pre>";
                str += "</div></div>";
                str += "</li>";

                $(".conversation").append(str);
                $("#text_in").empty();

                var div = document.getElementById("wrap");
                div.scrollTop = div.scrollHeight;
                setTimeout(function(){
                    $('.chatmsg').css({
                        height: 'auto'
                    });
                },0)
                $.ajax({
                    url:"/admin/set/chats",
                    type: "post",
                    data: {visiter_id:sid,content: msg, avatar: img}
                });


            };
            // 读取文件
            reader.readAsDataURL(blob);
        };
        document.getElementById('text_in').addEventListener('paste', function (e) {
            // 添加到事件对象中的访问系统剪贴板的接口
            var clipboardData = e.clipboardData,
                i = 0,
                items, item, types;

            if (clipboardData) {
                items = clipboardData.items;
                if (!items) {
                    return;
                }
                item = items[0];
                // 保存在剪贴板中的数据类型
                types = clipboardData.types || [];
                for (; i < types.length; i++) {
                    if (types[i] === 'Files') {
                        item = items[i];
                        break;
                    }
                }
                // 判断是否为图片数据
                if (item && item.kind === 'file' && item.type.match(/^image\//i)) {
                    imgReader(item);
                }
            }
        });
    })();


    // 视频通话
    var getvideo =function(){

        var sid = $('#channel').text();
        var pic = $("#se_avatar").attr('src');

        var times = (new Date()).valueOf();
        var se = $("#se").text();
        //申请
        $.ajax({
            url:'/admin/set/apply',
            type: 'post',
            data: {id: sid,channel: times,avatar:pic,name:se},
            success:function(res){
                if(res.code !=0){
                    layer.msg(res.msg,{icon:2,offset:'20px'});
                }else{

                    var str='';
                    str+='<div class="videos">';
                    str+='<video id="localVideo" autoplay></video>';
                    str+='<video id="remoteVideo" autoplay class="hidden"></video></div>';


                    layer.open({
                        type:1
                        ,title: '视频'
                        ,shade:0
                        ,closeBtn:1
                        ,area: ['440px', '378px']
                        ,content:str
                        ,end:function(){


                            mediaStreamTrack.getTracks().forEach(function (track) {
                                track.stop();
                            });

                        }
                    });


                    try{
                        connenctVide(times);
                    }catch(e){
                        console.log(e);
                        return;
                    }

                }
            }

        });


    }




    //
    var gethistory=function(){

        var sdata = $.cookie("cu_com");
        var jsondata = $.parseJSON(sdata);
        var vid =jsondata.visiter_id;
        layer.open({
            type: 2,
            title: '该用户所有历史消息',
            area: ['600px', '500px'],
            content: '/admin/index/history?visiter_id='+vid
        });

    }

    var getaudio =function(){

        //音频先加载
        var audio_context;
        var recorder;
        var wavBlob;
        //创建音频
        try {
            // webkit shim
            window.AudioContext = window.AudioContext || window.webkitAudioContext;
            navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia || navigator.mediaDevices.getUserMedia;
            window.URL = window.URL || window.webkitURL;

            audio_context = new AudioContext;

            if (!navigator.getUserMedia) {
                console.log('语音创建失败');
            }
            ;
        } catch (e) {
            console.log(e);
            return;
        }
        navigator.getUserMedia({audio: true}, function (stream) {
            var input = audio_context.createMediaStreamSource(stream);
            recorder = new Recorder(input);

            var falg = window.location.protocol;
            if (falg == 'https:') {
                recorder && recorder.record();

                //示范一个公告层
                layui.use(['jquery', 'layer'], function () {
                    var layer = layui.layer;

                    layer.msg('录音中...', {
                        icon: 16
                        , shade: 0.01
                        , skin: 'layui-layer-lan'
                        , time: 0 //20s后自动关闭
                        , btn: ['发送', '取消']
                        , yes: function (index, layero) {
                            //按钮【按钮一】的回调
                            recorder && recorder.stop();
                            recorder && recorder.exportWAV(function (blob) {
                                wavBlob = blob;
                                var fd = new FormData();
                                var wavName = encodeURIComponent('audio_recording_' + new Date().getTime() + '.wav');
                                fd.append('wavName', wavName);
                                fd.append('file', wavBlob);

                                var xhr = new XMLHttpRequest();
                                xhr.onreadystatechange = function () {
                                    if (xhr.readyState == 4 && xhr.status == 200) {
                                        jsonObject = JSON.parse(xhr.responseText);

                                        voicemessage = '<div style="cursor:pointer;text-align:center;" onclick="getstate(this)" data="play"><audio src="'+jsonObject.data.src+'"></audio><i class="layui-icon" style="font-size:25px;">&#xe652;</i><p>音频消息</p></div>';

                                        var sid = $('#channel').text();
                                        var pic = $("#se_avatar").attr('src');
                                        var time;

                                        var sdata = $.cookie('cu_com');

                                        if (sdata) {
                                            var json = $.parseJSON(sdata);
                                            var img = json.avater;

                                        }

                                        if($.cookie("time") == ""){
                                            var myDate = new Date();
                                            let hours = myDate.getHours();
                                            let minutes = myDate.getMinutes();
                                            if(hours < 10 ) {
                                                minutes = '0'+minutes.toString();
                                            }
                                            if(minutes < 10 ) {
                                                minutes = '0'+minutes.toString();
                                            }
                                            time = hours+":"+minutes;
                                            var timestamp = Date.parse(new Date());
                                            $.cookie("time",timestamp/1000);

                                        }else{

                                            var timestamp = Date.parse(new Date());

                                            var lasttime =$.cookie("time");
                                            if((timestamp/1000 - lasttime) >30){
                                                var myDate =new Date(timestamp*1000);
                                                let hours = myDate.getHours();
                                                let minutes = myDate.getMinutes();
                                                if(hours < 10 ) {
                                                    minutes = '0'+minutes.toString();
                                                }
                                                if(minutes < 10 ) {
                                                    minutes = '0'+minutes.toString();
                                                }
                                                time = hours+":"+minutes;
                                            }else{
                                                time ="";
                                            }

                                            $.cookie("time",timestamp/1000);
                                        }
                                        var str = '';
                                        str += '<li class="chatmsg"><div class="showtime">' + time + '</div>';
                                        str += '<div style="position: absolute;top: 26px;right: 2px;"><img  class="my-circle se_pic" src="' + pic + '" width="50px" height="50px"></div>';
                                        str += "<div class='outer-right'><div class='service'>";
                                        str += "<pre>" +  voicemessage + "</pre>";
                                        str += "</div></div>";
                                        str += "</li>";

                                        $(".conversation").append(str);
                                        $("#text_in").empty();

                                        var div = document.getElementById("wrap");
                                        div.scrollTop = div.scrollHeight;
                                        $(".chatmsg").css({
                                            height: 'auto'
                                        });
                                        $.ajax({
                                            url:"/admin/set/chats",
                                            type: "post",
                                            data: {visiter_id:sid,content:  voicemessage, avatar: img}
                                        });
                                    }
                                };
                                xhr.open('POST', '/admin/event/uploadVoice');
                                xhr.send(fd);
                            });
                            recorder.clear();
                            layer.close(index);
                        }
                        , btn2: function (index, layero) {
                            //按钮【按钮二】的回调
                            recorder && recorder.stop();
                            recorder.clear();
                            audio_context.close();
                            layer.close(index);
                        }
                    });

                });
            } else {

                layer.msg('音频输入只支持https协议！');

            }


        }, function (e) {
            layer.msg(e);
        });


    }

    var getstate =function(obj){

        var c=obj.children[0];

        var state=$(obj).attr('data');

        if(state == 'play'){
            c.play();
            $(obj).attr('data','pause');
            $(obj).find('i').html("&#xe651;");

        }else if(state == 'pause'){
            c.pause();
            $(obj).attr('data','play');
            $(obj).find('i').html("&#xe652;");
        }

        c.addEventListener('ended', function () {
            $(obj).attr('data','play');
            $(obj).find('i').html("&#xe652;");

        }, false);
    }

    var getswitch =function(){

        var sdata = $.cookie("cu_com");
        var jsondata = $.parseJSON(sdata);
        var sid = jsondata.visiter_id;

        var se = $("#se").text();

        layer.open({
            type: 2,
            title: '转接客服列表',
            area: ['400px', '420px'],
            shade: false,
            content: '/service/index/service?visiter_id='+sid+'&name='+se
        });
    }

    function chat2top(id,that){
        that=$(that);
        var istop=that.data('istop');
        $.ajax({
            url:'/admin/visiter/chat2top/visiter_id/'+id+'/istop/'+istop,
            dataType:"json",   //返回格式为json
            async:true,//请求是否异步，默认为异步，这也是ajax重要特性
            type:"POST",   //请求方式
            beforeSend:function(){
                //请求前的处理
                index = layer.load(2, {shade: false});
            },
            success:function(res){
                if(res.code){
                    layer.msg(res.msg,{icon:1});
                    that.data('istop',istop?0:1);
                    that.text(istop?'取消置顶':'置顶对话');
                    setclass=istop?that.addClass('layui-btn-normal').removeClass('layui-btn-danger'):that.addClass('layui-btn-danger').removeClass('layui-btn-normal');
                    window.location.reload(true);
                }else{
                    layer.msg(res.msg,{icon:2});
                }
                //请求成功时处理
            },
            complete:function(){
                //请求完成的处理
                layer.close(index);
            },
            error:function(){
                //请求出错处理
            }
        });
    }

</script>
<script type="text/javascript" src="/assets/js/admin/chat.js?v=1.5"></script>
