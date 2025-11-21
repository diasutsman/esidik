<script src="<?php echo base_url() ?>assets/js/plugins/jsTree/jstree.min.js"></script>
<link href="<?php echo base_url() ?>assets/css/plugins/jsTree/themes/proton/style.min.css" rel="stylesheet">
<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


?>
<div class="row">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>Data Pegawai</h5>
            <div class="ibox-tools">
                <a class="collapse-link">
                    <i class="fa fa-chevron-up"></i>
                </a>
            </div>
        </div>
        <div class="ibox-content">
            <div class="ibox float-e-margins ">
                <div class="ibox-content gray-bg">
                    <h5 style="font-size: 35px;text-align: center;"><b>404 <br>Oops! Sorry, that page could'nt be found.</b></h5>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var areaUsr = '<?php echo $listAreaUser?>';
        var mySplitResult = areaUsr.split(',');
        for (i = 0; i < mySplitResult.length; i++) {
            $('#areatree').jstree(true).select_node(mySplitResult[i]);
        }

    });

    function saveForm() {
        $('#area').val($('#areatree').jstree(true).get_selected());

        if (($("#area").val() != "")) {
            bootbox.confirm("Anda yakin menyimpan Area mesin untuk pegawai tersebut", function (result) {
                if (result) {
                    $.ajax({
                        url: '<?php echo site_url('pegawai/simpanarea');?>',
                        dataType: 'json',
                        type: 'POST',
                        data: $("#inputForm").serialize(),
                        success: function (data) {
                            bootbox.alert(data.msg);
                        }
                    });
                }
            });
        } else {
            bootbox.alert("Harap cek kembali Area yang dipilih!!");
        }

    }


</script>