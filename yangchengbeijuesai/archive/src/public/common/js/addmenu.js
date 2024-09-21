/**
 * Created by A.J on 2016/10/12.
 */
$(document).ready(function(){
    $("#caidanfenlei").change(function(){
        $("#carriedout").removeClass("hidden");
        $("#fuji").html('<option value="0">'+$('#yijicaidan').text()+'</option>');
        $.post("changeParent", { id: $(this).val(), fj: $("#fj").text()},
            function(data){
                $("#fuji").html(data);
                $("#carriedout").addClass("hidden");
            });
    });
    $.post("geticonlist", { verification:$("#verification").val()},
        function(data){
            var html = '';
            $.each(data, function(index,val){
                html += '<div class="col-xs-3 col-md-2 text-center icondiv" style="height:90px;padding:10px;cursor:pointer" id="icon_'+val["name"]+'"><svg class="bi" width="32" height="32" fill="currentColor"><use xlink:href="'+$("#domain").text()+'public/common/images/bootstrap-icons.svg#'+val["name"]+'"/></svg><div>'+val["name"]+'</div></div>';
            });
            $("#iconlist").html(html);
        });
    $('#iconlist').on('click', '.icondiv',function(){
        $(this).addClass("bg-info").addClass("text-warning").siblings().removeClass("bg-info").removeClass("text-warning");
        $('#currenticon').text($(this).children("div:last").text());
    });
    $('#iconlist').on('mouseenter','.icondiv',function(){
        $(this).addClass("bg-success");
    });
    $('#iconlist').on('mouseleave','.icondiv',function(){
        $(this).removeClass("bg-success");
    });
    $('#iconsModal').on('shown.bs.modal', function (e) {
        var currenticon = $('#currenticon').text();
        if(currenticon != ""){
            $("#icon_"+currenticon).addClass("bg-info").addClass("text-warning").siblings().removeClass("bg-info").removeClass("text-warning");
        }
    });
    $('#iconok').on('click',function(){
        $.inicon();
    });
    $('#iconlist').on('dblclick', '.icondiv',function(){
        $.inicon();
    });
    $("#changeicon").bind("input propertychange",function(event){
        var iconsize = $("#changeicon").val();
        var currenticon = $('#currenticon').text();
        if(currenticon != ""){
            $('#selectedicon').html('<svg class="bi" width="'+iconsize+'" height="'+iconsize+'" fill="currentColor"><use xlink:href="'+$("#domain").text()+'public/common/images/bootstrap-icons.svg#'+currenticon+'"/></svg>');
            $("#iconsize").text(iconsize);
            $("#icons").val($('#selectedicon').html());
        }
    });
    $('#closeicon').on('click',function(){
        $('#selectedicon').html("");
        $("#icons").val("");
        $("#iconselect").addClass("hidden");
        $("#iconinputdiv").removeClass("hidden");
    });
});
$.extend({'inicon':function(){
    var currenticon = $('#currenticon').text();
    if(currenticon == ""){
        $.alert({
            title: $('#chucuo').text(),
            content: $('#currenticonempty').text(),
            confirmButton: $('#queding').text()
        });
    }
    else{
        $('#selectedicon').html('<svg class="bi" width="32" height="32" fill="currentColor"><use xlink:href="'+$("#domain").text()+'public/common/images/bootstrap-icons.svg#'+currenticon+'"/></svg>');
        $("#iconsize").text(32);
        $('#iconsModal').modal('hide');
        $("#iconinputdiv").addClass("hidden");
        $("#iconselect").removeClass("hidden");
        $("#icons").val($('#selectedicon').html());
    }
}});
