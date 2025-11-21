<?php
/**
 * File: list.php
 * Author: abdiIwan.
 * Date: 12/28/2016
 * Time: 8:40 PM
 * absensi.kemendagri.go.id
 */

$url_pag =  site_url("usraction/pagging/0");


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
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th><input type="checkbox" name="cek_all" id="cek_all"></th>
            <th onClick="load_url('<?php echo $url_pag?>','menu_desc','<?php echo (isset($order)?(($order=='menu_desc')?$sorting:'sorting'):'sorting')?>')" ><div class="<?php echo (isset($order)?(($order=='menu_desc')?$typeorder:'sorting'):'sorting')?>">Keterangan</div></th>
            <th onClick="load_url('<?php echo $url_pag?>','flagadd','<?php echo (isset($order)?(($order=='flagadd')?$sorting:'sorting'):'sorting')?>')" ><div class="<?php echo (isset($order)?(($order=='flagadd')?$typeorder:'sorting'):'sorting')?>">Menambah Data</div></th>
            <th onClick="load_url('<?php echo $url_pag?>','flagedit','<?php echo (isset($order)?(($order=='flagedit')?$sorting:'sorting'):'sorting')?>')" ><div class="<?php echo (isset($order)?(($order=='flagedit')?$typeorder:'sorting'):'sorting')?>">Memperbaharui</div></th>
            <th onClick="load_url('<?php echo $url_pag?>','flagdelete','<?php echo (isset($order)?(($order=='flagdelete')?$sorting:'sorting'):'sorting')?>')" ><div class="<?php echo (isset($order)?(($order=='flagdelete')?$typeorder:'sorting'):'sorting')?>">Menghapus</div></th>
            <th onClick="load_url('<?php echo $url_pag?>','flagprint','<?php echo (isset($order)?(($order=='flagprint')?$sorting:'sorting'):'sorting')?>')" ><div class="<?php echo (isset($order)?(($order=='flagprint')?$typeorder:'sorting'):'sorting')?>">Melihat Laporan</div></th>
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
            <tr id="rowdata-<?php echo $row->id_action?>">
                <td width="20px"><input type="checkbox" name="cek_del" id="cek_del_<?php echo $row->id_action?>" class="selected" value="<?php echo $row->id_action?>"></td>
                <td><?php echo $row->menu_desc;?></td>
                <td><?php echo $row->flagadd==1?"Ya":"Tidak"?></td>
                <td><?php echo $row->flagedit==1?"Ya":"Tidak"?></td>
                <td><?php echo $row->flagdelete==1?"Ya":"Tidak"?></td>
                <td><?php echo $row->flagprint==1?"Ya":"Tidak"?></td>
                <td width="20px"><a class="btn btn-xs btn-warning editrow" data-id="<?php echo $row->id_action?>"><i class="fa fa-pencil"></i></a></td>
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
            <div class="col-sm-9 m-b-xs">
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
                        $js = 'id="limit_display" class="input-sm" onChange="load_url(\''.$url_pag.'\',\''.(isset($order)?$order:'user_level_id').'\',\''.(isset($typeorder)?$typeorder:'sorting').'\');" name="tabel_data_length" size="1" ';
                        echo form_dropdown('tabel_data_length',$options,$selected,$js);
                        ?>
                        Rec. <?php echo (($jum_data==0)?'0':($offset+1))?> s/d <?php echo(($offset+$jm))?> dari <?php echo$jum_data?> data
                    </div>

                </div>
            </div>
            <div class="col-sm-3 m-b-xs" id="pagering">
                <div class="dataTables_paginate paging_simple_numbers">
                    <?php echo $paging;?>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    $(function(){

        createpaging();

        $('.editrow').click(function(){
            $('#inputForm').trigger("reset");

            var validator = $( "#inputForm" ).validate();
            validator.resetForm();

            var xno = $(this).data('id');
            //loading();

            $.getJSON('<?php echo site_url('usraction/edit')?>/'+xno,function() {})
                .done(function(response) {
                    location.hash = "#inputForm";
                    $("#iddata").val(xno);
                    $('#txt1').val(response['id_menu']);
                    $('input[name="flagadd"][value="' + response['flagadd'] + '"]').prop('checked', true);
                    $('input[name="flagedit"][value="' + response['flagedit'] + '"]').prop('checked', true);
                    $('input[name="flagdelete"][value="' + response['flagdelete'] + '"]').prop('checked', true);
                    $('input[name="flagprint"][value="' + response['flagprint'] + '"]').prop('checked', true);

                    $("#btnAdd").hide();$("#btnDeleteRow").hide();
                    $("#view-form").slideDown('fast');
                    //unloading();
                });
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
                load_url(thisHref,'<?php echo (isset($order)?$order:'user_level_id')?>','<?php echo (isset($typeorder)?$typeorder:'sorting')?>');
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
        var par3 = $('#idlvl').val();
        //loading();
        $.ajax({
            method:"post",
            url: theurl,
            data:{"lmt":par4,"cari":par5,'order':orderby,'sorting':sortingby,'idlevel':par3},
            success: function(response){
                $('#list-data').html(response);
            },
            dataType:"html"
        });
        return false;
    }


</script>
