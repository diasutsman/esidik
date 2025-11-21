<?php
/**
 * File: list.php
 * Author: abdiIwan.
 * Date: 12/28/2016
 * Time: 8:40 PM
 * absensi.kemendagri.go.id
 */

$url_pag =  "'".site_url("usrlevel/pagging/0")."'";
$domId ="'#list-data'";

?>

<div class="table-responsive">
    <div class="dataTables_wrapper dt-bootstrap">
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th><input type="checkbox" name="cek_all" id="cek_all"></th>
            <th>Keterangan</th>
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
            <tr id="rowdata-<?php echo $row->user_level_id?>">
                <td width="20px"><input type="checkbox" name="cek_del" id="cek_del_<?php echo $row->user_level_id?>" class="selected" value="<?php echo $row->user_level_id?>"></td>
                <td><?php echo $row->user_level_name;?></td>
                <td width="20px"><a class="btn btn-xs btn-warning editrow" data-id="<?php echo $row->user_level_id?>"><i class="fa fa-pencil"></i></a></td>
            </tr>
            <?php

            $co++;
        }

        if (count($result)==0)
        {
            ?>
            <tr class="">
                <td colspan="3"><center>Tidak ada data..</center></td>
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
                        $js = 'id="limit_display" class="input-sm" onChange="load_url('.$url_pag.','.$domId.');" name="tabel_data_length" size="1" ';
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

        createpaging("#list-data");

        $('.editrow').click(function(){
            $('#inputForm').trigger("reset");

            var validator = $( "#inputForm" ).validate();
            validator.resetForm();

            var xno = $(this).data('id');
            //loading();

            $.getJSON('<?php echo site_url('usrlevel/edit')?>/'+xno,function() {})
                .done(function(response) {
                    location.hash = "#inputForm";
                    $("#iddata").val(xno);
                    $('#txt1').val(response['user_level_name']);
                    $("#btnAdd").hide();
                    $("#view-form").slideDown('fast');
                    //unloading();
                });
        });

    });

    function createpaging(domId)
    {
        $("#pagering").find("a").each(function(i){
            var thisHref = $(this).attr("href");
            $(this).prop('href','javascript:void(0)');
            $(this).prop('rel',thisHref);
            if ( !$( this ).prop( "class" ) ) {
                $(this).prop('class', 'paginate_button');
            }

            $(this).bind('click', function(){
                load_url(thisHref,domId);
                return false;
            });
        });
    }

    function load_url(theurl,div)
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
            data:{"lmt":par4,"cari":par5},
            success: function(response){
                $(div).html(response);
            },
            dataType:"html"
        });
        return false;
    }


</script>
