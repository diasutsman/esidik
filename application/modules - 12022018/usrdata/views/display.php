<script src="<?php echo base_url()?>assets/js/plugins/jsTree/jstree.min.js"></script>
<link href="<?php echo base_url()?>assets/css/plugins/jsTree/themes/proton/style.css" rel="stylesheet">
<div class="row">
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5>Daftar Pengguna</h5>
				<div class="ibox-tools">
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
				</div>
			</div>
			<div class="ibox-content">
				<div class="row">
					<div class="col-sm-5 m-b-xs">
						<a  id="btnDeleteRow" href="#" class="btn btn-sm btn-danger" onClick="<?php echo $aksesrule["flagdelete"] ? "doDelete();" : "#"?>" ><i class="fa fa-minus"></i> Hapus</a> <a id="btnAdd" class="btn btn-sm btn-success"><i class="fa fa-plus"></i> Tambah</a>
					</div>
					<div class="col-sm-4 m-b-xs">
					</div>
					<div class="col-sm-3">
						<div class="input-group">
							<input type="text" id="caridata" name="caridata" placeholder="Pencarian" class="input-sm form-control" value="<?php echo isset($caridata)?$caridata:''?>">
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
	$(function(){
		$("#btnAdd").click(function(e){
			$('#inputForm').trigger("reset");
            $('#ukertree').jstree(true).deselect_all();
            $('#areatree').jstree(true).deselect_all();
            $('#areatree').jstree(true).uncheck_all();
            $('#ukertree').jstree(true).uncheck_all();
			location.hash = "#inputForm";
			var validator = $( "#inputForm" ).validate();
			validator.resetForm();
            <?php if ($aksesrule["flagadd"]) {?>

            $("#view-form").slideDown('fast');
			$("#btnAdd").hide(); $("#btnDeleteRow").hide();
            <?php } ?>
		});

		$('#cek_all').click(function(){
			$(".selected").prop("checked", $("#cek_all").prop("checked"));
		});

		$('#btncari').click(function(){
			load_url('<?php echo site_url('usrdata/pagging/0') ?>','<?php echo (isset($order)?$order:'id')?>','<?php echo (isset($typeorder)?$typeorder:'sorting')?>');
		});

		$("#caridata").keypress(function(e){
			var key = (e.keyCode ? e.keyCode : e.which);
			if(key==13){
				load_url('<?php echo site_url('usrdata/pagging/0') ?>','<?php echo (isset($order)?$order:'id')?>','<?php echo (isset($typeorder)?$typeorder:'sorting')?>');
			}
		});

		$('.selected').click(function(){
			if($(".selected").length == $(".selected:checked").length) {
				$("#cek_all").prop("checked", true);
			} else {
				$("#cek_all").prop("checked", false);
			}
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
						url     : '<?php echo site_url('usrdata/hapus');?>',
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
