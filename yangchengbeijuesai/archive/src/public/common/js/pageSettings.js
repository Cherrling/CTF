/**
 * Created by A.J on 2019/4/18.
 */
$(document).ready(function(){
    if($("#hunhe").find(".hunhediv").length > 1){
        $("#hunhe").find(".hunhediv:last").find(".panel-heading button").removeClass("hidden");
    }
    $("#hunhe").on("click", ".hunhediv .panel-heading button.close", function(){
        $(this).parent().parent().parent().parent().remove();
        if($("#hunhe").find(".hunhediv").length > 1){
            $("#hunhe").find(".hunhediv:last").find(".panel-heading button").removeClass("hidden");
        }
    });
    if($("#tuwen").find(".tuwendiv").length > 1){
        $("#tuwen").find(".tuwendiv:last").find(".panel-heading button").removeClass("hidden");
    }
    $("#tuwen").on("click", ".tuwendiv .panel-heading button.close", function(){
        $(this).parent().parent().parent().parent().remove();
        if($("#tuwen").find(".tuwendiv").length > 1){
            $("#tuwen").find(".tuwendiv:last").find(".panel-heading button").removeClass("hidden");
        }
    });
    $("#addhunhe").click(function(){
        $("#hunhe").find(".hunhediv:last").find(".panel-heading button").addClass("hidden");
        var xh = $("#hunhe").find(".hunhediv").length + 1;
        var kuai = $("#hunhekuai").html().replace(/#xh#/g,xh);
        $("#addhunhe").before(kuai);
        $("#hunhe").find(".hunhediv:last").find(".panel-heading button").removeClass("hidden");
    });
    $("#addtuwen").click(function(){
        $("#tuwen").find(".tuwendiv:last").find(".panel-heading button").addClass("hidden");
        var xh = $("#tuwen").find(".tuwendiv").length + 1;
        var kuai = $("#tuwenkuai").html().replace(/#xh#/g,xh);
        $("#addtuwen").before(kuai);
        $("#tuwen").find(".tuwendiv:last").find(".panel-heading button").removeClass("hidden");
    });
});