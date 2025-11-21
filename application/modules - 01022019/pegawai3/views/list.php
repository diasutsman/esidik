<?php
/**
 * File: list.php
 * Author: abdiIwan.
 * Date: 12/28/2016
 * Time: 9:38 PM
 * absensi.kemendagri.go.id
 */

$url_pag =  site_url("pegawai/pagging/0");

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
        <table class="table table-striped table-bordered small">
            <thead>
            <tr>
                <th><input type="checkbox" name="cek_all" id="cek_all"></th>
                <th onClick="load_url('<?php echo $url_pag?>','userid','<?php echo (isset($order)?(($order=='userid')?$sorting:'sorting'):'sorting')?>')"><div class="<?php echo (isset($order)?(($order=='userid')?$typeorder:'sorting'):'sorting')?>">NIP</div></th>
                <!--<th onClick="load_url('<?php /*echo $url_pag*/?>','badgenumber','<?php /*echo (isset($order)?(($order=='badgenumber')?$sorting:'sorting'):'sorting')*/?>')"><div class="<?php /*echo (isset($order)?(($order=='badgenumber')?$typeorder:'sorting'):'sorting')*/?>">NIP</div></th>-->
                <th onClick="load_url('<?php echo $url_pag?>','name','<?php echo (isset($order)?(($order=='name')?$sorting:'sorting'):'sorting')?>')"><div class="<?php echo (isset($order)?(($order=='name')?$typeorder:'sorting'):'sorting')?>">Nama</div></th>
                <th onClick="load_url('<?php echo $url_pag?>','kelasjabatan','<?php echo (isset($order)?(($order=='kelasjabatan')?$sorting:'sorting'):'sorting')?>')"><div class="<?php echo (isset($order)?(($order=='kelasjabatan')?$typeorder:'sorting'):'sorting')?>">Kelas</div></th>
                <th onClick="load_url('<?php echo $url_pag?>','deptname','<?php echo (isset($order)?(($order=='deptname')?$sorting:'sorting'):'sorting')?>')"><div class="<?php echo (isset($order)?(($order=='deptname')?$typeorder:'sorting'):'sorting')?>">Unit Kerja</div></th>
                <th onClick="load_url('<?php echo $url_pag?>','title','<?php echo (isset($order)?(($order=='title')?$sorting:'sorting'):'sorting')?>')"><div class="<?php echo (isset($order)?(($order=='title')?$typeorder:'sorting'):'sorting')?>">Jabatan</div></th>
                <th onClick="load_url('<?php echo $url_pag?>','tmtjabatan','<?php echo (isset($order)?(($order=='tmtjabatan')?$sorting:'sorting'):'sorting')?>')"><div class="<?php echo (isset($order)?(($order=='tmtjabatan')?$typeorder:'sorting'):'sorting')?>">TMT Jabatan</div></th>
                <th onClick="load_url('<?php echo $url_pag?>','birthdate','<?php echo (isset($order)?(($order=='birthdate')?$sorting:'sorting'):'sorting')?>')"><div class="<?php echo (isset($order)?(($order=='birthdate')?$typeorder:'sorting'):'sorting')?>">Tgl Lahir</div></th>
                <th onClick="load_url('<?php echo $url_pag?>','tmtpangkat','<?php echo (isset($order)?(($order=='tmtpangkat')?$sorting:'sorting'):'sorting')?>')"><div class="<?php echo (isset($order)?(($order=='tmtpangkat')?$typeorder:'sorting'):'sorting')?>">TMT Pangkat</div></th>
                <th onClick="load_url('<?php echo $url_pag?>','plt_jbtn','<?php echo (isset($order)?(($order=='plt_jbtn')?$sorting:'sorting'):'sorting')?>')"><div class="<?php echo (isset($order)?(($order=='plt_jbtn')?$typeorder:'sorting'):'sorting')?>">PLT/PLH</div></th>
                <th onClick="load_url('<?php echo $url_pag?>','payable','<?php echo (isset($order)?(($order=='payable')?$sorting:'sorting'):'sorting')?>')"><div class="<?php echo (isset($order)?(($order=='payable')?$typeorder:'sorting'):'sorting')?>">Tunkir</div></th>
                <th style="white-space: nowrap"></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $co=1;
            $jm=0;
            foreach($result as $row)
            {
                $priv = isset($privi[$row->privilege])?$privi[$row->privilege]:"User";

                if($row->gender==1)
                    $gender = "Laki-laki";
                else if($row->gender==2)
                    $gender = "Perempuan";
                else
                    $gender = '';

                $jm=$co;
                ?>
                <tr id="rowdata-<?php echo $row->id?>">
                    <td width="20px"><input type="checkbox" name="cek_del" id="cek_del_<?php echo $row->id?>" class="selected" value="<?php echo $row->id?>"></td>
                    <td><?php echo $row->userid;?></td>
                    <!--<td><?php /*echo $row->badgenumber;*/?></td>-->
                    <td><?php echo $row->name;?></td>
                    <td><?php echo $row->kelasjabatan==0?'':$row->kelasjabatan;?></td>

                    <td><?php echo $row->deptname;?></td>
                    <td><?php echo $row->title;?></td>
                    <td><?php echo isset($row->tmtjabatan)?date('d-m-Y', strtotime($row->tmtjabatan)):'';?></td>
                    <td><?php echo isset($row->birthdate)?date('d-m-Y', strtotime($row->birthdate)):'';?></td>
                    <td><?php echo isset($row->tmtpangkat)?date('d-m-Y', strtotime($row->tmtpangkat)):''?></td>
                    <td><?php echo $row->plt_jbtn;;?></td>
                    <td><?php echo $row->payable==1?'Dibayarkan':'Tidak Dibayarkan'?></td>
                    <td style="white-space: nowrap"><div class="btn-toolbar" role="group">
                            <a class="btn btn-xs btn-warning" title="Edit data" href="<?php echo $aksesrule["flagedit"] ? site_url("pegawai/form/".$row->id) : "#"?>"><i class="fa fa-pencil"></i></a>
                            <a class="btn btn-xs btn-info" href="#" title="show history" onclick="showHist('<?php echo $row->userid?>')"><i class="fa fa-bookmark"></i></a>
                            <a class="btn btn-xs btn-success" title="Area Mesin" href="<?php echo $aksesrule["flagedit"] ? site_url("pegawai/area/".$row->id) : "#"?>"><i class="fa fa-map-marker"></i></a>
                        </div>
                    </td>
                </tr>
                <?php

                $co++;
            }

            if (count($result)==0)
            {
                ?>
                <tr class="">
                    <td colspan="12"><center>Tidak ada data..</center></td>
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
                        $js = 'id="limit_display" class="input-sm" onChange="load_url(\''.$url_pag.'\',\''.(isset($order)?$order:'id').'\',\''.(isset($typeorder)?$typeorder:'sorting').'\');" name="tabel_data_length" size="1" ';
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
            <?php if ($aksesrule["flagedit"]) { ?>

            $('#inputForm').trigger("reset");

            var validator = $( "#inputForm" ).validate();
            validator.resetForm();

            var xno = $(this).data('id');
            //loading();

            $.getJSON('<?php echo site_url('pegawai/edit')?>/'+xno,function() {})
                .done(function(response) {
                    location.hash = "#inputForm";
                    $("#iddata").val(xno);
                    $('#txt1').val(response['kelasjabatan']);
                    $("#txt2").val(response['tunjangan']);
                    $("#btnAdd").hide();
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
        var par6 = $('#unit_search').val();
        if (theurl.substr(theurl.lastIndexOf('/') + 1) == "") {
            theurl=theurl+'0';
        }
        par7 = $('#stspeg').val();
        par8 = $('#jnspeg').val();
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

    function showHist(xnoid)
    {
        $.ajax({
            url     : '<?php echo site_url('pegawai/gethistori');?>',
            method    : 'POST',
            data    : { 'id' : xnoid},
            success : function(response){
                var dialog = bootbox.dialog({
                    title: 'History Data Pegawai',
                    message: '<p><i class="fa fa-spin fa-spinner"></i> Loading...</p>'
                });
                dialog.init(function(){
                    setTimeout(function(){
                        dialog.find('.bootbox-body').html(response);
                    }, 200);
                });
            }
        });
    }
</script>
