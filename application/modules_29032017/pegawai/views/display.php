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
					<div class="col-sm-4 m-b-xs">
						<?php
						$js = 'id="stspeg" class="input-sm form-control chosen-select" name="stspeg" data-placeholder="Pilih Jenis Pegawai..."';
						$selected = array("1","2");
						echo form_multiselect('stspeg',$lstStsPeg,$selected,$js);
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
					<div class="col-sm-3">
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

</script>

