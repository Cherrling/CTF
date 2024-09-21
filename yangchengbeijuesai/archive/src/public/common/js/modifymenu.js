/**
 * Created by A.J on 2016/10/8.
 */
$(document).ready(function(){
    $("#lianjie").val($("#href").text());
    if($("#lianjie").val() == null || $("#lianjie").val() == ''){
        $("#lianjie").val('index');
    }
    $("#caidanfenlei").val($("#caidanfenleis").val());
    var ind = new Array(), stt = false, wz = 0, yc = '', nyc = '';
    //去子父级
    $("#fuji option").each(function(index,element){
        if($("#caidanId").val() == $(this).val()){
            stt =true;
            ind.unshift(index);
            wz = $(this).text().indexOf('└');
            yc = $(this).text().substr(0,wz);
            return true;
        }
        if(stt == true){
            wz = $(this).text().indexOf('└');
            nyc = $(this).text().substr(0,wz);
            if(nyc.length > yc.length){
                ind.unshift(index);
                return true;
            }
            else{
                return false;
            }
        }
    });
    if(ind.length > 0){
        $.each(ind, function(i, value) {
            $("#fuji option:eq("+value+")").remove();
        });
    }
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
    if($("#selectedicon").html() != ""){
        var icon = $("#selectedicon").html(), size = 32, name = "";
        var wind = icon.indexOf(' width="') + 8;
        icon = icon.substr(wind);
        wind =icon.indexOf('"');
        if(wind > -1){
            size = icon.substr(0, wind);
        }
        wind =icon.indexOf(".svg#") + 5;
        icon = icon.substr(wind);
        wind =icon.indexOf('"');
        if(wind > -1){
            name = icon.substr(0, wind);
        }
        $("#icons").val($("#selectedicon").html());
        $("#iconsize").text(size);
        $("#iconinputdiv").addClass("hidden");
        $("#iconselect").removeClass("hidden");
        $("#changeicon").val(size);
        $("#currenticon").text(name);
    }
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
