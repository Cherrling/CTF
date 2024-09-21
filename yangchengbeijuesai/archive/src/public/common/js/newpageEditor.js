/**
 * Created by A.J on 2017/7/14.
 */
$(document).ready(function(){
    var um = UM.getEditor('editor',{
        autoFloatEnabled:false
    });
    //保存
    $("#baocun").click(function(event){
        event.preventDefault();
        if($.catfish()){
            $("#zhengwen").val(um.getContent());
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
