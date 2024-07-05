var e={'emoji1f600':'1f600','emoji1f601':'1f601','emoji1f602':'1f602','emoji1f603':'1f603','emoji1f604':'1f604','emoji1f605':'1f605','emoji1f606':'1f606','emoji1f607':'1f607','emoji1f608':'1f608','emoji1f609':'1f609','emoji1f610':'1f610','emoji1f611':'1f611','emoji1f612':'1f612','emoji1f613':'1f613','emoji1f614':'1f614','emoji1f615':'1f615','emoji1f616':'1f616','emoji1f617':'1f617','emoji1f618':'1f618','emoji1f619':'1f619','emoji1f620':'1f620','emoji1f621':'1f621','emoji1f622':'1f622','emoji1f623':'1f623','emoji1f624':'1f624','emoji1f625':'1f625','emoji1f626':'1f626','emoji1f627':'1f627','emoji1f628':'1f628','emoji1f629':'1f629','emoji1f630':'1f630','emoji1f631':'1f631','emoji1f632':'1f632','emoji1f633':'1f633','emoji1f634':'1f634','emoji1f635':'1f635','emoji1f636':'1f636','emoji1f637':'1f637','emoji1f638':'1f638','emoji1f639':'1f639','emoji1f640':'1f640','emoji1f641':'1f641','emoji1f642':'1f642','emoji1f643':'1f643','emoji1f644':'1f644','emoji1f910':'1f910','emoji1f911':'1f911','emoji1f912':'1f912','emoji1f913':'1f913','emoji1f914':'1f914','emoji1f915':'1f915','emoji1f916':'1f916','emoji1f917':'1f917','emoji1f923':'1f923','emoji1f924':'1f924','emoji1f925':'1f925','emoji1f926':'1f926','emoji1f927':'1f927','emoji1f928':'1f928','emoji1f929':'1f929'};

var types=function(){
    if($.cookie('type') == 1){
        //快捷键
        document.getElementById("text_in").onkeydown = function (e) {
            e = e || window.event;

            if (e.ctrlKey && e.keyCode == 13) {
                $("#text_in").append("<div><br/></div>");
                var o = document.getElementById("text_in").lastChild;
                var textbox = document.getElementById('text_in');
                var sel = window.getSelection();
                var range = document.createRange();
                range.selectNodeContents(textbox);
                range.collapse(false);
                if(o){
                    range.setEndAfter(o);//
                    range.setStartAfter(o);//
                }

                sel.removeAllRanges();
                sel.addRange(range);

            }

            if(!e.ctrlKey && e.keyCode == 13){
                var a=$('#text_in').val();

                var str=a.replace(/(^\s*)|(\s*$)/g,"");
                if(!str){
                    layer.msg('内容不能为空',{icon:3});
                    $('#text_in').html('');
                    return false;
                }

                send();
                e.returnValue = false;
                return false;
            }
        };

    }else{

        document.getElementById("text_in").onkeydown = function (e) {
            e = e || window.event;
            if (e.ctrlKey && e.keyCode == 13) {
                if ($('#text_in').val() == "" || $.cookie("service") == '' ) {
                    layer.msg('请输入信息');
                } else {
                    send();
                }
            }
        }

    }

}


// 默认加载

var chaton = function () {
    var height =document.body.clientHeight;
    $("#chat_list").css("height",(height -110)+"px");
    $("#wait_list").css("height",(height-110)+"px");
    //判断当前有无排队人员
    getwait();
    getblacklist();
    $.cookie("hid","");
    var sdata = $.cookie("cu_com");
    getreply();

    if (sdata) {
        var jsondata = $.parseJSON(sdata);
        var chas = jsondata.channel;
        var cip = jsondata.ip;
        $("#customer").text(jsondata.visiter_name);
        var record =jsondata.from_url;
        if(record.search('http') != -1){
            var str="<a href='"+record+"' target='_blank'>"+record+"</a>";
        }else{
            var str=record
        }

        $(".record").html(str);
        $("#channel").text(jsondata.visiter_id);
        getstatus(chas);
        getip(cip)
        getdata(jsondata.visiter_id);
    } else {

        $("#channel").text(" ");
        $(".record").text(" ");
        $(".iparea").text(" ");
        $(".chatmsg").remove();
        $(".chatbox").addClass('hide');
        $(".no_chats").removeClass('hide');

    }


    types();
};
window.onload = chaton();


