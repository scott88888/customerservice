/**
 * Created by chenrui on 2017/7/10.
 */



var e={'emoji1f600':'1f600','emoji1f601':'1f601','emoji1f602':'1f602','emoji1f603':'1f603','emoji1f604':'1f604','emoji1f605':'1f605','emoji1f606':'1f606','emoji1f607':'1f607','emoji1f608':'1f608','emoji1f609':'1f609','emoji1f610':'1f610','emoji1f611':'1f611','emoji1f612':'1f612','emoji1f613':'1f613','emoji1f614':'1f614','emoji1f615':'1f615','emoji1f616':'1f616','emoji1f617':'1f617','emoji1f618':'1f618','emoji1f619':'1f619','emoji1f620':'1f620','emoji1f621':'1f621','emoji1f622':'1f622','emoji1f623':'1f623','emoji1f624':'1f624','emoji1f625':'1f625','emoji1f626':'1f626','emoji1f627':'1f627','emoji1f628':'1f628','emoji1f629':'1f629','emoji1f630':'1f630','emoji1f631':'1f631','emoji1f632':'1f632','emoji1f633':'1f633','emoji1f634':'1f634','emoji1f635':'1f635','emoji1f636':'1f636','emoji1f637':'1f637','emoji1f638':'1f638','emoji1f639':'1f639','emoji1f640':'1f640','emoji1f641':'1f641','emoji1f642':'1f642','emoji1f643':'1f643','emoji1f644':'1f644','emoji1f910':'1f910','emoji1f911':'1f911','emoji1f912':'1f912','emoji1f913':'1f913','emoji1f914':'1f914','emoji1f915':'1f915','emoji1f916':'1f916','emoji1f917':'1f917','emoji1f923':'1f923','emoji1f924':'1f924','emoji1f925':'1f925','emoji1f926':'1f926','emoji1f927':'1f927','emoji1f928':'1f928','emoji1f929':'1f929'};
var faceon = function () {
    $(".wl_faces_main").empty();
    var str =""
    str +='<ul>';
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
    str +="</ul>";
    $(".wl_faces_main").append(str);
    $(".tool_box").toggle();
}

var emoj=function (obj) {

    var a=  $(obj).attr("title");
    var str=$("#text_in").val();
    var reg = new RegExp( '<' , "g" );
    str =str.replace(reg,'&lt;');

    var reg2 = new RegExp( '>' , "g" );
    str =str.replace(reg2,'&gt;');
    var b = "";
    b += str+" face["+a+"]";
    $("#text_in").val(b);
    $(".tool_box").hide();

}

function put() {

    var value = $('input[name="upload"]').val();
    var index1=value.lastIndexOf(".");
    var index2=value.length;
    var suffix=value.substring(index1+1,index2);
    var debugs =suffix.toLowerCase();

    if (debugs == "jpg" || debugs == "gif" ||debugs == "png" ||debugs == "jpeg") {

        $("#picture").ajaxSubmit({
            url:ROOT_URL+'/admin/event/upload',
            type: "post",
            dataType:'json',
            data:{visiter_id:visiter_id,business_id: business_id, avatar: pic,record: record,service_id:service_id},
            success: function (res) {
                if(res.code == 0){

                    var msg =res.data;
                    var time;

                    if($.cookie("itime") == ""){
                        var myDate = new Date();
                        time = myDate.getHours()+":"+myDate.getMinutes();
                        var timestamp = Date.parse(new Date());
                        $.cookie("itime",timestamp/1000);

                    }else{

                        var timestamp = Date.parse(new Date());
                        var lasttime =$.cookie("itime");
                        if((timestamp/1000 - lasttime) >30){
                            var myDate =new Date(timestamp);
                            time = myDate.getHours()+":"+myDate.getMinutes();
                        }else{
                            time ="";
                        }

                        $.cookie("itime",timestamp/1000);
                    }

                    var str = '';
                    str += '<li class="chatmsg"><div class="showtime">' + time + '</div>';
                    str += '<div class="" style="position: absolute;top: 29px;right:6px;"><img  class="my-circle cu_pic" src="' + pic + '" width="40px" height="40px"></div>';
                    str += "<div class='outer-right' style='right:16%'><div class='customer'>";
                    str += "<pre>" + msg + "</pre>";
                    str += "</div></div>";
                    str += "</li>";

                    $(".conversation").append(str);
                    var div = document.getElementById("wrap");
                    div.scrollTop = div.scrollHeight;
                }else{
                    layer.msg(res.msg,{icon:2});
                }

            }
        });

    } else {

        layer.msg("请选择图片", {icon: 2});
    }
}

