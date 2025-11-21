<?php
/**
 * File: list.php
 * Author: abdiIwan.
 * Date: 12/28/2016
 * Time: 8:40 PM
 * absensi.kemendagri.go.id
 */

$url_pag =  site_url("periode/pagging/0");
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
            <th onClick="load_url('<?php echo $url_pag?>','idbln','<?php echo (isset($order)?(($order=='idbln')?$sorting:'sorting'):'sorting')?>')" ><div class="<?php echo (isset($order)?(($order=='idbln')?$typeorder:'sorting'):'sorting')?>">Bulan</div></th>
            <th onClick="load_url('<?php echo $url_pag?>','tahun','<?php echo (isset($order)?(($order=='tahun')?$sorting:'sorting'):'sorting')?>')" ><div class="<?php echo (isset($order)?(($order=='tahun')?$typeorder:'sorting'):'sorting')?>">Tahun</div></th>
            <th onClick="load_url('<?php echo $url_pag?>','status','<?php echo (isset($order)?(($order=='status')?$sorting:'sorting'):'sorting')?>')" ><div class="<?php echo (isset($order)?(($order=='status')?$typeorder:'sorting'):'sorting')?>">Status</div></th>
            <th>Unit Kerja</th>
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
                <td><?php echo $row->bulan;?></td>
                <td><?php echo $row->tahun;?></td>
                <td><input type="checkbox" data-id="<?php echo $row->id?>" onclick="checkState(this)" id="cek-<?php echo $row->id?>" <?php echo $row->status==0?"":"Checked";?>> <span id="lblcheck-<?php echo $row->id?>"><?php echo $row->status==0?"Tutup":"Buka";?><span></td>
                <td><?php echo strlen($row->unit_kerja)>50 ? ellipsize($row->unit_kerja, 50, .5):$row->unit_kerja;?></td>
                <td  width="20px"><a class="btn btn-xs btn-warning editrow" data-id="<?php echo $row->id?>"><i class="fa fa-pencil"></i></a></td>
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
                        $js = 'id="limit_display" class="input-sm" onChange="load_url(\''.$url_pag.'\',\''.(isset($order)?$order:'idbln').'\',\''.(isset($typeorder)?$typeorder:'sorting').'\');" name="tabel_data_length" size="1" ';
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
        $('#cek_all').click(function(){
            $(".selected").prop("checked", $("#cek_all").prop("checked"));
        });

        $('#btncari').click(function(){
            load_url('<?php echo site_url('periode/pagging/0') ?>','<?php echo (isset($order)?$order:'idbln')?>','<?php echo (isset($typeorder)?$typeorder:'sorting')?>');
        });

        $("#caridata").keypress(function(e){
            var key = (e.keyCode ? e.keyCode : e.which);
            if(key==13){
                load_url('<?php echo site_url('periode/pagging/0') ?>','<?php echo (isset($order)?$order:'idbln')?>','<?php echo (isset($typeorder)?$typeorder:'sorting')?>');
            }
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
            $('#ukertree').jstree("deselect_all");
            var xno = $(this).data('id');
            //loading();
            <?php if ($aksesrule["flagedit"]) { ?>
            $.getJSON('<?php echo site_url('periode/edit')?>/'+xno,function() {})
                .done(function(response) {
                    location.hash = "#inputForm";
                    $("#iddata").val(xno);
                    $('#txt1').val(response['idbln']);
                    $("#txt2").val(response['tahun']);
                    $("#txt3").val(response['status']);
                    $('#ukertree').jstree(true).select_node(response['unit_kerja']);
                    $("#txt5").val(response['unit_kerja']);
                    var $radios = $('input:radio[name=txt3]');
                    $radios.filter('[value='+response['status']+']').prop('checked', true);

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
                load_url(thisHref,'<?php echo (isset($order)?$order:'idbln')?>','<?php echo (isset($typeorder)?$typeorder:'sorting')?>');
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
        var par6 = $('#tahun').val();
        if (theurl.substr(theurl.lastIndexOf('/') + 1) == "") {
            theurl=theurl+'0';
        }
        //loading();
        $.ajax({
            method:"post",
            url: theurl,
            data:{"lmt":par4,"cari":par5,"thn":par6,'order':orderby,'sorting':sortingby},
            success: function(response){
                $("#list-data").html(response);
            },
            dataType:"html"
        });
        return false;
    }

    function checkState(checkbox)
    {
		var par4 = $(checkbox).data('id');
        var par5 = ($(checkbox).is(':checked'))? 1 : 0;
        var par6 = ($(checkbox).is(':checked'))? "Buka" : "Tutup";
        <?php if ($aksesrule["flagedit"]) { ?>
        $.ajax({
            method:"post",
            dataType: 'json',
            url: '<?php echo site_url("periode/setstatus")?>',
            data:{"id":par4,"sts":par5},
            success: function(data){
                if (data.status=="succes")
                {
                    $("#lblcheck-"+par4).html(par6);
                }
            }
        });
        <?php } ?>
    }

</script>
