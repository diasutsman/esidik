
<?php
/**
 * File: list.php
 * Author: abdiIwan.
 * Date: 12/28/2016
 * Time: 9:38 PM
 * absensi.kemendagri.go.id
 */

$url_pag =  site_url("setdev/pagging/0");

?>
<link href="<?php echo base_url()?>assets/css/plugins/datepicker/datepicker3.css" rel="stylesheet">
<script src="<?php echo base_url()?>assets/js/plugins/datapicker/bootstrap-datepicker.js"></script>
<script src="<?php echo base_url()?>assets/js/plugins/datapicker/bootstrap-datepicker.id.min.js"></script>
<script src="<?php echo base_url()?>assets/js/plugins/jsTree/jstree.min.js"></script>
<link href="<?php echo base_url()?>assets/css/plugins/jsTree/themes/proton/style.min.css" rel="stylesheet">
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
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
								Opsi <span class="caret"></span>
							</button>
							<ul class="dropdown-menu" role="menu">
								<li><a href="#" id="btn1" onClick="showSNForm();" >Ganti SN</a>
								</li>
								<li><a href="#" id="btn2" onClick="showMoveArea();" >Pindah Area</a>
								</li>
								<li><a href="#" onClick="doSynMesin();">Sinkronisasi</a>
								</li>
								<li><a href="#" id="btn1" onClick="doInfoMesin();" >Informasi Mesin</a>
								</li>
								<li><a href="#" id="btn2" onClick="doReboot();" >Reboot</a>
								</li>
								<li class="dropdown">
									<a href="javascript:;" class="dropdown" data-toggle="dropdown">Penghapusan <b class="fa fa-caret-right"></b></a>
									<ul class="dropdown-menu">
										<li><a href="#" id="btn2" onClick="doDelete();" >Hapus Mesin</a>
										</li>
										<li><a href="#" id="btn2" onClick="doDeleteAll();" >Hapus Semua Log Mesin</a>
										</li>
										<li><a href="#" id="btn2" onClick="doShowDate();" >Hapus Transaksi Log Mesin</a>
										</li>
									</ul>
								</li>

							</ul>
						</div>
						<div class="btn-group">
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
								Unduh <span class="caret"></span>
							</button>
							<ul class="dropdown-menu" role="menu">
								<li><a href="#" id="btn1" onclick="doTransMesin()">Transaksi</a>
								</li>
								<li><a href="#" id="btn2" onclick="doTransUser()">Pegawai</a>
								</li>
								<li><a href="#" id="btn3" onclick="doTransPict()">Foto</a>
								</li>
								<li><a href="#" id="btn3" onclick="doTransUsb()">Simpan ke USB</a>
								</li>
							</ul>
						</div>
					</div>
					<div class="col-sm-4 m-b-xs ">

					</div>
					<div class="col-sm-3">
						<div class="input-group">
							<input type="text" id="caridata" name="caridata" placeholder="Pencarian" class="input-sm form-control" value="<?php echo isset($caridata)?$caridata:''?>">
							<span class="input-group-btn"><button type="button" id="btncari" class="btn btn-sm btn-primary"> <i class="fa fa-search"></i> </button> </span>
						</div>
					</div>

				</div>
				<div id="list-data">
					<?php include ("list.php") ?>
				</div>
				<div id="list-data-temp">
					<?php include ("list2.php") ?>
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
		<div class="modal-body" >
				<input name="idold" value="" id="idold" type="hidden">
				<div class="form-group">
					<label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt1">SN Baru</label>
					<div class="col-md-5 col-sm-5 col-xs-12">
						<input id="txt1" name="txt1" type="text" placeholder="Serial number" class="form-control col-md-5 col-xs-12">
					</div>
				</div>

		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-sm btn-primary" onClick="hidePopup('#popup');">Batal</button>
			<button type="button" class="btn btn-sm btn-success" data-dismiss="modal" onClick="saveSNForm();">Simpan</button>
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
			<div class="modal-body" >
				<div class="form-group">
					<label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt1">Area Mesin</label>
					<div class="col-md-5 col-sm-5 col-xs-12">
						<?php createArea() ?>
						<input type="hidden" name="area" id="area" />
					</div>
				</div>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-sm btn-primary" onClick="hidePopup('#popup2');">Batal</button>
				<button type="button" class="btn btn-sm btn-success" data-dismiss="modal" onClick="saveAreaForm();">Simpan</button>
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
			<div class="modal-body" >
				<div class="form-group">
					<label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt1">Tanggal</label>
					<div class="col-md-5 col-sm-5 col-xs-12">
						<div class="input-group date">
							<input class="form-control" placeholder="Tanggal" name="deltanggal" value="<?php echo ymdTodmy(date('Y-m-d'))?>"  type="text"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
						</div>
					</div>
				</div>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-sm btn-primary" onClick="hidePopup('#popup3');">Batal</button>
				<button type="button" class="btn btn-sm btn-success" data-dismiss="modal" onClick="doDeleteDate();">Simpan</button>
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

		$('.input-group.date').datepicker({
			keyboardNavigation: false,
			forceParse: false,
			autoclose: true,
			format:"dd-mm-yyyy",
			language: 'id'
		});


	});

	function showSNForm()
	{
		var cek = $(".selected:checked").length;
		if(cek > 0) {
			showPopup('#popup');
		} else {
			bootbox.alert("Harap pilih data..!");
		}
	}


	function saveSNForm() {
		if (($("#txt1").val() != "")) {
			var cek_del  = $('input[name=cek_del]:checked').map(function(){
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



	function showMoveArea()
	{
		var cek = $(".selected:checked").length;
		if(cek > 0){
			showPopup('#popup2');

		} else{
			bootbox.alert("Harap pilih data..!");
		}
	}

	function saveAreaForm() {
		if (($("#area").val() != "")) {
			var cek_del  = $('input[name=cek_del]:checked').map(function(){
				return $(this).val();
			}).get();
			$("#idarea").val(cek_del);
			$('#area').val($('#areatree').jstree(true).get_selected());
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

	function doInfoMesin()
	{
		var cek = $(".selected:checked").length;
		if(cek > 0){
			bootbox.confirm("Anda yakin mengambil informasi mesin?", function(result) {
				if(result) {
					var cek_del = $('input[name=cek_del]:checked').map(function () {
						return $(this).val();
					}).get();

					$.ajax({
						url: '<?php echo site_url('setdev/fpinfo');?>', dataType: 'json', type: 'POST', data: {'id': cek_del}, success: function (data) {
							bootbox.alert(data.msg);
						}, beforeSend: function () {
							$("#resend").prop('disabled', true);
							$("#resend").html('<i class="fa fa-key"></i> Processing');
						}, complete: function () {
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
	function doReboot()
	{
		var cek = $(".selected:checked").length;
		if(cek > 0){
			bootbox.confirm("Anda yakin me-restart mesin?", function(result) {
				if(result) {
					var cek_del  = $('input[name=cek_del]:checked').map(function(){
						return $(this).val();
					}).get();

					$.ajax({
						url     : '<?php echo site_url('setdev/reboot');?>',
						dataType: 'json',
						type    : 'POST',
						data    : { 'id' : cek_del},
						success : function(data){
							bootbox.alert(data.msg);
						},
						beforeSend: function() {
							$("#resend").prop('disabled', true);
							$("#resend").html('<i class="fa fa-key"></i> Processing');
						},
						complete: function() {
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

	function doDelete()
	{
		var cek = $(".selected:checked").length;
		if(cek > 0){
			bootbox.confirm("Anda yakin menghapus data yang dipilih?", function(result) {
				if(result) {
					var cek_del  = $('input[name=cek_del]:checked').map(function(){
						return $(this).val();
					}).get();

					$.ajax({
						url     : '<?php echo site_url('setdev/hapus');?>',
						dataType: 'json',
						type    : 'POST',
						data    : { 'id' : cek_del},
						success : function(data){
							bootbox.alert(data.msg);
						},
						beforeSend: function() {
							$("#resend").prop('disabled', true);
							$("#resend").html('<i class="fa fa-key"></i> Processing');
						},
						complete: function() {
							$("#resend").prop('disabled', false);
							$("#resend").html('<i class="fa fa-repeat"></i> Resend');
						}
					});

				}
			});
		}else{
			bootbox.alert("Harap pilih data yang akan di hapus!");
		}
	}

	function doSynMesin()
	{
		var cek = $(".selected:checked").length;
		if(cek > 0){
			bootbox.confirm("Anda yakin me-sinkron data ke mesin?", function(result) {
				if(result) {
					var cek_del  = $('input[name=cek_del]:checked').map(function(){
						return $(this).val();
					}).get();

					$.ajax({
						url     : '<?php echo site_url('setdev/synkronisasi');?>',
						dataType: 'json',
						type    : 'POST',
						data    : { 'id' : cek_del},
						success : function(data){
							bootbox.alert(data.msg);
						},
						beforeSend: function() {
							$("#resend").prop('disabled', true);
							$("#resend").html('<i class="fa fa-key"></i> Processing');
						},
						complete: function() {
							$("#resend").prop('disabled', false);
							$("#resend").html('<i class="fa fa-repeat"></i> Resend');
						}
					});

				}
			});
		}else{
			bootbox.alert("Harap pilih data yang akan di sinkron kan!");
		}
	}

	function doTransMesin()
	{
		var cek = $(".selected:checked").length;
		if(cek > 0){
			bootbox.confirm("Anda yakin mengambil transaksi dari mesin?", function(result) {
				if (result) {
					var cek_del = $('input[name=cek_del]:checked').map(function () {
						return $(this).val();
					}).get();

					$.ajax({
						url: '<?php echo site_url('setdev/transakinfo');?>', dataType: 'json', type: 'POST', data: {'id': cek_del}, success: function (data) {
							bootbox.alert(data.msg);
						}, beforeSend: function () {
							$("#resend").prop('disabled', true);
							$("#resend").html('<i class="fa fa-key"></i> Processing');
						}, complete: function () {
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

	function doTransUser()
	{
		var cek = $(".selected:checked").length;
		if(cek > 0){
			bootbox.confirm("Anda yakin mengambil data pegawai dari mesin?", function(result) {
				if(result) {
					var cek_del = $('input[name=cek_del]:checked').map(function () {
						return $(this).val();
					}).get();

					$.ajax({
						url: '<?php echo site_url('setdev/transakuser');?>', dataType: 'json', type: 'POST', data: {'id': cek_del}, success: function (data) {
							bootbox.alert(data.msg);
						}, beforeSend: function () {
							$("#resend").prop('disabled', true);
							$("#resend").html('<i class="fa fa-key"></i> Processing');
						}, complete: function () {
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


	function doTransPict()
	{
		var cek = $(".selected:checked").length;
		if(cek > 0){
			bootbox.confirm("Anda yakin mengambil data poto pegawai dari mesin?", function(result) {
				if(result) {
					var cek_del = $('input[name=cek_del]:checked').map(function () {
						return $(this).val();
					}).get();

					$.ajax({
						url: '<?php echo site_url('setdev/transakfoto');?>', dataType: 'json', type: 'POST', data: {'id': cek_del}, success: function (data) {
							bootbox.alert(data.msg);
						}, beforeSend: function () {
							$("#resend").prop('disabled', true);
							$("#resend").html('<i class="fa fa-key"></i> Processing');
						}, complete: function () {
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


	function doDeleteAll()
	{
		var cek = $(".selected:checked").length;
		if(cek > 0){
			bootbox.confirm("Anda yakin menghapus data di mesin?", function(result) {
				if(result) {
					var cek_del = $('input[name=cek_del]:checked').map(function () {
						return $(this).val();
					}).get();

					$.ajax({
						url: '<?php echo site_url('setdev/transakdelalllog');?>', dataType: 'json', type: 'POST', data: {'id': cek_del}, success: function (data) {
							bootbox.alert(data.msg);
						}, beforeSend: function () {
							$("#resend").prop('disabled', true);
							$("#resend").html('<i class="fa fa-key"></i> Processing');
						}, complete: function () {
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

	function doShowDate()
	{
		var cek = $(".selected:checked").length;
		if(cek > 0) {
			showPopup('#popup3');
		} else {
			bootbox.alert("Harap pilih data..!");
		}
	}

	function doDeleteDate()
	{
		var cek = $(".selected:checked").length;
		if(cek > 0){
			bootbox.confirm("Anda yakin menghapus data di mesin?", function(result) {
				if(result) {
					var cek_del  = $('input[name=cek_del]:checked').map(function(){
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
		} else{
			bootbox.alert("Harap pilih data..!");
		}
	}
</script>
