<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>Aplikasi <?php echo $this->config->item('app_full_name')?></title>

		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
		<meta name="apple-mobile-web-app-capable" content="yes" />    
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="icon" type="image/png" href="<?php echo base_url(); ?>assets/addon/logo.png">

		<link href="<?php echo base_url()?>assets/css/bootstrap.min.css" rel="stylesheet">
		<link href="<?php echo base_url()?>assets/font-awesome/css/font-awesome.css" rel="stylesheet">
		<link href="<?php echo base_url()?>assets/css/plugins/toastr/toastr.min.css" rel="stylesheet">
		<link href="<?php echo base_url()?>assets/css/plugins/dataTables/datatables.min.css" rel="stylesheet">
		<link href="<?php echo base_url()?>assets/css/animate.css" rel="stylesheet">
		<link href="<?php echo base_url()?>assets/css/style.css" rel="stylesheet">
        <link href="<?php echo base_url()?>assets/css/plugins/jquery.smartmenus.bootstrap.css" rel="stylesheet">

		<script src="<?php echo base_url()?>assets/js/jquery-2.1.1.js"></script>
        <script src="<?php echo base_url() ?>assets/js/plugins/jquery-ui/jquery-ui.min.js"></script>
		<script src="<?php echo base_url()?>assets/js/bootstrap.min.js"></script>
		<script src="<?php echo base_url()?>assets/js/bootbox.js"></script>
        <script src="<?php echo base_url()?>assets/js/plugins/jquery.smartmenus.min.js"></script>
        <script src="<?php echo base_url()?>assets/js/plugins/jquery.smartmenus.bootstrap.min.js"></script>
		<script src="<?php echo base_url()?>assets/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
		<script src="<?php echo base_url()?>assets/js/Application.js"></script>
		<script src="<?php echo base_url()?>assets/js/plugins/validate/jquery.validate.min.js"></script>
		<script src="<?php echo base_url()?>assets/js/plugins/validate/messages_id.js"></script>
	</head>
	<style>
	body {
    height: 100%;
    margin: 0;
    padding: 0;
    background-color: #f3f3f4;
}
.footer {
  position: absolute;
  right: 0;
  bottom: 0;
  left: 0;
  padding: 1rem;
  background-color: #efefef;
  text-align: center;
}
	</style>
	<body >
    <audio id="notif_message"><source src="<?php echo base_url('assets/sounds/all-eyes-on-me.ogg');?>" type="audio/ogg"><source src="<?php echo base_url('assets/sounds/all-eyes-on-me.mp3');?>" type="audio/mpeg"></audio>
	
	<div class="main-container" id="main-container">
		<div class="row header-back-1">
		
			<div style=" background-color: #2f4050;"class="row header-back"></div>
			
		</div>
		
		<div class="row wrapper border-bottom white-bg" style="padding: 0 5px 5px 5px;">
			<div class="col-md-9">
				<ol class="breadcrumb">
					<li>
						<h5><a href="">DASHBOARD</a></h5>
					</li>
					<?php echo makeBreadcrumb()?>
				</ol>
			</div>
			<div class="col-md-3 ">
				<span class="pull-right"><?php echo indo_date(date("Y-m-d"))?> <span id="txtjam" class="txtjam"><?php echo date("H:i")?></span> </span>
			</div>
		
		
		</div>
		<div class="main-content">
			<div class="main-content-inner">
				<div class="page-content">
					<div class="row">
						<div class="col-xs-12">
							<?php
						ini_set('max_execution_time', 3600000);
						ini_set('memory_limit', '-1');
						echo $contents
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		
	</div>

	<script src="<?php echo base_url()?>assets/js/plugins/pace/pace.min.js"></script>
    <script src="<?php echo base_url()?>assets/js/plugins/session-timeout.min.js"></script>
	<script>
        <?php
        $bypass = strtolower($this->uri->segment(1));
        $arrproc=array("procdata","procuangmakan","procpost","pegawai");
        $allowproc=in_array($bypass,$arrproc);
        if (!$allowproc) {
        ?>
        /*$.sessionTimeout({
            title: "Sesi Kadaluarsa",
            message: "Sesi anda sudah kadaluarsa",
            keepAliveUrl: '<?php echo base_url(uri_string());?>',
            logoutUrl: '<?php echo site_url("logout")?>',
            redirUrl: '<?php echo site_url("logout")?>',
            warnAfter: <?php echo(getSesi() - 15000)?>,
            redirAfter: <?php echo getSesi()?>,
            countdownBar: true,
            keepAlive: false
        });
		*/
        <?php
        }
        ?>

		$(document).ready(function() {
             $('.popup-container').draggable({
             handle: ".modal-header"
             });
            $('.modal-dialog').draggable({
                handle: ".modal-header"
            });


			$('.navbar a.dropdown-toggle').on('click', function(e) {
				var $el = $(this);
				var $parent = $(this).offsetParent(".dropdown-menu");
				$(this).parent("li").toggleClass('open');

				if(!$parent.parent().hasClass('nav')) {
					$el.next().css({"top": $el[0].offsetTop, "left": $parent.outerWidth() - 4});
				}

				$('.nav li.open').not($(this).parents("li")).removeClass("open");

				return false;
			});
            <?php if ($this->uri->segment(1)!="home" ) {?>
			jQuery(document).ajaxStart(function () {
				loading();
			}).ajaxStop(function () {
				unloading();
			}).ajaxError(function (event, jqxhr, settings, thrownError) {
				unloading();
				//bootbox.alert("Maaf, ada kesalahan dalam proses data..<br>"+thrownError);
			});
			<?php } ?>
		});
		var serverTime = <?php echo time() * 1000; ?>; //this would come from the server
		var localTime = +Date.now();
		var timeDiff = serverTime - localTime;

		function clock() {
			var realtime = +Date.now() + timeDiff;
			var time = new Date(realtime),
				hours = time.getHours(),
				minutes = time.getMinutes(),
				seconds = time.getSeconds();
			    document.querySelectorAll('.txtjam')[0].innerHTML = harold(hours) + ":" +
                    harold(minutes); //+ ":" +
                    //harold(seconds);

			function harold(standIn) {
				if (standIn < 10) {
					standIn = '0' + standIn
				}
				return standIn;
			}
		}
		setInterval(clock, 1000);
	</script>
	</body>
</html>