// 文件上传
function putfile() {

    var value = $('input[name="folder"]').val();
    var sarr = value.split('\\');
    var name = sarr[sarr.length - 1];

    var arr = value.split(".");
    var debugs =arr[1].toLowerCase();
    if ( debugs == "js" ||  debugs == "css" ||  debugs == "html" ||  debugs == "php") {
        layer.msg("不支持该格式的文件", {icon: 2});
    } else {
        var myDate = new Date();
        var time =  myDate.getHours()+":"+myDate.getMinutes();

        $("#file").ajaxSubmit({
            url:ROOT_URL+'/admin/event/uploadfile',
            type: 'post',
            dataType:'json',
            data:{visiter_id:visiter_id,business_id: business_id, avatar: pic,record: record,service_id:service_id},
            success: function (res) {
                if(res.code == 0){
                    var str = '';
                    str += '<li class="chatmsg"><div class="showtime">' + time + '</div>';
                    str += '<div class="" style="position: absolute;top: 26px;right: 2px;"><img  class="my-circle cu_pic" src="' + pic + '" width="40px" height="40px"></div>';
                    str += "<div class='outer-right' style='right: 15%;'><div class='customer'>";
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
                    div.scrollTop = div.scrollHeight;
                    var msg = "<div><a href='" + res.data + "' style='display: inline-block;text-align: center;min-width: 70px;text-decoration: none;' download='" + name + "'><i class='layui-icon' style='font-size: 60px;'>&#xe61e;</i><br>" + name + "</a></div>";
                    var se = $('#services').text();
                    if(se){
                        var sid =$.cookie('services');
                    }


                }else{
                    layer.msg(res.msg,{icon:2});
                }

            }
        });

    }
}


function getbig(obj) {
    var text = $(obj).attr('src');
    layer.open({
        type: 1,
        title: false,
        closeBtn: 0,
        area: ['300px','300px'],
        skin: 'layui-layer-nobg', //没有背景色
        shadeClose: true,
        content: "<img src='" + text + "' style='width:100%;height:100%'>"
    });

}

function getdata(){

    var showtime="";
    if($.cookie("services")){
        var se = $.cookie("services");
    }else{
        var se =0;
    }

    var curentdata =new Date();
    var time =curentdata.toLocaleDateString();
    var cmin =curentdata.getMinutes();

    if($.cookie("cid") != "" ){
        var cid =$.cookie("cid");
    }else{
        var cid ="";
    }

    $.ajax({
        url:ROOT_URL+"/admin/event/chatdata",
        type: "post",
        data: {hid:cid,vid:visiter_id,business_id:business_id,service_id:se},
        dataType:'json',
        success: function (res) {
            //添加 最近的 聊天 记录
            if(res.code == 0){
                var str = '';

                $.each(res.data, function (k, v) {
                    if(getdata.puttime){

                        if((v.timestamp -getdata.puttime) > 60){
                            var myDate = new Date(v.timestamp*1000);
                            var puttime =myDate.toLocaleDateString();
                            if(puttime == time){
                                showtime =myDate.getHours()+":"+myDate.getMinutes();

                            }else{
                                showtime =myDate.getFullYear()+"-"+(myDate.getMonth()+1)+"-"+myDate.getDate()+" "+myDate.getHours()+":"+myDate.getMinutes();
                            }

                        }else{
                            showtime="";
                        }

                    }else{

                        var myDate = new Date(v.timestamp*1000);
                        var puttime =myDate.toLocaleDateString();
                        if(puttime == time){
                            showtime =myDate.getHours()+":"+myDate.getMinutes();
                        }else{
                            showtime =myDate.getFullYear()+"-"+(myDate.getMonth()+1)+"-"+myDate.getDate()+" "+myDate.getHours()+":"+myDate.getMinutes();
                        }
                    }
                    getdata.puttime = v.timestamp;

                    if (v.direction == 'to_service') {

                        str += '<li class="chatmsg"><div class="showtime">' + showtime + '</div>';
                        str += '<div class="" style="position: absolute;top: 29px;right: 6px;"><img class="my-circle" src="' + v.avatar + '" width="40px" height="40px"></div>';
                        str += "<div class='outer-right' style='right:16%;'><div class='customer'>";
                        str += "<pre>" + v.content + "</pre>";
                        str += "</div></div>";
                        str += "</li>";

                    } else {
                        str += '<li class="chatmsg" id="xiaox_'+v.cid+'"><div class="showtime">' + showtime + '</div><div style="position: absolute;left:8px">';
                        if(v.type == 2){
                            str += '<img  class="my-circle  se_pic" src="/assets/images/index/ai_service.png" width="40px" height="40px"></div>';
                        }else{
                            str += '<img  class="my-circle  se_pic" src="' + v.avatar + '" width="40px" height="40px"></div>';
                        }
                        str += "<div class='outer-left' style='right:4%;'><div class='service'>";
                        str += "<pre>" + v.content + "</pre>";
                        str += "</div></div>";
                        str += "</li>";

                    }
                });

                var div = document.getElementById("wrap");
                if($.cookie("cid") == ""){

                    $(".conversation").append(str);

                    if(div){
                        div.scrollTop = div.scrollHeight;
                    }
                }else{
                    $(".conversation").prepend(str);
                    if(res.data.length <= 2){
                        $("#top_div").remove();
                        $(".conversation").prepend("<div id='top_div' class='showtime'>已没有数据</div>");
                        if(div){
                            div.scrollTop =0;
                        }
                    }else {
                        if(div){
                            div.scrollTop =div.scrollHeight/3;
                        }
                    }
                }

                if(res.data.length >2){

                    $.cookie("cid",res.data[0]['cid']);
                }


            }
        }
    });
}





