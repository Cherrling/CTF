/**
 * Created by A.J on 2017/7/13.
 */
$(document).ready(function(){
    var um = UM.getEditor('editor',{
        autoFloatEnabled:false
    });
    //保存
    $("#baocun").on("click tap", function(event){
        event.preventDefault();
        if($.catfish()){
            $("#zhengwen").val(um.getContent());
            if($("#zhaiyao").val() == ''){
                if(um.getContentTxt().length > 500){
                    $("#zhaiyao").val(um.getContentTxt().substr(0,500)+'...');
                }
                else{
                    $("#zhaiyao").val(um.getContentTxt());
                }
            }
            $("#writeForm").submit();
        }
    });
    $.extend({'getEditorContent':function(){
        return um.getContent();
    }});
    $.extend({'setEditorContent':function(content){
        return um.setContent(content);
    }});
});