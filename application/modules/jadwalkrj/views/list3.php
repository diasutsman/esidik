<?php
/**
 * File: list3.php
 * Author: abdiIwan.
 * Date: 1/5/2017
 * Time: 1:52 AM
 * absensi.kemendagri.go.id
 */
?>
<div class="table-responsive" style="height: 200px !important;overflow: scroll;">
    <div class="dataTables_wrapper dt-bootstrap">
        <table class="table table-striped small">
            <thead>
            <tr>
                <th>Nama</th>
                <th>Tanggal</th>
                <th>Waktu</th>
                <th>Status</th>
                <th>Kehadiran</th>
                <th>Ketidakhadiran</th>
                <th>Lokasi</th>
                <th>Recorded</th>
            </tr>
            </thead>
            <tbody>
                <?php
               
                foreach($lstData as $val =>$key) {
                    if($key["status"] == '29' OR $key["atid"] == 'AT_WFH1' OR $key["atid"] == 'AT_WFH2' OR $key["atid"] == 'AT_WFH3' ){
                        $status = "WFH";
                    }else if($key["abid"] == 'AB_1' OR $key["abid"] == 'AB_2' OR $key["abid"] == 'AB_3' OR
                             $key["abid"] == 'AB_4' OR $key["abid"] == 'AB_5' OR $key["abid"] == 'AB_6' OR
                             $key["abid"] == 'AB_7' OR $key["abid"] == 'AB_8' OR $key["abid"] == 'AB_9' OR
                             $key["abid"] == 'AB_10' OR $key["abid"] == 'AB_12' OR $key["abid"] == 'AB_13' OR
                             $key["abid"] == 'AB_14' OR $key["abid"] == 'AB_15' OR $key["abid"] == 'AB_16' OR
                             $key["abid"] == 'AB_17' OR $key["abid"] == 'AB_18' OR $key["abid"] == 'AB_19' OR $key["abid"] == 'AB_20' OR $key["abid"] == 'WFH'){
                        $status = "";
                    }else{
                        $status = "WFO";
                    }
                ?>
                <tr>

                    <td><?php echo $key["name"]?></td>
                    <td><?php echo $key["transdate"]?></td>
                    <td><?php echo $key["transtime"]?></td>
                    <td><?php echo $status?></td>
                    <td><?php echo $key["attendance"]?></td>
                    <td><?php echo $key["absence"]?></td>
                    <td><?php echo $key["lokasi"]?></td>
                    <td><?php echo $key["editby"]?></td>
                </tr>
                <?php }
            ?>
            </tbody>
        </table>
    </div>
</div>