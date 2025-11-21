<?php
/**
 * File: display.php
 * Author: abdiIwan.
 * Date: 12/28/2016
 * Time: 8:40 PM
 * absensi.kemendagri.go.id
 */


?>
<div class="row">
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5>Setting Lainnya</h5>
				<div class="ibox-tools">
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
				</div>
			</div>
			<div class="ibox-content">
					<div class="ibox float-e-margins ">
						<div class="ibox-content gray-bg">
							<form class="form-horizontal form-label-left" name="inputForm" id="inputForm" method="post">
								<input type="hidden" name="iddata" id="iddata"/>
								<div class="form-group">
									<label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt1">Nama Institusi</label>
									<div class="col-md-8 col-sm-8 col-xs-12">
										<input id="txt1" name="txt1" type="text" placeholder="Nama Institusi" value="<?php echo $result["companyname"]?>" class="form-control col-md-5 col-xs-12">
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt2">Alamat</label>
									<div class="col-md-8 col-sm-8 col-xs-12">
										<input id="txt2" name="txt2" type="text" placeholder="Alamat" value="<?php echo $result["address1"]?>" class="form-control col-md-5 col-xs-12">
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt3">Telp</label>
									<div class="col-md-3 col-sm-3 col-xs-12">
										<input id="txt3" name="txt3" type="text" placeholder="Telp" value="<?php echo $result["phone"]?>" class="form-control col-md-5 col-xs-12">
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt4">Fax</label>
									<div class="col-md-3 col-sm-3 col-xs-12">
										<input id="txt4" name="txt4" type="text" placeholder="Fax" value="<?php echo $result["fax"]?>" class="form-control col-md-5 col-xs-12">
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt5">Porsentase Tunjangan</label>
									<div class="col-md-3 col-sm-3 col-xs-12">
										<input id="txt5" name="txt5" type="text" placeholder="Porsentase" value="<?php echo $result["thp"]?>" class="form-control col-md-5 col-xs-12">
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt6">Porsentase PLT</label>
									<div class="col-md-3 col-sm-3 col-xs-12">
										<input id="txt6" name="txt6" type="text" placeholder="PLT" value="<?php echo $result["plt"]?>" class="form-control col-md-5 col-xs-12">
									</div>
								</div>
								<div class="hr-line-dashed"></div>
								<div class="form-group">
									<div class="col-md-6 col-md-offset-3">
										<input type="submit" id="" name="" class="btn btn-primary btn-sm" value="Simpan"/>
									</div>
								</div>
							</form>
						</div>
					</div>
			</div>
		</div>
</div>

<script>
	$(document).ready(function () {
		$("#inputForm").validate({
			rules: {
				txt1: {
					required: true, minlength: 1, maxlength: 100
				}, txt2: {
					required: true, minlength: 1, maxlength: 255
				}, txt3: {
					required: true, minlength: 1, maxlength: 50
				}, txt4: {
					required: true, minlength: 1, maxlength: 50
				}, txt5: {
					required: true, number:true, minlength: 1, maxlength: 15
				}, txt6: {
					required: true, number:true, minlength: 1, maxlength: 15
				}
			}, submitHandler: function () {
				saveForm();
			}, highlight: function (element) {
				$(element).closest('.form-group').addClass('has-error');
			}, unhighlight: function (element) {
				$(element).closest('.form-group').removeClass('has-error');
			}
		});


	});

	function saveForm() {
		if (($("#txt1").val() != "")) {
			$.ajax({
				url: '<?php echo site_url('refseting/save');?>', dataType: 'json', type: 'POST', data: $("#inputForm").serialize(), success: function (data) {
					bootbox.alert(data.msg);
				}
			});
		} else {
			bootbox.alert("Harap cek kembali inputannya ??");
		}
	}


</script>
