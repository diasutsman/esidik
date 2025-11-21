<a href="#" onClick="showSimpanTmpForm();" class="btn btn-info btn-sm">Simpan</a>
<a href="#" onClick="showDelTmpForm();" class="btn btn-danger btn-sm">Hapus</a>
<div class="table-responsive">
    <div class="dataTables_wrapper dt-bootstrap">
    <table class="table table-striped  table-bordered">
        <thead>
        <tr>
            <th><input type="checkbox" name="cek_lst_all" id="cek_lst_all"></th>
            <th>No. Serial</th>
            <th>Koneksi Terakhir</th>
            <th>IP Address</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach($resulttemp as $rowx)
        {
            ?>
            <tr data-id="rowtmp-<?php echo $rowx->sn?>" id="rowtmp-<?php echo $rowx->sn?>">
                <td>
                <input type="checkbox" name="cek_lst" id="cek_lst_<?php echo $rowx->sn?>" class="selectedlst" value="<?php echo $rowx->sn?>"></td>
                <td><?php echo $rowx->sn;?></td>
                <td><?php echo $rowx->condate;?></td>
                <td><?php echo $rowx->ipaddress;?></td>
            </tr>
            <?php
        }

        if (count($resulttemp)==0)
        {
            ?>
            <tr class="">
                <td colspan="4"><center>Tidak ada data..</center></td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
    </div>
</div>

<script>
    function showSimpanTmpForm() {
        var cek = $(".selectedlst:checked").length;
        if (cek > 0) {
            showPopup('#popup5');

        } else {
            bootbox.alert("Harap pilih data..!");
        }
    }


    function showDelTmpForm() {
        var cek = $(".selectedlst:checked").length;
        if (cek > 0) {
            bootbox.confirm("Anda yakin menghapus data mesin?", function (result) {
                if (result) {
                    var cek_del = $('input[name=cek_lst]:checked').map(function () {
                        return $(this).val();
                    }).get();

                    $.ajax({
                        url: '<?php echo site_url('setdev/deltmp');?>',
                        dataType: 'json',
                        type: 'POST',
                        data: {'id': cek_del},
                        success: function (data) {
                              for(var i = 0; i < cek_del.length; i++) {
                                var idrow = cek_del[i].replace(/^\s*/, "").replace(/\s*$/, "");
                                $("#rowtmp-"+idrow).remove();
                            }
                            bootbox.alert(data.msg);
                        }
                    });
                }
            });

        } else {
            bootbox.alert("Harap pilih data..!");
        }
    }
</script>
