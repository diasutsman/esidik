<link href="<?php echo base_url()?>assets/css/plugins/datepicker/datepicker3.css" rel="stylesheet">
<link href="<?php echo base_url()?>assets/css/plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet">
<script src="<?php echo base_url()?>assets/js/plugins/datapicker/bootstrap-datepicker.js"></script>
<script src="<?php echo base_url()?>assets/js/plugins/datapicker/bootstrap-datepicker.id.min.js"></script>
<script src="<?php echo base_url()?>assets/js/plugins/daterangepicker/daterangepicker.js"></script>
<link href="<?php echo base_url()?>assets/css/plugins/chosen/bootstrap-chosen.css" rel="stylesheet">
<script src="<?php echo base_url()?>assets/js/plugins/chosen/chosen.jquery.js"></script>
<div class="row">
	<div class="ibox float-e-margins">
		<div class="ibox-title">
			<h5>Filtering</h5>
			<div class="ibox-tools">
				<a class="collapse-link">
					<i class="fa fa-chevron-up"></i>
				</a>
			</div>
		</div>
		<div class="ibox-content">
			<div class="row form-horizontal">
				<div class="form-group">
					<label class="control-label col-md-2 col-sm-3 col-xs-12" for="stspeg">Status Pegawai</label>
						<div class="col-sm-7">
							<?php
							$js = 'id="stspeg" class="input-sm form-control chosen-select" name="stspeg" data-placeholder="Pilih Jenis Pegawai..."';
							$selected = array("1","2");
							echo form_multiselect('stspeg',$lstStsPeg,$selected,$js);
							?>
						</div>
				</div>
                <div class="form-group">
                    <label class="control-label col-md-2 col-sm-3 col-xs-12" for="jnspeg">Jenis Pegawai</label>
                    <div class="col-sm-7">
                        <?php
                        $js = 'id="jnspeg" class="input-sm form-control chosen-select" name="jnspeg" data-placeholder="Pilih Jenis Pegawai..."';
                        $selected = array("1","2");
                        echo form_multiselect('jnspeg',$lstJnsPeg,$selected,$js);
                        ?>
                    </div>
                </div>
				<div class="form-group">
					<label class="control-label col-md-2 col-sm-3 col-xs-12" for="cari_unker">Unit Kerja</label>
					<div class="col-sm-7">
						<div class="unker input-group">
							<input type="text" class="input-sm form-control" readonly name="cari_unker" id="cari_unker" value="" placeholder="Unit Kerja ...">
							<input type="hidden" name="unit_search" id="unit_search" value="">
							<div class="input-group-btn">
								<button class="btn btn-white btn-sm" type="button"><span class="caret"></span></button>
							</div>
						</div>
						<div class="panel combo-p" style="position: absolute;  z-index:50001; display: none;">
							<div class="combo-panel panel-body panel-body-noheader" title="" style="max-height:250px; padding:5px;overflow-y:auto">
							</div>
						</div>
					</div>
				</div>
				<div class="form-group">
                    <label class="control-label col-md-2 col-sm-3 col-xs-12" for="stausketidakhadiran">Status Ketidakhadiran</label>
                    <div class="col-sm-7">
                        <?php
                        $js = 'id="stausketidakhadiran" class="input-sm form-control chosen-select" name="stausketidakhadiran" data-placeholder="Pilih Status Ketidakhadiran..."';
                        $selected = array("AB_6","AB_13","WFH");
                        echo form_multiselect('stausketidakhadiran',$lstKetidakhadiran,$selected,$js);
                        ?>
                    </div>
                </div>
				<div class="form-group">
                    <label class="control-label col-md-2 col-sm-3 col-xs-12" for="stausverifikasi">Status Verifikasi</label>
                    <div class="col-sm-7">
                        <?php
                        $js = 'id="stausverifikasi" class="input-sm form-control chosen-select" name="stausverifikasi" data-placeholder="Pilih Status Verifikasi..."';
                        $selected = array("0","1","2");
                        echo form_multiselect('stausverifikasi',$lstVerifikasi,$selected,$js);
                        ?>
                    </div>
                </div>
				<div class="form-group">
					<label class="control-label col-md-2 col-sm-3 col-xs-12" for="Tanggal">Tanggal Input</label>
					<div class="col-sm-5 form-inline" id="data_5">
						<div class="input-daterange input-group" id="datepicker">
							<input type="text" class="input-sm form-control" name="start" id="start" value="<?php echo date("01-m-Y") ?>"/>
							<span class="input-group-addon">s/d</span>
							<input type="text" class="input-sm form-control" name="end" id="end" value="<?php echo date("t-m-Y", strtotime(date("Y-m-d"))); ?>"/>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-2 col-sm-3 col-xs-12" for="stspeg">Pencarian</label>
					<div class="col-sm-7">
						<input type="text" id="caridata" name="caridata" placeholder="NIP/Nama" class="input-sm form-control" value="">
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-10 col-md-offset-2">
						<button type="button" id="btncari" class="btn btn-sm btn-primary"> Display</button>
						
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
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

				
				<div id="list-data">
					<?php include ("list.php") ?>
				</div>
			</div>
		</div>
