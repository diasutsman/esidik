<?php
/**
 * File: list.php
 * Author: abdiIwan.
 * Date: 12/28/2016
 * Time: 8:40 PM
 * absensi.kemendagri.go.id
 */

$url_pag =  site_url("setholy/pagging/0");

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
            <th onClick="load_url('<?php echo $url_pag?>','a.startdate','<?php echo (isset($order)?(($order=='a.startdate')?$sorting:'sorting'):'sorting_desc')?>')" ><div class="<?php echo (isset($order)?(($order=='a.startdate')?$typeorder:'sorting'):'sorting_desc')?>">Mulai</div></th>
            <th onClick="load_url('<?php echo $url_pag?>','enddate','<?php echo (isset($order)?(($order=='enddate')?$sorting:'sorting'):'sorting')?>')" ><div class="<?php echo (isset($order)?(($order=='enddate')?$typeorder:'sorting'):'sorting')?>">Akhir</div></th>
            <th onClick="load_url('<?php echo $url_pag?>','info','<?php echo (isset($order)?(($order=='info')?$sorting:'sorting'):'sorting')?>')" ><div class="<?php echo (isset($order)?(($order=='info')?$typeorder:'sorting'):'sorting')?>">Keterangan</div></th>
            <th onClick="load_url('<?php echo $url_pag?>','deptname','<?php echo (isset($order)?(($order=='deptname')?$sorting:'sorting'):'sorting')?>')" ><div class="<?php echo (isset($order)?(($order=='deptname')?$typeorder:'sorting'):'sorting')?>">Unit Kerja</div></th>
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
                <td><?php echo format_tanggal_bulan(dmyToymd($row->startdate."-".date("Y")));?></td>
                <td><?php echo format_tanggal_bulan(dmyToymd($row->enddate."-".date("Y")));?></td>
                <td><?php echo $row->info;?></td>
                <td><?php echo $row->deptname;?></td>
                <td width="20px"><a class="btn btn-xs btn-warning editrow" data-id="<?php echo $row->id?>"><i class="fa fa-pencil"></i></a></td>
            </tr>
            <?php

            $co++;
        }

        if (count($result)==0)
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
                        $js = 'id="limit_display" class="input-sm" onChange="load_url(\''.$url_pag.'\',\''.(isset($order)?$order:'a.startdate').'\',\''.(isset($typeorder)?$typeorder:'sorting_desc').'\');" name="tabel_data_length" size="1" ';
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

            var validator = $( "#inputForm" ).validate();
            validator.resetForm();
            <?php if ($aksesrule["flagedit"]) { ?>
            var xno = $(this).data('id');
            //loading();

            $.getJSON('<?php echo site_url('setholy/edit')?>/'+xno,function() {})
                .done(function(response) {
                    location.hash = "#inputForm";
                    $("#iddata").val(xno);
                    $('#txt1').val(response['startdate']);
                    $("#txt2").val(response['enddate']);
                    $("#txt3").val(response['info']);
                    $("#cari_unker").val(response['deptname']);
                    $("#unit_search").val(response['deptid']);
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
                load_url(thisHref,"<?php echo (isset($order)?$order:'a.startdate')?>","<?php echo (isset($typeorder)?$typeorder:'sorting_desc')?>");
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
