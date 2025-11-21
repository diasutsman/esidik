<?php


$url_pag =  "'".site_url("rptatt/pagging/0")."'";
$domId ="'#list-data'";

?>
<label class="control-label">Periode Laporan : <?php echo $mulai.' s/d '.$akhir;?></label>
<br>
<label class="control-label" style="color: red;"><?php echo $msg;?></label>
<div class="table-responsive">
    <div class="dataTables_wrapper dt-bootstrap">
        <table class="table table-striped  table-bordered">
            <thead>
            <tr>
                <th  style="text-align: center;width: 10px;">No</th>
                <th  style="text-align: center;">Keterangan</th>
                <th  style="text-align: center;width: 100px;">Jumlah Pegawai</th>
            </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1.</td>
                    <td>Jumlah Keseluruhan Pegawai</td>
                    <td style="text-align: center;"><?php echo isset($jum_pegawai)?$jum_pegawai:"0";?></td>
                </tr>
                <tr>
                    <td>2.</td>
                    <td>Rata-rata jumlah pegawai yang melaksanakan tugas kedinasan di luar kantor (perjalanan dinas atau tugas kedinasan lainnya)</td>
                    <td style="text-align: center;"><?php echo isset($jum_pegawai_dinas)?$jum_pegawai_dinas:"0";?></td>
                </tr>
                <tr>
                    <td>3.</td>
                    <td>Rata-rata jumlah pegawai yang melaksanakan tugas kedinasan di rumah (WFH)</td>
                    <td style="text-align: center;"><?php echo isset($jum_pegawai_wfh)?$jum_pegawai_wfh:"0";?></td>
                </tr>
                <tr>
                    <td></td>
                    <td>Rata-rata jumlah pegawai yang melaksanakan tugas kedinasan di kantor (WFO)</td>
                    <td style="text-align: center;"><?php echo isset($jum_pegawai_shift)?$jum_pegawai_shift:"0";?></td>
                </tr>
                <tr>
                    <td>4.</td>
                    <td>WFO Shift 1</td>
                    <td style="text-align: center;"><?php echo isset($jum_pegawai_shift1)?$jum_pegawai_shift1:"0";?></td>
                </tr>
                <tr>
                    <td></td>
                    <td>WFO Shift 2</td>
                    <td style="text-align: center;"><?php echo isset($jum_pegawai_shift2)?$jum_pegawai_shift2:"0";?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <label class="control-label" >Catatan Pelaksanaan</label>
    <div class="dataTables_wrapper dt-bootstrap">
        <table class="table table-striped  table-bordered">
            <thead>
            <tr>
                <th  style="text-align: center;width: 10px;">No</th>
                <th  style="text-align: center;">Keterangan</th>
                <th  style="text-align: center;width: 100px;">Jumlah Pegawai</th>
            </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1.</td>
                    <td>WFO Normal</td>
                    <td style="text-align: center;"><?php echo isset($jum_pegawai_normal)?$jum_pegawai_normal:"0";?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php 
        $ket1= $panrb[0]->ket1;
        $ket2= $panrb[0]->ket2;
        $ket3= $panrb[0]->ket3;
        $ket4= $panrb[0]->ket4;
        $ket5= $panrb[0]->ket5;
        $ket6= $panrb[0]->ket6;
        $ket7= $panrb[0]->ket7;
        $ket8= $panrb[0]->ket8;
        $ket9= $panrb[0]->ket9;
        $ket10= $panrb[0]->ket10;
    ?>
    <label class="control-label" >Pelaporan Jam Kerja ASN</label>
    <div class="dataTables_wrapper dt-bootstrap">
        <form id="inputForm" action="javascript:;" name="inputForm" method="post" class="form-horizontal form-validate">
        <table class="table table-striped  table-bordered">
            <thead>
            <tr>
                <th  style="text-align: center;width: 10px;">No</th>
                <th  style="text-align: center;">Keterangan</th>
            </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1.</td>
                    <td>
                        Jumlah pegawai yang mengikuti rapid test ? <br>
                        <input type="text" style="background: #ffffd0;" class="input-sm form-control" name="ket1" id="ket1" onkeypress="return hanyaAngka(event)" value="<?php echo isset($ket1)?$ket1:"0";?>" />
                    </td>
                </tr>
                <tr>
                    <td>2.</td>
                    <td>
                        Jumlah keselurahan pegawai yang bertugas melakukan pelayanan publik secara langsung ? <br>
                        <input type="text" class="input-sm form-control" name="ket2" id="ket2" value="<?php echo isset($jum_pegawai_normal)?$jum_pegawai_normal:"0";?>" readonly />
                    </td>
                </tr>
                <tr>
                    <td>3.</td>
                    <td>
                        Rata - rata jumlah pegawai yang bertugas melakukan pelayanan publik secara langsung ? <br>
                        <input type="text"  class="input-sm form-control" name="ket3" id="ket3" value="<?php echo isset($jum_pegawai_normal)?$jum_pegawai_normal:"0";?>" readonly />
                    </td>
                </tr>
                <tr>
                    <td>4.</td>
                    <td>
                        Dalam rangka pencegahan penyebaran COVID-19, berapa kali kantor dilakukan disinfeksi dalam satu minggu ? <br>
                        <input type="text" style="background: #ffffd0;" class="input-sm form-control" name="ket4" id="ket4" onkeypress="return hanyaAngka(event)" value="<?php echo isset($ket4)?$ket4:"0";?>"/>
                    </td>
                </tr>
                <tr>
                    <td>5.</td>
                    <td>
                        Apakah terdapat kasus terkonfirmasi positif COVID-19 pada instansi anda ? <br> 
                        <?php
                            $list_instansi['']='- Pilih Ya atau Tidak -';
                            $list_instansi['1']='Ya';
                            $list_instansi['2']='Tidak';
                            
                            $selected = (isset($ket5)?$ket5:'');
                            $js = 'id="ket5" name"ket5" class="form-control"';
                            echo form_dropdown('ket5',$list_instansi,$selected,$js);  
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>6.</td>
                    <td>
                        Apabila ada, berapa jumlah kasus terkonfirmasi positif COVID-19 pada instansi anda ? <br> 
                        <input type="text" style="background: #ffffd0;" class="input-sm form-control" name="ket6" id="ket6" onkeypress="return hanyaAngka(event)" value="<?php echo isset($ket6)?$ket6:"0";?>"/>
                    </td>
                </tr>
                <tr>
                    <td>7.</td>
                    <td>
                        Apabila ada, apa tindaklanjut yang dilakukan oleh instansi anda ? <br> 
                        <input type="text" style="background: #ffffd0;" class="input-sm form-control" name="ket7" id="ket7" value="<?php echo isset($ket7)?$ket7:"0";?>" />
                    </td>
                </tr>
                <tr>
                    <td>8.</td>
                    <td>
                        Apabila ada, berapa lama instansi anda ditutup dalam rangka disinfeksi ? (hari) <br>
                        <input type="text" style="background: #ffffd0;" class="input-sm form-control" name="ket8" id="ket8" onkeypress="return hanyaAngka(event)" value="<?php echo isset($ket8)?$ket8:"0";?>" />
                    </td>
                </tr>
                <tr>
                    <td>9.</td>
                    <td>
                        Apabila ada, apakah dilakukan penelusuran kontak erat (contact tracing) dengan pegawai terkonfirmasi COVID-19 ? <br> 
                        <?php
                            $list_instansi2['']='- Pilih Ya atau Tidak -';
                            $list_instansi2['1']='Ya';
                            $list_instansi2['2']='Tidak';
                            
                            $selected = (isset($ket9)?$ket9:'');
                            $js = 'id="ket9" name"ket9" class="form-control"';
                            echo form_dropdown('ket9',$list_instansi2,$selected,$js);  
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>10.</td>
                    <td>
                        Apabila ada, apakah pegawai yang kontak erat diberikan penugasan WFH ? <br> 
                        <?php
                            $list_instansi3['']='- Pilih Ya atau Tidak -';
                            $list_instansi3['1']='Ya';
                            $list_instansi3['2']='Tidak';
                            
                            $selected = (isset($ket10)?$ket10:'');
                            $js = 'id="ket10" name"ket10" class="form-control"';
                            echo form_dropdown('ket10',$list_instansi3,$selected,$js);  
                        ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="modal-footer" style="text-align: left;">
            <button class="btn btn-primary btn-sm" id="btn_simpan"  onClick="simpan_new();">Simpan</button>
        </div>
        </form>
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

    function hanyaAngka(evt) {
      var charCode = (evt.which) ? evt.which : event.keyCode
       if (charCode > 31 && (charCode < 48 || charCode > 57))

        return false;
      return true;
    }

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
        var par8 = $('#jnspeg').val();
        var par9 = $('#start').val();
        var par10 = $('#end').val();
        if (theurl.substr(theurl.lastIndexOf('/') + 1) == "") {
            theurl=theurl+'0';
        }
        //loading();
        $.ajax({
            method:"post",
            url: theurl,
            data:{"lmt":par4,"cari":par5,"org":par6,"stspeg":par7,"jnspeg":par8,"start":par9,"end":par10},
            success: function(response){
                $(div).html(response);
            },
            dataType:"html"
        });
        return false;
    }

    function simpan_new(){
        loading();
        var par5 = $('#caridata').val();
        var par6 = $('#unit_search').val();
        var par7 = $('#stspeg').val();
        var par8 = $('#jnspeg').val();
        var par9 = $('#start').val();
        var par10 = $('#end').val();
        var par11 = $('#ket').val();
        var par12 = $('#idcetak').val();

        var ket1  = $('#ket1').val();
        var ket2  = $('#ket2').val();
        var ket3  = $('#ket3').val();
        var ket4  = $('#ket4').val();
        var ket5  = $('#ket5').val();
        var ket6  = $('#ket6').val();
        var ket7  = $('#ket7').val();
        var ket8  = $('#ket8').val();
        var ket9  = $('#ket9').val();
        var ket10 = $('#ket10').val();

        $.ajax({
            url     : '<?php echo site_url('rptjbd/simpan_new/');?>',
            dataType: 'json',
            type    : 'POST',
            data    : { 
                        'cari'  :par5,
                        'org'   :par6,
                        'stspeg':par7,
                        'jnspeg':par8,
                        'start' :par9,
                        'end' :par10,
                        'ket1':ket1,
                        'ket2':ket2,
                        'ket3':ket3,
                        'ket4':ket4,
                        'ket5':ket5,
                        'ket6':ket6,
                        'ket7':ket7,
                        'ket8':ket8,
                        'ket9':ket9,
                        'ket10':ket10,
                      },
            success : function(data){
                if(data.status!="error"){
                    unloading();
                    bootbox.alert(data.msg);
                }else{
                    unloading();
                    bootbox.alert(data.msg);
                    
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                if (XMLHttpRequest.status === 200) {
                    unloading();
                    bootbox.alert(textStatus+' errornya '+errorThrown);
                }else{
                    unloading();
                    bootbox.alert('Maaf, Terjadi kesalahan dalam sistem!!');
                }
            }
        });
    }
</script>
