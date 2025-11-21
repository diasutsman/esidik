<?php
/**
 * File: list.php
 * Author: abdiIwan.
 * Date: 12/28/2016
 * Time: 8:40 PM
 * absensi.kemendagri.go.id
 */

$url_pag =  site_url("stathadir/pagging/0");
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
            <th onClick="load_url('<?php echo $url_pag?>','status_kategori_id','<?php echo (isset($order)?(($order=='status_kategori_id')?$sorting:'sorting'):'sorting')?>')" ><div class="<?php echo (isset($order)?(($order=='status_kategori_id')?$typeorder:'sorting'):'sorting')?>">Kategori</div></th>
            <th onClick="load_url('<?php echo $url_pag?>','atid','<?php echo (isset($order)?(($order=='atid')?$sorting:'sorting'):'sorting')?>')" ><div class="<?php echo (isset($order)?(($order=='atid')?$typeorder:'sorting'):'sorting')?>">Kode</div></th>
            <th onClick="load_url('<?php echo $url_pag?>','atname','<?php echo (isset($order)?(($order=='atname')?$sorting:'sorting'):'sorting')?>')" ><div class="<?php echo (isset($order)?(($order=='atname')?$typeorder:'sorting'):'sorting')?>">Keterangan</div></th>
            <th onClick="load_url('<?php echo $url_pag?>','value','<?php echo (isset($order)?(($order=='value')?$sorting:'sorting'):'sorting')?>')" ><div class="<?php echo (isset($order)?(($order=='value')?$typeorder:'sorting'):'sorting')?>">Pengurang</div></th>
            <th onClick="load_url('<?php echo $url_pag?>','state','<?php echo (isset($order)?(($order=='state')?$sorting:'sorting'):'sorting')?>')" ><div class="<?php echo (isset($order)?(($order=='state')?$typeorder:'sorting'):'sorting')?>">Status</div></th>
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
            <tr id="rowdata-<?php echo $row->id?>">
                <td width="20px"><input type="checkbox" name="cek_del" id="cek_del_<?php echo $row->id?>" class="selected" value="<?php echo $row->id?>"></td>
                <td><?php echo $row->ket_status_kategori;?></td>
                <td><?php echo $row->atid;?></td>
                <td><?php echo $row->atname;?></td>
                <td><?php echo number_format($row->value,0,'.',',');?></td>
                <td><?php echo $row->state==1?"Aktif":"Non Aktif"?></td>
                <td width="20px"><a class="btn btn-xs btn-warning editrow" data-id="<?php echo $row->id?>"><i class="fa fa-pencil"></i></a></td>
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
                        $js = 'id="limit_display" class="input-sm" onChange="load_url(\''.$url_pag.'\',\''.(isset($order)?$order:'id').'\',\''.(isset($typeorder)?$typeorder:'sorting').'\');" name="tabel_data_length" size="1" ';
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

        $('.editrow').click(function(){

            $('#inputForm').trigger("reset");
            <?php if ($aksesrule["flagedit"]) { ?>
            var validator = $( "#inputForm" ).validate();
            validator.resetForm();

            var xno = $(this).data('id');
            //loading();

            $.getJSON('<?php echo site_url('stathadir/edit')?>/'+xno,function() {})
            .done(function(response) {
                location.hash = "#inputForm";
                $("#iddata").val(xno);
                $('#txt1').val(response['atid']);
                $("#txt2").val(response['atname']);
                $("#txt3").val(response['value']);
                $("#idkat").val(response['status_kategori_id']);
                $('input[name="status"][value="' + response['state'] + '"]').prop('checked', true);
                $("#btnAdd").hide();$("#btnDeleteRow").hide();
                $("#view-form").slideDown('fast');
                //unloading();
                });
            <?php } ?>
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
                load_url(thisHref,'<?php echo (isset($order)?$order:'id')?>','<?php echo (isset($typeorder)?$typeorder:'sorting')?>');
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


</script>
