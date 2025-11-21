<?php
/**
 * File: list.php
 * Author: abdiIwan.
 * Date: 12/28/2016
 * Time: 9:38 PM
 * absensi.kemendagri.go.id
 */

$url_pag =  site_url("pegawai/pagging/0");

?>

<link href="<?php echo base_url()?>assets/css/plugins/chosen/bootstrap-chosen.css" rel="stylesheet">
<script src="<?php echo base_url()?>assets/js/plugins/chosen/chosen.jquery.js"></script>
<script src="<?php echo base_url() ?>assets/js/plugins/jsTree/jstree.min.js"></script>
<link href="<?php echo base_url() ?>assets/css/plugins/jsTree/themes/proton/style.min.css" rel="stylesheet">
<div class="row">
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5>Daftar Pegawai</h5>
				<div class="ibox-tools">
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
				</div>
			</div>
			<div class="ibox-content">

				<div class="row">
					<!--<div class="col-sm-1 m-b-xs">
						<a id="btnAdd" class="btn btn-sm btn-success" href="<?php /*echo site_url("pegawai/form")*/?>"><i class="fa fa-plus"></i> Tambah</a>
					</div>-->
                    <div class="col-sm-1 m-b-xs">
                        <div class="btn-group">
                            <?php if ($this->session->userdata('s_access')=="1") {?>
                            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                                Opsi <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="#" id="btn1" onclick="doDeleteFP()">Hapus FP</a>
                                </li>
                                <li><a href="#" id="btn2" onclick="doMoveArea()">Pindah Area</a>
                                </li>
                                <li><a href="#" id="btn3" onclick="doUploadData()">Unggah Data</a>
                                </li>
                                <li><a href="#" id="btn3" onclick="doCopyData()">Daftarkan FP Ke Mesin</a>
                                </li>
                                <li><a href="#" id="btn3" onclick="doSinkronData()">Sinkronisasi dengan SIMPEG</a>
                                </li>
                                <!--<li><a href="#" id="btn3" onclick="doEnrolFP()">Mendaftarkan FP</a>
                                </li>-->
                            </ul>
                            <?php } ?>
                        </div>
                    </div>
					<div class="col-sm-3 m-b-xs">
						<?php
						$js = 'id="stspeg" class="input-sm form-control chosen-select" name="stspeg" data-placeholder="Pilih Status Pegawai..."';
						$selected = array("1","2");
						echo form_multiselect('stspeg',$lstStsPeg,$selected,$js);
						?>
					</div>
                    <div class="col-sm-3 m-b-xs">
                        <?php
                        $js = 'id="jnspeg" class="input-sm form-control chosen-select" name="jnspeg" data-placeholder="Pilih Jenis Pegawai..."';
                        $selected = array("1","2");
                        echo form_multiselect('jnspeg',$lstJnsPeg,$selected,$js);
                        ?>
                    </div>
					<div class="col-sm-3 m-b-xs">
						<div class="unker input-group">
							<input type="text" class="input-sm form-control" readonly name="cari_unker" id="cari_unker" value="" placeholder="Unit Kerja ...">
							<input type="hidden" name="unit_search" id="unit_search" value="">
							<div class="input-group-btn">
								<button class="btn btn-white btn-sm" type="button"><span class="caret"></span></button>
							</div>
						</div>
						<div class="panel combo-p" style="position: absolute;  display: none;">
							<div class="combo-panel panel-body panel-body-noheader" title="" style="max-height:250px; padding:5px;overflow-y:auto">
							</div>
						</div>
					</div>
					<div class="col-sm-2">
						<div class="input-group">
							<input type="text" id="caridata" name="caridata" placeholder="Pencarian" class="input-sm form-control" value="<?php echo isset($caridata)?$caridata:''?>">
							<span class="input-group-btn"><button type="button" id="btncari" class="btn btn-sm btn-primary"><i class="fa fa-search"></i> </button> </span>
						</div>
					</div>


				</div>
				<div id="list-data">
					<?php include ("list.php") ?>
				</div>
			</div>
		</div>
</div>

<form class="form-horizontal form-label-left" name="areaForm" id="areaForm" method="post">
    <div class="popup-wrapper" id="popup2">
        <div class="popup-container">
            <div class="modal-header">
                <span type="button" class="close" data-dismiss="modal" onClick="hidePopup('#popup2');">&times;</span>
                <h3>Penggantian Area Mesin Pegawai</h3>
            </div>
            <input name="idarea" value="" id="idarea" type="hidden">
            <div class="modal-body">
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt1">Area</label>
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

<form class="form-horizontal form-label-left" name="copyForm" id="copyForm" method="post">
    <div class="popup-wrapper" id="popup3">
        <div class="popup-container">
            <div class="modal-header">
                <span type="button" class="close" data-dismiss="modal" onClick="hidePopup('#popup3');">&times;</span>
                <h3>Mendaftarkan FP Ke Mesin</h3>
            </div>
            <div class="modal-body" style="overflow-y: auto;height: 250px;">
                <div class="form-group">

                </div>
                <div class="form-group">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <?php createListMesin() ?>
                        <input type="hidden" name="mesinid" id="mesinid"/>
                        <input type="hidden" name="useid" id="useid"/>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary" onClick="hidePopup('#popup3');">Batal</button>
                <button type="button" class="btn btn-sm btn-success" data-dismiss="modal" onClick="saveCopyForm();">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</form>

