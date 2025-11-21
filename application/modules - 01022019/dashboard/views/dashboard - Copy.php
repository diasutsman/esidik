<script src="<?php echo base_url()?>assets/js/plugins/newsTicker.js"></script>
<script src="<?php echo base_url()?>assets/amcharts/amcharts.js" type="text/javascript"></script>
<script src="<?php echo base_url()?>assets/amcharts/serial.js" type="text/javascript"></script>
<script src="<?php echo base_url()?>assets/amcharts/pie.js" type="text/javascript"></script>
<!-- Styles -->
<style>
#chartdiv {
	min-width: 310px; height: 332px; max-width: 600px; margin: 0 auto
}
</style>

<audio id="notif_audio"><source src="<?php echo base_url('assets/sounds/notify.ogg');?>" type="audio/ogg"><source src="<?php echo base_url('assets/sounds/notify.mp3');?>" type="audio/mpeg"><source src="<?php echo base_url('assets/sounds/notify.wav');?>" type="audio/wav"></audio>
<audio id="notif_presensi"><source src="<?php echo base_url('assets/sounds/you-know.ogg');?>" type="audio/ogg"><source src="<?php echo base_url('assets/sounds/you-know.mp3');?>" type="audio/mpeg"></audio>
<div class="row">
	<div class="col-lg-12">
		<div class="ibox float-e-margins">
			<div style="10px 10px 10px 10px;" class="ibox-content">
				<div class="row form-horizontal">
				<form action="<?php echo site_url('dashboard/index_list'); ?>" method="post" name="bb" id="bb" class="form-horizontal form-label-left">	
				<div class="form-group">
					<label class="control-label col-md-1 col-sm-3 col-xs-12" for="jnspal">Unit Kerja :</label>
					<div class="col-sm-3" id="data_5">
						<select class="input-sm form-control" id="unitkerja" name="unitkerja">
							<?php 
								$query = $this->db->query("SELECT * FROM departments WHERE deptid ='1' ");
								foreach ($query->result() as $row)
								{
							?>
								<option value="<?php echo $row->id; ?>" 
								<?php if ($this->session->userdata('s_unitkerja')==$row->id) { echo "selected";} ?>> 
								<?php echo $row->deptname; ?></option>
							<?php 
								} 
							?>		
						</select>
					</div>
					<div class="col-sm-3" id="data_5">
						<!--<span id="simpan" onClick="Proses();" class="btn btn-small btn-danger">
							Generate
							<i class="icon-arrow-right icon-on-right bigger-110"></i>
						</span>-->
						<button type="submit" id="btncaridata" class="btn btn-sm btn-primary"> Generate</button>
					</div>
				</div>
				</form>
				</div>
			</div>
			<div class="ibox-content">
				
		
	

	<div class="ibox float-e-margins">
		<div class="ibox-content  gray-bg">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="ibox float-e-margins">
                                <div class="ibox-title">
                                    <span class="label pull-right" style="background-color: #1AB394;color: #ffffff"><?php echo $lstprocess;?></span>
                                    <h5>Tepat Waktu</h5>
                                </div>
                                <div class="ibox-content">
                                    <h1 class="no-margins"><?php echo $jmlTepatWaktu>0? number_format($jmlTepatWaktu,0,",",".")." Pegawai": "-";?> </h1>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="ibox float-e-margins">
                                <div class="ibox-title">
                                    <span class="label pull-right" style="background-color: #567fac;color: #ffffff"><?php echo $lstprocess;?></span>
                                    <h5>Terlambat</h5>
                                </div>
                                <div class="ibox-content">
                                    <h1 class="no-margins"><?php echo $jmlTerlambat>0 ? number_format($jmlTerlambat,0,",",".")." Pegawai": "-";?></h1>
                                </div>
                            </div>
                        </div>
						 <div class="col-lg-4">
                            <div class="ibox float-e-margins">
                                <div class="ibox-title">
                                    <span class="label pull-right"  style="background-color: #8eac4c;color: #ffffff"><?php echo $lstprocess;?></span>
                                    <h5>Cuti</h5>
                                </div>
                                <div class="ibox-content">
                                    <h1 class="no-margins"><?php echo ($jmlCuti+$jmlSakit)>0? number_format(($jmlCuti+$jmlSakit),0,",",".")." Pegawai": "-";?></h1>
                                </div>
                            </div>
                        </div>
                    </div>
					</div>
					<div class="col-lg-12">
						<div class="row">
							<div class="col-lg-4">
								<div class="ibox float-e-margins">
									<div class="ibox-title">
										<span class="label pull-right"  style="background-color: #4c4cac;color: #e3ff3a"><?php echo $lstprocess;?></span>
										<h5>Tugas Belajar</h5>
									</div>
									<div class="ibox-content">
										<h1 class="no-margins"><?php echo $jmlTb>0? number_format($jmlTb,0,",",".")." Pegawai": "-";?></h1>
									</div>
								</div>
							</div>
						
							<div class="col-lg-4">
								<div class="ibox float-e-margins">
									<div class="ibox-title">
										<span class="label pull-right"  style="background-color: #3eff72;color: #1c13b2"><?php echo $lstprocess;?></span>
										<h5>Dinas Luar</h5>
									</div>
									<div class="ibox-content">
										<h1 class="no-margins"><?php echo $jmlDinas>0? number_format($jmlDinas,0,",",".")." Pegawai": "-";?></h1>
									</div>
								</div>
							</div>
						
							<div class="col-lg-4">
								<div class="ibox float-e-margins">
									<div class="ibox-title">
										<span class="label pull-right"  style="background-color: #484b90;color: #e0e3ee"><?php echo $lstprocess;?></span>
										<h5>Shift Khusus</h5>
									</div>
									<div class="ibox-content">
										<h1 class="no-margins"><?php echo $jmlLain>0? number_format($jmlLain,0,",",".")." Pegawai": "-";?></h1>
									</div>
								</div>
							</div>
						</div>
					</div>
				<div class="col-lg-12">
				<div class="row">
					<div class="col-lg-6">
						<div class="ibox float-e-margins">
							<div class="ibox-title">
								<span class="label pull-right"  style="background-color: #8eac4c;color: #ffffff"><?php echo $lstprocess;?></span>
								<h5>Statistik Jumlah Kehadiran Pegawai </h5>
							</div>
							<div class="ibox-content">
								<script>
