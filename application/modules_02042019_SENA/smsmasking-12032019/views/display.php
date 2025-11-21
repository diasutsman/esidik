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
						$js = 'id="stspeg" class="input-sm form-control chosen-select" name="stspeg" data-placeholder="Pilih Status Pegawai..."';
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
						<input type="text" placeholder="Pencarian" class="input-sm form-control" id="caridata">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-2 col-sm-3 col-xs-12" for="stspeg">Tanggal</label>
					<div class="col-sm-7 form-inline" id="data_5">
						<div class="input-daterange input-group" id="datepicker">
							<input type="text" class="input-sm form-control" name="start" id="start" value="<?php echo date("01-m-Y") ?>"/>
							<span class="input-group-addon">s/d</span>
							<input type="text" class="input-sm form-control" name="end" id="end" value="<?php echo date("t-m-Y", strtotime(date("Y-m-d"))); ?>" />
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-3 col-md-offset-2">
						<button type="button" id="btncaridata" class="btn btn-sm btn-primary"> Display</button> <button type="button" id="btnproses" class="btn btn-sm btn-success"><i class="fa fa-inbox"></i> Send SMS</button> <button type="button" id="btnexcel" class="btn btn-sm btn-success"><i class="fa fa-file-excel-o"></i>  Excel</button>
						<button type="button" id="btnpdf" class="btn btn-sm btn-warning"><i class="fa fa-file-pdf-o"></i>  PDF</button>
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
	$('.chosen-select').chosen({width: "100%"});

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
			//$('.unker').data('clicked',0);
		}
	});

	$('#data_5 .input-daterange').datepicker({
		keyboardNavigation: false,
		forceParse: false,
		autoclose: true,
		format:"dd-mm-yyyy",
		language: 'id'
	});

    $('#caridata').keypress(function(e) {
        if ( e.keyCode == 13 ) {
            $('#btncaridata').click();
        }
    });

	$('#btncaridata').click(function(){
		load_url('<?php echo site_url('smsmasking/pagging/0') ?>', '<?php echo (isset($order)?$order:'id')?>','<?php echo (isset($typeorder)?$typeorder:'sorting')?>');
	});

	$('#btnexcel').click(function(){
		var cek_del  = $('input[name=cek_del]:checked').map(function(){
			return $(this).val();
		}).get();

		var par4 = $('#limit_display').val();
		if ($('#caridata').val() != '')
			par5 = $('#caridata').val();
		else
			par5 = 'cri';
		var par6 = $('#unit_search').val();
		var par7 = $('#stspeg').val();
		var par8 = $('#start').val();
		var par9 = $('#end').val();
        var par10 = $('#jnspeg').val();
        if (par6=="")
        {
            par6="undefined"
        }
        if (cek_del=="")
        {
            cek_del="undefined"
        }
        var s1 = $('#s1').val();
        var s2 = $('#s2').val();
        var s3 = $('#s3').val();
        var s4 = $('#s4').val();
        var sall = s1+"|"+s2+"|"+s3+"|"+s4;
        <?php if ($aksesrule["flagprint"]) {?>
        if ((par6 == "undefined") && (cek_del=="undefined") ) {
            bootbox.alert("Silakan pilih pegawai atau pilih unit kerja terlebih dahulu..");
            return false;
        }
		$('<form action="<?php echo site_url('smsmasking/view');?>" method="POST" target="_blank" style="display:none">' +
			'<input type="hidden" name="org" value="'+par6+'" />'+
			'<input type="hidden" name="stspeg" value="'+par7+'" />'+
            '<input type="hidden" name="jnspeg" value="'+par10+'" />'+
			'<input type="hidden" name="uid" value="'+cek_del+'" />'+
			'<input type="hidden" name="start" value="'+par8+'" />'+
			'<input type="hidden" name="xls" value="1" />'+
            '<input type="hidden" name="pdf" value="0" />'+
            '<input type="hidden" name="ttd" value="'+sall+'" />'+
			'<input type="hidden" name="end" value="'+par9+'" />'
		).appendTo("body").submit().remove();
        <?php } ?>
	});

	$('#btnproses').click(function(){
		var cek_del  = $('input[name=cek_del]:checked').map(function(){
			return $(this).val();
		}).get();

		if (cek_del=="")
		{
			cek_del="undefined"
		}
		var par4 = $('#limit_display').val();
		if ($('#caridata').val() != '')
			par5 = $('#caridata').val();
		else
			par5 = 'cri';
		var par6 = $('#unit_search').val();
		var par7 = $('#stspeg').val();
		var par8 = $('#start').val();
		var par9 = $('#end').val();
        var par10 = $('#jnspeg').val();
		if (par6=="")
		{
			par6="undefined"
		}
        var s1 = $('#s1').val();
        var s2 = $('#s2').val();
        var s3 = $('#s3').val();
        var s4 = $('#s4').val();
        var sall = s1+"|"+s2+"|"+s3+"|"+s4;
        <?php if ($aksesrule["flagprint"]) {?>
        if ((par6 == "undefined") && (cek_del=="undefined") ) {
            bootbox.alert("Silakan pilih pegawai atau pilih unit kerja terlebih dahulu..");
            return false;
        }
		$('<form action="<?php echo site_url('smsmasking/proses');?>" method="POST" target="_blank" style="display:none">' +
			'<input type="hidden" name="org" value="'+par6+'" />'+
			'<input type="hidden" name="stspeg" value="'+par7+'" />'+
            '<input type="hidden" name="jnspeg" value="'+par10+'" />'+
			'<input type="hidden" name="uid" value="'+cek_del+'" />'+
			'<input type="hidden" name="start" value="'+par8+'" />'+
			'<input type="hidden" name="xls" value="0" />'+
            '<input type="hidden" name="pdf" value="0" />'+
            '<input type="hidden" name="ttd" value="'+sall+'" />'+
			'<input type="hidden" name="end" value="'+par9+'" />'
		).appendTo("body").submit().remove();
        <?php } ?>
	});

    $('#btnpdf').click(function(){
        var cek_del  = $('input[name=cek_del]:checked').map(function(){
            return $(this).val();
        }).get();

        var par4 = $('#limit_display').val();
        if ($('#caridata').val() != '')
            par5 = $('#caridata').val();
        else
            par5 = 'cri';
        var par6 = $('#unit_search').val();
        var par7 = $('#stspeg').val();
        var par8 = $('#start').val();
        var par9 = $('#end').val();
        var par10 = $('#jnspeg').val();
        if (par6=="")
        {
            par6="undefined"
        }
        if (cek_del=="")
        {
            cek_del="undefined"
        }
        var s1 = $('#s1').val();
        var s2 = $('#s2').val();
        var s3 = $('#s3').val();
        var s4 = $('#s4').val();
        var sall = s1+"|"+s2+"|"+s3+"|"+s4;
        <?php if ($aksesrule["flagprint"]) {?>
        if ((par6 == "undefined") && (cek_del=="undefined") ) {
            bootbox.alert("Silakan pilih pegawai atau pilih unit kerja terlebih dahulu..");
            return false;
        }
        $('<form action="<?php echo site_url('smsmasking/view');?>" method="POST" target="_blank" style="display:none">' +
            '<input type="hidden" name="org" value="'+par6+'" />'+
            '<input type="hidden" name="stspeg" value="'+par7+'" />'+
            '<input type="hidden" name="jnspeg" value="'+par10+'" />'+
            '<input type="hidden" name="uid" value="'+cek_del+'" />'+
            '<input type="hidden" name="start" value="'+par8+'" />'+
            '<input type="hidden" name="xls" value="1" />'+
            '<input type="hidden" name="pdf" value="1" />'+
            '<input type="hidden" name="ttd" value="'+sall+'" />'+
            '<input type="hidden" name="end" value="'+par9+'" />'
        ).appendTo("body").submit().remove();
        <?php } ?>
    });
});
</script>
