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
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt1">User Name</label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input id="txt1" name="txt1" type="text" placeholder="User Name" class="form-control col-md-5 col-xs-12">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt2">Email</label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input id="txt2" name="txt2" type="text" placeholder="Email" class="form-control col-md-5 col-xs-12">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt3">NIP</label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input id="txt3" name="txt3" type="text" placeholder="NIP/UserID" class="form-control col-md-5 col-xs-12">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="level">Grup Pengguna</label>
                    <div class="col-md-4 col-sm-4 col-xs-12">
                        <?php echo form_dropdown("level", $lstLevel, null, "id='level' class='form-control input-sm'"); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt4">Unit Kerja</label>
                    <div class="col-md-4 col-sm-4 col-xs-12">
                        <?php createUnitKerjaCheckbox() ?>
                        <input type="hidden" name="txt4" id="txt4"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt5">Area Mesin</label>
                    <div class="col-md-4 col-sm-4 col-xs-12">
                        <?php createAreaCheckbox() ?>
                        <input type="hidden" name="txt5" id="txt5"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt6">Status</label>
                    <div class="col-md-3 col-sm-3 col-xs-12">
                        <label class="radio-inline"> <input checked value="1" id="txt6" name="status" type="radio"> Aktif </label>
                        <label class="radio-inline"> <input value="0" id="txt7" name="status" type="radio"> Tidak Aktif </label>
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
                    required: true, minlength: 1, maxlength: 50
                }
            }, submitHandler: function () {
                saveForm();
            }, highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error');
            }, unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error');
            }
        });
    });

    function Batal() {
        $("#iddata").val("");
        $('#ukertree').jstree(true).deselect_all();
        $('#areatree').jstree(true).deselect_all();
        $('#areatree').jstree(true).uncheck_all();
        $('#ukertree').jstree(true).uncheck_all();
        var validator = $("#inputForm").validate();
        validator.resetForm();
        $("#btnAdd").show();$("#btnDeleteRow").show();
        $("#view-form").slideUp('fast');
    }

    function RefreshData() {
        load_url('<?php echo site_url('usrdata/pagging/0') ?>', '<?php echo(isset($order) ? $order : 'id')?>', '<?php echo(isset($typeorder) ? $typeorder : 'sorting')?>');
    }

    function saveForm() {
        if (($("#txt1").val() != "")) {
            $('#txt4').val($('#ukertree').jstree("get_checked", null, true));
            $('#txt5').val($('#areatree').jstree("get_checked", null, true));

                $.ajax({
                url: '<?php echo site_url('usrdata/save');?>', dataType: 'json', type: 'POST', data: $("#inputForm").serialize(), success: function (data) {
                    if (data.status != "error") {
                        bootbox.alert(data.msg, function () {
                            $("#btnAdd").show(); $("#btnDeleteRow").show();
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