function getreply(){
    $.ajax({
        url:"/admin/manager/replyinfo",
        type:'post',
        success:function(res){

            if(res.code == 0){

                $("#quit_reply").empty();

                var str="";
                $.each(res.data,function(k,v){
                    var tag =v.tag;

                    str+='<div style="position:relative" id="reply'+v.id+'">';
                    str+='<a class="del-reply" style="display:none;" href="javascript:close('+v.id+')"><img src="'+'/assets/images/admin/B/delete.png"></img></a>';
                    str+='<a class="reply-text" href="javascript:showon('+"'"+v.word.replace("'", "\\'")+"'"+')">'+tag+'</a>';
                    str+='<span class="reply-border"></span><span class="reply-about">'+v.word+'</span></div>';

                });


                str+='<div class="add-reply" onclick="addreply()" >添加快捷回复</div><div class="manager-reply" onclick="show()" >管理快捷回复</div>';

                $("#quit_reply").prepend(str);
            }

        }
    })
}

// 选择对象

function choose(vid) {
    if (choose_lock) {
        return false;
    }
    choose_lock = true;
    var data =chat_data['visiter'+vid];
    $.cookie("cu_com", JSON.stringify(data));
    $("#c"+data.channel).addClass('hide');
    $(".conversation").empty();
    $("#v"+data.channel).siblings("div").removeClass("onclick");
    $("#v"+data.channel).addClass("onclick");
    $(".chatbox").removeClass('hide');
    $(".no_chats").addClass('hide');
    //标记已看信息
    getwatch(data.visiter_id);
    chaton();
    getchat();
}

