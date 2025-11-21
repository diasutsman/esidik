<?php
/**
 * File: list.php
 * Author: abdiIwan.
 * Date: 12/28/2016
 * Time: 9:38 PM
 * absensi.kemendagri.go.id
 */

$url_pag =  "'".site_url("prosess/pagging/0")."'";
$domId ="'#list-data'";

?>

<div class="table-responsive">
    <div class="dataTables_wrapper dt-bootstrap">
        <table class="table table-striped  table-bordered">
            <thead>
            <tr>
                <th><input type="checkbox" name="cek_all" id="cek_all"></th>
                <th>NIP</th>
                <th>Nama</th>
                <th>Kelas</th>
                <th>Unit Kerja</th>
                <th>Jabatan</th>
                <th>TMT Jabatan</th>
                <th>Tgl Lahir</th>
                <th>TMT Pangkat</th>
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
                <tr>
                    <td width="20px"><input type="checkbox" name="cek_del" id="cek_del_<?php echo $row->userid?>" class="selected" value="<?php echo $row->userid?>"></td>
                    <td><?php echo $row->userid;?></td>
                    <td><?php echo $row->name;?></td>
                    <td><?php echo $row->kelasjabatan==0?'':$row->kelasjabatan;?></td>
                    <td><?php echo $row->deptname;?></td>
                    <td><?php echo $row->title;?></td>
                    <td><?php echo isset($row->tmtjabatan)?date('Y-m-d', strtotime($row->tmtjabatan)):'';?></td>
                    <td><?php echo isset($row->birthdate)?date('Y-m-d', strtotime($row->birthdate)):'';?></td>
                    <td><?php echo isset($row->tmtpangkat)?date('Y-m-d', strtotime($row->tmtpangkat)):''?></td>
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
                        $js = 'id="limit_display" class="input-sm" onChange="load_url('.$url_pag.','.$domId.');" name="tabel_data_length" size="1" ';
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


        createpaging("#list-data");


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
        var par6 = $('#unit_search').val();
        var par7 = $('#stspeg').val();
        if (theurl.substr(theurl.lastIndexOf('/') + 1) == "") {
            theurl=theurl+'0';
        }
        //loading();
        $.ajax({
            method:"post",
            url: theurl,
            data:{"lmt":par4,"cari":par5,"org":par6,"stspeg":par7},
            success: function(response){
                $(div).html(response);
            },
            dataType:"html"
        });
        return false;
    }


</script>
