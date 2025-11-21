<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<table class="head">
    <tr>
        <td style="border:0px" class="text">NIP</td>
        <td width="150" style="border:0px" class="text">Nama</td>
        <td style="border:0px" class="text">Jumlah</td>
    </tr>
    <?php
    foreach ($listdata as $row){
    ?>
        <tr>
            <td style="border:0px" class="text"><?php echo $row->nip?></td>
            <td width="150" style="border:0px" class="text"><?php echo $row->name?><</td>
            <td style="border:0px" class="text"><?php echo $row->jumlah?><</td>
        </tr>
    <?php }
    ?>
</table>