//拖到黑名单
function getblack() {
    var data = $.cookie("cu_com");
    var vid;
    if (data) {
        var jsondata = $.parseJSON(data);
        vid = jsondata.visiter_id
    }
    $.ajax({
        url:"/admin/set/blacklist",
        type: "post",
        data: {
            visiter_id: vid
        },
        success: function (res) {

            if (res.code == 0) {
                $.cookie("cu_com", "");
            }

            layer.msg("已拖入黑名单", {offset: "20px"});
            getchat();
            getblacklist();
        }
    });
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
var send = function () {
    //获取 游客id
    var msg = $("#text_in").val();
    var reg = new RegExp( '<' , "g" )
    msg =msg.replace(reg,'&lt;');
    var reg2 = new RegExp( '>' , "g" )
    msg =msg.replace(reg2,'&gt;');
    msg =msg.replace('http://','');
    msg =msg.replace('https://','');
    msg=msg.replace(/[a-z]+[.]{1}[a-z\d\-]+[.]{1}[a-z\d]*[\/]*[A-Za-z\d]*[\/]*[A-Za-z\d]*[\/]*[A-Za-z\d]*[\/]*[A-Za-z\d]/g,function (i) {
        return 'http://'+i;
    });


    msg=msg.replace(/(https?|http|ftp|file):\/\/[-A-Za-z0-9+&@#/%?=~_|!:,.;]+[-A-Za-z0-9+&@#/%=~_|]/g,function (i) {
        a=i.replace('http://','');
        return '<a href="'+i+'" target="_blank">'+a+'</a>';

    });


    if(msg.indexOf("face[")!=-1){
        msg=msg.replace(/face\[([^\s\[\]]+?)\]/g,function (i) {
            var a = i.replace(/^face/g, "");
            a=a.replace('[','');
            a=a.replace(']','');
            return '<img src="'+'/upload/emoji/'+e[a]+'.png"/>'
        });

    }

    var sdata = $.cookie('cu_com');
    if (sdata) {
        var json = $.parseJSON(sdata);
        var img = json.avater;
    }
    if (msg == "") {
        layer.msg('请输入信息');
    } else {
        var sid = $('#channel').text();
        var se = $("#chatmsg_submit").attr('name');
        var customer = $("#customer").text();
        var pic = $("#se_avatar").attr('src');
        var time;

        if($.cookie("time") == ""){
            var myDate = new Date();
            var minutes = myDate.getMinutes();
            if(minutes < 10 ) {
                minutes = '0'+minutes.toString();
            }
            time = myDate.getHours()+":"+minutes;
            var timestamp = Date.parse(new Date());
            $.cookie("time",timestamp/1000);

        }else{

            var timestamp = Date.parse(new Date());

            var lasttime =$.cookie("time");
            if((timestamp/1000 - lasttime) >30){
                var myDate =new Date(timestamp);
                var minutes = myDate.getMinutes();
                if(minutes < 10 ) {
                    minutes = '0'+minutes.toString();
                }
                time = myDate.getHours()+":"+minutes;
            }else{
                time ="";
            }

            $.cookie("time",timestamp/1000);

        }
        var unstr=(new Date()).valueOf()+randomChar(5)+sid;
        var str = '';
        str += '<li class="chatmsg" id="xiaox_'+unstr+'"><div class="showtime">' + time + '</div>';
        str += '<div style="position: absolute;top: 26px;right: 0px;"><img  class="my-circle se_pic" src="' + pic + '" width="50px" height="50px"></div>';
        str += "<div class='outer-right' ><div class='service'>";
        str += "<pre>" + msg + "&nbsp;&nbsp;<span onclick=revoke('" + unstr + "',2); class='revoke-text'>撤销</span></pre>";
        str += "</div></div>";
        str += "</li>";
        $(".conversation").append(str);
        $("#text_in").val('');
        var div = document.getElementById("wrap");
        div.scrollTop = $('.conversation')[0].scrollHeight;
        $(".chatmsg").css({
            height: 'auto'
        });

        $.ajax({
            url:"/admin/set/chats",
            type: "post",
            data: {visiter_id:sid,content: msg, avatar: img,unstr:unstr}
        });
    }
};

// 认领
function get(id) {
    $.ajax({
        url:"/admin/set/get",
        type: "post",
        data: {visiter_id: id},
        dataType:'json',
        success: function (res) {
            if(res.code == 0){
                layer.msg("认领成功", {offset: "20px",end:function(){
                        getwait();
                        getchat();
                    }});
            }
        }
    });
}

//表情
var faceon = function () {
    var e = window.event || arguments.callee.caller.arguments[0];
    $(".tool_box").toggle();
    e.stopPropagation();
};

$('body').click(function(){
    $(".tool_box").hide();
});

//获取表情图片
$(".wl_faces_main img").click(function () {
    var a = $(this).attr("title");
    var str=$("#text_in").val();
    var reg = new RegExp( '<' , "g" )
    str =str.replace(reg,'&lt;');

    var reg2 = new RegExp( '>' , "g" )

    str =str.replace(reg2,'&gt;');
    var b = "";
    b += str+" face["+a+"]";
    $("#text_in").val(b);
    $("#text_in").focus();
    $(".tool_box").hide();
});


//删除对象

function cut(id) {

    var data = $.cookie("cu_com");
    var visiter_checked;
    if (data) {
        var jsondata = $.parseJSON(data);
        visiter_checked = jsondata.visiter_id;
    }
    $.ajax({
        url:"/admin/set/deletes",
        type: "post",
        data: {
            visiter_id: id
        },
        dataType:'json',
        success: function (res) {

            if(res.code == 0){
                if (visiter_checked == id) {

                    $(".chatbox").addClass('hide');
                    $(".no_chats").removeClass('hide');
                }
                // 删除修改
                getblacklist();
            }
        }
    });
}

function recovery(id){
    $.ajax({
        url:"/admin/set/removeblacklist",
        type: "post",
        data: {
            visiter_id: id
        },
        dataType:'json',
        success: function (res) {

            if(res.code == 0){
                // 删除修改
                getblacklist();
                getchat();
            }
        }
    });
}

//删除cookie方法
function delCookie(name) {
    var date = new Date();
    date.setTime(date.getTime() - 10000);
    document.cookie = name + "=a; expires=" + date.toGMTString()
};

//文件上传
function putfile() {

    var value = $('input[name="folder"]').val();
    var sarr = value.split('\\');
    var name = sarr[sarr.length - 1];
    var arr = value.split(".");

    if (arr[1] == "js" || arr[1] == "css" || arr[1] == "html" || arr[1] == "php") {
        layer.msg("不支持该格式的文件", {icon: 2});

    } else {

        var myDate = new Date();
        var time =  myDate.getHours()+":"+myDate.getMinutes();
        var pic = $("#se_avatar").attr('src');
        $("#file").ajaxSubmit({
            url:'/admin/set/uploadfile',
            type: 'post',
            datatype:'json',
            success: function (res) {
                if(res.code == 0){
                    var str = '';
                    str += '<li class="chatmsg"><div class="showtime">' + time + '</div>';
                    str += '<div class="" style="position: absolute;top: 26px;right: 2px;"><img  class="my-circle cu_pic" src="'+pic+'" width="40px" height="40px"></div>';
                    str += "<div class='outer-right'><div class='service'>";
                    str += "<pre>";

                    if(res.data.indexOf('.mp4')>= 0){
                        str += "<video src='" + res.data + "' controls='controls' style='width: 100%'>ERROR</video>";
                    }else{
                        str += "<div><a href='" + res.data + "' style='display: inline-block;text-align: center;min-width: 70px;text-decoration: none;' download='" + name + "'><i class='layui-icon' style='font-size: 60px;'>&#xe61e;</i><br>" + name + "</a></div>";
                    }

                    str += "</pre>";
                    str += "</div></div>";
                    str += "</li>";

                    $(".conversation").append(str);
                    var div = document.getElementById("wrap");
                    div.scrollTop = $('.conversation')[0].scrollHeight;
                    $(".chatmsg").css({
                        height: 'auto'
                    });
                    var sdata = $.cookie('cu_com');

                    if (sdata) {
                        var json = $.parseJSON(sdata);
                        var img = json.avater;
                    }

                    var msg = "<div><a href='" + res.data + "' style='display: inline-block;text-align: center;min-width: 70px;text-decoration: none;' download='" + name + "'><i class='layui-icon' style='font-size: 60px;'>&#xe61e;</i><br>" + name + "</a></div>";

                    if(res.data.indexOf('.mp4')>= 0){
                        msg = "<video src='" + res.data + "' controls='controls' style='width: 100%'>ERROR</video>";
                    }

                    var sid = $('#channel').text();
                    var se = $("#chatmsg_submit").attr('name');
                    var customer = $("#customer").text();
                    $.ajax({
                        url:"/admin/set/chats",
                        type: "post",
                        data: {visiter_id:sid,content: msg, avatar: img}
                    });
                }else{
                    layer.msg(res.msg,{icon:2});
                }

            }
        });

    }
}


//图片上传

function put() {

    var value = $('input[name="upload"]').val();
    var index1=value.lastIndexOf(".");
    var index2=value.length;
    var suffix=value.substring(index1+1,index2);
    var debugs =suffix.toLowerCase();

    if (debugs == "jpg" || debugs == "gif" ||debugs == "png" ||debugs == "jpeg") {

        $("#picture").ajaxSubmit({
            url:'/admin/set/upload',
            type: "post",
            dataType:'json',
            success: function (res) {
                if(res.code == 0){

                    var sdata = $.cookie('cu_com');
                    if (sdata) {
                        var json = $.parseJSON(sdata);
                        var img = json.avater;
                    }

                    var msg = '<img class="chat-img" src="' + res.data +'" >';
                    var sid = $('#channel').text();
                    var se = $("#chatmsg_submit").attr('name');
                    var customer = $("#customer").text();
                    var pic = $("#se_avatar").attr('src');
                    var time;

                    if($.cookie("time") == ""){
                        var myDate = new Date();
                        time = myDate.getHours()+":"+myDate.getMinutes();
                        var timestamp = Date.parse(new Date());
                        $.cookie("time",timestamp/1000);

                    }else{

                        var timestamp = Date.parse(new Date());

                        var lasttime =$.cookie("time");
                        if((timestamp/1000 - lasttime) >30){
                            var myDate =new Date(timestamp);
                            time = myDate.getHours()+":"+myDate.getMinutes();
                        }else{
                            time ="";
                        }

                        $.cookie("time",timestamp/1000);

                    }
                    var str = '';
                    str += '<li class="chatmsg"><div class="showtime">' + time + '</div>';
                    str += '<div style="position: absolute;top: 26px;right: 2px;"><img  class="my-circle se_pic" src="' + pic + '" width="46px" height="46px"></div>';
                    str += "<div class='outer-right'><div class='service' style='padding:0;border-radius:0;max-height:100px'>";
                    str += "<pre>" + msg + "</pre>";
                    str += "</div></div>";
                    str += "</li>";

                    $(".conversation").append(str);
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
                        data: {visiter_id:sid,content: msg, avatar: img},
                        success:function(res){
                            if(res.code != 0){
                                layer.msg(res.msg,{icon:2});
                            }
                        }
                    });
                }else{
                    layer.msg(res.msg,{icon:2});
                }
            }
        });

    } else {

        layer.msg("请选择图片", {icon: 2});
    }
}

//图片放大预览

function getbig(obj) {

    var text = $(obj).attr('src');

    var img = new Image();

    img.src = $(obj).attr('src');
    var nWidth = img.width;
    var nHeight = img.height;

    var rate=nWidth/nHeight;

    var maxwidth =window.innerWidth;
    var maxheight=window.innerHeight;

    var size;

    if((nHeight-maxheight) > 0 || (nWidth-maxwidth) >0 ){

        var widths,heights;
        heights=maxheight-100;
        widths=heights*rate;
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