<?php
/**
 * File: list.php
 * Author: abdiIwan.
 * Date: 12/28/2016
 * Time: 9:38 PM
 * absensi.kemendagri.go.id
 */

$url_pag = site_url("setdev/pagging/0");

?>
<link href="<?php echo base_url() ?>assets/css/plugins/datepicker/datepicker3.css" rel="stylesheet">
<script src="<?php echo base_url() ?>assets/js/plugins/datapicker/bootstrap-datepicker.js"></script>
<script src="<?php echo base_url() ?>assets/js/plugins/datapicker/bootstrap-datepicker.id.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/plugins/jsTree/jstree.min.js"></script>
<link href="<?php echo base_url() ?>assets/css/plugins/jsTree/themes/proton/style.min.css" rel="stylesheet">
<div class="row">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>Daftar Mesin FP</h5>
            <div class="ibox-tools">
                <a class="collapse-link">
                    <i class="fa fa-chevron-up"></i>
                </a>
            </div>
        </div>
        <div class="ibox-content">
            <div class="row">
                <div class="col-sm-5 m-b-xs">
                    <div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                            Opsi <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="#" id="btn1" onClick="showFormUbah();">Ubah Data</a>
                            </li>
                            <li><a href="#" id="btn2" onClick="showSNForm();">Ganti SN</a>
                            </li>
                            <li><a href="#" id="btn3" onClick="showMoveArea();">Area Mesin</a>
                            </li>
                            <li><a href="#" id="btn4" onClick="showUnit();">Unit Kerja</a>
                            </li>
                            <li><a href="#" onClick="doSynMesin();">Sinkronisasi</a>
                            </li>
                            <li><a href="#" id="btn5" onClick="doInfoMesin();">Informasi Mesin</a>
                            </li>
                            <li><a href="#" id="btn6" onClick="doReboot();">Reboot</a>
                            </li>
                            <li class="dropdown">
                                <a href="javascript:;" class="dropdown" data-toggle="dropdown">Penghapusan <b
                                            class="fa fa-caret-right"></b></a>
                                <ul class="dropdown-menu">
                                    <li><a href="#" id="btn7" onClick="doDelete();">Hapus Mesin</a>
                                    </li>
                                    <li><a href="#" id="btn8" onClick="doDeleteAll();">Hapus Semua Log Mesin</a>
                                    </li>
                                    <li><a href="#" id="btn9" onClick="doShowDate();">Hapus Transaksi Log Mesin</a>
                                    </li>
                                </ul>
                            </li>
                            <li><a href="#" id="btn10" onclick="doTransUsb()">Unggah transaksi dari USB</a>
                            </li>
                        </ul>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                            Unduh <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="#" id="btn11" onclick="doTransMesin()">Transaksi</a>
                            </li>
                            <li><a href="#" id="btn12" onclick="doTransUser()">Pegawai</a>
                            </li>
                            <li><a href="#" id="btn13" onclick="doTransPict()">Foto</a>
                            </li>

                        </ul>
                    </div>
                </div>
                <div class="col-sm-4 m-b-xs ">
                </div>
                <div class="col-sm-3">
                    <div class="input-group">
                        <input type="text" id="caridata" name="caridata" placeholder="Pencarian"
                               class="input-sm form-control" value="<?php echo isset($caridata) ? $caridata : '' ?>">
                        <span class="input-group-btn"><button type="button" id="btncari" class="btn btn-sm btn-primary"> <i
                                        class="fa fa-search"></i> </button> </span>
                    </div>
                </div>
            </div>
            <div id="list-data">
                <?php include("list.php") ?>
            </div>
            <div id="list-data-temp">
                <?php include("list2.php") ?>
            </div>
        </div>
    </div>
</div>

