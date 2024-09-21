/**
 * Created by A.J on 2016/11/5.
 */
$(document).ready(function(){
    $(".yincang").click(function(){
        var obj = $(this);
        obj.children("span").removeClass("hidden");
        var pn = obj.parent().siblings(":eq(0)").text();
        $.post("pluginkaiguan", { pn: pn, verification: $("#verification").text()},
            function(data){
                obj.siblings(":first").val(0);
                obj.parent().prev().html('<h5 class="text-muted">'+$('#weikaiqi').text()+'</h5>');
                obj.children("span").addClass("hidden");
                obj.addClass("hidden").next().removeClass("hidden");
                obj.next().next().removeClass("hidden");
                $("#kz_plugin_"+pn).remove();
            });
    });
    $(".qiyong").click(function(){
        var obj = $(this);
        obj.children("span").removeClass("hidden");
        $.post("pluginkaiguan", { pn: obj.parent().siblings(":eq(0)").text(), verification: $("#verification").text()},
            function(data){
                obj.siblings(":first").val(1);
                obj.parent().prev().html('<h5 class="text-success"><span class="glyphicon glyphicon-ok"></span> '+$('#yikaiqi').text()+'</h5>');
                obj.children("span").addClass("hidden");
                obj.addClass("hidden").prev().removeClass("hidden");
                obj.next().addClass("hidden");
            });
    });
    $(".shanchu").click(function(){
        var obj = $(this), pname = $(this).data("name");
        $.confirm({
            title: $('#quedingshanchu').text(),
            content: pname + " " + $('#wufahuifu').text(),
            confirmButton: $('#jixu').text(),
            cancelButton: $('#quxiao').text(),
            confirm: function(){
                obj.children("span").removeClass("hidden");
                $.post("delplugin", { name: pname, verification: $("#verification").text()},
                    function(data){
                        obj.children("span").addClass("hidden");
                        if(data == "ok"){
                            obj.parent().parent().remove();
                        }
                        else{
                            $.alert({
                                title: $('#chucuo').text(),
                                content: data,
                                confirmButton: $('#queding').text()
                            });
                        }
                    });
            }
        });
    });
    $('#upload').uploadify({
        auto:true,
        fileTypeExts:'*.zip',
        multi:false,
        formData:{verification:$("#verification").text()},
        fileSizeLimit:9999,
        buttonText:$('#buttonText').text(),
        showUploadedPercent:true,
        showUploadedSize:false,
        removeTimeout:3,
        uploader:'uploadplugin',
        onUploadComplete:function(file,data){
            if(data == "ok"){
                location.reload();
            }
            else{
                $.alert({
                    title: $('#chucuo').text(),
                    content: data,
                    confirmButton: $('#queding').text()
                });
            }
        }
    });
});