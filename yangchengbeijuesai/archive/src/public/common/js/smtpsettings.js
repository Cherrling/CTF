/**
 * Created by A.J on 2019/11/3.
 */
$(document).ready(function(){
    $("#ceshi").click(function(){
        if($.catfish()){
            var obj = $(this);
            obj.children("span").removeClass("hidden");
            $.post("sendtestmail", { verification: $("#verification").val()},
                function(data){
                    obj.children("span").addClass("hidden");
                    if(data != 'ok'){
                        alert(data);
                    }
                });
        }
    });
});