<?php
/**
 * File: list.php
 * Author: abdiIwan.
 * Date: 12/28/2016
 * Time: 9:38 PM
 * absensi.kemendagri.go.id
 */

$url_pag =  site_url("unitkerja/pagging/0");
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
            <th  onClick="load_url('<?php echo $url_pag?>','a.deptid','<?php echo (isset($order)?(($order=='a.deptid')?$sorting:'sorting'):'sorting_asc')?>')"><div class="<?php echo (isset($order)?(($order=='a.deptid')?$typeorder:'sorting'):'sorting_asc')?>">Kode</div></th>
            <th  onClick="load_url('<?php echo $url_pag?>','a.deptname','<?php echo (isset($order)?(($order=='a.deptname')?$sorting:'sorting'):'sorting_asc')?>')"><div class="<?php echo (isset($order)?(($order=='a.deptname')?$typeorder:'sorting'):'sorting_asc')?>">Nama Unit Kerja</div></th>
            <th  onClick="load_url('<?php echo $url_pag?>','b.deptname','<?php echo (isset($order)?(($order=='b.deptname')?$sorting:'sorting'):'sorting_asc')?>')"><div class="<?php echo (isset($order)?(($order=='b.deptname')?$typeorder:'sorting'):'sorting_asc')?>">Induk Unit Kerja</div></th>
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
                <td><?php echo $row->deptid;?></td>
                <td><?php echo $row->deptname;?></td>
                <td><?php echo $row->nama_parent;?></td>
                <td><a class="btn btn-xs btn-warning editrow" data-id="<?php echo $row->id?>"><i class="fa fa-pencil"></i></a></td>
            </tr>
            <?php

            $co++;
        }

        if (count($result)==0)
        {
            ?>
            <tr class="">
                <td colspan="5"><center>Tidak ada data..</center></td>
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
                        $js = 'id="limit_display" class="input-sm" onChange="load_url(\''.$url_pag.'\',\''.(isset($order)?$order:'a.id').'\',\''.(isset($typeorder)?$typeorder:'sorting').'\');" name="tabel_data_length" size="1" ';
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
        createpaging();
        $('#cek_all').click(function(){
            $(".selected").prop("checked", $("#cek_all").prop("checked"));
        });



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

            var xno = $(this).data('id');
            //loading();

            $.getJSON('<?php echo site_url('unitkerja/edit')?>/'+xno,function() {})
                .done(function(response) {
                    location.hash = "#inputForm";
                    $("#iddata").val(xno);
                    $('#txt1').val(response['deptid']);
                    $("#txt2").val(response['deptname']);
                    $("#cari_unker").val(response['nama_parent']);
                    $("#unit_search").val(response['parentid']);
                    $("#btnAdd").hide();
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
                load_url(thisHref,'<?php echo (isset($order)?$order:'a.id')?>','<?php echo (isset($typeorder)?$typeorder:'sorting')?>');
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
            dataType:"html",
            data:{"lmt":par4,"cari":par5,'order':orderby,'sorting':sortingby},
            success: function(response){
                $("#list-data").html(response);
            }

        });
        return false;
    }


</script>
