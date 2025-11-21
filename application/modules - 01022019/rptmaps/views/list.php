<style>
    .tableBodyScroll tbody {
        display:block;
        max-height:300px;
        overflow-y:scroll;
    }
    .tableBodyScroll thead, tbody tr {
        display:table;
        width:100%;
        table-layout:fixed;
    }
</style>
<div class="table-responsive">
    <div class="dataTables_wrapper dt-bootstrap">
    <table class="table table-striped table-bordered tableBodyScroll" >
        <thead>
        <tr>
            <th width="100px">No. Serial</th>
            <th width="100px">Nama Alat</th>
            <th width="100px">Status</th>
            <!--<th width="100px">Unit Kerja</th>-->
            <th width="100px">Aktivitas Terakhir</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $co=1;
        $jm=0;
        foreach($result as $row)
        {
            $lblStatus = $row->status==1?"Online":"Offline";
            $colStatus = $row->status==1?"#00CC00":"#953b39";
            $jm=$co;
            ?>
            <tr>
                <td><?php echo $row->sn;?></td>
                <td><?php echo $row->alias;?></td>
                <td style="background-color:<?php echo $colStatus;?>;color: #ffffff"><?php echo $lblStatus;?></td>
                <!--<td><?php /*echo $row->deptname;*/?></td>-->
                <td><?php echo $row->lastactivity;?></td>
            </tr>
            <?php

            $co++;
        }

        if (count($result)==0)
        {
            ?>
            <tr class="">
                <td colspan="10"><center>Tidak ada data..</center></td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>

    </div>
</div

