<script src="<?php echo base_url()?>assets/js/plugins/validate/jquery.validate.min.js"></script>
<script src="<?php echo base_url()?>assets/js/plugins/validate/messages_id.js"></script>
<script src="<?php echo base_url()?>assets/js/plugins/colorpicker/jquery.minicolors.js"></script>
<link href="<?php echo base_url()?>assets/js/plugins/colorpicker/jquery.minicolors.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url()?>assets/js/plugins/datapicker/wickedpicker.min.js"></script>
<link href="<?php echo base_url()?>assets/js/plugins/datapicker/wickedpicker.min.css" rel="stylesheet">
<?php
/**
 * File: display.php
 * Author: abdiIwan.
 * Date: 12/28/2016
 * Time: 8:40 PM
 * absensi.kemendagri.go.id
 */

$url_pag =  "'".site_url("shift/pagging/0")."'";
$domId ="'#list-data'";

?>
<div class="row">
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5>Daftar Shift Kerja</h5>
				<div class="ibox-tools">
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
				</div>
			</div>
			<div class="ibox-content">
				<div class="row">
					<div class="col-sm-5 m-b-xs">
						<a href="#" class="btn btn-sm btn-danger" onClick="doDelete();" ><i class="fa fa-minus"></i> Hapus</a> <a id="btnAdd" class="btn btn-sm btn-success"><i class="fa fa-plus"></i> Tambah</a>
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
					<div id="list-data">
					<?php include ("list.php") ?>
				</div>

			</div>
		</div>
