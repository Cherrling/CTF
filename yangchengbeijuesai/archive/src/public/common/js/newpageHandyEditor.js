/**
 * Created by A.J on 2018/7/12.
 */
$(document).ready(function(){
    var UA = navigator.userAgent.toLowerCase();
    var ver = 100,autoHt = true;
    if(UA.indexOf("chrome")>0){
        ver = parseInt(UA.match(/chrome\/([\d]+)/)[1]);
    }
    if(ver < 63){
        autoHt = false;
    }
    var he = HE.getEditor('zhengwen',{
        autoHeight : autoHt,
        autoFloat : true,
        topOffset : 51,
        uploadPhoto : true,
        uploadPhotoHandler : 'uploadWrite',
        uploadPhotoSize : 2000,
        uploadPhotoType : 'gif,png,jpg,jpeg',
        uploadPhotoSizeError : $("#sizeError").text(),
        uploadPhotoTypeError : $("#typeError").text(),
        skin : 'catfish'
    });
    //保存
    $("#baocun").click(function(event){
        event.preventDefault();
        if($.catfish()){
            $("#zhengwen").val(he.getHtml());
            $("#writeForm").submit();
        }
    });
    $.extend({'getEditorContent':function(){
        return he.getHtml();
    }});
    $.extend({'setEditorContent':function(content){
        return he.set(content);
    }});
});
