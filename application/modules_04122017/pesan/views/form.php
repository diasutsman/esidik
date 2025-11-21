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
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="lstusr">Untuk</label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <?php
                        $js = 'id="lstusr" class="input-sm form-control chosen-select" data-placeholder="Pilih user..."';
                        $selected = "";
                        echo form_multiselect('lstusr[]',$lstusr,$selected,$js);
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt1">Judul Pesan</label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input id="txt1" name="txt1" type="text" placeholder="Judul Pesan" class="form-control col-md-5 col-xs-12">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt2">Isi Pesan</label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <textarea id="txt2" class="form-control input-sm" name="txt2" placeholder="Isi Pesan" rows="10" cols="40"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="lstsifat"></label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <?php
                        $js = 'id="lstsifat" class="input-sm form-control chosen-select" name="lstsifat" data-placeholder="State..."';
                        $selected = "";
                        echo form_dropdown('lstsifat',$lstsifat,$selected,$js);
                        ?>
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
                    required: true
                }, lstusr: {
                    required: true
                }, lstsifat: {
                    required: true
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
        var validator = $("#inputForm").validate();
        validator.resetForm();
        $("#btnAdd").show();
        $("#view-form").slideUp('fast');
    }

    function RefreshData() {
        load_url('<?php echo site_url('pesan/pagging/0') ?>', "#list-data");
    }

    function saveForm() {
        if (($("#txt1").val() != "")) {
            $.ajax({
                url: '<?php echo site_url('pesan/save');?>', dataType: 'json', type: 'POST', data: $("#inputForm").serialize(), success: function (data) {
                    if (data.status != "error") {
                        bootbox.alert(data.msg, function () {
                            $("#btnAdd").show();
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