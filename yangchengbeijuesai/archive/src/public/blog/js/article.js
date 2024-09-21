/**
 * Created by A.J on 2016/10/14.
 */
$(document).ready(function(){
    if($("#editor").length > 0 && $("#catfish").length > 0){
        var um = UM.getEditor('editor',{
            toolbar:[
                'undo redo | bold italic underline strikethrough | superscript subscript | forecolor |',
                'emotion'
            ]
        });
    }
    $("#pinglun").click(function(){
        var obj = $(this);
        if(obj.children("span:eq(1)").hasClass("hidden") && $("#catfish").length > 0){
            if($.trim(um.getContentTxt()) == '')
            {
                alert("您还没有写任何评论内容！");
                return false;
            }
            obj.children("span:eq(0)").removeClass("hidden");
            obj.children("span:eq(1)").addClass("hidden");
            $.post($("#webroot").text()+"index/Index/pinglun", { id: $(this).prev().val(), pinglun: um.getContent() },
                function(data){
                    if(data == "ok"){
                        obj.children("span:eq(0)").addClass("hidden");
                        obj.children("span:eq(1)").removeClass("hidden");
                        alert("评论成功！");
                        location.reload();
                    }
                    else if(data == "prohibit"){
                        obj.children("span:eq(0)").addClass("hidden");
                        alert("您的评论内容有违禁词，请调整后提交");
                    }
                    else if(data == "wait"){
                        obj.children("span:eq(0)").addClass("hidden");
                        obj.children("span:eq(1)").removeClass("hidden");
                        alert("您已经提交评论，您的评论要审核通过后显示");
                    }
                });
        }
        else
        {
            alert("您已经评论过了!");
        }
    });
    $(".huifupinglun").click(function(){
        var obj = $(this).parent().parent().next();
        if(obj.hasClass('hidden')){
            obj.removeClass('hidden');
        }
        else{
            obj.addClass('hidden');
        }
    });
    $(".huifu").click(function(){
        var obj = $(this), textobj = $(this).parent().prev();
        if(obj.children("span:eq(1)").hasClass("hidden") && $("#catfish").length > 0)
        {
            if($.trim(textobj.val()) == '')
            {
                alert("您还没有写任何回复内容！");
                return false;
            }
            obj.children("span:eq(0)").removeClass("hidden");
            obj.children("span:eq(1)").addClass("hidden");
            $.post($("#webroot").text()+"index/Index/pinglun", { id: $(this).prev().prev().val(), pid: $(this).prev().val(), pinglun: textobj.val() },
                function(data){
                    if(data == "ok"){
                        obj.children("span:eq(0)").addClass("hidden");
                        obj.children("span:eq(1)").removeClass("hidden");
                        alert("回复成功！");
                        location.reload();
                    }
                    else if(data == "prohibit"){
                        obj.children("span:eq(0)").addClass("hidden");
                        alert("您的回复内容有违禁词，请调整后提交");
                    }
                    else if(data == "wait"){
                        obj.children("span:eq(0)").addClass("hidden");
                        obj.children("span:eq(1)").removeClass("hidden");
                        alert("您已经提交回复，您的回复内容要审核通过后显示");
                    }
                });
        }
        else
        {
            alert("不能重复回复!");
        }
    });
    var zan = false;
    $("#zan").click(function(){
        if(zan == false && $("#catfish").length > 0){
            $.post($("#webroot").text()+"index/Index/zan", { id: $(this).prev().val() },
                function(data){
                    $("#zanshu").text(parseInt($("#zanshu").text())+1);
                    zan = true;
                });
        }
        else{
            alert('只能赞一次哦!');
        }
    });
    $("#shoucang").click(function(){
        if($("#yishoucang").hasClass("hidden") && $("#catfish").length > 0){
            $.post($("#webroot").text()+"index/Index/shoucang", { id: $(this).parent().parent().children("input:first").val() },
                function(data){
                    $("#yishoucang").removeClass("hidden");
                });
        }
        else
        {
            alert("您已经收藏啦!");
        }
    });
    $("#lijidenglu").click(function(){
        if($("#catfish").length > 0){
            var obj = $(this);
            obj.children("span").removeClass("hidden");
            $.post($("#webroot").text()+"login/index/denglu", { user: $("#user").val(),pwd: $("#pwd").val() },
                function(data){
                    obj.children("span").addClass("hidden");
                    if(data == 'ok'){
                        $("#dengluyonghuming").text($("#user").val());
                        $(".weidenglu, .denglu").addClass("hidden");
                        $(".yidenglu, .pinglun, #woyaoshoucang").removeClass("hidden");
                        $('#myModal').modal('hide');
                    }
                    else{
                        $("#tishi").text(data);
                    }
                });
        }
    });
});