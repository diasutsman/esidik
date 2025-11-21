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
					<label class="control-label col-md-2 col-sm-3 col-xs-12" for="stspeg">Pencarian</label>
					<div class="col-sm-7">
						<input type="text" id="caridata" name="caridata" placeholder="NIP/Nama" class="input-sm form-control" value="">
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-10 col-md-offset-2">
                        <button type="button" id="btncaridata" class="btn btn-sm btn-primary" > Display</button> <button type="button" id="btnproses" onclick="doSinkronData()" class="btn btn-sm btn-success"><i class="fa fa-user"></i> Proses Pegawai</button>
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


<script>
	$(function(){

        $('#data_5 .input-daterange').datepicker({
            keyboardNavigation: false,
            forceParse: false,
            autoclose: true,
            format:"dd-mm-yyyy",
            language: 'id'
        });

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
                $('#btncaridata').click();
            }
        });

		$('#btncaridata').click(function(){
			load_url('<?php echo site_url('proculapeg/pagging/0') ?>', '<?php echo (isset($order)?$order:'id')?>','<?php echo (isset($typeorder)?$typeorder:'sorting')?>');
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

    function BootboxContent(varMsg) {
        var frm_str = varMsg+'<form class="bootbox-form">'
            +'<select class="bootbox-input bootbox-input-select form-control" id="pilsink"><option value="Y">Disetujui</option><option value="N">Ditolak</option></select>'
            +'</form>';

        var object = $('<div/>').html(frm_str).contents();

        return object
    }

    function doSinkronData() {
        var cek = $(".selected:checked").length;
        var cMsg= "Anda yakin memproses data yang dipilih? <strong>Silakan pilih kondisi dibawah</strong> ";
        if (cek > 0) {
            cekData = $('input[name=cek_del]:checked').map(function () {
                return $(this).val();
            }).get();

            bootbox.dialog({
                title: 'Proses data ULAPEG',
                message: BootboxContent(cMsg),
                size: 'medium',
                buttons: {
                    cancel: {
                        label: "Batal",
                        className: 'btn-danger'
                    },
                    ok: {
                        label: "Proses!",
                        className: 'btn-success',
                        callback: function(){
                            $.ajax({
                                url: '<?php echo site_url('proculapeg/pegawai');?>',
                                dataType: 'json',
                                type: 'POST',
                                data: {'userid': cekData,'stsapprov':$("#pilsink").val()},
                                success: function (data) {
                                    load_url('<?php echo site_url('proculapeg/pagging/0') ?>', '<?php echo (isset($order)?$order:'id')?>','<?php echo (isset($typeorder)?$typeorder:'sorting')?>');
                                    bootbox.alert(data.msg);
                                }
                            });
                        }
                    }
                }
            });

        } else{
            bootbox.alert("Harap pilih pegawai nya terlebih dahulu!");
        }
        return false;


    }

</script>

