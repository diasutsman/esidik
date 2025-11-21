<?php
/**
 * File: list.php
 * Author: abdiIwan.
 * Date: 12/28/2016
 * Time: 8:40 PM
 * absensi.kemendagri.go.id
 */

$url_pag =  site_url("usrdata/pagging/0");
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
            <th onClick="load_url('<?php echo $url_pag?>','username','<?php echo (isset($order)?(($order=='username')?$sorting:'sorting'):'sorting')?>')" ><div class="<?php echo (isset($order)?(($order=='username')?$typeorder:'sorting'):'sorting')?>">Username</div></th>
            <th onClick="load_url('<?php echo $url_pag?>','email','<?php echo (isset($order)?(($order=='email')?$sorting:'sorting'):'sorting')?>')" ><div class="<?php echo (isset($order)?(($order=='email')?$typeorder:'sorting'):'sorting')?>">Email</div></th>
            <th onClick="load_url('<?php echo $url_pag?>','user_level_name','<?php echo (isset($order)?(($order=='user_level_name')?$sorting:'sorting'):'sorting')?>')" ><div class="<?php echo (isset($order)?(($order=='user_level_name')?$typeorder:'sorting'):'sorting')?>">Level</div></th>
            <th onClick="load_url('<?php echo $url_pag?>','dept_id','<?php echo (isset($order)?(($order=='dept_id')?$sorting:'sorting'):'sorting')?>')" ><div class="<?php echo (isset($order)?(($order=='dept_id')?$typeorder:'sorting'):'sorting')?>">Unit Kerja</div></th>
            <th onClick="load_url('<?php echo $url_pag?>','area_id','<?php echo (isset($order)?(($order=='area_id')?$sorting:'sorting'):'sorting')?>')" ><div class="<?php echo (isset($order)?(($order=='area_id')?$typeorder:'sorting'):'sorting')?>">Area</div></th>
            <th onClick="load_url('<?php echo $url_pag?>','userid','<?php echo (isset($order)?(($order=='userid')?$sorting:'sorting'):'sorting')?>')" ><div class="<?php echo (isset($order)?(($order=='userid')?$typeorder:'sorting'):'sorting')?>">NIP</div></th>
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
                <td><?php echo $row->username;?></td>
                <td><?php echo $row->email;?></td>
                <td><?php echo $row->user_level_name;?></td>
                <td><?php echo $row->nama_unit;?></td>
                <td><?php echo $row->nama_area;?></td>
                <td><?php echo $row->userid;?></td>
                <td><?php echo $row->state==1?"Aktif":"Non Aktif"?></td>
                <td width="40px"><a class="btn btn-xs btn-warning editrow" data-id="<?php echo $row->id?>"><i class="fa fa-pencil"></i></a>
                    <a class="btn btn-xs btn-danger resetrow" data-id="<?php echo $row->id?>"><i class="fa fa-undo"></i></a>
                </td>
            </tr>
            <?php

            $co++;
        }

        if (count($result)==0)
        {
            ?>
            <tr class="">
                <td colspan="9"><center>Tidak ada data..</center></td>
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

<form class="form-horizontal form-label-left" name="snForm" id="snForm" method="post">
    <div class="popup-wrapper" id="popup">
        <div class="popup-container">
            <div class="modal-header">
                <span type="button" class="close" data-dismiss="modal" onClick="hidePopup('#popup');">&times;</span>
                <h3>Penggantian Katakunci</h3>
            </div>
            <div class="modal-body" >
                <input name="idold" value="" id="idold" type="hidden">
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt1">Katakunci Baru</label>
                    <div class="col-md-5 col-sm-5 col-xs-12">
                        <input name="txtpassnew" id="txtpassnew" type="text" placeholder="Password" class="form-control col-md-5 col-xs-12">
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary" onClick="hidePopup('#popup');">Batal</button>
                <button type="button" class="btn btn-sm btn-success" data-dismiss="modal" onClick="saveSNForm();">Simpan</button>
            </div>
        </div>
    </div>
</form>

<script>
    $(function(){
        $('#cek_all').click(function(){
            $(".selected").prop("checked", $("#cek_all").prop("checked"));
        });

        $('#btncari').click(function(){
            load_url('<?php echo site_url('usrdata/pagging/0') ?>','<?php echo (isset($order)?$order:'id')?>','<?php echo (isset($typeorder)?$typeorder:'sorting')?>');
        });
        createpaging();
        $("#caridata").keypress(function(e){
            var key = (e.keyCode ? e.keyCode : e.which);
            if(key==13){
                load_url('<?php echo site_url('usrdata/pagging/0') ?>','<?php echo (isset($order)?$order:'id')?>','<?php echo (isset($typeorder)?$typeorder:'sorting')?>');
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
            $('#areatree').jstree("deselect_all");
            var validator = $( "#inputForm" ).validate();
            validator.resetForm();

            var xno = $(this).data('id');
            //loading();

            $.getJSON('<?php echo site_url('usrdata/edit')?>/'+xno,function() {})
                .done(function(response) {
                    location.hash = "#inputForm";
                    $("#iddata").val(xno);
                    $('#txt1').val(response['username']);
                    $('#txt2').val(response['email']);
                    $('#txt3').val(response['userid']);
                    $('#level').val(response['user_level_id']);
                    $('input[name="status"][value="' + response['state'] + '"]').prop('checked', true);

                    $('#ukertree').jstree("select_node", response['dept_id'], false);
                    $("#txt4").val(response['dept_id']);

                    $('#areatree').jstree("select_node", response['area_id'], false);
                    $("#txt5").val(response['area_id']);
                    //alert(response['area_id']);
                    $("#btnAdd").hide();
                    $("#view-form").slideDown('fast');
                    //unloading();
                });
        });

        $('.resetrow').click(function(){
            $( "#idold" ).val($(this).data('id'));
            showPopup('#popup');
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

    function saveSNForm() {
        if (($("#txtpassnew").val() != "")) {

            $.ajax({
                url: '<?php echo site_url('usrdata/rubihpwd');?>',
                dataType: 'json',
                type: 'POST',
                data: $("#snForm").serialize(),
                success: function (data) {
                    bootbox.alert(data.msg);
                }
            });
        } else {
            bootbox.alert("Harap cek kembali inputannya ??");
        }
    }

</script>
