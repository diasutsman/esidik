<?php
/**
 * File: list.php
 * Author: abdiIwan.
 * Date: 12/28/2016
 * Time: 9:38 PM
 * absensi.kemendagri.go.id
 */

?>

<link href="<?php echo base_url()?>assets/css/plugins/chosen/bootstrap-chosen.css" rel="stylesheet">
<script src="<?php echo base_url()?>assets/js/plugins/chosen/chosen.jquery.js"></script>
<script src="<?php echo base_url() ?>assets/js/plugins/jsTree/jstree.min.js"></script>
<link href="<?php echo base_url() ?>assets/css/plugins/jsTree/themes/proton/style.min.css" rel="stylesheet">
<div class="row">
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5>Daftar Pegawai Simpeg yang Belum Ter-registrasi</h5>
				<div class="ibox-tools">
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
				</div>
			</div>
			<div class="ibox-content">
				<div id="list-data">
					<?php include ("list-simpeg.php") ?>
				</div>
			</div>
		</div>
</div>

<script>
	$(function(){




	});

    function RefreshData()
    {
        load_url('<?php echo site_url('pegawai/listpagging/0') ?>',"<?php echo (isset($order)?$order:'id')?>","<?php echo (isset($typeorder)?$typeorder:'sorting')?>");
    }

    function doSinkronData() {
        var cMsg= "Mengambil <b>semua data pegawai</b> di SIMPEG ?<br>Waktu proses membutuhkan waktu cukup lama.. ";

        bootbox.confirm(cMsg, function (result) {
            if (result) {
                $.ajax({
                    url: '<?php echo site_url('pegawai/getdatasimpeg');?>',
                    dataType: 'json',
                    type: 'POST',
                    success: function (data) {
                        bootbox.alert(data.msg);
                    },
                    beforeSend: function () {
                        $("#resend").prop('disabled', true);
                        $("#resend").html('<i class="fa fa-key"></i> Processing');
                    },
                    complete: function () {
                        $("#resend").prop('disabled', false);
                        $("#resend").html('<i class="fa fa-repeat"></i> Resend');
                    }
                });
            }
        });
    }

</script>