<form class="form-horizontal form-label-left" name="snForm" id="snForm" method="post">
    <div class="popup-wrapper" id="popup">
        <div class="popup-container">
            <div class="modal-header">
                <span type="button" class="close" data-dismiss="modal" onClick="hidePopup('#popup');">&times;</span>
                <h3>Penggantian SN Mesin</h3>
            </div>
            <div class="modal-body">
                <input name="idold" value="" id="idold" type="hidden">
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt1">SN Baru</label>
                    <div class="col-md-5 col-sm-5 col-xs-12">
                        <input id="txt1" name="txt1" type="text" placeholder="Serial number"
                               class="form-control col-md-5 col-xs-12">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary" onClick="hidePopup('#popup');">Batal</button>
                <button type="button" class="btn btn-sm btn-success" data-dismiss="modal" onClick="saveSNForm();">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</form>

<form class="form-horizontal form-label-left" name="areaForm" id="areaForm" method="post">
    <div class="popup-wrapper" id="popup2">
        <div class="popup-container">
            <div class="modal-header">
                <span type="button" class="close" data-dismiss="modal" onClick="hidePopup('#popup2');">&times;</span>
                <h3>Penggantian Area Mesin</h3>
            </div>
            <input name="idarea" value="" id="idarea" type="hidden">
            <div class="modal-body">
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt1">Area Mesin</label>
                    <div class="col-md-5 col-sm-5 col-xs-12">
                        <?php createArea() ?>
                        <input type="hidden" name="area" id="area"/>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary" onClick="hidePopup('#popup2');">Batal</button>
                <button type="button" class="btn btn-sm btn-success" data-dismiss="modal" onClick="saveAreaForm();">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</form>

<form class="form-horizontal form-label-left" name="delTglForm" id="delTglForm" method="post">
    <div class="popup-wrapper" id="popup3">
        <div class="popup-container">
            <div class="modal-header">
                <span type="button" class="close" data-dismiss="modal" onClick="hidePopup('#popup3');">&times;</span>
                <h3>Menghapus Transaksi Mesin</h3>
            </div>
            <input name="idmesin" value="" id="idmesin" type="hidden">
            <div class="modal-body">
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt1">Tanggal</label>
                    <div class="col-md-5 col-sm-5 col-xs-12">
                        <div class="input-group date">
                            <input class="form-control" placeholder="Tanggal" name="deltanggal"
                                   value="<?php echo ymdTodmy(date('Y-m-d')) ?>" type="text"><span
                                    class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary" onClick="hidePopup('#popup3');">Batal</button>
                <button type="button" class="btn btn-sm btn-success" data-dismiss="modal" onClick="doDeleteDate();">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</form>

<form class="form-horizontal form-label-left" name="unitForm" id="unitForm" method="post">
    <div class="popup-wrapper" id="popup4">
        <div class="popup-container">
            <div class="modal-header">
                <span type="button" class="close" data-dismiss="modal" onClick="hidePopup('#popup4');">&times;</span>
                <h3>Men-seting Unit Kerja Mesin</h3>
            </div>
            <input name="idUnitKerja" value="" id="idUnitKerja" type="hidden">
            <div class="modal-body">
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt1">Unit Kerja</label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                        <div class="unker input-group">
                            <input type="text" class="input-sm form-control" readonly name="cari_unker" id="cari_unker"
                                   value="" placeholder="Unit Kerja ...">
                            <input type="hidden" name="unit_search" id="unit_search" value="">
                            <div class="input-group-btn">
                                <button class="btn btn-white btn-sm" type="button"><span class="caret"></span></button>
                            </div>
                        </div>
                        <div class="panel combo-p" style="position: absolute;  display: none;">
                            <div class="combo-panel panel-body panel-body-noheader" title=""
                                 style="max-height:250px; padding:5px;overflow-y:auto">
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary" onClick="hidePopup('#popup4');">Batal</button>
                <button type="button" class="btn btn-sm btn-success" data-dismiss="modal" onClick="saveUnitForm();">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</form>