// 通知 客服

var init = function () {


    $.cookie("cid",'');
    wolive_connect();
    getdata();
    $.ajax({
        url:ROOT_URL+"/admin/event/notice",
        type: 'post',
        data: {visiter_id:visiter_id, visiter_name: visiter, business_id: business_id, from_url: record, avatar: pic,groupid:gid},
        success: function (res) {

            if(res.code == 0){
                var data =res.data;
                $("#img_head").attr('src',data.avatar);
                $("#services").text(data.nick_name);

                $("#services").attr('data',data.service_id);


                if(data.state == 'online'){
                    $("#img_head").removeClass("icon_gray");
                }else{
                    $("#img_head").addClass("icon_gray");
                }

                // 问候语
                var msg = '';
                msg += '<li class="chatmsg" style="margin-bottom: 45px;"><div style="position: absolute;top:16px;left:10px;">';
                msg += '<img  class="my-circle" src="' + data.avatar + '" width="40px" height="40px"></div>';
                msg += "<div class='outer-left' style='right:4%;top:13px;'><div class='service'>";
                msg += "<pre>" + data.content + "</pre>";
                msg += "</div></div>";
                msg += "</li>";
                $(".conversation").append(msg);
                var div = document.getElementById("wrap");
                div.scrollTop = div.scrollHeight;

                $.cookie("services",data.service_id);
                service_id =data.service_id;

            }else if(res.code == 1){

                layer.msg(res.msg,{icon:2});

            }else if(res.code == 2){

                $("#img_head").attr("src","/assets/images/index/workerman_logo.png");
                $("#services").text("");
                var num= getnums(business_id);
                // 告知客服在排队
                var msg='';
                msg+='<li class="chatmsg_notice"><div style="position: absolute;left:3px;top:17px">';
                msg+='<img  class="my-circle" src="'+ROOT_URL+'/assets/images/index/workerman_logo.png" width="40px" height="40px"></div>';
                msg+="<div class='outer-left' style='right:4%;top:13px;'><div class='service'>";
                msg+="<pre>通知 ： 现在还有"+num+" 人在排队，请等待 ....</pre>";
                msg+="</div></div>";
                msg+="</li>";
                $(".conversation").append(msg);

                $.cookie("services",'');

            }else if(res.code == 3){

                layer.msg(res.msg,{icon:2,end:function(){
                        window.location.href = url + "/index/message?business_id=" + business_id;
                    }});
                $.cookie("services",'');

            }else if(res.code == 4){
                var data =res.data;
                $("#img_head").attr('src',data.avatar);
                $("#services").text(data.nick_name);
                $("#img_head").addClass("icon_gray");

                layer.open({
                    title:'提示框',
                    area: ['300px', '180px'],
                    content:'该客服离线中，是否转接其他客服？',
                    btn:['是','否'],
                    yes:function(){
                        $.ajax({
                            url:ROOT_URL+'/admin/event/getchangekefu',
                            type:'post',
                            data:{visiter_id:visiter_id,business_id:business_id},
                            success:function(res){
                                if(res.code == 0){

                                    layer.msg('转接中....',{icon:3,end:function(){
                                            location.reload();
                                        }});
                                }
                            }
                        });
                    },
                    btn2:function(){
                        layer.close();
                    }
                });
            }
        }
    });


    getquestion(business_id);


    $.cookie("itime","");

}

window.onload = init();

function getquestion(business_id){
    $.ajax({
        url:ROOT_URL+'/admin/event/getquestion',
        type:'post',
        data:{business_id:business_id,visiter_id:visiter_id},
        success:function(res){
            if(res.code == 0){
                var str='';
                str += '<li class="chatmsg"><div style="position: absolute;left:3px;top:17px">';
                str += '<img  class="my-circle" src="/assets/images/index/workerman_logo.png" width="40px" height="40px"></div>';
                str += "<div class='outer-left' style='right:4%;top:13px;'><div class='service'>";
                str += "<pre>" ;

                str+='<p style="font-weight:blod;">我猜你想问：</p>'
                var num ='';
                $.each(res.data,function(k,v){
                    var a = JSON.stringify(v);
                    if(num){
                        num =num+1;
                    }else{
                        num =1
                    }

                    str+='<div class="question" onclick="getanswer('+v.qid+')">'+v.question+'</div>';
                });

                str += "</pre></div></div>";
                str += "</li>";

                $(".conversation").append(str);
                var div = document.getElementById("wrap");
                div.scrollTop = div.scrollHeight;
            }
        }
    });
}


