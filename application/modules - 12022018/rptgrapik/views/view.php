<script src="<?php echo base_url()?>assets/js/plugins/chartJs/Chart.min.js"></script>
<script src="<?php echo base_url()?>assets/js/plugins/newsTicker.js"></script>

<div class="row">
	<div class="ibox float-e-margins">
		<div class="ibox-content  gray-bg">
			<div class="row">
				<div class="col-lg-12">
					<!--<div class="row">
						<div class="col-lg-4">
							<div class="ibox float-e-margins">
								<div class="ibox-title">
									<span class="label pull-right" style="background-color: #1AB394;color: #ffffff"><?php /*echo $lstprocess;*/?></span>
									<h5>Tepat Waktu</h5>
								</div>
								<div class="ibox-content">
									<h1 class="no-margins"><?php /*echo $jmlTepatWaktu>0? number_format($jmlTepatWaktu,0,",",".")." Pegawai": "-";*/?> </h1>
								</div>
							</div>
						</div>
						<div class="col-lg-4">
							<div class="ibox float-e-margins">
								<div class="ibox-title">
									<span class="label pull-right" style="background-color: #567fac;color: #ffffff"><?php /*echo $lstprocess;*/?></span>
									<h5>Terlambat</h5>
								</div>
								<div class="ibox-content">
									<h1 class="no-margins"><?php /*echo $jmlTerlambat ? number_format($jmlTerlambat,0,",",".")." Pegawai": "-";*/?></h1>
								</div>
							</div>
						</div>
						<div class="col-lg-4">
					<div class="ibox float-e-margins">
						<div class="ibox-title">
							<span class="label pull-right"  style="background-color: #ec8572;color: #ffffff"><?php /*echo $lstprocess;*/?></span>
							<h5>Ijin</h5>
						</div>
						<div class="ibox-content">
							<h1 class="no-margins"><?php /*echo $jmlIjin? number_format($jmlIjin,0,",",".")." Pegawai": "-";*/?></h1>
						</div>
					</div>
				</div>
						<div class="col-lg-4">
							<div class="ibox float-e-margins">
								<div class="ibox-title">
									<span class="label pull-right"  style="background-color: #B29180;color: #ffffff"><?php /*echo $lstprocess;*/?></span>
									<h5>Alpha</h5>
								</div>
								<div class="ibox-content">
									<h1 class="no-margins"><?php /*echo $jmlAlpha? number_format($jmlAlpha,0,",",".")." Pegawai": "-";*/?></h1>
								</div>
							</div>
						</div>
						<div class="col-lg-4">
							<div class="ibox float-e-margins">
								<div class="ibox-title">
									<span class="label pull-right"  style="background-color: #FAD52D;color: #000"><?php /*echo $lstprocess;*/?></span>
									<h5>Sakit</h5>
								</div>
								<div class="ibox-content">
									<h1 class="no-margins"><?php /*echo $jmlSakit? number_format($jmlSakit,0,",",".")." Pegawai": "-";*/?></h1>
								</div>
							</div>
						</div>
						<div class="col-lg-4">
							<div class="ibox float-e-margins">
								<div class="ibox-title">
									<span class="label pull-right"  style="background-color: #8eac4c;color: #ffffff"><?php /*echo $lstprocess;*/?></span>
									<h5>Cuti</h5>
								</div>
								<div class="ibox-content">
									<h1 class="no-margins"><?php /*echo $jmlCuti? number_format($jmlCuti,0,",",".")." Pegawai": "-";*/?></h1>
								</div>
							</div>
						</div>
					</div>-->
					<div class="row">
						<div class="col-lg-6">
							<div class="ibox float-e-margins">
								<div class="ibox-content">
									<div>
										<canvas id="barChart1" height="140"></canvas>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="ibox float-e-margins">
								<div class="ibox-content">
									<div>
										<canvas id="barChart2" height="140"></canvas>
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

<script>
	window.paceOptions = {
		startOnPageLoad: false,
        ajax: false,
        restartOnRequestAfter: false
	};

	$(function () {

		var barData1 = {
			labels: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul","Agt","Sep","Okt","Nov","Des"],
			datasets: [
				{
					label: "Tepat Waktu",
					backgroundColor: '#1AB394',
					pointBorderColor: "#fff",
					data: [<?php echo $lstTepatWaktu?>]
				},
				{
					label: "Terlambat",
					backgroundColor: '#567fac',
					pointBorderColor: "#fff",
					data: [<?php echo $lstTerlambat?>]
				},
				{
					label: "Ijin",
					backgroundColor: '#ec8572',
					pointBorderColor: "#fff",
					data: [<?php echo $lstIjin?>]
				}
			]
		};

		var barData2 = {
			labels: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul","Agt","Sep","Okt","Nov","Des"],
			datasets: [
				{
					label: "Alpha",
					backgroundColor: '#B29180',
					pointBorderColor: "#fff",
					data: [<?php echo $lstlAlpha?>]
				},
				{
					label: "Sakit",
					backgroundColor: '#FAD52D',
					pointBorderColor: "#fff",
					data: [<?php echo $lstSakit?>]
				},
				{
					label: "Cuti",
					backgroundColor: '#8eac4c',
					pointBorderColor: "#fff",
					data: [<?php echo $lstCuti?>]
				}
			]
		};

		var barOptions = {
			responsive: true,
			title: {
				display: true,
				text: 'Periode Tahun <?php echo $thn?>'
			},
			scales: {
				yAxes: [{
					ticks: {
						beginAtZero: true
					}
				}]
			}
		};


		var ctx1 = document.getElementById("barChart1").getContext("2d");
		new Chart(ctx1, {type: 'bar', data: barData1, options:barOptions});

		var ctx2 = document.getElementById("barChart2").getContext("2d");
		new Chart(ctx2, {type: 'bar', data: barData2, options:barOptions});

	});
</script>