<script>
	$(function(){


		$('#cek_all').click(function(){
			$(".selected").prop("checked", $("#cek_all").prop("checked"));
		});

		$('.selected').click(function(){
			if($(".selected").length == $(".selected:checked").length) {
				$("#cek_all").prop("checked", true);
			} else {
				$("#cek_all").prop("checked", false);
			}
		});

        $('#caridata').keypress(function(e) {
            if ( e.keyCode == 13 ) {
                $('#btncari').click();
            }
        });

		$('#btncari').click(function(){
			load_url('<?php echo site_url('pegawai/pagging/0') ?>',"<?php echo (isset($order)?$order:'kelas')?>","<?php echo (isset($typeorder)?$typeorder:'sorting')?>");
		});

		$('.unker').click(function(){
			var inwidth = $(this).width();
			var dis = $('.panel').css("display");
			if(dis=='none'){
				$('.panel').css({
					display : 'block',
					width : inwidth
				});

				//$('.combo-panel').empty();
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
				//$('.unker').data('clicked',1);
			}
		});

		$('.chosen-select').chosen({width: "100%"});
	});

    function doDeleteFP() {
        var cek = $(".selected:checked").length;
        if (cek > 0) {
            bootbox.confirm("Anda yakin menghapus FP pegawai dari mesin?", function (result) {
                if (result) {
                    var cek_del = $('input[name=cek_del]:checked').map(function () {
                        return $(this).val();
                    }).get();

                    $.ajax({
                        url: '<?php echo site_url('pegawai/delfp');?>',
                        dataType: 'json',
                        type: 'POST',
                        data: {'id': cek_del},
                        success: function (data) {
                            bootbox.alert(data.msg);
                        },
                        beforeSend: function () {
                            $("#resend").prop('disabled', true);
                            $("#resend").html('<i class="fa fa-key"></i> Processing');
                        },
                        complete: function () {
                            $("#resend").prop('disabled', false);
                            $("#resend").html('<i class="fa fa-repeat"></i> Resend');
                        }
                    });
                }
            });
        }
        else{
            bootbox.alert("Harap pilih data..!");
        }
    }

    function doMoveArea() {
        var cek = $(".selected:checked").length;
        if (cek > 0) {
            showPopup('#popup2');
        } else{
            bootbox.alert("Harap pilih data..!");
        }
    }

    function doCopyData() {
        var cek = $(".selected:checked").length;
        if (cek > 0) {
            showPopup('#popup3');
        } else{
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
                url: '<?php echo site_url('pegawai/rubaharea');?>',
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

    function saveCopyForm()
    {
        var cek = $(".selectedmsin:checked").length;
        if (cek > 0) {
            var cek_msn = $('input[name=chek_mesin]:checked').map(function () {
                return $(this).val();
            }).get();
            $("#mesinid").val(cek_msn);

            var cek_del = $('input[name=cek_del]:checked').map(function () {
                return $(this).val();
            }).get();
            $("#useid").val(cek_del);
            $.ajax({
                url: '<?php echo site_url('pegawai/copyfpkemesin');?>',
                dataType: 'json',
                type: 'POST',
                data: $("#copyForm").serialize(),
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
    function doUploadData() {
        var cek = $(".selected:checked").length;
        if (cek > 0) {
            bootbox.confirm("Anda yakin data pegawai dimasukkan ke mesin?", function (result) {
                if (result) {
                    var cek_del = $('input[name=cek_del]:checked').map(function () {
                        return $(this).val();
                    }).get();

                    $.ajax({
                        url: '<?php echo site_url('pegawai/synchronizing');?>',
                        dataType: 'json',
                        type: 'POST',
                        data: {'id': cek_del},
                        success: function (data) {
                            bootbox.alert(data.msg);
                        },
                        beforeSend: function () {
                            $("#resend").prop('disabled', true);
                            $("#resend").html('<i class="fa fa-key"></i> Processing');
                        },
                        complete: function () {
                            $("#resend").prop('disabled', false);
                            $("#resend").html('<i class="fa fa-repeat"></i> Resend');
                        }
                    });
                }
            });
        } else{
            bootbox.alert("Harap pilih data..!");
        }
    }

    function doEnrolFP() {
        var cek = $(".selected:checked").length;
        if (cek > 0) {

        } else{
            bootbox.alert("Harap pilih data..!");
        }
    }

    function RefreshData()
    {
        load_url('<?php echo site_url('pegawai/pagging/0') ?>',"<?php echo (isset($order)?$order:'id')?>","<?php echo (isset($typeorder)?$typeorder:'sorting')?>");
    }
    function doSinkronData() {
        bootbox.confirm("Anda yakin men-sikronisasi <b>semua data pegawai</b> dengan data SIMPEG ?<br>Waktu proses membutuhkan waktu cukup lama.. ", function (result) {
            if (result) {
                $.ajax({
                    url: '<?php echo site_url('pegawai/sinkrondata');?>',
                    dataType: 'json',
                    type: 'POST',
                    success: function (data) {
                        bootbox.alert(data.msg);
                    },
                    beforeSend: function () {
                        $("#resend").prop('disabled', true);
                        $("#resend").html('<i class="fa fa-key"></i> Processing');
                    },
                    complete: function () {
                        $("#resend").prop('disabled', false);
                        $("#resend").html('<i class="fa fa-repeat"></i> Resend');
                    }
                });
            }
        });
    }

</script>

