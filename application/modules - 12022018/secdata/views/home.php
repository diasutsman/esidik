<script src="<?php echo base_url()?>assets/js/plugins/chartJs/Chart.min.js"></script>
<script src="<?php echo base_url()?>assets/js/plugins/newsTicker.js"></script>

<audio id="notif_audio"><source src="<?php echo base_url('assets/sounds/notify.ogg');?>" type="audio/ogg"><source src="<?php echo base_url('assets/sounds/notify.mp3');?>" type="audio/mpeg"><source src="<?php echo base_url('assets/sounds/notify.wav');?>" type="audio/wav"></audio>
<audio id="notif_presensi"><source src="<?php echo base_url('assets/sounds/you-know.ogg');?>" type="audio/ogg"><source src="<?php echo base_url('assets/sounds/you-know.mp3');?>" type="audio/mpeg"></audio>
<div class="row">
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
									<h1 class="no-margins"><?php echo $jmlTerlambat ? number_format($jmlTerlambat,0,",",".")." Pegawai": "-";?></h1>
								</div>
							</div>
						</div>
						<div class="col-lg-4">
					<div class="ibox float-e-margins">
						<div class="ibox-title">
							<span class="label pull-right"  style="background-color: #ec8572;color: #ffffff"><?php echo $lstprocess;?></span>
							<h5>Ijin</h5>
						</div>
						<div class="ibox-content">
							<h1 class="no-margins"><?php echo $jmlIjin? number_format($jmlIjin,0,",",".")." Pegawai": "-";?></h1>
						</div>
					</div>
				</div>
						<div class="col-lg-4">
							<div class="ibox float-e-margins">
								<div class="ibox-title">
									<span class="label pull-right"  style="background-color: #B29180;color: #ffffff"><?php echo $lstprocess;?></span>
									<h5>Alpha</h5>
								</div>
								<div class="ibox-content">
									<h1 class="no-margins"><?php echo $jmlAlpha? number_format($jmlAlpha,0,",",".")." Pegawai": "-";?></h1>
								</div>
							</div>
						</div>
						<div class="col-lg-4">
							<div class="ibox float-e-margins">
								<div class="ibox-title">
									<span class="label pull-right"  style="background-color: #FAD52D;color: #000"><?php echo $lstprocess;?></span>
									<h5>Sakit</h5>
								</div>
								<div class="ibox-content">
									<h1 class="no-margins"><?php echo $jmlSakit? number_format($jmlSakit,0,",",".")." Pegawai": "-";?></h1>
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
									<h1 class="no-margins"><?php echo $jmlCuti? number_format($jmlCuti,0,",",".")." Pegawai": "-";?></h1>
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <span class="label label-info pull-right"><?php echo date('d-m-Y');?></span>
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
			<div class="row">
				<div class="col-lg-12">
					<div class="ibox float-e-margins">
						<div class="ibox-title">
							<h5>Unit Kerja</h5>
						</div>
						<div class="ibox-content">
							<?php echo $unitkerjaUser?>
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
        row_height: 10,
        speed: 800,
        duration:30000,
        pauseOnHover: 0
    });

    $('#data-presensi li').each(function(i, li) {
        arrPres.push($(li).html());
    });


</script>

<script src="<?php echo base_url('node_server/node_modules/socket.io-client/dist/socket.io.js');?>"></script>
<script>
    var socket = io.connect( 'http://192.168.1.63:8000' );

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

