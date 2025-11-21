<?php
/**
 * File: list.php
 * Author: abdiIwan.
 * Date: 12/28/2016
 * Time: 9:38 PM
 * absensi.kemendagri.go.id
 */

$url_pag =  site_url("setdev/pagging/0");

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
    <table class="table table-striped  table-bordered">
        <thead>
        <tr>
            <th><input type="checkbox" name="cek_all" id="cek_all"></th>
            <!--<th  onClick="load_url('<?php /*echo $url_pag*/?>','terminal_id','<?php /*echo (isset($order)?(($order=='terminal_id')?$sorting:'sorting'):'sorting_asc')*/?>')"><div class="<?php /*echo (isset($order)?(($order=='terminal_id')?$typeorder:'sorting'):'sorting_asc')*/?>">ID</div></th>-->
            <th  onClick="load_url('<?php echo $url_pag?>','sn','<?php echo (isset($order)?(($order=='sn')?$sorting:'sorting'):'sorting_asc')?>')"><div class="<?php echo (isset($order)?(($order=='sn')?$typeorder:'sorting'):'sorting_asc')?>">No. Serial</div></th>
            <th  onClick="load_url('<?php echo $url_pag?>','alias','<?php echo (isset($order)?(($order=='alias')?$sorting:'sorting'):'sorting_asc')?>')"><div class="<?php echo (isset($order)?(($order=='alias')?$typeorder:'sorting'):'sorting_asc')?>">Nama Alat</div></th>
            <th  onClick="load_url('<?php echo $url_pag?>','status','<?php echo (isset($order)?(($order=='status')?$sorting:'sorting'):'sorting_asc')?>')"><div class="<?php echo (isset($order)?(($order=='status')?$typeorder:'sorting'):'sorting_asc')?>">Status</div></th>
            <th  onClick="load_url('<?php echo $url_pag?>','areaname','<?php echo (isset($order)?(($order=='areaname')?$sorting:'sorting'):'sorting_asc')?>')"><div class="<?php echo (isset($order)?(($order=='areaname')?$typeorder:'sorting'):'sorting_asc')?>">Area</div></th>
            <th  onClick="load_url('<?php echo $url_pag?>','deptname','<?php echo (isset($order)?(($order=='deptname')?$sorting:'sorting'):'sorting_asc')?>')"><div class="<?php echo (isset($order)?(($order=='deptname')?$typeorder:'sorting'):'sorting_asc')?>">Unit Kerja</div></th>
            <th  onClick="load_url('<?php echo $url_pag?>','errdelay','<?php echo (isset($order)?(($order=='errdelay')?$sorting:'sorting'):'sorting_asc')?>')"><div class="<?php echo (isset($order)?(($order=='errdelay')?$typeorder:'sorting'):'sorting_asc')?>">Err Delay</div></th>
            <th  onClick="load_url('<?php echo $url_pag?>','delay','<?php echo (isset($order)?(($order=='delay')?$sorting:'sorting'):'sorting_asc')?>')"><div class="<?php echo (isset($order)?(($order=='delay')?$typeorder:'sorting'):'sorting_asc')?>">Delay</div></th>
            <th  onClick="load_url('<?php echo $url_pag?>','timezone','<?php echo (isset($order)?(($order=='timezone')?$sorting:'sorting'):'sorting_asc')?>')"><div class="<?php echo (isset($order)?(($order=='timezone')?$typeorder:'sorting'):'sorting_asc')?>">TimeZone</div></th>
            <th  onClick="load_url('<?php echo $url_pag?>','lastactivity','<?php echo (isset($order)?(($order=='lastactivity')?$sorting:'sorting'):'sorting_asc')?>')"><div class="<?php echo (isset($order)?(($order=='lastactivity')?$typeorder:'sorting'):'sorting_asc')?>">Aktivitas Terakhir</div></th>
            <th  onClick="load_url('<?php echo $url_pag?>','ipaddress','<?php echo (isset($order)?(($order=='ipaddress')?$sorting:'sorting'):'sorting_asc')?>')"><div class="<?php echo (isset($order)?(($order=='ipaddress')?$typeorder:'sorting'):'sorting_asc')?>">IP</div></th>
            <th  onClick="load_url('<?php echo $url_pag?>','user_count','<?php echo (isset($order)?(($order=='user_count')?$sorting:'sorting'):'sorting_asc')?>')"><div class="<?php echo (isset($order)?(($order=='user_count')?$typeorder:'sorting'):'sorting_asc')?>">User</div></th>
            <th>FP</th>
            <th>Transaksi</th>
            <th  onClick="load_url('<?php echo $url_pag?>','is_reguler','<?php echo (isset($order)?(($order=='is_reguler')?$sorting:'sorting'):'sorting_asc')?>')"><div class="<?php echo (isset($order)?(($order=='is_reguler')?$typeorder:'sorting'):'sorting_asc')?>">Jenis Mesin</div></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $co=1;
        $jm=0;
        foreach($result as $row)
        {
            /*$last = strtotime($row->lastactivity);
            $cur = strtotime(date("Y-m-d H:i:s"));
            $avg = $cur - $last;

            if ($row->status !=2)
            {
                $stat = array('status' => 0);
                if($avg >($row->delay+120)) {
                    $this->db->update('iclock', $stat, array('sn'=>$row->sn));
                }
            }*/

            switch ($row->status)
            {
                case 1:
                    $lblStatus = "Online";
                    $colStatus = "#00CC00";
                    break;
                case 2:
                    $lblStatus = "Komunikasi";
                    $colStatus = "#ffbc2b";
                    break;
                default:
                    $lblStatus = "Offline";
                    $colStatus = "#953b39";
                    break;
            }
            /*$lblStatus = $row->status==1?"Online":"Offline";
            $colStatus = $row->status==1?"#00CC00":"#953b39";*/
            $jm=$co;
            ?>
            <tr>
                <td width="20px"><input type="checkbox" name="cek_del" id="cek_del_<?php echo $row->id?>" class="selected" value="<?php echo $row->id?>"></td>
                <!--<td><?php /*echo $row->terminal_id;*/?></td>-->
                <td><?php echo $row->sn;?></td>
                <td><?php echo $row->alias;?></td>
                <td style="background-color:<?php echo $colStatus;?>;color: #ffffff"><?php echo $lblStatus;?></td>
                <td><?php echo $row->areaname;?></td>
                <td><?php echo $row->deptname;?></td>
                <td><?php echo $row->errdelay;?></td>
                <td><?php echo $row->delay;?></td>
                <td><?php echo $row->timezone;?></td>
                <td><?php echo $row->lastactivity;?></td>
                <td><?php echo $row->ipaddress;?></td>
                <td><?php echo $row->user_count;?></td>
                <td><?php echo $row->fp_count.' / '.$row->max_finger_count*100;?></td>
                <td><?php echo $row->transaction_count.' / '.$row->max_attlog_count*10000;?></td>
                <td><?php echo $row->is_reguler==0?"Upacara":"Reguler"?></td>
            </tr>
            <?php

            $co++;
        }

        if (count($result)==0)
        {
            ?>
            <tr class="">
                <td colspan="15"><center>Tidak ada data..</center></td>
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
                        <?php
                        $selected = '10';
                        if(isset($limit_display) && trim($limit_display) != '')
                            $selected = $limit_display;
                        $js = 'id="limit_display" class="input-sm" onChange="load_url(\''.$url_pag.'\',\''.(isset($order)?$order:'a.id').'\',\''.(isset($typeorder)?$typeorder:'sorting').'\');" name="tabel_data_length" size="1" ';
                        echo form_dropdown('tabel_data_length',listPager(),$selected,$js);
                        ?>
                        Rec. <?php echo (($jum_data==0)?'0':($offset+1))?> s/d <?php echo(($offset+$jm))?> dari <?php echo$jum_data?> data
                    </div>

                </div>
            </div>
            <div class="col-sm-6 m-b-xs" id="pagering">
                <div class="dataTables_paginate paging_simple_numbers">
                    <?php echo $paging;?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('#cek_all').click(function(){
            $(".selected").prop("checked", $("#cek_all").prop("checked"));
        });

        $('#cek_lst_all').click(function(){
            $(".selectedlst").prop("checked", $("#cek_lst_all").prop("checked"));
        });

        $('#btncari').click(function(){
            load_url('<?php echo site_url('setdev/pagging/0') ?>',"<?php echo (isset($order)?$order:'a.id')?>","<?php echo (isset($typeorder)?$typeorder:'sorting')?>");
        });
        createpaging();
        $("#caridata").keypress(function(e){
            var key = (e.keyCode ? e.keyCode : e.which);
            if(key==13){
                load_url('<?php echo site_url('setdev/pagging/0') ?>',"<?php echo (isset($order)?$order:'a.id')?>","<?php echo (isset($typeorder)?$typeorder:'sorting')?>");
            }
        });

        $('.selected').click(function(){
            if($(".selected").length == $(".selected:checked").length) {
                $("#cek_all").prop("checked", true);
            } else {
                $("#cek_all").prop("checked", false);
            }
        });

        $('.spinner .btn:first-of-type').on('click', function() {
            $('.spinner input').val( parseInt($('.spinner input').val(), 10) + 1);
        });
        $('.spinner .btn:last-of-type').on('click', function() {
            $('.spinner input').val( parseInt($('.spinner input').val(), 10) - 1);
        });

        $('.spinner2 .btn:first-of-type').on('click', function() {
            $('.spinner2 input').val( parseInt($('.spinner2 input').val(), 10) + 1);
        });
        $('.spinner2 .btn:last-of-type').on('click', function() {
            $('.spinner2 input').val( parseInt($('.spinner2 input').val(), 10) - 1);
        });
    });

    function createpaging()
    {
        $("#pagering").find("a").each(function(i){
            var thisHref = $(this).attr("href");
            $(this).prop('href','javascript:void(0)');
            $(this).prop('rel',thisHref);
            if ( !$( this ).prop( "class" ) ) {
                $(this).prop('class', 'paginate_button');
            }

            $(this).bind('click', function(){
                load_url(thisHref,"<?php echo (isset($order)?$order:'a.id')?>","<?php echo (isset($typeorder)?$typeorder:'sorting')?>");
                return false;
            });
        });
    }

    function load_url(theurl,orderby,sortingby)
    {
        var par4 = $('#limit_display').val();
        if ($('#caridata').val() != '')
            par5 = $('#caridata').val();
        else
            par5 = 'cri';

        if (theurl.substr(theurl.lastIndexOf('/') + 1) == "") {
            theurl=theurl+'0';
        }
        //loading();
        $.ajax({
            method:"post",
            url: theurl,
            data:{"lmt":par4,"cari":par5,'order':orderby,'sorting':sortingby},
            success: function(response){
                $('#list-data').html(response);
            },
            dataType:"html"
        });
        return false;
    }

    function RefreshData() {
        load_url('<?php echo site_url('setdev/pagging/0') ?>',"<?php echo (isset($order)?$order:'a.id')?>","<?php echo (isset($typeorder)?$typeorder:'sorting')?>");
    }



</script>