function getanswer(id){

    $.ajax({
        url:ROOT_URL+'/admin/event/getanswer',
        type:'post',
        data:{qid:id},
        success:function(res){

            if(res.code == 0){
                var str = '';
                str += '<li class="chatmsg">';
                str += '<div  style="position: absolute;top: 29px;right: 6px;"><img  class="my-circle" src="' + pic + '" width="40px" height="40px"></div>';
                str += "<div class='outer-right' style='right:16%;top:24px;'><div class='customer'>";
                str += "<pre>" + res.data.question + "</pre>";
                str += "</div></div>";
                str += "</li>";
                $(".conversation").append(str);

                var msg = '';
                msg += '<li class="chatmsg"></div><div style="position: absolute;left:3px;top:17px">';
                msg += '<img  class="my-circle se_pic" src="/assets/images/index/workerman_logo.png" width="40px" height="40px"></div>';
                msg += "<div class='outer-left'  style='right:4%;top:13px;'><div class='service'>";
                msg += "<pre><div class='markdown-body ' style='width:' >" +res.data.answer+ "</div></pre>";
                msg += "</div></div>";
                msg += "</li>";
                $(".conversation").append(msg);
                var div = document.getElementById("wrap");
                div.scrollTop = div.scrollHeight;
            }
        }
    });
}



// 获取排队的数量
function getnums(id){
    var value ="";
    $.ajax({
        url:ROOT_URL+"/admin/event/getwaitnum",
        type:"post",
        async: false,
        data:{business_id:id,groupid:gid},
        success:function(res){
            value =res;
        }
    });
    return value;
}
// 发送消息

var send = function () {
    //获取 游客id
    var msg = $("#text_in").val();
    var reg = new RegExp( '<' , "g" )
    var msg2 =msg.replace(reg,'&lt;');

    var reg2 = new RegExp( '>' , "g" )
    msg2 =msg2.replace(reg2,'&gt;');

    if(msg2.indexOf("face[")!=-1){

        msg2=msg2.replace(/face\[([^\s\[\]]+?)\]/g,function (i) {
            var a = i.replace(/^face/g, "");
            a=a.replace('[','');
            a=a.replace(']','');
            return '<img src="/upload/emoji/'+e[a]+'.gif"/>'
        });

    }


    if (msg == '' || $.cookie("service") == '') {
        layer.msg('请输入信息');
    } else {
        var time;

        if($.cookie("itime") == ""){
            var myDate = new Date();
            time = myDate.getHours()+":"+myDate.getMinutes();
            var timestamp = Date.parse(new Date());
            $.cookie("itime",timestamp/1000);

        }else{

            var timestamp = Date.parse(new Date());
            var lasttime =$.cookie("itime");
            if((timestamp/1000 - lasttime) >30){
                var myDate =new Date(timestamp);
                time = myDate.getHours()+":"+myDate.getMinutes();
            }else{
                time ="";
            }

            $.cookie("itime",timestamp/1000);
        }

        var str = '';
        str += '<li class="chatmsg"><div class="showtime">' + time + '</div>';
        str += '<div style="position: absolute;top: 29px;right: 6px;"><img  class="my-circle cu_pic" src="' + pic + '" width="40px" height="40px"></div>';
        str += "<div class='outer-right' style='right:16%;'><div class='customer'>";
        str += "<pre>" + msg2 + "</pre>";
        str += "</div></div>";
        str += "</li>";

        $(".conversation").append(str);
        $("#text_in").val('');
        var div = document.getElementById("wrap");
        div.scrollTop = div.scrollHeight;

        $.ajax({
            url:ROOT_URL+"/admin/event/chat",
            type: "post",
            data: {visiter_id:visiter_id,content: msg2,business_id: business_id, avatar: pic,record: record,service_id:service_id},
            dataType:'json',
            success:function(res){
                if(res.code != 0){
                    layer.msg(res.msg,{icon:2});
                    var warm = '';
                    warm = "<img src='/assets/images/admin/notice.png' style='float: right;width: 22px;margin-top: 15px;margin-right: 10px'/>";
                    $(".outer-right:last").append(warm);
                    warm = '';
                    warm = "<div style='clear: both'></div><div class='showtime' style='margin-top: 10px'>"+res.msg+"</div>";
                    $(".chatmsg:last").append(warm);
                    var div = document.getElementById("wrap");
                    div.scrollTop = div.scrollHeight;
                }
            }
        });
    }
};
