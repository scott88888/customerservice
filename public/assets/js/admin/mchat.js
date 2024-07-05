

function a() {
    var e = document.getElementById("chat-message-audio-source").src
        , b = document.getElementById("chat-message-audio");
    b.src = "";
    var p = b.play();
    p && p.then(function(){}).catch(function(e){});
    b.src = e;
    $(document).unbind("click", a);
}
$(document).on("click", a);
var group = new Vue({
    el: '#group',
    data() {
        let that = this;
        return {
            list: [],
            user: [],
            vid: null,
            openGroup: false,
        };
    },

    methods: {
        // 请求用户数据
        getList(page) {
            let that = this;
            $.ajax({
                url: ROOT_URL + '/admin/custom/group',
                type: 'get',
                data: {
                    page: page
                },
                success: function(res) {
                    if (res.code == 0) {
                        that.list = that.list.concat(res.data.data);
                        if (res.data.data.length == 10) {
                            that.getList(page + 1);
                        }
                        for (let i = 0; i < that.user.length; i++) {
                            for (let y = 0; y < that.list.length; y++) {
                                if (that.list[y].group_name == that.user[i]) {
                                    that.list[y].choose = true;
                                }
                            }
                        }
                    }
                }
            });
        },
        getUser() {
            let that = this;
            $.ajax({
                url: ROOT_URL + '/admin/custom/search',
                type: 'get',
                data: {
                    group_id: 0,
                    page: 1,
                    nickname: nickname
                },
                success: function(res) {
                    if (res.code == 0) {
                        that.user = res.data.data[0].group_name_array;
                        that.vid = [res.data.data[0].vid];
                        that.getList(1);
                    }
                }
            });
        },
        edit() {
            let that = this;
            let group_id = [];
            for (let i = 0; i < that.list.length; i++) {
                if (that.list[i].choose) {
                    group_id.push(that.list[i].id)
                }
            }
            $.ajax({
                url: ROOT_URL + '/admin/custom/visitergroup',
                type: 'post',
                data: {
                    group_id: group_id,
                    vid: that.vid
                },
                success: function(res) {
                    if (res.code == 0) {
                        that.openGroup = false
                    }
                }
            });
        }
    }
})
$(function() {
    // let height = +document.documentElement.clientHeight;
    // window.scrollTop(height);
    // $('.content').css({
    //     height: height - 144
    // });
    group.getUser();
})
// 推送评价
var toEvaluate = function() {
    $.ajax({
        url:ROOT_URL + '/admin/set/pushComment',
        type:'post',
        data:{visiter_id:visiter_id},
        success:function(res){
            if(res.code == 0){
                var str = '';
                str += "<div class='push-evaluation'>已推送评价</div>"
                $(".conversation").append(str);
                var div = document.getElementById("wrap");
                div.scrollTop = div.scrollHeight;
            } else {
                layer.msg(res.msg, {icon: 2});
            }
        }
    });
};

function toTrans() {
    var text = $('#text_all').val();
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
                    $('#text_all').val(res.data);
                }else{
                    layer.msg(res.msg, {icon: 2});
                }
            }
        });
    }
}

var openGroup = function() {
    group.openGroup = true;
};

$(document).on('touchend', '.content', function() {
    $("#text_all").blur();
    $('.tool_box').css({
        display: 'none'
    });
});



