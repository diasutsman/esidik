<div class="row">
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5>SMS Setting</h5>
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
									<label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt1">SIMPEG host URL</label>
									<div class="col-md-8 col-sm-8 col-xs-12">
										<input id="txt1" name="txt1" type="text" placeholder="SIMPEG host URL" value="<?php echo $result["simpeg_host"]?>" class="form-control col-md-5 col-xs-12">
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt2">SMS Sender</label>
									<div class="col-md-8 col-sm-8 col-xs-12">
										<input id="txt2" name="txt2" type="text" placeholder="SMS Sender" value="<?php echo $result["sms_sender"]?>" class="form-control col-md-5 col-xs-12" disabled="disabled">
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt3">Checkinout (Jam)</label>
									<div class="col-md-3 col-sm-3 col-xs-12">
										<input id="txt3" name="txt3" type="text" placeholder="Jam" value="<?php echo $result["checkinout_hour"]?>" class="form-control col-md-5 col-xs-12">
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt4">Checkinout (Menit)</label>
									<div class="col-md-3 col-sm-3 col-xs-12">
										<input id="txt4" name="txt4" type="text" placeholder="Menit" value="<?php echo $result["checkinout_minute"]?>" class="form-control col-md-5 col-xs-12">
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt1">Pesan SMS</label>
									<div class="col-md-8 col-sm-8 col-xs-12">
										Yth {nama}, <input id="txt5" name="txt5" type="text" placeholder="Pesan SMS" value="<?php echo $result["sms_msg1"]?>" class="form-control col-md-5 col-xs-12">
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
					required: true, minlength: 1, maxlength: 11
				}, txt3: {
					required: true, number:true, minlength: 1, maxlength: 2
				}, txt4: {
					required: true, number:true, minlength: 1, maxlength: 2
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
		<?php if ($aksesrule["flagedit"]) { ?>
		if (($("#txt1").val() != "")) {
            loading();
			$.ajax({
				url: '<?php echo site_url('setsms/save');?>', dataType: 'json', type: 'POST', data: $("#inputForm").serialize(), success: function (data) {
					bootbox.alert(data.msg);
				}
			});
            unloading();
		} else {
			bootbox.alert("Harap cek kembali inputannya ??");
		}
	<?php } else  {?>
		bootbox.alert("Maaf, akses anda dibatasi!!");
		<?php }?>
	}


</script>