<form class="form-horizontal form-label-left" name="TmpForm" id="TmpForm" method="post">
    <div class="popup-wrapper" id="popup5">
        <div class="popup-container">
            <div class="modal-header">
                <span type="button" class="close" data-dismiss="modal" onClick="hidePopup('#popup5');">&times;</span>
                <h3>Simpan Mesin Sementara</h3>
            </div>
            <input name="idtmp" value="" id="idtmp" type="hidden">
            <div class="modal-body">
                <div class="row">
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt1">Delay Error</label>
                        <div class="col-md-2 col-sm-2 col-xs-12">
                            <div class="input-group spinner">
                                <input type="text" class="form-control" value="60"  id="txt1"  name="delayerror">
                                <div class="input-group-btn-vertical">
                                    <button class="btn btn-default" type="button"><i class="fa fa-caret-up"></i></button>
                                    <button class="btn btn-default" type="button"><i class="fa fa-caret-down"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt2">Delay</label>
                        <div class="col-md-2 col-sm-2 col-xs-12">
                            <div class="input-group spinner2">
                                <input type="text" class="form-control" value="30" id="txt2" name="delay">
                                <div class="input-group-btn-vertical">
                                    <button class="btn btn-default" type="button"><i class="fa fa-caret-up"></i></button>
                                    <button class="btn btn-default" type="button"><i class="fa fa-caret-down"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt1">TimeZone</label>
                        <div class="col-md-2 col-sm-2 col-xs-12">
                            <select class="form-control" name="timezone">
                                <?php foreach(tz_list() as $t) {
                                    $selc = $t==7 ? "selected":"";
                                    ?>
                                    <option value="<?php echo $t ?>" <?php echo $selc ?>>
                                        <?php echo $t?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt1">Area Mesin</label>
                        <div class="col-md-5 col-sm-5 col-xs-12">
                            <?php createArea("areatmp") ?>
                            <input type="hidden" name="idareatmp" id="idareatmp"/>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary" onClick="hidePopup('#popup5');">Batal</button>
                <button type="button" class="btn btn-sm btn-success" data-dismiss="modal" onClick="saveTmpForm();">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</form>


<form class="form-horizontal form-label-left" name="usbForm" id="usbForm" method="post"  enctype="multipart/form-data" action="javascript:;" >
    <div class="popup-wrapper" id="popup6">
        <div class="popup-container">
            <div class="modal-header">
                <span type="button" class="close" data-dismiss="modal" onClick="hidePopup('#popup6');">&times;</span>
                <h3>Unggah data ke Transaksi</h3>
            </div>
            <input type="hidden" id="sn" name="sn">
            <div class="modal-body">
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt1">File</label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                        <input type="file" name="uploadfile" id="uploadfile">
                    </div>
                    <p class="help-block" id="infofile"></p>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary" onClick="hidePopup('#popup6');">Batal</button>
                <button type="button" class="btn btn-sm btn-success" data-dismiss="modal" onClick="saveUnggahForm();">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</form>

<form class="form-horizontal form-label-left" name="FormUbah" id="FormUbah" method="post">
    <div class="popup-wrapper" id="popup7">
        <div class="popup-container">
            <div class="modal-header">
                <span type="button" class="close" data-dismiss="modal" onClick="hidePopup('#popup7');">&times;</span>
                <h3>Ubah Data Mesin</h3>
            </div>
            <input name="iddata" value="0" id="iddata-ubah" type="hidden">
            <div class="modal-body">
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="alias-ubah">Nama Mesin</label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                        <input type="text" name="alias" id="alias-ubah" class="form-control col-md-9 col-xs-12">
                    </div>
					</div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="errdelay-ubah">Delay Error</label>
                        <div class="col-md-2 col-sm-2 col-xs-12">
                            <div class="input-group spinner">
                                <input type="text" class="form-control" value="60" id="errdelay-ubah"  name="delayerror">
                                <div class="input-group-btn-vertical">
                                    <button class="btn btn-default" type="button"><i class="fa fa-caret-up"></i></button>
                                    <button class="btn btn-default" type="button"><i class="fa fa-caret-down"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="delay-ubah">Delay</label>
                        <div class="col-md-2 col-sm-2 col-xs-12">
                            <div class="input-group spinner2">
                                <input type="text" class="form-control" value="30" id="delay-ubah" name="delay">
                                <div class="input-group-btn-vertical">
                                    <button class="btn btn-default" type="button"><i class="fa fa-caret-up"></i></button>
                                    <button class="btn btn-default" type="button"><i class="fa fa-caret-down"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="timezone-ubah">TimeZone</label>
                        <div class="col-md-2 col-sm-2 col-xs-12">
                            <select class="form-control" name="timezone" id="timezone-ubah">
                                <?php foreach(tz_list() as $t) {
                                    $selc = $t==7 ? "selected":"";
                                    ?>
                                    <option value="<?php echo $t ?>" <?php echo $selc ?>>
                                        <?php echo $t?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary" onClick="hidePopup('#popup7');">Batal</button>
                <button type="button" class="btn btn-sm btn-success" data-dismiss="modal" onClick="saveFormUbah();">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</form>

<script>


    $(document).ready(function () {

        /*$("#snForm").validate({
         rules: {
         txt1: {
         required: true, minlength: 1, maxlength: 255
         }
         }, submitHandler: function () {
         saveSNForm();
         }, highlight: function (element) {
         $(element).closest('.form-group').addClass('has-error');
         }, unhighlight: function (element) {
         $(element).closest('.form-group').removeClass('has-error');
         }
         });*/

        $('#caridata').keypress(function(e) {
            if ( e.keyCode == 13 ) {
                $('#btncari').click();
            }
        });

        $('.input-group.date').datepicker({
            keyboardNavigation: false,
            forceParse: false,
            autoclose: true,
            format: "dd-mm-yyyy",
            language: 'id'
        });

        $('.unker').click(function () {
            var inwidth = $(this).width();
            var dis = $('.panel').css("display");
            if (dis == 'none') {
                $('.panel').css({
                    display: 'block',
                    width: inwidth
                });

                //$('.combo-panel').empty();
                if ($('.combo-panel > *').length == 0) {
                    $('.combo-panel').html('Loading...........');
                    $.ajax({
                        url: '<?php echo site_url('ajax/getUnitKerja')?>',
                        dataType: 'html',
                        type: 'POST',
                        success: function (data) {
                            $('.combo-panel').html(data);
                        }
                    });
                }
            } else {
                //$('.combo-panel').empty();
                $('.panel').css({
                    display: 'none'
                });
                //$('.unker').data('clicked',1);
            }
        });

        $('#uploadfile').bind('change', function() {

            var nSize = this.files[0].size / 1024;
            if (nSize / 1024 > 1) {
                $("#infofile").append("File cukup besar, membutuhkan cukup lama untuk memprosesnya..");
            } else {
                $("#infofile").empty();
            }
        });

    });

    function showSNForm() {
        var cek = $(".selected:checked").length;
        if (cek > 0) {
            showPopup('#popup');
        } else {
            bootbox.alert("Harap pilih data..!");
        }
    }

    function saveSNForm() {
        if (($("#txt1").val() != "")) {
            var cek_del = $('input[name=cek_del]:checked').map(function () {
                return $(this).val();
            }).get();
            $("#idold").val(cek_del);
            $.ajax({
                url: '<?php echo site_url('setdev/rubahsn');?>',
                dataType: 'json',
                type: 'POST',
                data: $("#snForm").serialize(),
                success: function (data) {
                    if (data.status != "error") {
                        bootbox.alert(data.msg, function () {
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


    function showMoveArea() {
        var cek = $(".selected:checked").length;
        if (cek > 0) {
            showPopup('#popup2');

        } else {
            bootbox.alert("Harap pilih data..!");
        }
    }

    function showUnit() {
        var cek = $(".selected:checked").length;
        if (cek > 0) {
            showPopup('#popup4');

        } else {
            bootbox.alert("Harap pilih data..!");
        }
    }

    function saveAreaForm() {
        $('#area').val($('#areatree').jstree(true).get_selected());
        if (($("#area").val() != "")) {
            var cek_del = $('input[name=cek_del]:checked').map(function () {
                return $(this).val();
            }).get();
            $("#idarea").val(cek_del);
            $.ajax({
                url: '<?php echo site_url('setdev/rubaharea');?>',
                dataType: 'json',
                type: 'POST',
                data: $("#areaForm").serialize(),
                success: function (data) {
                    if (data.status != "error") {
                        bootbox.alert(data.msg, function () {
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


    function saveUnitForm() {

        var cek = $(".selected:checked").length;
        if (cek > 0) {
            if (($("#unit_search").val() != "")) {
                var cek_del = $('input[name=cek_del]:checked').map(function () {
                    return $(this).val();
                }).get();
                $("#idUnitKerja").val(cek_del);
                $.ajax({
                    url: '<?php echo site_url('setdev/rubahunit');?>',
                    dataType: 'json',
                    type: 'POST',
                    data: $("#unitForm").serialize(),
                    success: function (data) {
                        if (data.status != "error") {
                            bootbox.alert(data.msg, function () {
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
        } else {
            bootbox.alert("Harap pilih data..!");
        }
    }

    function doInfoMesin() {
        var cek = $(".selected:checked").length;
        if (cek > 0) {
            bootbox.confirm("Anda yakin mengambil informasi mesin?", function (result) {
                if (result) {
                    var cek_del = $('input[name=cek_del]:checked').map(function () {
                        return $(this).val();
                    }).get();

                    $.ajax({
                        url: '<?php echo site_url('setdev/fpinfo');?>',
                        dataType: 'json',
                        type: 'POST',
                        data: {'id': cek_del},
                        success: function (data) {
                            bootbox.alert(data.msg);
                        }
                    });
                }
            });
        }
        else {
            bootbox.alert("Harap pilih data..!");
        }
    }
    function doReboot() {
        var cek = $(".selected:checked").length;
        if (cek > 0) {
            bootbox.confirm("Anda yakin me-restart mesin?", function (result) {
                if (result) {
                    var cek_del = $('input[name=cek_del]:checked').map(function () {
                        return $(this).val();
                    }).get();

                    $.ajax({
                        url: '<?php echo site_url('setdev/reboot');?>',
                        dataType: 'json',
                        type: 'POST',
                        data: {'id': cek_del},
                        success: function (data) {
                            bootbox.alert(data.msg);
                        }
                    });

                }
            });

        } else {
            bootbox.alert("Harap pilih data..!");
        }
    }

    function doDelete() {
        var cek = $(".selected:checked").length;
        if (cek > 0) {
            bootbox.confirm("Anda yakin menghapus data yang dipilih?", function (result) {
                if (result) {
                    var cek_del = $('input[name=cek_del]:checked').map(function () {
                        return $(this).val();
                    }).get();

                    $.ajax({
                        url: '<?php echo site_url('setdev/hapus');?>',
                        dataType: 'json',
                        type: 'POST',
                        data: {'id': cek_del},
                        success: function (data) {

                            bootbox.alert(data.msg);
                            RefreshData();
                        }
                    });

                }
            });
        } else {
            bootbox.alert("Harap pilih data yang akan di hapus!");
        }
    }

    function doSynMesin() {
        var cek = $(".selected:checked").length;
        if (cek > 0) {
            bootbox.confirm("Anda yakin me-sinkron data ke mesin?", function (result) {
                if (result) {
                    var cek_del = $('input[name=cek_del]:checked').map(function () {
                        return $(this).val();
                    }).get();

                    $.ajax({
                        url: '<?php echo site_url('setdev/synkronisasi');?>',
                        dataType: 'json',
                        type: 'POST',
                        data: {'id': cek_del},
                        success: function (data) {
                            bootbox.alert(data.msg);
                        }
                    });

                }
            });
        } else {
            bootbox.alert("Harap pilih data yang akan di sinkron kan!");
        }
    }

    function doTransMesin() {
        var cek = $(".selected:checked").length;
        if (cek > 0) {
            bootbox.confirm("Anda yakin mengambil transaksi dari mesin?", function (result) {
                if (result) {
                    var cek_del = $('input[name=cek_del]:checked').map(function () {
                        return $(this).val();
                    }).get();

                    $.ajax({
                        url: '<?php echo site_url('setdev/transakinfo');?>',
                        dataType: 'json',
                        type: 'POST',
                        data: {'id': cek_del},
                        success: function (data) {
                            bootbox.alert(data.msg);
                        }
                    });
                }
            });
        } else {
            bootbox.alert("Harap pilih data..!");
        }
    }

    function doTransUser() {
        var cek = $(".selected:checked").length;
        if (cek > 0) {
            bootbox.confirm("Anda yakin mengambil data pegawai dari mesin?", function (result) {
                if (result) {
                    var cek_del = $('input[name=cek_del]:checked').map(function () {
                        return $(this).val();
                    }).get();

                    $.ajax({
                        url: '<?php echo site_url('setdev/transakuser');?>',
                        dataType: 'json',
                        type: 'POST',
                        data: {'id': cek_del},
                        success: function (data) {
                            bootbox.alert(data.msg);
                        }
                    });
                }
            });
        } else {
            bootbox.alert("Harap pilih data..!");
        }
    }

    function doTransUsb()
    {
        var cek = $(".selected:checked").length;
        if (cek > 0) {
            var $el = $('#uploadfile');
            $el.wrap('<form>').closest('form').get(0).reset();
            $el.unwrap();
            $("#infofile").empty();
            showPopup('#popup6');

        } else {
            bootbox.alert("Harap pilih data..!");
        }
    }
    function saveUnggahForm()
    {
        //var formData = new FormData($("#usbForm")[0]);
        //var formData = $("#usbForm").serialize();

        var data = new FormData();
        jQuery.each(jQuery('#uploadfile')[0].files, function(i, file) {
            data.append('file-'+i, file);
        });

        var cek = $(".selected:checked").length;
        if (cek > 0) {
            bootbox.confirm("Anda yakin memasukkan data pegawai ke transaksi data?", function (result) {
                if (result) {
                    var cek_del = $('input[name=cek_del]:checked').map(function () {
                        return $(this).val();
                    }).get();
                    data.append('usrSN', cek_del);
                    $.ajax({
                        url: '<?php echo site_url('setdev/uploadfile');?>',
                        dataType: 'json',
                        type: 'POST',
                        data: data,
                        async: false,
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function (data) {
                            var $el = $('#uploadfile');
                            $el.wrap('<form>').closest('form').get(0).reset();
                            $el.unwrap();
                            $("#infofile").empty();
                            bootbox.alert(data.msg);
                            hidePopup('#popup6');
                            RefreshData();
                        }
                    });
                }
            });
        } else {
            bootbox.alert("Harap pilih data..!");
        }
    }

    function doTransPict() {
        var cek = $(".selected:checked").length;
        if (cek > 0) {
            bootbox.confirm("Anda yakin mengambil data poto pegawai dari mesin?", function (result) {
                if (result) {
                    var cek_del = $('input[name=cek_del]:checked').map(function () {
                        return $(this).val();
                    }).get();

                    $.ajax({
                        url: '<?php echo site_url('setdev/transakfoto');?>',
                        dataType: 'json',
                        type: 'POST',
                        data: {'id': cek_del},
                        success: function (data) {
                            bootbox.alert(data.msg);
                            RefreshData();
                        }
                    });
                }
            });
        } else {
            bootbox.alert("Harap pilih data..!");
        }
    }


    function doDeleteAll() {
        var cek = $(".selected:checked").length;
        if (cek > 0) {
            bootbox.confirm("Anda yakin menghapus semua data di mesin?", function (result) {
                if (result) {
                    var cek_del = $('input[name=cek_del]:checked').map(function () {
                        return $(this).val();
                    }).get();

                    $.ajax({
                        url: '<?php echo site_url('setdev/transakdelalllog');?>',
                        dataType: 'json',
                        type: 'POST',
                        data: {'id': cek_del},
                        success: function (data) {
                            bootbox.alert(data.msg);
                            RefreshData();
                        }
                    });
                }
            });
        } else {
            bootbox.alert("Harap pilih data..!");
        }
    }

    function doShowDate() {
        var cek = $(".selected:checked").length;
        if (cek > 0) {
            showPopup('#popup3');
        } else {
            bootbox.alert("Harap pilih data..!");
        }
    }

    function saveTmpForm() {
        $('#idareatmp').val($('#areatmp').jstree(true).get_selected());
        if (($("#idareatmp").val() != "")) {
            var cek_del = $('input[name=cek_lst]:checked').map(function () {
                return $(this).val();
            }).get();
            $("#idtmp").val(cek_del);
            $.ajax({
                url: '<?php echo site_url('setdev/simpantmp');?>',
                dataType: 'json',
                type: 'POST',
                data: $("#TmpForm").serialize(),
                success: function (data) {
                    if (data.status != "error") {
                        bootbox.alert(data.msg, function () {
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

    function doDeleteDate() {
        var cek = $(".selected:checked").length;
        if (cek > 0) {
            bootbox.confirm("Anda yakin menghapus data di mesin?", function (result) {
                if (result) {
                    var cek_del = $('input[name=cek_del]:checked').map(function () {
                        return $(this).val();
                    }).get();
                    $("#idmesin").val(cek_del);
                    $.ajax({
                        url: '<?php echo site_url('setdev/transakdeldatelog');?>',
                        dataType: 'json',
                        type: 'POST',
                        data: $("#snForm").serialize(),
                        success: function (data) {
                            if (data.status != "error") {
                                bootbox.alert(data.msg, function () {
                                    RefreshData();
                                });
                            } else {
                                bootbox.alert(data.msg);
                            }
                        }
                    });
                }
            });
        } else {
            bootbox.alert("Harap pilih data..!");
        }
    }

    function showFormUbah() {
        var cek = $(".selected:checked").length;
        if (cek == 1) {
            var cek_del = $('input[name=cek_del]:checked').map(function () {
                return $(this).val();
            }).get();

            $.getJSON('<?php echo site_url('setdev/edit')?>/'+cek_del,function() {})
                .done(function(response) {
                    $("#iddata-ubah").val(cek_del);
                    $('#alias-ubah').val(response['alias']);
                    $("#errdelay-ubah").val(response['errdelay']);
                    $("#delay-ubah").val(response['delay']);
                    $("#timezone-ubah").val(response['timezone']);
                });

            showPopup('#popup7');
        } else {
            bootbox.alert("Harap pilih data atau Silakan dipilih satu data..!");
        }
    }

    function saveFormUbah() {
            $.ajax({
                url: '<?php echo site_url('setdev/simpan');?>',
                dataType: 'json',
                type: 'POST',
                data: $("#FormUbah").serialize(),
                success: function (data) {
                    if (data.status != "error") {
                        bootbox.alert(data.msg, function () {
                            RefreshData();
                        });
                    } else {
                        bootbox.alert(data.msg);
                    }
                }
            });
    }
</script>
