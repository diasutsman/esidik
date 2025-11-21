<?php
/**
 * File: list.php
 * Author: abdiIwan.
 * Date: 12/28/2016
 * Time: 9:38 PM
 * absensi.kemendagri.go.id
 */

$url_pag =  site_url("procdata/pagging/0");



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
                <th>No.</th>
                <th onClick="load_url('<?php echo $url_pag?>','userid','<?php echo (isset($order)?(($order=='userid')?$sorting:'sorting'):'sorting')?>')"><div class="<?php echo (isset($order)?(($order=='userid')?$typeorder:'sorting'):'sorting')?>">NIP</div></th>
                <th onClick="load_url('<?php echo $url_pag?>','name','<?php echo (isset($order)?(($order=='name')?$sorting:'sorting'):'sorting')?>')"><div class="<?php echo (isset($order)?(($order=='name')?$typeorder:'sorting'):'sorting')?>">Nama</div></th>
                <th onClick="load_url('<?php echo $url_pag?>','kelasjabatan','<?php echo (isset($order)?(($order=='kelasjabatan')?$sorting:'sorting'):'sorting')?>')"><div class="<?php echo (isset($order)?(($order=='kelasjabatan')?$typeorder:'sorting'):'sorting')?>">Kelas</div></th>
                <th onClick="load_url('<?php echo $url_pag?>','deptname','<?php echo (isset($order)?(($order=='deptname')?$sorting:'sorting'):'sorting')?>')"><div class="<?php echo (isset($order)?(($order=='deptname')?$typeorder:'sorting'):'sorting')?>">Unit Kerja</div></th>
                <th onClick="load_url('<?php echo $url_pag?>','title','<?php echo (isset($order)?(($order=='title')?$sorting:'sorting'):'sorting')?>')"><div class="<?php echo (isset($order)?(($order=='title')?$typeorder:'sorting'):'sorting')?>">Jabatan</div></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $co=1;
            $jm=0;
            foreach($result as $row)
            {

                $priv = isset($privi[$row->privilege])?$privi[$data->privilege]:"User";

                $jm=$co;
                ?>
                <tr>
                    <td width="20px"><input type="checkbox" name="cek_del" id="cek_del_<?php echo $row->id?>" class="selected" value="<?php echo $row->userid?>"></td>
                    <td><?php echo ($co+$offset);?></td>
                    <td><?php echo $row->userid;?></td>
                    <td><?php echo $row->name;?></td>
                    <td><?php echo $row->kelasjabatan==0?'':$row->kelasjabatan;?></td>
                    <td><?php echo $row->deptname;?></td>
                    <td><?php echo $row->title;?></td>
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
                        

                        $selected = '10';
                        if(isset($limit_display) && trim($limit_display) != '')
                            $selected = $limit_display;
                        $js = 'id="limit_display" class="input-sm" onChange="load_url(\''.$url_pag.'\',\''.(isset($order)?$order:'id').'\',\''.(isset($typeorder)?$typeorder:'sorting').'\');" name="tabel_data_length" size="1" ';
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
        var par6 = $('#unit_search').val();
        if (theurl.substr(theurl.lastIndexOf('/') + 1) == "") {
            theurl=theurl+'0';
        }
        var par7 = $('#stspeg').val();
        var par8 = $('#jnspeg').val();
        $.ajax({
            method:"post",
            url: theurl,
            data:{"lmt":par4,"cari":par5,"org":par6,"stspeg":par7,"jnspeg":par8,'order':orderby,'sorting':sortingby},
            success: function(response){
                $('#list-data').html(response);
            },
            dataType:"html"
        });
        return false;
    }


</script>
