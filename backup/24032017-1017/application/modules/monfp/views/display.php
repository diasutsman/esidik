<link href="<?php echo base_url()?>assets/css/plugins/datepicker/datepicker3.css" rel="stylesheet">
<link href="<?php echo base_url()?>assets/css/plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet">
<script src="<?php echo base_url()?>assets/js/plugins/datapicker/bootstrap-datepicker.js"></script>
<script src="<?php echo base_url()?>assets/js/plugins/daterangepicker/daterangepicker.js"></script>
<div class="row">
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5>Transaksi Finger Print</h5>
				<div class="ibox-tools">
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
				</div>
			</div>
			<div class="ibox-content">
				<div class="row">
					<div class="col-sm-3 m-b-xs">
						<div class="form-group form-inline" id="data_5">
							<div class="input-daterange input-group" id="datepicker">
								<input type="text" class="input-sm form-control" name="start" id="start" value="<?php echo date("d-m-Y")?>"/>
								<span class="input-group-addon">s/d</span>
								<input type="text" class="input-sm form-control" name="end" id="end" value="<?php echo date("d-m-Y")?>" />
							</div>
						</div>
					</div>
					<div class="col-sm-3 m-b-xs">
						<div class="area input-group">
							<input type="text" class="input-sm form-control" readonly name="cari_area" id="cari_area" value="" placeholder="Area ...">
							<input type="hidden" name="area_search" id="area_search" value="">
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
						<select class="input-sm form-control" id="state">
							<option value="-1">--Pilih Status--</option>
							<?php
							$this->db->order_by("id","asc");
							$rty=$this->db->get("state");
							foreach($rty->result() as $row) {
								?>
								<option value="<?php echo $row->id?>"><?php echo $row->state?></option>
								<?php
							}
							?>
						</select>
					</div>
					<div class="col-sm-3">
						<div class="input-group">
							<input type="text" placeholder="Pencarian" id="caridata" class="input-sm form-control"> <span class="input-group-btn">
                            <button type="button" id="btncari" class="btn btn-sm btn-primary"> <i class="fa fa-search"></i> </button> </span>
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
	$('.area').click(function(){
		var inwidth = $(this).width();
		var dis = $('.panel').css("display");
		if(dis=='none'){
			$('.panel').css({
				display : 'block',
				width : inwidth
			});

			$('.combo-panel').empty();
			$('.combo-panel').html('Loading...........');
			$.ajax({
				url     : '<?php echo site_url('ajax/getArea')?>',
				dataType: 'html',
				type    : 'POST',
				success : function(data){
					$('.combo-panel').html(data);
				}
			});
		}else{
			$('.combo-panel').empty();
			$('.panel').css({
				display : 'none'
			});
			$('.area').data('clicked',0);
		}
	});

	$('#data_5 .input-daterange').datepicker({
		keyboardNavigation: false,
		forceParse: false,
		autoclose: true,
		format:"dd-mm-yyyy",
		language: 'id'
	});

	$('#btncari').click(function(){
		load_url('<?php echo site_url('monfp/pagging/0') ?>',"<?php echo (isset($order)?$order:'checktime')?>","<?php echo (isset($typeorder)?$typeorder:'sorting')?>");
	});
});
</script>
