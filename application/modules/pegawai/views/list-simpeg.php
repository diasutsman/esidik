<?php
/**
 * File: list.php
 * Author: abdiIwan.
 * Date: 12/28/2016
 * Time: 9:38 PM
 * absensi.kemendagri.go.id
 */

$url_pag =  site_url("pegawai/listpagging/0");

$sorting='';
if($typeorder=='sorting' || $typeorder=='sorting_desc'){
    $sorting='sorting_asc';
}

if($typeorder=='sorting_asc'){
    $sorting='sorting_desc';
}
?>

<div class="table-responsive">
    <div class="dataTables_wrapper dt-bootstrap">
        <table class="table table-striped table-bordered small">
            <thead>
            <tr>
                <th >NIP</th>
                <th >Nama</th>
                <th >Kelas</th>
                <th >Unit Kerja</th>
                <th >Jabatan</th>
                <th >Status</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $co=1;
            $jm=0;
            if(isset($ListArray)) {
                foreach ($ListArray as $row) {
                    $jm = $co;
                    ?>
                    <tr >
                        <td><a href="<?php echo site_url("pegawai/adddata/".$row['nip']) ?>" target="_blank"> <?php echo $row['nip']; ?></td>
                        <td><?php echo $row['nama']; ?></td>
                        <td><?php echo $row['kelasjabatan']?></td>
                        <td><?php echo $row['nama_unor']; ?></td>
                        <td><?php echo $row['jabatan']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                    </tr>
                    <?php
                    $co++;
                }
            } else
            {
                ?>
                <tr class="">
                    <td colspan="6"><center>Tidak ada data..</center></td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        <div class="row form-inline ">
            <div class="col-sm-6 m-b-xs">
                <div id="tabel_data_length" class="dataTables_length">
                    <div class="form-inline">
                        Rec. <?php echo$jum_data?> data
                    </div>
                </div>
            </div>
            <div class="col-sm-6 m-b-xs" id="pagering">
            </div>
        </div>
    </div>
</div>

<script>
    $(function(){

    });

</script>
