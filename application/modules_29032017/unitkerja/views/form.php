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
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt1">Kode Unit Kerja</label>
                    <div class="col-md-3 col-sm-3 col-xs-12">
                        <input id="txt1" name="txt1" type="text" placeholder="Kode Unit Kerja" class="form-control col-md-5 col-xs-12">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt2">Nama Unit Kerja</label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input id="txt2" name="txt2" type="text" placeholder="Nama Unit Kerja" class="form-control col-md-5 col-xs-12">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt2">Unit Kerja Induk</label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="unker input-group">
                            <input type="text" class="input-sm form-control" readonly name="cari_unker" id="cari_unker" value="" placeholder="Unit Kerja ...">
                            <input type="hidden" name="unit_search" id="unit_search" value="">
                            <div class="input-group-btn">
                                <button class="btn btn-white btn-sm" type="button"><span class="caret"></span></button>
                            </div>
                        </div>
                        <div class="panel combo-p" style="position: absolute;  z-index:110003; display: none;">
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
                    required: true, digits: true, minlength: 1, maxlength: 15
                }, txt2: {
                    required: true, minlength: 1, maxlength: 255
                }
            }, submitHandler: function () {
                saveForm();
            }, highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error');
            }, unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error');
            }
        });

        $('.unker').click(function () {
            var inwidth = $(this).width();
            var dis = $('.panel').css("display");
            if (dis == 'none') {
                $('.panel').css({
                    display: 'block', width: inwidth
                });

                //$('.combo-panel').empty();
                if ($('.combo-panel > *').length == 0) {
                    $('.combo-panel').html('Loading...........');
                    $.ajax({
                        url: '<?php echo site_url('ajax/getUnitKerja')?>', dataType: 'html', type: 'POST', success: function (data) {
                            $('.combo-panel').html(data);
                        }
                    });
                }
            } else {
                // $('.combo-panel').empty();
                $('.panel').css({
                    display: 'none'
                });
                //$('.unker').data('clicked',0);
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
        load_url('<?php echo site_url('unitkerja/pagging/0') ?>', "<?php echo(isset($order) ? $order : 'a.id')?>", "<?php echo(isset($typeorder) ? $typeorder : 'sorting')?>");
    }

    function saveForm() {
        if (($("#txt1").val() != "")) {
            $.ajax({
                url: '<?php echo site_url('unitkerja/save');?>', dataType: 'json', type: 'POST', data: $("#inputForm").serialize(), success: function (data) {
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