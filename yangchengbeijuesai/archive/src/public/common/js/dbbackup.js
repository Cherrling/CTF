/**
 * Created by A.J on 2019/9/3.
 */
$(document).ready(function(){
    var jixu = $('#jixu').text(), quxiao = $('#quxiao').text(), huanyuanshuoming = $('#huanyuanshuoming').text();
    $('table a.shanchu').confirm({
        title: $('#quedingshanchu').text(),
        content: $('#fangruhuishouzhan').text(),
        confirmButton: jixu,
        cancelButton: quxiao,
        onAction: function(action){
            if(action == 'confirm'){
                var obj = this.$target;
                $.post("deldbbackup", { fn: this.$target.siblings(":first").val(), verification: $("#verification").text()},
                    function(data){
                        if(data == 'ok'){
                            obj.parent().parent().remove();
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
        }
    });
    $('table a.huanyuan').confirm({
        title: $('#quedinghuanyuan').text(),
        content: huanyuanshuoming,
        confirmButton: jixu,
        cancelButton: quxiao,
        onAction: function(action){
            if(action == 'confirm'){
                var obj = this.$target;
                $("#dbbackupModal").modal();
                $.post("redbbackup", { fn: this.$target.siblings(":first").val(), verification:$("#verification").text()},
                    function(data){
                        $('#dbbackupModal').modal('hide');
                        if(data == 'ok'){
                            $.alert({
                                title: '',
                                content: $('#redbbackupok').text(),
                                confirmButton: $('#queding').text()
                            });
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
        }
    });
    $("#submitupreform").confirm({
        title: $('#quedingshangchuan').text(),
        content: huanyuanshuoming+"<br>"+$('#shangchuanshuoming').text(),
        confirmButton: jixu,
        cancelButton: quxiao,
        onAction: function(action){
            if(action == 'confirm'){
                if($("#bkfile").val() == ""){
                    $.alert({
                        title: $('#chucuo').text(),
                        content: $('#notselected').text(),
                        confirmButton: $('#queding').text()
                    });
                }
                else{
                    $("#upreform").submit();
                }
            }
        }
    });
});