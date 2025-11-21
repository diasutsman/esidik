<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Author       : _abdi_iwan_
 * Project         :
 */
?>

<script src="<?php echo base_url()?>assets/js/plugins/datapicker/bootstrap-datepicker.js"></script>
<script src="<?php echo base_url()?>assets/js/plugins/datapicker/bootstrap-datepicker.id.min.js"></script>
<script src="<?php echo base_url()?>assets/js/plugins/daterangepicker/daterangepicker.js"></script>

<div id="view-form" style="display:none">
    <div class="ibox float-e-margins ">
        <div class="ibox-content gray-bg">
            <form class="form-horizontal form-label-left" name="inputForm" id="inputForm" method="post">
                <input type="hidden" name="iddata" id="iddata"/>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt1">Kelas</label>
                    <div class="col-md-3 col-sm-3 col-xs-12">
                        <input id="txt1" name="txt1" type="text" placeholder="Kelas" class="form-control col-md-5 col-xs-12">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt2">Tunjangan</label>
                    <div class="col-md-3 col-sm-3 col-xs-12">
                        <input id="txt2" name="txt2" type="text" placeholder="Tunjangan" class="form-control col-md-5 col-xs-12">
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="tmt">TMT Berlaku</label>
                    <div class="col-md-3 col-sm-3 col-xs-12" >
                        <div class="input-group date">
                            <input class="form-control" placeholder="TMT" name="tmt" type="text" id="tmt"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
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
                    required: true, digits: true, minlength: 1, maxlength: 15
                }, txt2: {
                    required: true, digits: true, minlength: 1, maxlength: 15
                }
            }, submitHandler: function () {
                saveForm();
            }, highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error');
            }, unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error');
            }
        });

        $('.input-group.date').datepicker({
            keyboardNavigation: false,
            forceParse: false,
            autoclose: true,
            format:"dd-mm-yyyy",
            language: 'id'
        });


    });

    function Batal() {
        $("#iddata").val("");
        var validator = $("#inputForm").validate();
        validator.resetForm();
        $("#btnAdd").show();
        $("#btnDeleteRow").show();
        $("#view-form").slideUp('fast');
    }

    function RefreshData() {
        load_url('<?php echo site_url('mtunkir/pagging/0') ?>', '<?php echo (isset($order)?$order:'kelasjabatan')?>','<?php echo (isset($typeorder)?$typeorder:'sorting')?>');
    }

    function saveForm() {
        if (($("#txt1").val() != "")) {
            $.ajax({
                url: '<?php echo site_url('mtunkir/save');?>', dataType: 'json', type: 'POST', data: $("#inputForm").serialize(), success: function (data) {
                    if (data.status != "error") {
                        bootbox.alert(data.msg, function () {
                            $("#btnAdd").show();
                            $("#btnDeleteRow").show();

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