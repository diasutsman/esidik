<?php
/**
 * File: list.php
 * Author: abdiIwan.
 * Date: 12/28/2016
 * Time: 9:38 PM
 * absensi.kemendagri.go.id
 */

$url_pag =  site_url("monfp/pagging/0");

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
                <th onClick="load_url('<?php echo $url_pag?>','sn','<?php echo (isset($order)?(($order=='sn')?$sorting:'sorting'):'sorting_asc')?>')"><div class="<?php echo (isset($order)?(($order=='sn')?$typeorder:'sorting'):'sorting_asc')?>">No.Serial</div></th>
                <th onClick="load_url('<?php echo $url_pag?>','devicename','<?php echo (isset($order)?(($order=='devicename')?$sorting:'sorting'):'sorting_asc')?>')"><div class="<?php echo (isset($order)?(($order=='devicename')?$typeorder:'sorting'):'sorting_asc')?>">Nama alat</div></th>
                <th onClick="load_url('<?php echo $url_pag?>','areaname','<?php echo (isset($order)?(($order=='areaname')?$sorting:'sorting'):'sorting_asc')?>')"><div class="<?php echo (isset($order)?(($order=='areaname')?$typeorder:'sorting'):'sorting_asc')?>">Area</div></th>
                <th onClick="load_url('<?php echo $url_pag?>','userid','<?php echo (isset($order)?(($order=='userid')?$sorting:'sorting'):'sorting_asc')?>')"><div class="<?php echo (isset($order)?(($order=='userid')?$typeorder:'sorting'):'sorting_asc')?>">NIP</div></th>
                <th onClick="load_url('<?php echo $url_pag?>','name','<?php echo (isset($order)?(($order=='name')?$sorting:'sorting'):'sorting_asc')?>')"><div class="<?php echo (isset($order)?(($order=='name')?$typeorder:'sorting'):'sorting_asc')?>">Nama</div></th>
                <th onClick="load_url('<?php echo $url_pag?>','checktime','<?php echo (isset($order)?(($order=='checktime')?$sorting:'sorting'):'sorting_asc')?>')"><div class="<?php echo (isset($order)?(($order=='checktime')?$typeorder:'sorting'):'sorting_desc')?>">Waktu</div></th>
                <th onClick="load_url('<?php echo $url_pag?>','verifycode','<?php echo (isset($order)?(($order=='verifycode')?$sorting:'sorting'):'sorting_asc')?>')"><div class="<?php echo (isset($order)?(($order=='verifycode')?$typeorder:'sorting'):'sorting_asc')?>">Verifikasi</div></th>
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
                <tr>
                    <td><?php echo $row->sn;?></td>
                    <td><?php echo $row->devicename;?></td>
                    <td><?php echo $row->areaname;?></td>
                    <td><?php echo $row->userid;?></td>
                    <td><?php echo $row->name;?></td>
                    <td><?php echo $row->checktime;?></td>
                    <td><?php echo $row->verifycode;?></td>
                </tr>
                <?php

                $co++;
            }

            if (count($result)==0)
            {
                ?>
                <tr class="">
                    <td colspan="7"><center>Tidak ada data..</center></td>
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
                        $options = array(
                            '10' => '10',
                            '25' => '25',
                            '50' => '50',
                            '100' => '100'
                        );


                        $selected = '10';
                        if(isset($limit_display) && trim($limit_display) != '')
                            $selected = $limit_display;
                        $js = 'id="limit_display" class="input-sm" onChange="load_url(\''.$url_pag.'\',\''.(isset($order)?$order:'sn').'\',\''.(isset($typeorder)?$typeorder:'sorting').'\');" name="tabel_data_length" size="1" ';
                        echo form_dropdown('tabel_data_length',$options,$selected,$js);
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

        createpaging();

        $('.selected').click(function(){
            if($(".selected").length == $(".selected:checked").length) {
                $("#cek_all").prop("checked", true);
            } else {
                $("#cek_all").prop("checked", false);
            }
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
                load_url(thisHref,'<?php echo (isset($order)?$order:'sn')?>','<?php echo (isset($typeorder)?$typeorder:'sorting')?>');
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

        var par6 = $('#area_search').val();
        var par7 = $('#start').val();
        var par8 = $('#end').val();
        var par9 = $('#state').val();

        if (theurl.substr(theurl.lastIndexOf('/') + 1) == "") {
            theurl=theurl+'0';
        }
        //loading();
        $.ajax({
            method:"post",
            url: theurl,
            data:{"lmt":par4,"cari":par5,"org":par6,"strt":par7,"end":par8,"stt":par9,'order':orderby,'sorting':sortingby},
            success: function(response){
                $('#list-data').html(response);
            },
            dataType:"html"
        });
        return false;
    }


</script>