var getaudio = function() {

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
        };
    } catch (e) {
        console.log(e);
        return;
    }
    navigator.getUserMedia({ audio: true }, function(stream) {
        var input = audio_context.createMediaStreamSource(stream);
        recorder = new Recorder(input);

        var falg = window.location.protocol;
        if (falg == 'https:') {
            recorder && recorder.record();

            //示范一个公告层
            layui.use(['jquery', 'layer'], function() {
                var layer = layui.layer;

                layer.msg('录音中...', {
                    icon: 16,
                    shade: 0.01,
                    skin: 'layui-layer-lan',
                    time: 0 //20s后自动关闭
                    ,
                    btn: ['发送', '取消'],
                    yes: function(index, layero) {
                        //按钮【按钮一】的回调
                        recorder && recorder.stop();
                        recorder && recorder.exportWAV(function(blob) {
                            wavBlob = blob;
                            var fd = new FormData();
                            var wavName = encodeURIComponent('audio_recording_' + new Date().getTime() + '.wav');
                            fd.append('wavName', wavName);
                            fd.append('file', wavBlob);

                            var xhr = new XMLHttpRequest();
                            xhr.onreadystatechange = function() {
                                if (xhr.readyState == 4 && xhr.status == 200) {
                                    jsonObject = JSON.parse(xhr.responseText);

                                    voicemessage = '<div style="cursor:pointer;text-align:center;" onclick="getstate(this)" data="play"><audio src="' + jsonObject.data.src + '"></audio><i class="layui-icon" style="font-size:25px;">&#xe652;</i><p>音频消息</p></div>';

                                    var sid = $('#channel').text();
                                    var pic = $("#se_avatar").attr('src');
                                    var time;

                                    var sdata = $.cookie('cu_com');

                                    if (sdata) {
                                        var json = $.parseJSON(sdata);
                                        var img = json.avater;

                                    }

                                    if ($.cookie("time") == "") {
                                        var myDate = new Date();
                                        time = myDate.getHours() + ":" + myDate.getMinutes();
                                        var timestamp = Date.parse(new Date());
                                        $.cookie("time", timestamp / 1000);

                                    } else {

                                        var timestamp = Date.parse(new Date());

                                        var lasttime = $.cookie("time");
                                        if ((timestamp / 1000 - lasttime) > 30) {
                                            var myDate = new Date(timestamp * 1000);
                                            time = myDate.getHours() + ":" + myDate.getMinutes();
                                        } else {
                                            time = "";
                                        }

                                        $.cookie("time", timestamp / 1000);
                                    }
                                    var str = '';
                                    str += '<li class="chatmsg"><div class="showtime">' + time + '</div>';
                                    str += '<div style="position: absolute;top: 26px;right: 2px;"><img  class="my-circle se_pic" src="' + pic + '" width="50px" height="50px"></div>';
                                    str += "<div class='outer-right'><div class='service'>";
                                    str += "<pre>" + voicemessage + "</pre>";
                                    str += "</div></div>";
                                    str += "</li>";

                                    $(".conversation").append(str);
                                    $("#text_all").empty();

                                    var div = document.getElementById("wrap");
                                    div.scrollTop = div.scrollHeight;

                                    $("img[src*='upload/images']").parent().parent('.customer').css({
                                        padding: '0',borderRadius: '0',maxHeight:'100px'
                                    });
                                    $("img[src*='upload/images']").parent().parent('.service').css({
                                        padding: '0',borderRadius: '0',maxHeight:'100px'
                                    });
                                    setTimeout(function(){
                                        $('.chatmsg').css({
                                            height: 'auto'
                                        });
                                    },0)
                                    $.ajax({
                                        url: ROOT_URL + "/admin/set/chats",
                                        type: "post",
                                        data: { visiter_id: visiter_id, content: voicemessage, avatar: img }
                                    });
                                }
                            };
                            xhr.open('POST', '/admin/event/uploadVoice');
                            xhr.send(fd);
                        });
                        recorder.clear();
                        layer.close(index);
                    },
                    btn2: function(index, layero) {
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

    }, function(e) {
        layer.msg('音频输入只支持https协议！');
    });
}

var getstate = function(obj) {

    var c = obj.children[0];

    var state = $(obj).attr('data');

    if (state == 'play') {
        c.play();
        $(obj).attr('data', 'pause');
        $(obj).find('i').html("&#xe651;");

    } else if (state == 'pause') {
        c.pause();
        $(obj).attr('data', 'play');
        $(obj).find('i').html("&#xe652;");
    }

    c.addEventListener('ended', function() {
        $(obj).attr('data', 'play');
        $(obj).find('i').html("&#xe652;");

    }, false);
}

var back = function() {
    history.go(-1);
}

var init = function() {
    // 获取历史消息
    $.cookie("hid", '');
    getwatch(visiter_id);
    wolive_connect();
}
function revoke(id,type) {
    $.ajax({
        url:ROOT_URL+"/admin/set/revokemsg",
        type:"post",
        data:{id:id,type:type},
        dataType:'json',
        success:function (res) {
            if(res.code == 0){
                layer.msg(res.msg,{icon:1,end:function(){
                        $("#xiaox_"+id).remove();
                    }});
            }else{
                layer.msg(res.msg,{icon:2});
            }
        }
    });
}


function getdata() {

    var showtime = "";
    var curentdata = new Date();
    var time = curentdata.toLocaleDateString();

    if ($.cookie("hid") != "") {
        var cid = $.cookie("hid");
    } else {
        var cid = "";

    }
    $.ajax({
        url: ROOT_URL + "/weixin/chat/chatdata",
        type: "post",
        data: { visiter_id: visiter_id, hid: cid },
        success: function(res) {
            //添加 最近的 聊天 记录
            if (res.code == 0) {
                var str = '';
                if (!res.data.length) {
                    return;
                }
                $.each(res.data, function(k, v) {
                    if (getdata.puttime) {

                        if ((v.timestamp - getdata.puttime) > 60) {
                            var myDate = new Date(v.timestamp * 1000);
                            var puttime = myDate.toLocaleDateString();
                            let year = myDate.getFullYear();
                            let month = myDate.getMonth() + 1;
                            let date = myDate.getDate();
                            let hours = myDate.getHours();
                            let minutes = myDate.getMinutes();
                            if (hours < 10) {
                                minutes = minutes.toString();
                            }
                            if (minutes < 10) {
                                minutes = '0' + minutes.toString();
                            }

                            if (puttime == time) {
                                showtime = hours + ":" + minutes;
                            } else {
                                showtime = year + "-" + month + "-" + date + " " + hours + ":" + minutes;
                            }

                        } else {
                            showtime = "";
                        }

                    } else {

                        var myDate = new Date(v.timestamp * 1000);
                        var puttime = myDate.toLocaleDateString();
                        let year = myDate.getFullYear();
                        let month = myDate.getMonth() + 1;
                        let date = myDate.getDate();
                        let hours = myDate.getHours();
                        let minutes = myDate.getMinutes();
                        if (hours < 10) {
                            minutes = minutes.toString();
                        }
                        if (minutes < 10) {
                            minutes = '0' + minutes.toString();
                        }

                        if (puttime == time) {
                            showtime = hours + ":" + minutes;
                        } else {
                            showtime = year + "-" + month + "-" + date + " " + hours + ":" + minutes;
                        }

                    }

                    getdata.puttime = v.timestamp;

                    if(v.content.indexOf('target="_blank') > -1) {
                        v.content = v.content.replace(/alt="">/g,'alt=""></a>')
                    }
                    if (v.direction == 'to_service') {


                        str += '<li class="chatmsg"><div class="showtime">' + showtime + '</div><div class="" style="position: absolute;left:12px;">';
                        str += '<img class="my-circle  se_pic" src="' + v.avatar + '" width="40px" height="40px"></div>';
                        str += "<div class='outer-left'><div class='customer'>";

                        if(v.content.indexOf('<img')>= 0||v.content.indexOf('<a')>= 0||!isNaN(v.content)||v.content.indexOf('<video')>= 0){
                            str += "<pre>" + v.content + "</pre>";
                        }else{
                            if(v.content_trans !=''){
                                str += "<pre>" + v.content + "<p class='trans-data'>译文："+v.content_trans+"</p></pre>";
                            }else{
                                str += "<pre>" + v.content + "<span class='trans' data-cid='"+v.cid+"'>翻 译</span></pre>";
                            }
                        }
                        str += "</div></div>";
                        str += "</li>";

                    } else {

                        str += '<li class="chatmsg" id="xiaox_'+v.cid+'"><div class="showtime">' + showtime + '</div>';
                        str += '<div class="" style="position: absolute;top: 26px;right: 5px;"><img  class="my-circle cu_pic" src="' + v.avatar + '" width="40px" height="40px"></div>';
                        str += "<div class='outer-right'><div class='service'>";
                        str += "<pre>" + v.content + "&nbsp;&nbsp;<span onclick='revoke("+v.cid+",1);' class='revoke-text'>(撤销)</span></pre>";
                        str += "</div></div>";
                        str += "</li>";


                    }
                });
                var div = document.getElementById("wrap");
                if ($.cookie("hid") == "") {
                    $(".conversation").append(str);
                    if (div) {
                        $("img").load(function(){
                            div.scrollTop = div.scrollHeight;
                        });
                    }
                } else {

                    $(".conversation").prepend(str);
                    if (res.length <= 2) {
                        $("#top_div").remove();
                        $(".conversation").prepend("<div id='top_div' class='showtime'>已没有数据</div>");
                        if (div) {
                            div.scrollTop = 0;
                        }
                    } else {
                        if (div) {
                            div.scrollTop = div.scrollHeight / 4.2;
                        }
                    }
                }

                $("img[src*='upload/images']").parent().parent('.customer').css({
                    padding: '0',borderRadius: '0',maxHeight:'100px'
                });
                $("img[src*='upload/images']").parent().parent('.service').css({
                    padding: '0',borderRadius: '0',maxHeight:'100px'
                });
                setTimeout(function(){
                    $('.chatmsg').css({
                        height: 'auto'
                    });
                },0)
                if (res.data.length > 2) {


                    $.cookie("hid", res.data[0]['cid']);
                    $(".chatmsg_notice").remove();
                }
            }
        }
    });
}

window.onload = init();


var e={'emoji1f600':'1f600','emoji1f601':'1f601','emoji1f602':'1f602','emoji1f603':'1f603','emoji1f604':'1f604','emoji1f605':'1f605','emoji1f606':'1f606','emoji1f607':'1f607','emoji1f608':'1f608','emoji1f609':'1f609','emoji1f610':'1f610','emoji1f611':'1f611','emoji1f612':'1f612','emoji1f613':'1f613','emoji1f614':'1f614','emoji1f615':'1f615','emoji1f616':'1f616','emoji1f617':'1f617','emoji1f618':'1f618','emoji1f619':'1f619','emoji1f620':'1f620','emoji1f621':'1f621','emoji1f622':'1f622','emoji1f623':'1f623','emoji1f624':'1f624','emoji1f625':'1f625','emoji1f626':'1f626','emoji1f627':'1f627','emoji1f628':'1f628','emoji1f629':'1f629','emoji1f630':'1f630','emoji1f631':'1f631','emoji1f632':'1f632','emoji1f633':'1f633','emoji1f634':'1f634','emoji1f635':'1f635','emoji1f636':'1f636','emoji1f637':'1f637','emoji1f638':'1f638','emoji1f639':'1f639','emoji1f640':'1f640','emoji1f641':'1f641','emoji1f642':'1f642','emoji1f643':'1f643','emoji1f644':'1f644','emoji1f910':'1f910','emoji1f911':'1f911','emoji1f912':'1f912','emoji1f913':'1f913','emoji1f914':'1f914','emoji1f915':'1f915','emoji1f916':'1f916','emoji1f917':'1f917','emoji1f923':'1f923','emoji1f924':'1f924','emoji1f925':'1f925','emoji1f926':'1f926','emoji1f927':'1f927','emoji1f928':'1f928','emoji1f929':'1f929'};

var faceon = function() {
    $(".wl_faces_main").empty();
    var str = ""
    str += '<ul>';
    str +='<li><img title="emoji1f600" src='+ROOT_URL+'/upload/emoji/1f600.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f601" src='+ROOT_URL+'/upload/emoji/1f601.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f602" src='+ROOT_URL+'/upload/emoji/1f602.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f603" src='+ROOT_URL+'/upload/emoji/1f603.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f604" src='+ROOT_URL+'/upload/emoji/1f604.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f605" src='+ROOT_URL+'/upload/emoji/1f605.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f606" src='+ROOT_URL+'/upload/emoji/1f606.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f607" src='+ROOT_URL+'/upload/emoji/1f607.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f608" src='+ROOT_URL+'/upload/emoji/1f608.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f609" src='+ROOT_URL+'/upload/emoji/1f609.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f610" src='+ROOT_URL+'/upload/emoji/1f610.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f611" src='+ROOT_URL+'/upload/emoji/1f611.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f612" src='+ROOT_URL+'/upload/emoji/1f612.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f613" src='+ROOT_URL+'/upload/emoji/1f613.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f614" src='+ROOT_URL+'/upload/emoji/1f614.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f615" src='+ROOT_URL+'/upload/emoji/1f615.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f616" src='+ROOT_URL+'/upload/emoji/1f616.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f617" src='+ROOT_URL+'/upload/emoji/1f617.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f618" src='+ROOT_URL+'/upload/emoji/1f618.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f619" src='+ROOT_URL+'/upload/emoji/1f619.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f620" src='+ROOT_URL+'/upload/emoji/1f620.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f621" src='+ROOT_URL+'/upload/emoji/1f621.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f622" src='+ROOT_URL+'/upload/emoji/1f622.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f623" src='+ROOT_URL+'/upload/emoji/1f623.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f624" src='+ROOT_URL+'/upload/emoji/1f624.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f625" src='+ROOT_URL+'/upload/emoji/1f625.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f626" src='+ROOT_URL+'/upload/emoji/1f626.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f627" src='+ROOT_URL+'/upload/emoji/1f627.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f628" src='+ROOT_URL+'/upload/emoji/1f628.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f629" src='+ROOT_URL+'/upload/emoji/1f629.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f630" src='+ROOT_URL+'/upload/emoji/1f630.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f631" src='+ROOT_URL+'/upload/emoji/1f631.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f632" src='+ROOT_URL+'/upload/emoji/1f632.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f633" src='+ROOT_URL+'/upload/emoji/1f633.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f634" src='+ROOT_URL+'/upload/emoji/1f634.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f635" src='+ROOT_URL+'/upload/emoji/1f635.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f636" src='+ROOT_URL+'/upload/emoji/1f636.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f637" src='+ROOT_URL+'/upload/emoji/1f637.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f638" src='+ROOT_URL+'/upload/emoji/1f638.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f639" src='+ROOT_URL+'/upload/emoji/1f639.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f640" src='+ROOT_URL+'/upload/emoji/1f640.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f641" src='+ROOT_URL+'/upload/emoji/1f641.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f642" src='+ROOT_URL+'/upload/emoji/1f642.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f643" src='+ROOT_URL+'/upload/emoji/1f643.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f644" src='+ROOT_URL+'/upload/emoji/1f644.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f910" src='+ROOT_URL+'/upload/emoji/1f910.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f911" src='+ROOT_URL+'/upload/emoji/1f911.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f912" src='+ROOT_URL+'/upload/emoji/1f912.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f913" src='+ROOT_URL+'/upload/emoji/1f913.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f914" src='+ROOT_URL+'/upload/emoji/1f914.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f915" src='+ROOT_URL+'/upload/emoji/1f915.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f916" src='+ROOT_URL+'/upload/emoji/1f916.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f917" src='+ROOT_URL+'/upload/emoji/1f917.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f923" src='+ROOT_URL+'/upload/emoji/1f923.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f924" src='+ROOT_URL+'/upload/emoji/1f924.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f925" src='+ROOT_URL+'/upload/emoji/1f925.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f926" src='+ROOT_URL+'/upload/emoji/1f926.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f927" src='+ROOT_URL+'/upload/emoji/1f927.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f928" src='+ROOT_URL+'/upload/emoji/1f928.png onclick="emoj(this)"/></li>';
    str +='<li><img title="emoji1f929" src='+ROOT_URL+'/upload/emoji/1f929.png onclick="emoj(this)"/></li>';
    str += "</ul>";
    $(".wl_faces_main").append(str);
    $(".tool_box").toggle();
    var e = window.event || arguments.callee.caller.arguments[0];
    e.stopPropagation();
}

$('body').click(function() {
    $(".tool_box").hide();
});

var emoj = function(obj) {
    var a = $(obj).attr("title");
    var str = $("#text_all").val();
    var reg = new RegExp('<', "g")
    str = str.replace(reg, '&lt;');

    var reg2 = new RegExp('>', "g")

    str = str.replace(reg2, '&gt;');
    var b = "";
    b += str + " face[" + a + "]";
    $("#text_all").val(b);
    $(".tool_box").hide()

}

// 图片上传
function put() {

    var value = $('input[name="upload"]').val();
    var index1 = value.lastIndexOf(".");
    var index2 = value.length;
    var suffix = value.substring(index1 + 1, index2);
    var debugs = suffix.toLowerCase();

    if (debugs == "jpg" || debugs == "gif" || debugs == "png" || debugs == "jpeg") {

        $("#picture").ajaxSubmit({
            url: ROOT_URL + '/admin/set/upload',
            type: "post",
            dataType: 'json',
            success: function(res) {
                if (res.code == 0) {

                    var msg = '<img style="height:100px" src="' + res.data + '"  >';

                    var se = $("#chatmsg_submit").attr('name');
                    var customer = $("#customer").text();
                    var time;

                    if ($.cookie("time") == "") {
                        var myDate = new Date();
                        time = myDate.getHours() + ":" + myDate.getMinutes();
                        var timestamp = Date.parse(new Date());
                        $.cookie("time", timestamp / 1000);

                    } else {

                        var timestamp = Date.parse(new Date());

                        var lasttime = $.cookie("time");
                        if ((timestamp / 1000 - lasttime) > 30) {
                            var myDate = new Date(timestamp);
                            time = myDate.getHours() + ":" + myDate.getMinutes();
                        } else {
                            time = "";
                        }

                        $.cookie("time", timestamp / 1000);

                    }
                    var str = '';
                    str += '<li class="chatmsg"><div class="showtime">' + time + '</div>';
                    str += '<div style="position: absolute;top: 26px;right: 2px;"><img  class="my-circle se_pic" src="' + imghead + '" width="50px" height="50px"></div>';
                    str += "<div class='outer-right'><div class='service'>";
                    str += "<pre>" + msg + "</pre>";
                    str += "</div></div>";
                    str += "</li>";

                    $(".conversation").append(str);
                    var div = document.getElementById("wrap");
                    div.scrollTop = div.scrollHeight;
                    $("img[src*='upload/images']").parent().parent('.customer').css({
                        padding: '0',borderRadius: '0',maxHeight:'100px'
                    });
                    $("img[src*='upload/images']").parent().parent('.service').css({
                        padding: '0',borderRadius: '0',maxHeight:'100px'
                    });

                    setTimeout(function(){
                        $('.chatmsg').css({
                            height: 'auto'
                        });
                    },0)
                    $.ajax({
                        url: ROOT_URL + "/admin/set/chats",
                        type: "post",
                        data: { visiter_id: visiter_id, content: msg, avatar: img },
                        success: function(res) {
                            if (res.code != 0) {
                                layer.msg(res.msg, { icon: 2 });
                            }
                        }
                    });
                } else {
                    layer.msg(res.msg, { icon: 2 });
                }
            }
        });

    } else {

        layer.msg("请选择图片", { icon: 2 });
    }
}


// 文件上传
function putfile() {

    var value = $('input[name="folder"]').val();
    var sarr = value.split('\\');
    var name = sarr[sarr.length - 1];
    var arr = value.split(".");

    if (arr[1] == "js" || arr[1] == "css" || arr[1] == "html" || arr[1] == "php") {
        layer.msg("不支持该格式的文件", { icon: 2 });

    } else {

        var myDate = new Date();
        var time = myDate.getHours() + ":" + myDate.getMinutes();

        $("#file").ajaxSubmit({
            url: ROOT_URL + '/admin/set/uploadfile',
            type: 'post',
            datatype: 'json',
            success: function(res) {
                if (res.code == 0) {
                    var str = '';
                    str += '<li class="chatmsg"><div class="showtime">' + time + '</div>';
                    str += '<div class="" style="position: absolute;top: 26px;right: 2px;"><img  class="my-circle cu_pic" src="' + imghead + '" width="40px" height="40px"></div>';
                    str += "<div class='outer-right'><div class='service'>";
                    str += "<pre>";

                    if(res.data.indexOf('.mp4')>= 0){
                        str += "<video src='" + res.data + "' controls='controls' style='width: 100%'>ERROR</video>";
                    }else{
                        str += "<div style='height:90px'><a href='" + res.data + "' style='display: inline-block;text-align: center;min-width: 70px;text-decoration: none;' download='" + name + "'><i class='layui-icon' style='font-size: 60px;'>&#xe61e;</i><br>" + name + "</a></div>";
                    }

                    str += "</pre>";
                    str += "</div></div>";
                    str += "</li>";

                    $(".conversation").append(str);
                    var div = document.getElementById("wrap");
                    div.scrollTop = div.scrollHeight;
                    $("img[src*='upload/images']").parent().parent('.customer').css({
                        padding: '0',borderRadius: '0',maxHeight:'100px'
                    });
                    $("img[src*='upload/images']").parent().parent('.service').css({
                        padding: '0',borderRadius: '0',maxHeight:'100px'
                    });

                    setTimeout(function(){
                        $('.chatmsg').css({
                            height: 'auto'
                        });
                    },0)
                    var msg = "<div><a href='" + res.data + "' style='display: inline-block;text-align: center;min-width: 70px;text-decoration: none;' download='" + name + "'><i class='layui-icon' style='font-size: 60px;'>&#xe61e;</i><br>" + name + "</a></div>";

                    if(res.data.indexOf('.mp4')>= 0){
                        msg = "<video src='" + res.data + "' controls='controls' style='width: 100%'>ERROR</video>";
                    }

                    var sid = $('#channel').text();
                    var se = $("#chatmsg_submit").attr('name');
                    var customer = $("#customer").text();
                    $.ajax({
                        url: ROOT_URL + "/admin/set/chats",
                        type: "post",
                        data: { visiter_id: visiter_id, content: msg, avatar: img }
                    });
                } else {
                    layer.msg(res.msg, { icon: 2 });
                }

            }
        });

    }
}

function randomChar(l){
    var x="123456789poiuytrewqasdfghjklmnbvcxzQWERTYUIPLKJHGFDSAZXCVBNM";
    var tmp="";
    for(var i=0;i<l;i++){
        tmp += x.charAt(Math.ceil(Math.random()*10000000000)%x.length);
    }
    return tmp;
}
//发送消息
var send = function() {
    //获取 游客id
    var msg = $("#text_all").val();


    var reg = new RegExp('<', "g")
    msg = msg.replace(reg, '&lt;');

    var reg2 = new RegExp('>', "g")
    msg = msg.replace(reg2, '&gt;');

    if (msg.indexOf("face[") != -1) {

        msg = msg.replace(/face\[([^\s\[\]]+?)\]/g, function(i) {
            var a = i.replace(/^face/g, "");
            a = a.replace('[', '');
            a = a.replace(']', '');
            return '<img src="/upload/emoji/' + e[a] + '.png"/>'
        });

    }

    if (msg == "") {
        layer.msg('请输入信息');
    } else {

        var se = $("#chatmsg_submit").attr('name');
        var customer = $("#customer").text();
        var time;

        if ($.cookie("time") == "") {
            var myDate = new Date(timestamp);
            let year = myDate.getFullYear();
            let month = myDate.getMonth() + 1;
            let date = myDate.getDate();
            let hours = myDate.getHours();
            let minutes = myDate.getMinutes();
            if (hours < 10) {
                minutes = '0' + minutes.toString();
            }
            if (minutes < 10) {
                minutes = '0' + minutes.toString();
            }
            time = year + "-" + month + "-" + date + " " + hours + ":" + minutes;
            var timestamp = Date.parse(new Date());
            $.cookie("time", timestamp / 1000);

        } else {

            var timestamp = Date.parse(new Date());

            var lasttime = $.cookie("time");
            if ((timestamp / 1000 - lasttime) > 30) {
                var myDate = new Date(timestamp);
                let hours = myDate.getHours();
                let minutes = myDate.getMinutes();
                if (hours < 10) {
                    minutes = '0' + minutes.toString();
                }
                if (minutes < 10) {
                    minutes = '0' + minutes.toString();
                }
                time = hours + ":" + minutes;
            } else {
                time = "";
            }

            $.cookie("time", timestamp / 1000);

        }
        var unstr=(new Date()).valueOf()+randomChar(5)+visiter_id;
        var str = '';
        str += '<li class="chatmsg" id="xiaox_'+unstr+'"><div class="showtime">' + time + '</div>';
        str += '<div style="position: absolute;top: 26px;right: 2px;"><img  class="my-circle se_pic" src="' + imghead + '" width="40px" height="40px"></div>';
        str += "<div class='outer-right'><div class='service'>";
        str += "<pre>" + msg + "&nbsp;&nbsp;<span onclick=revoke('" + unstr + "',2); class='revoke-text'>(撤销)</span></pre>";
        str += "</div></div>";
        str += "</li>";

        $(".conversation").append(str);
        $("#text_all").val('');


        var div = document.getElementById("wrap");
        $("img").load(function(){
            div.scrollTop = div.scrollHeight;
        });
        $("img[src*='upload/images']").parent().parent('.customer').css({
            padding: '0',borderRadius: '0',maxHeight:'100px'
        });
        $("img[src*='upload/images']").parent().parent('.service').css({
            padding: '0',borderRadius: '0',maxHeight:'100px'
        });

        setTimeout(function(){
            $('.chatmsg').css({
                height: 'auto'
            });
        },0)
        $.ajax({
            url: ROOT_URL + "/admin/set/chats",
            type: "post",
            data: { visiter_id: visiter_id, content: msg, avatar: img,unstr:unstr }
        });
    }
};

$(document).on('click','.trans',function (){
    var cid = $(this).data('cid');
    var reg = new RegExp("翻 译","g");
    var txt = $(this).parent().text().replace(reg,"");
    var that = this;
    $.ajax({
        url: '/service/index/trans',
        type:'post',
        data:{text:txt,cid:cid},
        success:function(res){
            if(res.code ===1){
                $(that).parent().append(" <p class='trans-data'>译文："+res.data+"</p>");
            }else{
                layer.msg(res.msg, {icon: 2});
            }
        }
    });
});

document.getElementById("wrap").onscroll = function() {
    var t = document.getElementById("wrap").scrollTop;
    if (t == 0) {
        if ($.cookie("hid") != "") {
            console.log(t);
            getdata();
        }
    }
};



var text = document.getElementById('text_all');
// 获取焦点，拉到底部
text.onfocus = function() {
    $(".tool_box").hide();
    let height = +document.documentElement.clientHeight;
    setTimeout(function(){
        $('html ,body').animate({scrollTop: height}, 0);
    },200)
}
// 失去焦点，拉到顶部
text.onblur = function() {
    setTimeout(function() {
        $('html ,body').animate({ scrollTop: 0 }, 0);
    }, 0)
}
//图片放大预览
function getbig(obj) {
    var text = $(obj).attr('src');
    // alert(text);
    layer.open({
        type: 1,
        title: false,
        closeBtn: 0,
        area: '400px',
        skin: 'layui-layer-nobg', //没有背景色
        shadeClose: true,
        content: "<img src='" + text + "' width='100%' height='100%'>"
    });
}
function showBigImg(nWidth,nHeight,text){
    var maxwidth =window.innerWidth;
    var maxheight=window.innerHeight;
    var size;
    if((nHeight>maxheight-10) || (nWidth>maxwidth-10)){
        var widths,heights;
        widths=maxwidth-30;
        heights=widths*nHeight/nWidth;
        if(heights>maxheight){
            heights=maxheight-60;
            widths=heights*nWidth/nHeight;
        }
        size=[widths+'px',heights+'px'];
    }else{
        size=[nWidth+'px',nHeight+'px'];
    }
    layer.open({
        type: 1,
        title: false,
        closeBtn: 1,
        area: size,
        skin: 'layui-layer-nobg', //没有背景色
        shadeClose: true,
        content: "<img src='" + text + "' style='width:100%;height:100%;'>"
    });
}
$(document).on('click','.outer-left .customer img,.outer-right .service img',function(e) {
    var that = this;
    var img = new Image();
    img.src = this.src
    // 如果图片被缓存，则直接返回缓存数据
    if (img.complete) {
        var nWidth = img.width;
        var nHeight = img.height;
        if (this.width < nWidth || this.height < nHeight) {
            e.preventDefault();
            showBigImg(nWidth, nHeight,img.src);
        }
    } else {
        img.onload = function () {
            var nWidth = img.width;
            var nHeight = img.height;
            if (that.width < nWidth || that.height < nHeight) {
                e.preventDefault();
                showBigImg(nWidth, nHeight,img.src);
            }
        }
    }
});