</div>

<form id="inputForm" name="inputForm" action="javascript:;" method="post" class="form-horizontal form-validate">
	<input type="hidden" name="orgid" id="orgid2"/>
	<input type="hidden" name="stspeg" id="stspeg2"/>
	<input type="hidden" name="userid" id="userid"/>

	<div class="popup-wrapper fade" id="popup">
		<div class="popup-container">
			<div class="modal-header">
				<span type="button" class="close" data-dismiss="modal" onClick="hidePopup('#popup');">&times;</span>
				<h3>Input Jadwal</h3>
			</div>
			<div class="modal-body" id="input_data" style="max-height:350px;min-height:250px;">

			</div>
			<div class="modal-footer">
				<a class="btn btn-danger btn-sm" data-dismiss="modal" onClick="hidePopup('#popup');">Keluar</a>
				<button class="btn btn-primary btn-sm" id="btn_simpan" onClick="saveForm();">Simpan</button>
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

        $("#caridata").keypress(function(e){
            var key = (e.keyCode ? e.keyCode : e.which);
            if(key==13){
                $("#btncari").click();
            }
        });

		$('#btncari').click(function(){
			load_url('<?php echo site_url('ketidakhadiran/pagging/0') ?>', '<?php echo (isset($order)?$order:'id')?>','<?php echo (isset($typeorder)?$typeorder:'sorting')?>');
		});

		$('.unker').click(function(){
			var inwidth = $(this).width();
			var dis = $('.panel').css("display");
			if(dis=='none'){
				$('.panel').css({
					display : 'block',
					width : inwidth
				});

				if ( $('.combo-panel > *').length == 0 ) {
					//$('.combo-panel').empty();
					$('.combo-panel').html('Loading...........');
					$.ajax({
						url     : '<?php echo site_url('ajax/getUnitKerja')?>',
						dataType: 'html',
						type    : 'POST',
						success : function(data){
							$('.combo-panel').html(data);
						}
					});
				}

			}else{
				//$('.combo-panel').empty();
				$('.panel').css({
					display : 'none'
				});
				$('.unker').data('clicked',1);
			}
		});


		$('.chosen-select').chosen({width: "100%"});


	});

	$('#btnproses').click(function(){
		var cek = $(".selected:checked").length;
		if(cek > 0)
		{
			var cek_del  = $('input[name=cek_del]:checked').map(function(){
				return $(this).val();
			}).get();

			var par6 = $('#unit_search').val();
			var par8 = $('#start').val();
			var par9 = $('#end').val();
			var par7 = $('#stspeg').val();
			var par10 = $('#jnspeg').val();
			var par11 = $('#stausverifikasi').val();
			$.ajax({
				dataType: 'json',
				type    : 'POST',
				url: '<?php echo site_url('ketidakhadiran/statushadir') ?>',
				data:{"org":par6,"stspeg":par7,"jnspeg":par10,"userid":cek_del,"startdate":par8,"enddate":par9,"stausverifikasi":par11},
				success: function(data){
					$("#btnproses").prop('disabled', false);
					$("#btnproses").html('<i class="fa fa-repeat"></i>  Proses Disetujui');
					bootbox.alert(data.msg);
				},
				beforeSend: function() {
					$("#btnproses").prop('disabled', true);
					$("#btnproses").html('<i class="fa fa-key"></i> Processing');
				},
				complete: function() {
					$("#btnproses").prop('disabled', false);
					$("#btnproses").html('<i class="fa fa-repeat"></i>  Proses Disetujui');
				}
			});
		}else{
			bootbox.alert("Harap pilih pegawai nya terlebih dahulu!");
		}
		return false;
	});
	
	$('#btntolak').click(function(){
		var cek = $(".selected:checked").length;
		if(cek > 0)
		{
			var cek_del  = $('input[name=cek_del]:checked').map(function(){
				return $(this).val();
			}).get();

			var par6 = $('#unit_search').val();
			var par8 = $('#start').val();
			var par9 = $('#end').val();
			var par7 = $('#stspeg').val();
			var par10 = $('#jnspeg').val();
			$.ajax({
				dataType: 'json',
				type    : 'POST',
				url: '<?php echo site_url('ketidakhadiran/statusditolak') ?>',
				data:{"org":par6,"stspeg":par7,"jnspeg":par10,"userid":cek_del,"startdate":par8,"enddate":par9},
				success: function(data){
					$("#btntolak").prop('disabled', false);
					$("#btntolak").html('<i class="fa fa-repeat"></i>  Proses Ditolak');
					bootbox.alert(data.msg);
				},
				beforeSend: function() {
					$("#btntolak").prop('disabled', true);
					$("#btntolak").html('<i class="fa fa-key"></i> Processing');
				},
				complete: function() {
					$("#btntolak").prop('disabled', false);
					$("#btntolak").html('<i class="fa fa-repeat"></i>  Proses Ditolak');
				}
			});
		}else{
			bootbox.alert("Harap pilih pegawai nya terlebih dahulu!");
		}
		return false;
	});
	
	$('#btnkembalikan').click(function(){
		var cek = $(".selected:checked").length;
		if(cek > 0)
		{
			var cek_del  = $('input[name=cek_del]:checked').map(function(){
				return $(this).val();
			}).get();

			var par6 = $('#unit_search').val();
			var par8 = $('#start').val();
			var par9 = $('#end').val();
			var par7 = $('#stspeg').val();
			var par10 = $('#jnspeg').val();
			$.ajax({
				dataType: 'json',
				type    : 'POST',
				url: '<?php echo site_url('ketidakhadiran/statusdikembalikan') ?>',
				data:{"org":par6,"stspeg":par7,"jnspeg":par10,"userid":cek_del,"startdate":par8,"enddate":par9},
				success: function(data){
					$("#btnkembalikan").prop('disabled', false);
					$("#btnkembalikan").html('<i class="fa fa-repeat"></i>  Proses Dikembalikan');
					bootbox.alert(data.msg);
				},
				beforeSend: function() {
					$("#btnkembalikan").prop('disabled', true);
					$("#btnkembalikan").html('<i class="fa fa-key"></i> Processing');
				},
				complete: function() {
					$("#btnkembalikan").prop('disabled', false);
					$("#btnkembalikan").html('<i class="fa fa-repeat"></i>  Proses Dikembalikan');
				}
			});
		}else{
			bootbox.alert("Harap pilih pegawai nya terlebih dahulu!");
		}
		return false;
	});

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
						url     : '<?php echo site_url('ketidakhadiran/hapus');?>',
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

	function saveForm() {
		if (($("#txt1").val() != "")) {
			$.ajax({
				url: '<?php echo site_url('ketidakhadiran/save');?>', dataType: 'json', type: 'POST', data: $("#inputForm").serialize(), success: function (data) {
					bootbox.alert(data.msg);
				}
			});
		} else {
			bootbox.alert("Harap cek kembali inputannya ??");
		}
	}
	
	$('#data_5 .input-daterange').datepicker({
		keyboardNavigation: false,
		forceParse: false,
		autoclose: true,
		format:"dd-mm-yyyy",
		language: 'id'
	});

</script>