</div>
<script>
	var isNew=0;
	$(document).ready(function(){

		$("#btnAdd").click(function(e){
			if (isNew==0) {

				var tblBody = document.getElementById("tableku").tBodies[0];
				var newRow = tblBody.insertRow(0);
				newRow.id="newdata";
				newRow.insertCell(0).innerHTML = '';
				cell = newRow.insertCell(1);
				cell.innerHTML = '<input type="input"  width="100%" name="code_shift" class="newdata" />';
				cell.style.width = '40px';
				cell =  newRow.insertCell(2);
				cell.innerHTML = '<input type="input" width="100%" name="name_shift" class="newdata"/>';
				cell.style.width= "103px";
				cell =  newRow.insertCell(3);
				cell.innerHTML = '<input  width="100%" type="input" name="start_in" class="newdata timepicker""/>';
				cell.style.width= "50px";
				cell =  newRow.insertCell(4);
				cell.innerHTML = '<input  width="100%" type="input" name="check_in" class="newdata timepicker""/>';
				cell.style.width= "50px";
				cell =  newRow.insertCell(5);
				cell.innerHTML = '<input  width="100%" type="input" name="end_check_in" class="newdata timepicker""/>';
				cell.style.width= "50px";
				cell =  newRow.insertCell(6);
				cell.innerHTML = '<input  width="100%" type="input" name="start_break" class="newdata timepicker""/>';
				cell.style.width= "50px";
				cell =  newRow.insertCell(7);
				cell.innerHTML = '<input  width="100%" type="input" name="break_out" class="newdata timepicker""/>';
				cell.style.width= "50px";
				cell =  newRow.insertCell(8);
				cell.innerHTML = '<input  width="100%" type="input" name="break_in" class="newdata timepicker""/>';
				cell.style.width= "50px";
				cell =  newRow.insertCell(9);
				cell.innerHTML = '<input  width="100%" type="input" name="end_break" class="newdata timepicker""/>';
				cell.style.width= "50px";
				cell =  newRow.insertCell(10);
				cell.innerHTML = '<input  width="100%" type="input" name="start_out" class="newdata timepicker""/>';
				cell.style.width= "50px";
				cell =  newRow.insertCell(11);
				cell.innerHTML = '<input  width="100%" type="input" name="check_out" class="newdata timepicker""/>';
				cell.style.width= "50px";
				cell =  newRow.insertCell(12);
				cell.innerHTML = '<input  width="100%" type="input" name="end_check_out" class="newdata timepicker""/>';
				cell.style.width= "50px";
				cell =  newRow.insertCell(13);
				cell.innerHTML = '<input  width="100%" type="input" name="late_tolerance" class="newdata"/>';
				cell.style.width= "50px";
				cell =  newRow.insertCell(14);
				cell.innerHTML = '<input  width="100%" type="input" name="early_departure" class="newdata"/>';
				cell.style.width= "50px";
				cell =  newRow.insertCell(15);
				cell.innerHTML = '<input  width="100%" type="input" name="ot_tolerance" class="newdata"/>';
				cell.style.width= "50px";
				newRow.insertCell(16).innerHTML = '<select name="shift_in" class="newdata"><option value="0">T</option><option value="1">Y</option></select>';
				newRow.insertCell(17).innerHTML = '<select  name="shift_out" class="newdata"><option value="0">T</option><option value="1">Y</option></select>';
				newRow.insertCell(18).innerHTML = '<select  name="in_ot_tolerance" class="newdata"><option value="0">T</option><option value="1">Y</option></select>';
				newRow.insertCell(19).innerHTML = '<select  name="out_ot_tolerance" class="newdata"><option value="0">T</option><option value="1">Y</option></select>';
				newRow.insertCell(20).innerHTML = '<input type="input" name="colour_shift" class="colorpict"/>';
				$('.colorpict').minicolors({
					format: $(this).attr('data-format') || 'hex', inline: $(this).attr('data-inline') === 'true', letterCase: $(this).attr('data-letterCase') || 'lowercase', theme: 'default'
				});
				newRow.insertCell(21).innerHTML = '<a class="btn btn-xs btn-primary" onclick="doSave()" data-id="0" title="Save"><i class="fa fa-save"></i></a><a class="btn btn-xs btn-danger" onclick="doCancel();" data-id="0" title="Remove"><i class="fa fa-undo"></i></a>';
				isNew++;

				var options = {
					twentyFour: true,  //Display 24 hour format, defaults to false
					upArrow: 'wickedpicker__controls__control-up',  //The up arrow class selector to use, for custom CSS
					downArrow: 'wickedpicker__controls__control-down', //The down arrow class selector to use, for custom CSS
					close: 'wickedpicker__close', //The close class selector to use, for custom CSS
					hoverState: 'hover-state', //The hover state class to use, for custom CSS
					title: 'Jam', //The Wickedpicker's title,
					showSeconds: false, //Whether or not to show seconds,
					secondsInterval: 1, //Change interval for seconds, defaults to 1,
					minutesInterval: 15, //Change interval for minutes, defaults to 1
					beforeShow: null, //A function to be called before the Wickedpicker is shown
					show: null, //A function to be called when the Wickedpicker is shown
					clearable: false //Make the picker's input clearable (has clickable "x")
				};
				$('.timepicker').wickedpicker(options);

			}

		});

		$('#btncari').click(function(){
			load_url('<?php echo site_url('shift/pagging/0') ?>',"#list-data");
		});

		$("#caridata").keypress(function(e){
			var key = (e.keyCode ? e.keyCode : e.which);
			if(key==13){
				load_url('<?php echo site_url('shift/pagging/0') ?>',"#list-data");
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
						url     : '<?php echo site_url('shift/hapus');?>',
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

	function doCancel() {
		var rowIndex = document.getElementById("newdata").rowIndex;
		var table = document.getElementById ("tableku");
		if (table.rows.length > 1) {
			table.deleteRow(rowIndex);
			isNew=0;
		}

	}
	function doSave()
	{
			bootbox.confirm("Anda yakin menyimpan data tersebut?", function(result) {
				if(result) {
					var rowIndex = document.getElementById("newdata").rowIndex;
					var data = $("tr").eq(rowIndex).find("input,select").serializeArray();

					$.ajax({
						url     : '<?php echo site_url('shift/simpanbaru');?>',
						dataType: 'json',
						type    : 'POST',
						data    : data,
						success : function(data){
							bootbox.alert(data.msg);
						}
					});
				}
			});

	}
</script>
