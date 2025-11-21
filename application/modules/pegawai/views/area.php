<script src="<?php echo base_url() ?>assets/js/plugins/jsTree/jstree.min.js"></script>
<link href="<?php echo base_url() ?>assets/css/plugins/jsTree/themes/proton/style.min.css" rel="stylesheet">
<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


?>
<div class="row">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>Data Pegawai</h5>
            <div class="ibox-tools">
                <a class="collapse-link">
                    <i class="fa fa-chevron-up"></i>
                </a>
            </div>
        </div>
        <div class="ibox-content">
            <div class="ibox float-e-margins ">
                <div class="ibox-content gray-bg">
                    <form class="form-horizontal form-label-left" name="inputForm" id="inputForm" method="post">
                        <input type="hidden" name="iddata" id="iddata" value="<?php echo $id?>"/>
                        <input type="hidden" name="area" id="area"/>
                        <div class="form-group">
                            <label class="control-label col-md-2 col-sm-2 col-xs-12" >NIP</label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <p class="form-control-static font-bold"><?php echo $field["userid"];?></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2 col-sm-2 col-xs-12" >Nama</label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <p class="form-control-static font-bold"><?php echo $field["name"];?></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2 col-sm-2 col-xs-12" >Area</label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <p class="form-control-static font-bold"><?php echo $areaOld;?></p>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="control-label col-md-2 col-sm-2 col-xs-12" >Area Mesin</label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <?php createArea() ?>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-12">
                                <div class="alert alert-info">
                                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                    <strong>Info!</strong> Gunakan tombol <strong>Ctrl</strong> <i>(Kontrol)</i> untuk memilih beberapa item.
                                </div>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-2">
                                <input type="button" id="" name="" onclick="saveForm()" class="btn btn-primary btn-sm" value="Simpan"/>
                                <a href="<?php echo site_url("pegawai")?>" class="btn btn-danger btn-sm">Batal</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var areaUsr = '<?php echo $listAreaUser?>';
        var mySplitResult = areaUsr.split(',');
        for (i = 0; i < mySplitResult.length; i++) {
            $('#areatree').jstree(true).select_node(mySplitResult[i]);
        }

    });

    function saveForm() {
        $('#area').val($('#areatree').jstree(true).get_selected());

        if (($("#area").val() != "")) {
            bootbox.confirm("Anda yakin menyimpan Area mesin untuk pegawai tersebut", function (result) {
                if (result) {
                    $.ajax({
                        url: '<?php echo site_url('pegawai/simpanarea');?>',
                        dataType: 'json',
                        type: 'POST',
                        data: $("#inputForm").serialize(),
                        success: function (data) {
                            bootbox.alert(data.msg);
                        }
                    });
                }
            });
        } else {
            bootbox.alert("Harap cek kembali Area yang dipilih!!");
        }

    }


</script>