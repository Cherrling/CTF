/**
 * Created by A.J on 2016/10/19.
 */
$(document).ready(function(){
    //上传logo
    if($("#tubiao").val() != ''){
        $('#tubiaoImg').attr("src", $("#tubiao").val());
    }
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
        uploader:'uploadLogo',
        onUploadComplete:function(file,data){
            pic = $("#webroot").text()+'data/uploads/'+data.replace('\\','/');
            $('#tubiao').val(pic);
            $('#tubiaoImg').attr("src", pic);
        }
    });
    //上传ico
    if($("#icotubiao").val() != ''){
        $('#icotubiaoIco').attr("src", $("#icotubiao").val());
    }
    var pic_ico='';
    $('#upload_ico').uploadify({
        auto:true,
        fileTypeExts:'*.ico',
        multi:false,
        formData:{},
        fileSizeLimit:9999,
        buttonText:$('#icobuttonText').text(),
        showUploadedPercent:true,//是否实时显示上传的百分比，如20%
        showUploadedSize:false,
        removeTimeout:3,
        uploader:'uploadIco',
        onUploadComplete:function(file,data){
            pic_ico = $("#webroot").text()+'data/uploads/'+data.replace('\\','/');
            $('#icotubiao').val(pic_ico);
            $('#icotubiaoIco').attr("src", pic_ico);
        }
    });
    if($("#gudingbi").prop("checked")){
        $("#kuangaobi").removeClass("hidden");
    }
    $("#gudingbi").change(function() {
        if($("#gudingbi").prop("checked")){
            $("#kuangaobi").removeClass("hidden");
        }
        else{
            $("#kuangaobi").addClass("hidden");
        }
    });
    $("form").submit(function(e){
        var ckdm = /^(http:\/\/|https:\/\/)[^ |,]+\/$/;
        if(!ckdm.test($("#domainid").val())){
            alert($("#yumingtishi").text());
            setTimeout(function(){$("#submitid").find("span:eq(0)").addClass("hidden");},1);
            return false;
        }
    });
    $("#recordqueding").on("click", function(event){
        var recordicp = "", recordgg = "";
        if($("#icpming").val() != "" && $("#icphao").val() != ""){
            recordicp = $("#icpming").val() + "ICP备 " + $("#icphao").val() + "号";
            if($.trim($("#icpzihao").val()) != ""){
                recordicp += "-" + $.trim($("#icpzihao").val());
            }
            recordicp = "<a href='http://beian.miit.gov.cn/' target='_blank'>" + recordicp + "</a>";
        }
        if($("#gonganming").val() != "" && $("#gonganhao").val() != ""){
            recordgg = "&nbsp;<a href='http://www.beian.gov.cn/portal/registerSystemInfo?recordcode=" + $("#gonganhao").val() + "' target='_blank'><img src='" + $("#webroot").text() + "public/common/images/gongan.png'> " + $("#gonganming").val() + "公网安备 " + $("#gonganhao").val() + "号</a>";
        }
        var reicpgg = recordicp + recordgg;
        if(reicpgg != ""){
            $("#record").val(recordicp + recordgg);
        }
        $('#recordhtml').modal('hide');
    });
});