<?php
/**
 * File: list.php
 * Author: abdiIwan.
 * Date: 12/28/2016
 * Time: 9:38 PM
 * absensi.kemendagri.go.id
 */
?>
<div class="row">
    <div class="col-lg-12">
        <div class="tabs-container">
            <ul class="nav nav-tabs">
                <li class="active btn-warning"><a data-toggle="tab" href="#tab-1"><span class="label label-warning">Transaksi Log</span></a></li>
                <li class="btn-info"><a data-toggle="tab" href="#tab-2"><span class="label label-info">Daftar shif</span></a></a></li>
            </ul>
            <div class="tab-content">
                <div id="tab-1" class="tab-pane active">
                    <div class="panel-body">
                        <div id="list-data-history">
                            <div class="table-responsive" style="height: 200px !important;overflow: scroll;">
                                <div class="dataTables_wrapper dt-bootstrap">
                                    <table class="table table-striped small">
                                        <thead>
                                        <tr>
                                            <th>Nama</th>
                                            <th>Tanggal</th>
                                            <th>Waktu</th>
                                            <th>Kehadiran</th>
                                            <th>Ketidakhadiran</th>
                                            <th>Recorded</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="tab-2" class="tab-pane">
                    <div class="panel-body">
                        <div class="table-responsive" style="height: 200px !important;overflow: scroll;">
                            <div class="dataTables_wrapper dt-bootstrap">
                                <table class="table table-striped ">
                                    <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Keterangan</th>
                                        <th>Masuk</th>
                                        <th>Keluar Istirahat</th>
                                        <th>Masuk Istirahat</th>
                                        <th>Keluar</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    foreach($lstshift as $row)
                                    {
                                        ?>
                                        <tr class="rows">
                                            <td style="background-color: <?php echo $row->colour_shift;?>"><?php echo $row->code_shift;?></td>
                                            <td><?php echo $row->name_shift;?></td>
                                            <td><?php echo format_jammenit($row->check_in);?></td>
                                            <td><?php echo format_jammenit($row->end_check_in);?></td>
                                            <td><?php echo format_jammenit($row->start_out);?></td>
                                            <td><?php echo format_jammenit($row->check_out);?></td>
                                        </tr>
                                        <?php
                                    }

                                    if (count($lstshift)==0)
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>