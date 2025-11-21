<?php


$url_pag =  site_url("ketidakhadiran/pagging/0");



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
				<th></th>
                <th>No.</th>
                <th>Nama / NIP</th>
                <th width="9%">Tanggal Input</th>
				<th width="10%">Status</th>
                <th>Lokasi</th>
				<th>Status Verifikasi</th>
				<th>Foto</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $co=1;
            $jm=0;
			if ($result>=1)
            {
				foreach($result as $row)
				{
					$priv = isset($privi[$row->privilege])?$privi[$data->privilege]:"User";
					if($row->status == 30){
						$namastatus = "UPACARA";
					}else if($row->status == 29){
						$namastatus = "WFH";
					}else{
						$namastatus = "";
					}
					
					$jm=$co;
					if($row->status_verifikasi == 1){
						$bg = 'green';
						$statusverifikasi = 'Disetujui';
					}else if ($row->status_verifikasi == 2){
						$statusverifikasi = 'Ditolak';
						$bg = 'red';
					}else{
						$statusverifikasi = 'Belum Diproses';
						$bg = 'blue';
					}

					?>
					<tr>
						<td>
							<a data-toggle="tooltip" data-placement="top" title="Disetujui" style="margin-bottom:5px;" class="btn btn-success btn-labeled btn-xs" onClick="btnproses('<?php echo $row->idpegawai;?>')" ><b><i class="fa fa-check"></i></b></a>
							<a data-toggle="tooltip" data-placement="top" title="Tidak Disetujui" style="margin-bottom:5px;" class="btn btn-danger btn-labeled btn-xs"  onClick="btntolak('<?php echo $row->idpegawai;?>')" ><b><i class="fa fa-close"></i></b></a>
							<a data-toggle="tooltip" data-placement="top" title="Dikembalikan" style="margin-bottom:5px;" class="btn btn-warning btn-labeled btn-xs" onClick="btnkembalikan('<?php echo $row->idpegawai;?>')" ><b><i class="fa fa-clock-o"></i></b></a>
						</td>
						<td><?php echo ($co+$offset);?></td>
						<td><?php echo $row->name."<br>".$row->userid;?></td>
						<td><?php echo date('d-m-Y H:i:s', strtotime($row->tanggalinput));?></td>
						<td><?php echo $namastatus;?></td>
						<td><?php echo $row->lokasi;?></td>
						<td><?php echo $statusverifikasi;?></td>
						<td>
							<a target="_blank" href="<?php echo 'https://ropeg.setjen.kemendagri.go.id/restsimpeg/uploads/kehadiran/'.$row->foto;?>">
							<?php echo $row->foto;?>
							</a>
						</td>
					</tr>
					<?php

					$co++;
				}

            }else{
                ?>
                <tr>
                    <td colspan="11"><center>Tidak ada data..</center></td>
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
		var par9 = $('#start').val();
		var par10 = $('#end').val();
		var par11 = $('#stausverifikasi').val();
		var par12 = $('#stausketidakhadiran').val();
        $.ajax({
            method:"post",
            url: theurl,
            data:{"lmt":par4,"cari":par5,"org":par6,"stspeg":par7,"jnspeg":par8,'start':par9,'end':par10,'order':orderby,'sorting':sortingby,"stausverifikasi":par11,"stausketidakhadiran":par12},
            success: function(response){
                $('#list-data').html(response);
            },
            dataType:"html"
        });
        return false;
    }
	
	function btnproses(a)
	{
		var par6 = $('#unit_search').val();
		var par8 = $('#start').val();
		var par9 = $('#end').val();
		var par7 = $('#stspeg').val();
		var par10 = $('#jnspeg').val();
		
		bootbox.confirm("Yakin Anda Akan Menyetujui Usulan Ketidakhadiran Ini ?", function(result) {
			if(result) {
				$.ajax({
					url: '<?php echo site_url('verifikasi_kehadiran/statushadir') ?>',
					dataType: 'json',
					type    : 'POST',
					data:{"org":par6,"stspeg":par7,"jnspeg":par10,"userid":a,"startdate":par8,"enddate":par9},
					success: function(data){
						load_url('<?php echo site_url('verifikasi_kehadiran/pagging/0') ?>', '<?php echo (isset($order)?$order:'id')?>','<?php echo (isset($typeorder)?$typeorder:'sorting')?>');
						bootbox.alert(data.msg);
					}
				});
			}
		});
	}
	
	function btntolak(a)
	{
		var par6 = $('#unit_search').val();
		var par8 = $('#start').val();
		var par9 = $('#end').val();
		var par7 = $('#stspeg').val();
		var par10 = $('#jnspeg').val();
		bootbox.confirm("Yakin Anda Akan Tidak Menyetujui Usulan Ketidakhadiran Ini ?", function(result) {
			if(result) {
				$.ajax({
					url: '<?php echo site_url('verifikasi_kehadiran/statusditolak') ?>',
					dataType: 'json',
					type    : 'POST',
					data:{"org":par6,"stspeg":par7,"jnspeg":par10,"userid":a,"startdate":par8,"enddate":par9},
					success: function(data){
						load_url('<?php echo site_url('verifikasi_kehadiran/pagging/0') ?>', '<?php echo (isset($order)?$order:'id')?>','<?php echo (isset($typeorder)?$typeorder:'sorting')?>');
						bootbox.alert(data.msg);
					}
				});
			}
		});
	}
	
	function btnkembalikan(a)
	{
		var par6 = $('#unit_search').val();
		var par8 = $('#start').val();
		var par9 = $('#end').val();
		var par7 = $('#stspeg').val();
		var par10 = $('#jnspeg').val();
		bootbox.confirm("Yakin Anda Akan Mengkembalikan Usulan Ketidakhadiran Ini ?", function(result) {
			if(result) {
				$.ajax({
					url: '<?php echo site_url('verifikasi_kehadiran/statusdikembalikan') ?>',
					dataType: 'json',
					type    : 'POST',
					data:{"org":par6,"stspeg":par7,"jnspeg":par10,"userid":a,"startdate":par8,"enddate":par9},
					success: function(data){
						load_url('<?php echo site_url('verifikasi_kehadiran/pagging/0') ?>', '<?php echo (isset($order)?$order:'id')?>','<?php echo (isset($typeorder)?$typeorder:'sorting')?>');
						bootbox.alert(data.msg);
					}
				});
			}
		});
	}

</script>
