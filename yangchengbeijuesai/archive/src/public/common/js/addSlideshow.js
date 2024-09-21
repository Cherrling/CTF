/**
 * Created by A.J on 2016/10/11.
 */
$(document).ready(function(){
    var pic='';
    $('#upload').uploadify({
        auto:true,
        fileTypeExts:'*.jpg;*.png;*.gif;*.jpeg',
        multi:false,
        formData:{},
        fileSizeLimit:9999,
        buttonText:$('#buttonText').text(),
        showUploadedPercent:true,//是否实时显示上传的百分比，如20%
        showUploadedSize:false,
        removeTimeout:3,
        uploader:'uploadSlideshow',
        onUploadStart:function(){
            this.formData = {width:$("#sdwidth").text(),height:$("#sdheight").text(),verification:$("#verification").val()};
        },
        onUploadComplete:function(file,data){
            pic = $("#webroot").text()+'data/uploads/'+data.replace('\\','/');
            $('#slideshow').val(pic);
            $('#slideshowImg').attr("src", pic);
        }
    });
    $("#sdwidth").text($(this).find("option:selected").data("wid"));
    $("#sdheight").text($(this).find("option:selected").data("hei"));
    $("#sgroup").change(function(){
        $("#sdwidth").text($(this).find("option:selected").data("wid"));
        $("#sdheight").text($(this).find("option:selected").data("hei"));
    });
});