var chart = AmCharts.makeChart( "chartdiv", {
  "type": "pie",
  "theme": "light",
  "dataProvider": [ {
    "country": "Tepat Waktu",
    "litres": <?php echo $jmlTepatWaktu;?>
  }, {
    "country": "Terlambat",
    "litres": <?php echo $jmlTerlambat;?>
  }, {
    "country": "Cuti",
    "litres": <?php echo $jmlCuti+$jmlSakit;?>
  }, {
    "country": "Tugas Belajar",
    "litres": <?php echo $jmlTb;?>
  }, {
    "country": "Dinas Luar",
    "litres": <?php echo $jmlDinas;?>
  }, {
    "country": "Shift Khusus",
    "litres": <?php echo $jmlLain;?>
  } ],
  "titleField": "country",
  "valueField": "litres",
  
   "balloon":{
   "fixedPosition":true
  },
  "export": {
    "enabled": true
  }
} );
</script>

<!-- HTML -->
<div id="chartdiv"></div>

							</div>
							
							
						</div>
					</div>
					<div class="col-lg-6">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <span class="label label-info pull-right"><?php echo indo_date(date('y-m-d'));?></span>
                            <h5>Presensi</h5>
                        </div>
                        <div class="ibox-content">
                            <div id="new-message-notif"></div>
                            <div id="multilines">
                                <ul class="list-group" id="data-presensi">
                                    <?php

                                    foreach ($lsthariini as $row)
                                    {
                                        ?>
                                        <li class="list-group-item">
                                            <p><span class="text-info"><?php echo $row["name"]?></span> | <small><span class="text-left"><?php echo $row["deptname"]?></span></small></p>
                                            <small class="block text-muted"><i class="fa fa-clock-o"></i> <?php echo ymdToIna($row["checktime"]) ?></small>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                </ul>

                            </div>


                        </div>
                    </div>
                </div>
					
				</div>
				</div>

            </div>
		</div>
	</div>
</div>
</div>
	</div>
		</div>

<?php
/*
$a = strtotime("2016-11-04 16:30:00");
$b = strtotime("2016-11-04 16:02:41");
$selisih = $a - $b;
echo $selisih."<br>";
$jarak = $selisih / 86400;
echo $jarak."<br>";

echo timeStampDiff("2016-11-04 16:30:00","2016-11-04 16:02:41") ;
*/

?>
<script>
	window.paceOptions = {
		startOnPageLoad: false,
        ajax: false,
        restartOnRequestAfter: false
	};
    var arrPres = [];

    var multilines = $('#data-presensi').newsTicker({
        row_height: 85,
        speed: 800,
        duration:30000,
        pauseOnHover: 0
    });

    $('#data-presensi li').each(function(i, li) {
        arrPres.push($(li).html());
    });

    setInterval(function () {
        $.ajax({
            method:"get",
            url: '<?php echo site_url('dashboard/listabsen')?>',
            success: function(response){
                $('#multilines').html(response);
            },
            dataType:"html"
        });
    }, 5000);

</script>

<!--<script src="<?php echo base_url('node_server/node_modules/socket.io-client/dist/socket.io.js');?>"></script>
<script>
        var socket = io.connect( '<?php echo ENVIRONMENT=="development"?"http://192.168.1.63:8000' ":"http://192.168.193.172:8000'" ?>);
        socket.on('new-presensi', function(message) {
        var obj = JSON.parse(message);
        multilines.newsTicker('stop'); //stop ticker

        var showdata='<li class="list-group-item bounce"><p><span class="text-info">'+obj[0].name+'</span> | <small><span class="text-left">'+obj[0].deptname+'</span></small></p><small class="block text-muted"><i class="fa fa-clock-o"></i>'+obj[0].checktime+'</small></li>';

        var newArr = arrPres.slice(0, -1); //remove first row array

        var list = $("#data-presensi");
        list.empty().each(function(i){
            $("#data-presensi").append(showdata); //add data to first row
            for (var x = 0; x < newArr.length; x++){
                $(this).append('<li class="list-group-item">' + newArr[x] + '</li>');
            }
        });

        $('#notif_message')[0].play();
        $('#data-presensi li:first').fadeIn();

        arrPres = []; //remove all array

        $('#data-presensi li').each(function(i, li) {
            arrPres.push($(li).html()); //add to array
        });

        multilines.newsTicker('start');//start ticker



    });

</script>
-->
