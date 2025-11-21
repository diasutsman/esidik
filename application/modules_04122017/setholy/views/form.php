<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Author       : _abdi_iwan_
 * Project         :
 */
?>
<link href="<?php echo base_url()?>assets/css/plugins/datepicker/datepicker3.css" rel="stylesheet">
<link href="<?php echo base_url()?>assets/css/plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet">
<script src="<?php echo base_url()?>assets/js/plugins/datapicker/bootstrap-datepicker.js"></script>
<script src="<?php echo base_url()?>assets/js/plugins/daterangepicker/daterangepicker.js"></script>
<div id="view-form" style="display:none">
    <div class="ibox float-e-margins ">
        <div class="ibox-content gray-bg">
            <form class="form-horizontal form-label-left" name="inputForm" id="inputForm" method="post">
                <input type="hidden" name="iddata" id="iddata"/>
                <div class="form-group form-inline" id="data_5">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt1">Tanggal</label>
                    <div class="col-md-4 col-sm-4 col-xs-12 input-daterange input-group" id="datepicker" style="padding-left: 15px">
                        <input type="text" class="input-sm form-control" name="start" id="txt1" value="<?php echo date("d-m")?>" required/>
                        <span class="input-group-addon">s/d</span>
                        <input type="text" class="input-sm form-control" name="end" id="txt2" value="<?php echo date("d-m")?>" required/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt3">Keterangan</label>
                    <div class="col-md-7 col-sm-7 col-xs-12">
                        <input id="txt3" name="txt3" type="text" placeholder="Keterangan" class="form-control col-md-5 col-xs-12" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="cari_unker">Unit Kerja</label>
                    <div class="col-md-7 col-sm-7 col-xs-12">
                        <div class="unker input-group">
                            <input type="text" class="input-sm form-control" readonly name="cari_unker" id="cari_unker" value="" placeholder="Unit Kerja ...">
                            <input type="hidden" name="unit_search" id="unit_search" value="">
                            <div class="input-group-btn">
                                <button class="btn btn-white btn-sm" type="button"><span class="caret"></span></button>
                            </div>
                        </div>
                        <div class="panel combo-p" style="position: absolute;  z-index: 110003; display: none;">
                            <div class="combo-panel panel-body panel-body-noheader" title="" style="max-height:250px; padding:5px;overflow-y:auto">
                            </div>
                        </div>
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
                    required: true, minlength: 1, maxlength: 15
                }, txt2: {
                    required: true, minlength: 1, maxlength: 15
                }, txt3: {
                    required: true, minlength: 1, maxlength: 200
                }, unit_search: {
                    required: true, minlength: 1, maxlength: 15
                }
            }, submitHandler: function () {
                saveForm();
            }, highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error');
            }, unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error');
            }
        });

        $('.unker').click(function(){
            var inwidth = $(this).width();
            var dis = $('.panel').css("display");
            if(dis=='none'){
                $('.panel').css({
                    display : 'block',
                    width : inwidth
                });

               // $('.combo-panel').empty();
                if ( $('.combo-panel > *').length == 0 ) {
                    $('.combo-panel').html('Loading...........');
                    $.ajax({
                        url: '<?php echo site_url('ajax/getUnitKerja')?>', dataType: 'html', type: 'POST', success: function (data) {
                            $('.combo-panel').html(data);
                        }
                    });
                }
            }else{
                //$('.combo-panel').empty();
                $('.panel').css({
                    display : 'none'
                });
                //$('.unker').data('clicked',0);
            }
        });

        $('#data_5 .input-daterange').datepicker({
            keyboardNavigation: false,
            forceParse: false,
            autoclose: true,
            format:"dd-mm",
            language: 'id'
        });

    });

    function Batal() {
        $("#iddata").val("");
        var validator = $("#inputForm").validate();
        validator.resetForm();
        $("#btnAdd").show();$("#btnDeleteRow").show();
        $("#view-form").slideUp('fast');
    }

    function RefreshData() {
        load_url('<?php echo site_url('setholy/pagging/0') ?>',"<?php echo (isset($order)?$order:'a.startdate')?>","<?php echo (isset($typeorder)?$typeorder:'sorting_desc')?>");
    }

    function saveForm() {
        if (($("#txt1").val() != "")) {
            $.ajax({
                url: '<?php echo site_url('setholy/save');?>', dataType: 'json', type: 'POST', data: $("#inputForm").serialize(), success: function (data) {
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