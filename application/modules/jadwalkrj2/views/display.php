<link href="<?php echo base_url()?>assets/css/plugins/datepicker/datepicker3.css" rel="stylesheet">
<link href="<?php echo base_url()?>assets/css/plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet">
<script src="<?php echo base_url()?>assets/js/plugins/datapicker/bootstrap-datepicker.js"></script>
<script src="<?php echo base_url()?>assets/js/plugins/datapicker/bootstrap-datepicker.id.min.js"></script>
<script src="<?php echo base_url()?>assets/js/plugins/daterangepicker/daterangepicker.js"></script>

<link href="<?php echo base_url() ?>assets/css/plugins/chosen/bootstrap-chosen.css" rel="stylesheet">
<script src="<?php echo base_url() ?>assets/js/plugins/chosen/chosen.jquery.js"></script>
<div class="row">
	<div class="ibox float-e-margins">
		<div class="ibox-title">
			<h5>Filtering</h5>
			<div class="ibox-tools">
				<a class="collapse-link">
					<i class="fa fa-chevron-up"></i>
				</a>
			</div>
		</div>
		<div class="ibox-content">
			<div class="row form-horizontal">
				<div class="form-group">
					<label class="control-label col-md-2 col-sm-3 col-xs-12" for="stspeg">Status Pegawai</label>
					<div class="col-sm-7">
						<?php
						$js = 'id="stspeg" class="input-sm form-control chosen-select" name="stspeg" data-placeholder="Pilih Jenis Pegawai..."';
						$selected = array("1", "2");
						echo form_multiselect('stspeg', $lstStsPeg, $selected, $js);
						?>
					</div>
				</div>
                <div class="form-group">
                    <label class="control-label col-md-2 col-sm-3 col-xs-12" for="jnspeg">Jenis Pegawai</label>
                    <div class="col-sm-7">
                        <?php
                        $js = 'id="jnspeg" class="input-sm form-control chosen-select" name="jnspeg" data-placeholder="Pilih Jenis Pegawai..."';
                        $selected = array("1","2");
                        echo form_multiselect('jnspeg',$lstJnsPeg,$selected,$js);
                        ?>
                    </div>
                </div>
				<div class="form-group">
					<label class="control-label col-md-2 col-sm-3 col-xs-12" for="cari_unker">Unit Kerja</label>
					<div class="col-sm-7">
						<div class="unker input-group">
							<input type="text" class="input-sm form-control" readonly name="cari_unker" id="cari_unker" value="" placeholder="Unit Kerja ...">
							<input type="hidden" name="unit_search" id="unit_search" value="">
							<div class="input-group-btn">
								<button class="btn btn-white btn-sm" type="button"><span class="caret"></span></button>
							</div>
						</div>
						<div class="panel combo-p" style="position: absolute;  z-index:50001; display: none;">
							<div class="combo-panel panel-body panel-body-noheader" title="" style="max-height:250px; padding:5px;overflow-y:auto">
							</div>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-2 col-sm-3 col-xs-12" for="start">Tanggal</label>
					<div class="col-sm-5 form-inline" id="data_5">
						<div class="input-daterange input-group" id="datepicker">
							<input type="text" class="input-sm form-control" name="start" id="start" value="<?php echo date("01-m-Y") ?>"/>
							<span class="input-group-addon">s/d</span>
							<input type="text" class="input-sm form-control" name="end" id="end" value="<?php echo date("t-m-Y", strtotime(date("Y-m-d"))); ?>"/>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-2 col-sm-3 col-xs-12" for="caridata">Pencarian</label>
					<div class="col-sm-7">
						<input type="text" id="caridata" name="caridata" placeholder="NIP/Nama" class="input-sm form-control" value="">
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-2 col-md-offset-2">
						<span class="input-group-btn"><button type="button" id="btncari" class="btn btn-sm btn-primary"> Display</button> </span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="ibox float-e-margins">
		<div class="ibox-title">
			<h5>Daftar Jadwal Kerja Pegawai</h5>
			<div class="ibox-tools">
				<a class="dropdown-toggle btn btn-sm btn-primary" data-toggle="dropdown" href="#" title="Menu">
					Status <i class="fa fa-chevron-down"></i>
				</a>
				<ul class="dropdown-menu dropdown-user">
					<li><a href="#" id="btn1">Kehadiran</a>
					</li>
					<li><a href="#" id="btn2">Ketidakhadiran</a>
					</li>
					<li><a href="#" id="btn3">Hapus Status</a>
					</li>
				</ul>
				<a class="collapse-link">
					<i class="fa fa-chevron-up"></i>
				</a>

			</div>
		</div>
		<div class="ibox-content">
			<div id="list-data">
			</div>
			<div class="hr-line-dashed"></div>
			<div id="list2-data">
				<?php include("list2.php") ?>
			</div>
		</div>
	</div>
