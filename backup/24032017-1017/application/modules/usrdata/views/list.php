<?php
/**
 * File: list.php
 * Author: abdiIwan.
 * Date: 12/28/2016
 * Time: 8:40 PM
 * absensi.kemendagri.go.id
 */

$url_pag =  "'".site_url("usrdata/pagging/0")."'";
$domId ="'#list-data'";

?>
<div class="row">
    <div class="col-sm-5 m-b-xs">
        <a  href="#" class="btn btn-sm btn-danger" onClick="doDelete();" ><i class="fa fa-minus"></i> Hapus</a> <a id="btnAdd" class="btn btn-sm btn-success"><i class="fa fa-plus"></i> Tambah</a>
    </div>
    <div class="col-sm-4 m-b-xs">
    </div>
    <div class="col-sm-3">
        <div class="input-group">
            <input type="text" id="caridata" name="caridata" placeholder="Pencarian" class="input-sm form-control" value="<?php echo isset($caridata)?$caridata:''?>">
            <span class="input-group-btn"><button type="button" id="btncari" class="btn btn-sm btn-primary"> <i class="fa fa-search"></i> </button> </span>
        </div>
    </div>
</div>
<div class="table-responsive">
    <div class="dataTables_wrapper dt-bootstrap">
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th><input type="checkbox" name="cek_all" id="cek_all"></th>
            <th>Username</th>
            <th>Email</th>
            <th>Level</th>
            <th>Unit Kerja</th>
            <th>Area</th>
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
                <td><?php echo $row->username;?></td>
                <td><?php echo $row->email;?></td>
                <td><?php echo $row->user_level_name;?></td>
                <td><?php echo $row->dept_id;?></td>
                <td><?php echo $row->area_id;?></td>
                <td width="20px"><a class="btn btn-xs btn-warning"><i class="fa fa-pencil"></i></a></td>
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
            load_url('<?php echo site_url('usrdata/pagging/0') ?>',"#list-data");
        });
        createpaging("#list-data");
        $("#caridata").keypress(function(e){
            var key = (e.keyCode ? e.keyCode : e.which);
            if(key==13){
                load_url('<?php echo site_url('usrdata/pagging/0') ?>',"#list-data");
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

    function doDelete()
    {
        var cek = $(".selected:checked").length;
        if(cek > 0){
            bootbox.confirm("Anda yakin menghapus data yang dipilih?", function(result) {
                if(result) {
                    var cek_del  = $('input[name=cek_del]:checked').map(function(){
                        return $(this).val();
                    }).get();

                    $.ajax({
                        url     : '<?php echo site_url('usrdata/hapus');?>',
                        dataType: 'json',
                        type    : 'POST',
                        data    : { 'id' : cek_del},
                        success : function(data){
                            if (data.status=='succes')
                            {
                                for(var i = 0; i < cek_del.length; i++) {
                                    var idrow = cek_del[i].replace(/^\s*/, "").replace(/\s*$/, "");
                                    $("#rowdata-"+idrow).remove();
                                }

                            }
                           bootbox.alert(data.msg);
                        },
                        beforeSend: function() {
                            $("#resend").prop('disabled', true);
                            $("#resend").html('<i class="fa fa-key"></i> Processing');
                        },
                        complete: function() {
                            $("#resend").prop('disabled', false);
                            $("#resend").html('<i class="fa fa-repeat"></i> Resend');
                        }
                    });

                }
            });
        }else{
            bootbox.alert("Harap pilih data yang akan di hapus!");
        }
    }
</script>
