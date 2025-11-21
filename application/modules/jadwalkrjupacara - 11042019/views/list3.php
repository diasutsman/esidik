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
				<th>Waktu Upacara</th>
                <th>Absen</th>
                <th>Kehadiran</th>
                <th>Ketidakhadiran</th>
                <th>Recorded</th>
            </tr>
            </thead>
            <tbody>
                <?php

                foreach($lstData as $val =>$key) {
				?>
                <tr>
                    <td><?php echo $key["name"]?></td>
                    <td><?php echo $key["transdate"]?></td>
					<td><?php echo $key["kegiatan"]?></td>
                    <td><?php echo $key["transtime"]?></td>
                    <td><?php echo $key["attendance"]?></td>
                    <td><?php echo $key["absence"]?></td>
                    <td><?php echo $key["editby"]?></td>
                </tr>
                <?php }
            ?>
            </tbody>
        </table>
    </div>
</div>