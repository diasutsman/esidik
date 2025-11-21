<?php
/**
 * File: list.php
 * Author: abdiIwan.
 * Date: 12/28/2016
 * Time: 8:40 PM
 * absensi.kemendagri.go.id
 */

$url_pag =  "'".site_url("pesan/pagging/0")."'";
$domId ="'#list-data'";

?>

<div class="table-responsive">
    <div class="dataTables_wrapper dt-bootstrap">
    <table class="table">
        <thead>
        <tr>
            <th><input type="checkbox" name="cek_all" id="cek_all"></th>
            <th>Dari</th>
            <th>Pesan</th>
            <th>Waktu</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $co=1;
        $jm=0;
        foreach($result as $row)
        {
            $jm=$co;
            switch ( $row->sifat)
            {
                case "0":
                    $t4 ="Biasa";
                    $t5="label-info";
                    break;
                case "1":
                    $t4 ="Penting";
                    $t5="label-warning";
                    break;
                case "2":
                    $t4 ="Rahasia";
                    $t5="label-danger";
                    break;
                default:
                    $t4 ="Biasa";
                    $t5="label-info";
                    break;
            }

            ?>
            <tr id="rowdata-<?php echo $row->id_detail?>" <?php echo $row->isread==0?"class='unread'":"";?>>
                <td width="20px"><input type="checkbox" name="cek_del" id="cek_del_<?php echo $row->id_detail?>" class="selected" value="<?php echo $row->id?>"></td>
                <td><?php echo $row->username;?> <span class="label <?php echo $t5?> pull-right"><?php echo $t4?></span></td>
                <td><?php echo $row->judul;?></td>
                <td><?php echo indo_full_date($row->tgl_pesan);?></td>
                <td><input type="checkbox" data-id="<?php echo $row->id_detail?>" onclick="checkState(this)" id="cek-<?php echo $row->id_detail?>" <?php echo $row->isread==0?"":"Checked";?>> <span id="lblcheck-<?php echo $row->id_detail?>"><?php echo $row->isread==0?"Belum dibaca":"Sudah dibaca";?><span></td>
            </tr>
            <?php

            $co++;
        }

        if (count($result)==0)
        {
            ?>
            <tr class="">
                <td colspan="4"><center>Tidak ada data..</center></td>
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
        $('#cek_all').click(function(){
            $(".selected").prop("checked", $("#cek_all").prop("checked"));
        });

        $('#btncari').click(function(){
            load_url('<?php echo site_url('pesan/pagging/0') ?>',"#list-data");
        });
        createpaging("#list-data");
        $("#caridata").keypress(function(e){
            var key = (e.keyCode ? e.keyCode : e.which);
            if(key==13){
                load_url('<?php echo site_url('pesan/pagging/0') ?>',"#list-data");
            }
        });

        $('.selected').click(function(){
            if($(".selected").length == $(".selected:checked").length) {
                $("#cek_all").prop("checked", true);
            } else {
                $("#cek_all").prop("checked", false);
            }
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

    function checkState(checkbox)
    {
        var par4 = $(checkbox).data('id');
        var par5 = ($(checkbox).is(':checked'))? 1 : 0;
        var par6 = ($(checkbox).is(':checked'))? "Sudah dibaca" : "Belum dibaca";

        $.ajax({
            method:"post",
            dataType: 'json',
            url: '<?php echo site_url("pesan/setstatus")?>',
            data:{"id":par4,"sts":par5},
            success: function(data){
                if (data.status=="succes")
                {
                    $("#lblcheck-"+par4).html(par6);
                    if (par6=="Sudah dibaca") {
                        $("#rowdata-" + par4).removeClass('unread');
                    } else {
                        $("#rowdata-" + par4).addClass('unread');
                    }

                    $(".count-info .label").html(data.jmlUnread);
                }
            }

        });
    }

</script>
