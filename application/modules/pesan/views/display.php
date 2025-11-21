<link href="<?php echo base_url()?>assets/css/plugins/chosen/bootstrap-chosen.css" rel="stylesheet">
<script src="<?php echo base_url()?>assets/js/plugins/chosen/chosen.jquery.js"></script>
<?php
/**
 * File: list.php
 * Author: abdiIwan.
 * Date: 12/28/2016
 * Time: 8:40 PM
 * absensi.kemendagri.go.id
 */

$url_pag =  "'".site_url("pesan/pagging/0")."'";
$domId ="'#list-data'";

?>
<div class="row">
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5>Pesan</h5>
				<div class="ibox-tools">
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
				</div>
			</div>
			<div class="ibox-content">

				<div class="row">
					<div class="col-sm-6 m-b-xs">
						<a  href="#" class="btn btn-sm btn-danger" onClick="doDelete();" ><i class="fa fa-minus"></i> Hapus</a> <a id="btnAdd" class="btn btn-sm btn-success"><i class="fa fa-plus"></i> Tambah</a>
					</div>
					<div class="col-sm-3 m-b-xs">

					</div>
					<div class="col-sm-3">
						<div class="input-group">
							<input type="text" id="caridata" name="caridata" placeholder="Pencarian" class="input-sm form-control" >
							<span class="input-group-btn"><button type="button" id="btncari" class="btn btn-sm btn-primary"> <i class="fa fa-search"></i> </button> </span>
						</div>
					</div>
				</div>
				<div id="form-data">
					<?php include ("form.php") ?>
				</div>
				<div id="list-data">
					<?php include ("list.php") ?>
				</div>
			</div>
		</div>
</div>

<script>
	$(document).ready(function(){
		$('.chosen-select').chosen({width: "100%"});
		$("#btnAdd").click(function(e){
			$("#view-form").slideDown('fast');
			$("#txt2").val();
			var $radios = $('input:radio[name=txt3]');
			$radios.filter('[value=1]').prop('checked', true);
			location.hash = "#inputForm";
			$("#btnAdd").hide();
		});

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
						url     : '<?php echo site_url('pesan/hapus');?>',
						dataType: 'json',
						type    : 'POST',
						data    : { 'id' : cek_del},
						success : function(data){
							if (data.status=='succes')
							{
								for(var i = 0; i < cek_del.length; i++) {
									var idrow = cek_del[i].replace(/^\s*/, "").replace(/\s*$/, "");
									$("#rowdata-"+idrow).remove();
								}

							}
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
</script>