</div>

<input type="hidden" id="pil" value="0">
<input type="hidden" id="useriddata" value="">
<input type="hidden" id="tglpil" value="">
<input type="hidden" id="tglpil2" value="">
<input type="hidden" id="idPilCell" value="">

<form id="inputForm" name="inputForm" action="javascript:;" method="post" class="form-horizontal form-validate">
	<div class="popup-wrapper fade" id="popup">
		<div class="popup-container">
			<div class="modal-header">
				<span type="button" class="close" data-dismiss="modal" onClick="hidePopup('#popup');">&times;</span>
				<h3 id="title"></h3>
			</div>
			<div class="modal-body" id="input_data" style="max-height:350px;min-height:250px;">

			</div>
			<div class="modal-footer">
				<a class="btn btn-danger btn-sm" data-dismiss="modal" onClick="hidePopup('#popup');">Tutup</a>
				<button class="btn btn-primary btn-sm" id="btn_simpan" onClick="saveForm();">Simpan</button>
			</div>
		</div>
	</div>
</form>



<script>

	$(function(){
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

        $('#caridata').keypress(function(e) {
            if ( e.keyCode == 13 ) {
                $('#btncari').click();
            }
        });

		$('#btncari').click(function(){

			var par4 = $('#limit_display').val();
			if ($('#caridata').val() != '')
				par5 = $('#caridata').val();
			else
				par5 = 'cri';
			var par6 = $('#unit_search').val();

			par7 = $('#stspeg').val();
			par8 = $('#start').val();
			par9 = $('#end').val();
            par10 = $('#jnspeg').val();

			$.ajax({
				method:"post",
				url: "<?php echo site_url('jadwalkrj/buatjadwal') ?>",
				data:{"lmt":par4,"cari":par5,"org":par6,"stspeg":par7,"jnspeg":par10,"start":par8,"end":par9},
				success: function(response){
					$("#list-data").html(response);
				},
				dataType:"html"
			});
			return false;

		});

		$('.unker').click(function(){
			var inwidth = $(this).width();
			var dis = $('.panel').css("display");
			if(dis=='none'){
				$('.panel').css({
					display : 'block',
					width : inwidth
				});

				//$('.combo-panel').empty();
				if ( $('.combo-panel > *').length == 0 ) {
					$('.combo-panel').html('Loading...........');
					$.ajax({
						url: '<?php echo site_url('ajax/getUnitKerja')?>', dataType: 'html', type: 'POST', success: function (data) {
							$('.combo-panel').html(data);
						}
					});
				}
			}else{
				//$('.combo-panel').empty();
				$('.panel').css({
					display : 'none'
				});
				//$('.unker').data('clicked',1);
			}
		});

		$('.chosen-select').chosen({width: "100%"});

		$('#data_5 .input-daterange').datepicker({
			keyboardNavigation: false,
			forceParse: false,
			autoclose: true,
			format:"dd-mm-yyyy",
			language: 'id'
		});

		$('#btn1').click(function(){
            showPil1()
		});

		$('#btn2').click(function(){
            showPil2();
		});

		$('#btn3').click(function(){
            showPil3()
		});

	});

	function saveForm() {
        var ln = $('#catatan').val().length;
        var pildata = $('#pil').val();
        var pilrow = $('#idPilCell').val();

        if (pildata==1 || pildata==2) {
            if (ln == 0) {
                bootbox.alert("Catatan Harus diisi..");
                return false;
            }

            ln = $('#nosk').val().length;
            if (ln == 0) {
                bootbox.alert("Nomor Harus diisi..");
                return false;
            }
        } else
        {
            if (ln == 0) {
                bootbox.alert("Catatan Harus diisi..");
                return false;
            }
        }

		$("#userid").val($('#useriddata').val());
		$("#startattilog").val($('#time1').val());
		$("#endattilog").val($('#time2').val());

		$.ajax({
			url: '<?php echo site_url('jadwalkrj/statusdata');?>/'+pildata,
				dataType: 'json', type: 'POST',
				data: $("#inputForm").serialize(),
				success: function (response) {

			    if (parseInt(response.jmldata)>0) {
                    if ((response.data != "[]") || (response.data != "[[]]")) {
                        $datv = jQuery.parseJSON(response.data);
                        for (var i = 0; i < $datv.length; i++) {
                            if ($datv[i] != 'undefined') {
                                var strd = $("#" + $datv[i].id).data('rec');
                                //$("#" + $datv[i].id).removeClass("row-status").addClass("row-no-status");
                                if (( $('#pil').val() == 1) || ($('#pil').val() == 2 )) {
                                    $("#" + $datv[i].id).attr('data-rec', strd + $datv[i].sts);
                                    $("#" + $datv[i].id).removeClass("row-no-status").addClass("row-status");
                                } else
                                {
                                    if (( $('#pil').val() == 3)) {
                                        $("#" + $datv[i].id).removeClass("row-status").addClass("row-no-status");
                                        if (strd.indexOf("#") > -1) {
                                            var res = strd.split("#");
                                            $("#" + $datv[i].id).attr('data-rec', res[0]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

				loadHistory(pilrow);
				bootbox.alert(response.msg);
                hidePopup('#popup');
			}
		});
	}


    function showPil1()
    {

        $('#pil').val("1");
        $('#title').html("<span>Status Kehadiran</span>");
        if ($('#useriddata').val()=="")
        {
            bootbox.alert("Pilih dahulu datanya..");
            return;
        }

        var url ="<?php echo site_url('jadwalkrj/form1')?>/"+$('#tglpil').val()+"/"+$('#tglpil2').val();
        $("#input_data").html('<div style="position:absolute;left:0;right:0;top:50%;"><center></center></div>').load(url);
        showPopup('#popup');
    }
    function showPil2()
    {
        $('#pil').val("2");
        $('#title').html("<span>Status Ketidakhadiran</span>");
        if ($('#useriddata').val()=="")
        {
            bootbox.alert("Pilih dahulu datanya..");
            return;
        }
        var url ="<?php echo site_url('jadwalkrj/form2')?>/"+$('#tglpil').val()+"/"+$('#tglpil2').val();
        $("#input_data").html('<div style="position:absolute;left:0;right:0;top:50%;"><center></center></div>').load(url);
        showPopup('#popup');
    }

    function showPil3()
    {
        $('#pil').val("3");
        $('#title').html("<span>Penghapusan Status</span>");
        var cek_del  = $('input[name=cek_del]:checked').map(function(){
            return $(this).val();
        }).get();


        var tgl1 = $("#tglpil").val();
        var tgl2 = $("#tglpil2").val();
        if (tgl1=="" ) $("#tglpil").val($("#start").val());
        if (tgl2=="" ) $("#tglpil2").val($("#end").val());

        var uid =$("#useriddata").val();
        if (uid=="") {
            var cek = $(".selected:checked").length;
            if(cek == 0){
                bootbox.alert("Pilih dahulu datanya..");
                return;
            }
            $("#useriddata").val(cek_del);
        }

        var url ="<?php echo site_url('jadwalkrj/form3')?>/"+$('#tglpil').val()+"/"+$('#tglpil2').val();
        $("#input_data").html('<div style="position:absolute;left:0;right:0;top:50%;"><center></center></div>').load(url);
        showPopup('#popup');
    }

</script>

