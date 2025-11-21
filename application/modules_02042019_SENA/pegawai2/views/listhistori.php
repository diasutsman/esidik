<?php
/**
 * File: list.php
 * Author: abdiIwan.
 * Date: 12/28/2016
 * Time: 9:38 PM
 * absensi.kemendagri.go.id
 */

?>

<div class="tabs-container">
    <ul class="nav nav-tabs">
        <li class="active btn-warning"><a data-toggle="tab" href="#tab-1">Kelas Jabatan</a></li>
        <li class="btn-info"><a data-toggle="tab" href="#tab-2">Tunjangan Profesi</a></a></li>
    </ul>
    <div class="tab-content">
        <div id="tab-1" class="tab-pane active">
            <div class="table-responsive">
                <div class="dataTables_wrapper dt-bootstrap">
                    <table class="table table-striped table-bordered small">
                        <thead>
                        <tr>
                            <th>Kelas</th>
                            <th>TMT</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $co=1;
                        $jm=0;
                        foreach($result as $row)
                        {
                            $jm=$co;
                            ?>
                            <tr id="rowkelas-<?php echo $row->id?>">
                                <td><?php echo $row->kelas;?></td>
                                <td><?php echo format_date_singkat($row->tmtjabatan);?></td>
                                <td><?php if ( $aksesrule["flagdelete"] ) { ?><a class="btn btn-xs btn-warning" title="Hapus data" href="#" onclick="hapusKelas('<?php echo $row->id?>')"><i class="fa fa-remove"></i></a><?php } ?>
                                </td>
                            </tr>
                            <?php

                            $co++;
                        }

                        if (count($result)==0)
                        {
                            ?>
                            <tr class="">
                                <td colspan="3"><center>Tidak ada data..</center></td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div id="tab-2" class="tab-pane">
            <div class="table-responsive">
                <div class="dataTables_wrapper dt-bootstrap">
                    <table class="table table-striped table-bordered small">
                        <thead>
                        <tr>
                            <th>TMT</th>
                            <th>Tunjangan</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $co=1;
                        $jm=0;
                        foreach($result2 as $row)
                        {
                            $jm=$co;
                            ?>
                            <tr id="rowprof-<?php echo $row->id?>">
                                <td><?php echo format_date_singkat($row->tunjprofdate);?></td>
                                <td><?php echo format_angka($row->tunjanganprofesi);?></td>
                                <td><a class="btn btn-xs btn-warning" title="Hapus data" href="#" onclick="hapusProf('<?php echo $row->id?>')"><i class="fa fa-remove"></i></a></td>
                            </tr>
                            <?php

                            $co++;
                        }

                        if (count($result)==0)
                        {
                            ?>
                            <tr class="">
                                <td colspan="3"><center>Tidak ada data..</center></td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>



<script>
function hapusKelas(vID) {
    bootbox.confirm("Anda yakin menghapus data yang dipilih?", function(result) {
        if (result) {
            $.ajax({
                url     : '<?php echo site_url('pegawai/hapuskelas');?>',
                dataType: 'json',
                type    : 'POST',
                data    : { 'id' : vID},
                success : function(data){
                    if (data.status=='succes')
                    {
                        $("#rowkelas-"+vID).remove();
                    }
                    bootbox.alert(data.msg);
                }
            });
        }
    });
}

function hapusProf(vID) {
    bootbox.confirm("Anda yakin menghapus data yang dipilih?", function(result) {
        if (result) {

            $.ajax({
                url     : '<?php echo site_url('pegawai/rowprof');?>',
                dataType: 'json',
                type    : 'POST',
                data    : { 'id' : vID},
                success : function(data){
                    if (data.status=='succes')
                    {
                        $("#rowprof-"+vID).remove();
                    }
                    bootbox.alert(data.msg);
                }
            });
        }
    });
}

</script>
