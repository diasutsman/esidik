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
                    <label class="control-label col-md-2 col-sm-3 col-xs-12" for="pilihan">Pilihan</label>
                    <div class="col-sm-2">
                        <select id="pilihan" class="input-sm form-control" onchange="pilihan(this)" name="pilihan" required>
                            <option value="">--Pilih--</option>
                            <option value="harian">Harian</option>
                            <option value="bulanan">Bulanan</option>
                        </select>
                    </div>
                </div>
                <div class="form-group" id="harian">
                    <label class="control-label col-md-2 col-sm-3 col-xs-12" for="stspeg">Tanggal</label>
                    <div class="col-sm-7 form-inline" id="data_5">
                        <div class="input-daterange input-group" id="datepicker">
                            <input type="text" class="input-sm form-control" name="start" id="start" value="<?php echo date("01-m-Y") ?>"/>
                            <span class="input-group-addon">s/d</span>
                            <input type="text" class="input-sm form-control" name="end" id="end" value="<?php echo date("t-m-Y", strtotime(date("Y-m-d"))); ?>" />
                        </div>
                    </div>
                </div>
                <div class="form-group" id="bulanan">
                    <label class="control-label col-md-2 col-sm-3 col-xs-12" for="start1">Periode</label>
                    <div class="col-sm-2 input-group date" style="padding-left: 15px">
                        <input class="form-control" placeholder="Periode" type="text" id="start1" value="<?php echo date("Y-m")?>"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    </div>
                </div>
				<div class="form-group">
					<div class="col-md-10 col-md-offset-2">
                        <button type="button" id="btncaridata" class="btn btn-sm btn-primary"> Display</button> <button type="button" id="btnproses" class="btn btn-sm btn-success hidden"><i class="fa fa-user"></i> Proses Pegawai</button> <button type="button" id="btnsemua" class="btn btn-sm btn-warning"><i class="fa fa-users"></i>  Proses Semua</button>
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

        $('.input-group.date').datepicker({
            keyboardNavigation: false,
            forceParse: false,
            autoclose: true,
            format:"yyyy-mm",
            startView: "months",
            minViewMode: "months",
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
			load_url('<?php echo site_url('procpost/pagging/0') ?>', '<?php echo (isset($order)?$order:'id')?>','<?php echo (isset($typeorder)?$typeorder:'sorting')?>');
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
                var par11 = $('#pilihan').val();
                var par12 = $('#start1').val();
                $.ajax({
                    dataType: 'json',
                    type    : 'POST',
                    url: '<?php echo site_url('procpost/pegawai') ?>',
                    data:{"org":par6,"stspeg":par7,"jnspeg":par10,"userid":cek_del,"startdate":par8,"enddate":par9,
                    "pilihan":par11,"start1":par12},
                    success: function(data){
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
            }else{
                bootbox.alert("Harap pilih pegawai nya terlebih dahulu!");
            }
            return false;
        });

        $('#btnsemua').click(function(){
            bootbox.confirm("Anda yakin memproses semua data (berdasarkan unit kerja)?", function(result) {
                if(result) {
                    var par6 = $('#unit_search').val();
                    var par8 = $('#start').val();
                    var par9 = $('#end').val();
                    var par7 = $('#stspeg').val();
                    var par10 = $('#jnspeg').val();
                    var par11 = $('#pilihan').val();
                    var par12 = $('#start1').val();
                    if (par6 == "")
                    {
                        par6="undefined";
                    }

                    $.ajax({
                        dataType: 'json',
                        type    : 'POST',
                        url: '<?php echo site_url('procpost/allpegawai') ?>',
                        data:{"org":par6,"stspeg":par7,"jnspeg":par10,"startdate":par8,"enddate":par9,
                            "pilihan":par11,"start1":par12},
                        success: function(data){
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


            return false;
        });

	});
    window.onload = function WindowLoad(event) {
        $('#bulanan').hide();
        $('#harian').hide();
    }

    function pilihan(obj) {
        if (obj.value=="harian")
        {
            $('#bulanan').hide();
            $('#harian').show();
        } else {
            $('#bulanan').show();
            $('#harian').hide();
        }

    }
</script>

