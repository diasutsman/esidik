<div class="table-responsive">
    <div class="dataTables_wrapper dt-bootstrap">
    <table class="table table-striped  table-bordered">
        <thead>
        <tr>
            <th>No. Serial</th>
            <th>Koneksi Terakhir</th>
            <th>IP Address</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach($resulttemp as $rowx)
        {
            ?>
            <tr data-id="row-<?php echo $rowx->id?>">
                <td><?php echo $rowx->sn;?></td>
                <td><?php echo $rowx->condate;?></td>
                <td><?php echo $rowx->ipaddress;?></td>
                <td width="20px"><a class="btn btn-xs btn-warning editrow" data-id="<?php echo $rowx->id?>"><i class="fa fa-pencil"></i></a></td>

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
