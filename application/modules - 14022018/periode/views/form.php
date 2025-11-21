<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Author       : _abdi_iwan_
 * Project         :
 */
?>


<div id="view-form" style="display:none">
    <div class="ibox float-e-margins ">
        <div class="ibox-content gray-bg">
            <form class="form-horizontal form-label-left" name="inputForm" id="inputForm" method="post">
                <input type="hidden" name="iddata" id="iddata"/>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt1">Bulan</label>
                    <div class="col-md-3 col-sm-3 col-xs-12">
                        <?php
                        $js = 'id="txt1" class="input-sm form-control" data-placeholder="Pilih Bulan..."';
                        echo form_dropdown('txt1',$lstBulan,null,$js);
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt2">Tahun</label>
                    <div class="col-md-3 col-sm-3 col-xs-12">
                        <?php
                        $js = 'id="txt2" class="input-sm form-control" data-placeholder="Pilih Tahun..."';
                        echo form_dropdown('txt2',$tahun,$pilthn,$js);
                        ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt3">Status</label>
                    <div class="col-md-3 col-sm-3 col-xs-12">
                        <label class="checkbox-inline"> <input value="1" id="txt3" name="txt3" type="radio"> Buka </label>
                        <label class="checkbox-inline"> <input value="0" id="txt4" name="txt3" type="radio"> Tutup </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" >Unit Kerja</label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                       <?php createUnitKerjaCheckbox() ?>
                        <input type="hidden" name="txt5" id="txt5" />
                    </div>
                </div>

                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <div class="col-md-6 col-md-offset-3">
                        <input type="submit" id="" name="" class="btn btn-primary btn-sm" value="Simpan"/>
                        <a href="#" onClick="Batal();" class="btn btn-danger btn-sm">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $("#inputForm").validate({
            rules: {
                txt1: {
                    required: true
                }, txt2: {
                    required: true, digits: true, minlength: 1, maxlength: 4
                }

            }, submitHandler: function () {
                saveForm();
            }, highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error');
            }, unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error');
            }
        });

        $('.unker2').click(function(){
            var inwidth = $(this).width();
            var dis = $('#panel2').css("display");
            if(dis=='none'){
                $('#panel2').css({
                    display : 'block',
                    width : inwidth
                });

                //$('#combo-panel2').empty();
                if ( $('#combo-panel2 > *').length == 0 ) {
                    $('#combo-panel2').html('Loading...........');
                    $.ajax({
                        url: '<?php echo site_url('ajax/getUnitKerjaN')?>', dataType: 'html', type: 'POST', success: function (data) {
                            $('#combo-panel2').html(data);
                        }
                    });
                }
            }else{
                // $('#combo-panel2').empty();
                $('#panel2').css({
                    display : 'none'
                });
                //$('.unker2').data('clicked',0);
            }
        });
    });

    function Batal() {
        $("#iddata").val("");
        var validator = $("#inputForm").validate();
        validator.resetForm();
        $("#btnAdd").show();$("#btnDeleteRow").show();
        $('#ukertree').jstree(true).deselect_all();
        $('#ukertree').jstree(true).uncheck_all();
        $("#view-form").slideUp('fast');
    }

    function RefreshData() {
        load_url('<?php echo site_url('periode/pagging/0') ?>','<?php echo (isset($order)?$order:'idbln')?>','<?php echo (isset($typeorder)?$typeorder:'sorting')?>');
    }

    function saveForm() {
        if (($("#txt1").val() != "")) {
            $('#txt5').val($('#ukertree').jstree(true).get_selected());
            $.ajax({
                url: '<?php echo site_url('periode/save');?>', dataType: 'json', type: 'POST', data: $("#inputForm").serialize(), success: function (data) {
                    if (data.status != "error") {
                        bootbox.alert(data.msg, function () {
                            $("#btnAdd").show();$("#btnDeleteRow").show();
                            $("#view-form").slideUp('fast');
                            $("#list-data").slideDown('fast');
                            RefreshData();
                        });
                    } else {
                        bootbox.alert(data.msg);
                    }
                }
            });
        } else {
            bootbox.alert("Harap cek kembali inputannya ??");
        }
    }


</script>