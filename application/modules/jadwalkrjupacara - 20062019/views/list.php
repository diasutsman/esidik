<?php
/**
 * File: list.php
 * Author: abdiIwan.
 * Date: 12/28/2016
 * Time: 9:38 PM
 * absensi.kemendagri.go.id
 */

$url_pag =  site_url("jadwalkrjupacara/pagging/0");


$sorting='';
if($typeorder=='sorting' || $typeorder=='sorting_desc'){
    $sorting='sorting_asc';
}

if($typeorder=='sorting_asc'){
    $sorting='sorting_desc';
}

?>
<style>
    #tbl tr td {
        cursor: pointer;
    }

    input[type="checkbox"]{
        margin: 0 0 0;
        line-height: normal;
    }

    .dataTables_wrapper {
        padding-bottom: 0;
    }

    .hr-line-dashed {
        margin: 5px 0;
    }

    .nav.nav-tabs > li > a {
        padding: 3px 4px;
        color: #676a6c;
    }

    .tabs-container .panel-body {
        padding: 5px;
    }
</style>
<div class="table-responsive">
    <div class="dataTables_wrapper dt-bootstrap">
        <table class="table table-bordered small" id="tbl">
            <thead>
            <tr>
                <th rowspan="2"><input type="checkbox" name="cek_all" id="cek_all"></th>
                <th rowspan="2" >No.</th>
                <th rowspan="2"  onClick="load_url('<?php echo $url_pag?>','userid','<?php echo (isset($order)?(($order=='userid')?$sorting:'sorting'):'sorting_asc')?>')"><div class="<?php echo (isset($order)?(($order=='userid')?$typeorder:'sorting'):'sorting_asc')?>">NIP</div></th>
                <th rowspan="2" style="white-space: nowrap" onClick="load_url('<?php echo $url_pag?>','name','<?php echo (isset($order)?(($order=='name')?$sorting:'sorting'):'sorting')?>')"><div class="<?php echo (isset($order)?(($order=='name')?$typeorder:'sorting'):'sorting')?>">Nama</div></th>
                <?php
                $str1=$start_date;
                $end1=$end_date;
                $xxi=1;
                while (strtotime($str1) <= strtotime($end1)) {
                    $xxi++;
                    $str1 = date("Y-m-d", strtotime("+1 days", strtotime($str1)));
                }
                echo "<th class='text-center' colspan='$xxi'>Tanggal </th>";
                ?>

            </tr>
                <?php
                $str1=$start_date;
                $end1=$end_date;
                while (strtotime($str1) <= strtotime($end1)) {
                    echo "<th class='text-center' data-original-title='".format_date_ind(date("Y-m-d",strtotime($str1)))."' data-toggle='tooltip' data-placement='top' data-container='body'>".date("j",strtotime($str1))."</th>";
                    $str1 = date("Y-m-d", strtotime("+1 days", strtotime($str1)));
                }
                ?>
            </thead>
            <tbody>
                <?php
                $co=1;
                $jm=0;
                //print_r($result);
                    foreach ($result as $key =>$value)
                    {
                        $jm=$co;
                        $userid=$value["userid"] ;
                        ?>
                        <tr>
                            <td width="20px"><input type="checkbox" name="cek_del" id="cek_del_<?php echo $userid?>" class="selected" value="<?php echo $userid ?>"></td>
                            <td ><?php echo ($co+$offset)?></td>
                            <td id="<?php echo $userid?>" data-tgl='' data-fid="userid" data-id="<?php echo $userid?>"  data-rec="<?php echo $userid?>" class="rows"><?php echo $value["userid"]?></td>
                            <td style="white-space: nowrap" id="<?php echo $userid.'-name'?>" data-tgl='' data-fid="name"  data-id="<?php echo $userid?>"  data-rec="<?php echo $value["name"]?>"  class="rows"><?php echo $value["name"]?></td>
                            <?php

                            $str2 = $start_date;
                            $end2 = $end_date;
                            while (strtotime($str2) <= strtotime($end2)) {
                                $col = "";
                                $lrec="";
                                $lbltgl = date("Y-m-d", strtotime($str2));
                                $lket="-";
                                $cls="row-no-status";
                                if (array_key_exists(strtotime($str2),$value))
                                {
                                    $lrec = $value[strtotime($str2)];

                                    $ket = explode("#", $value[strtotime($str2)]);
                                    //print_r($ket);echo'<br>';
                                    if ( $ket[0] != "" ) {
                                        $col = $arrcolor[$ket[0]];
                                        $lket = "<b>".$ket[0]."</b>";
                                    } else {
                                        $col ="white";
                                        $lket = "";
                                    }

                                    if (count($ket)>1)
                                    {
                                        $cls="row-status";
                                    }

                                    echo "<td style='background-color:$col' class='rows text-center $cls' id='".$userid.'-'.$str2."' data-fid='$str2' data-id='$userid' data-tgl='$lbltgl' data-rec='".$lrec ."'>" .$lket."</td>";
                                } else
                                {
                                    echo "<td style='background-color:$col' class='rows text-center $cls' id='".$userid.'-'.$str2."' data-fid='$str2' data-id='$userid' data-tgl='$lbltgl' data-rec='".$lrec ."'>" .$lket."</td>";
                                }

                                $str2 = date("Y-m-d", strtotime("+1 days", strtotime($str2)));
                            }
                            ?>
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

                        $selected = '10';
                        if(isset($limit_display) && trim($limit_display) != '')
                            $selected = $limit_display;
                        $js = 'id="limit_display" class="input-sm" onChange="load_url(\''.$url_pag.'\',\''.(isset($order)?$order:'userid').'\',\''.(isset($typeorder)?$typeorder:'sorting_asc').'\');" name="tabel_data_length" size="1" ';
                        echo form_dropdown('tabel_data_length',listPager(),$selected,$js);
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
<div id="context-menu" style="display:none">
    <ul class="dropdown-menu">
        <li><a href="#"  onclick="showPil2()">Pembatalan Upacara</a></li>
        <li><a href="#"  onclick="showPil3()">Hapus Jadwal Kerja</a></li>
    </ul>
</div>
<script>
    var oldcolor="";
    var oldbackcolor="";
    var oldid = "";
    $(function(){

        // If the document is clicked somewhere
        $(document).bind("mousedown", function (e) {

            // If the clicked element is not the menu
            if (!$(e.target).parents(".custom-menu").length > 0) {

                // Hide it
                $(".custom-menu").hide(100);
            }
        });

        createpaging();
        $("[data-toggle='tooltip']").tooltip();
        $('#cek_all').click(function(){
            $(".selected").prop("checked", $("#cek_all").prop("checked"));
        });

        $(".rows").click(function (){
            var xfield = $(this).data('fid');
            var xdata = $(this).data('rec');
            var xuid = $(this).data('id');
            var xtgl = $(this).data('tgl');

            $("#idPilCell").val( $(this).attr('id') );

            if (xtgl=="") {
                $("#tglpil").val($("#start").val());
                $("#tglpil2").val($("#end").val());
            } else {
                $("#tglpil").val(xtgl);
                $("#tglpil2").val(xtgl);
            }

            $("#useriddata").val(xuid);
            if (oldbackcolor !="")
            {
                $('#'+oldid).css('backgroundColor', oldbackcolor);
                $('#'+oldid).css('color', oldcolor);
            }
            oldid = this.id;
            oldbackcolor = $(this).css('backgroundColor');
            oldcolor = $(this).css('color');
            
            $(this).css('backgroundColor', '#000');
            $(this).css('color', '#fff');

            $.ajax({
                method:"post",
                url: "<?php echo site_url("jadwalkrjupacara/gethistory")?>",
                data:{"userid":xuid,"coldate":xfield,"colrec":xdata},
                success: function(response){
                    $("#list-data-history").html(response);
                },
                dataType:"html"
            });
        });


        $('.rows').contextmenu({
            target: "#context-menu"
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
                load_url(thisHref,'<?php echo (isset($order)?$order:'userid')?>','<?php echo (isset($typeorder)?$typeorder:'sorting_asc')?>');
                return false;
            });
        });
    }

    function load_url(theurl,orderby,sortingby)
    {
        var par4 = $('#limit_display').val();
        if (theurl.substr(theurl.lastIndexOf('/') + 1) == "") {
            theurl=theurl+'0';
        }
        var par7 = $('#stspeg').val();
        var par8 = $('#jnspeg').val();
        $.ajax({
            method:"post",
            url: theurl,
            data:{"lmt":par4,'order':orderby,'sorting':sortingby},
            success: function(response){
                $('#list-data').html(response);
            },
            dataType:"html"
        });
        return false;
    }

    function loadHistory($idcell)
    {
        var xfield = $("#"+$idcell).attr('data-fid');
        var xdata = $("#"+$idcell).attr('data-rec');
        var xuid = $("#"+$idcell).attr('data-id');
        var xtgl = $("#"+$idcell).attr('data-tgl');

        if (xtgl=="") {
            $("#tglpil").val($("#start").val());
            $("#tglpil2").val($("#end").val());
        } else {
            $("#tglpil").val(xtgl);
            $("#tglpil2").val(xtgl);
        }

        $("#useriddata").val(xuid);

        $.ajax({
            method:"post",
            url: "<?php echo site_url("jadwalkrjupacara/gethistory")?>",
            data:{"userid":xuid,"coldate":xfield,"colrec":xdata},
            success: function(response){
                $("#list-data-history").html(response);
            },
            dataType:"html"
        });
    }

</script>


