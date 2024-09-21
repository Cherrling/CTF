/**
 * Created by A.J on 2021/1/26.
 */
$(document).ready(function(){
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
        uploader:'uploadtheme',
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
    $(".deltheme").click(function(){
        var tname = $(this).data("name");
        $.confirm({
            title: $('#quedingshanchu').text(),
            content: tname + " " + $('#wufahuifu').text(),
            confirmButton: $('#jixu').text(),
            cancelButton: $('#quxiao').text(),
            confirm: function(){
                $.post("deltheme", { name: tname, verification: $("#verification").text()},
                    function(data){
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
                    });
            }
        });
    });
});