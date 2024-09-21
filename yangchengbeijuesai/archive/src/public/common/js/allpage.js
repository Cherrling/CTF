/**
 * Created by A.J on 2016/10/7.
 */
$(document).ready(function(){
    $("#fromdatetime, #todatetime").datetimepicker({format: 'yyyy-mm-dd hh:ii:ss'});
    $('table a.twitter').confirm({
        title: $('#quedingshanchu').text(),
        content: $('#shanchuyemian').text()+'<b>'+$('#wufahuifu').text()+'</b>',
        confirmButton: $('#jixu').text(),
        cancelButton: $('#quxiao').text(),
        confirm: function(){
            var obj = this.$target;
            $.post("removePage", { id: this.$target.parent().parent().children(":eq(0)").children("input").val(), verification: $("#verification").text()},
                function(data){
                    obj.parent().parent().remove();
                });
        }
    });
    $("#zxuan").click(function(){
        if($(this).prop("checked")){
            $(".gouxuan").prop("checked",true);
        }
        else{
            $(".gouxuan").prop("checked",false);
        }
    });
    $("#pshanchu").click(function(){
        $.confirm({
            title: $('#quedingshanchu').text(),
            content: $('#shanchuyemian').text()+'<b>'+$('#wufahuifu').text()+'</b>',
            confirmButton: $('#jixu').text(),
            cancelButton: $('#quxiao').text(),
            confirm: function(){
                $.caozuo($(this),'pshanchu');
            }
        });
    });
    if($(".shezhizhuyemian").length > 0){
        $(".chang").hover(function(){
            if($(this).find(".quxiaozhuyemian").closest("div").hasClass("hidden")){
                $(this).find(".shezhizhuyemian").closest("div").removeClass("hidden");
            }
            else{
                $(this).find(".quxiaozhuyemian").removeClass("hidden");
            }
        },function(){
            $(this).find(".shezhizhuyemian").closest("div").addClass("hidden");
            $(this).find(".quxiaozhuyemian").addClass("hidden");
        });
        $(".shezhizhuyemian").on("click", function(){
            var tobj = $(this);
            var tdiv = $(this).closest("div");
            tobj.next().removeClass("hidden");
            $.post("zhuyemian", { id: tobj.data("id")},
                function(data){
                    tobj.next().addClass("hidden");
                    $(".quxiaozhuyemian").closest("div").addClass("hidden");
                    tdiv.addClass("hidden").prev().removeClass("hidden");
                });
        });
        $(".quxiaozhuyemian").on("click", function(){
            var tobj = $(this);
            var tdiv = $(this).closest("div");
            tobj.next().removeClass("hidden");
            $.post("quxiaozhuyemian", { id: tobj.data("id")},
                function(data){
                    tobj.next().addClass("hidden");
                    tdiv.addClass("hidden").next().removeClass("hidden");
                });
        });
    }
});
$.extend({'caozuo':function(obj,cz){
    var zcuan = '',ind = new Array();
    //获取选中项和序号
    $(".gouxuan").each(function(index,element){
        if($(this).prop("checked")){
            ind.unshift(index);
            if(zcuan == ''){
                zcuan = $(this).val();
            }
            else{
                zcuan += ',' + $(this).val();
            }
        }
    });
    if(zcuan != ''){
        obj.children("span").removeClass("hidden");
        $.post("removeAllPage", { zcuan: zcuan, cz: cz, verification: $("#verification").text()},
            function(data){
                obj.children("span").addClass("hidden");
                $.each(ind, function(i, value) {
                    switch(cz){
                        case 'pshanchu':
                            $(".gouxuan:eq("+value+")").parent().parent().remove();
                            break;
                    }
                });
            });
    }
    else{
        $.alert({
            title: $('#jinggao').text(),
            content: $('#zhishaoxuanyixiang').text(),
            confirmButton: $('#queding').text()
        });
    }
}});