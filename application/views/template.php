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

	<body class="top-navigation">
    <audio id="notif_message"><source src="<?php echo base_url('assets/sounds/all-eyes-on-me.ogg');?>" type="audio/ogg"><source src="<?php echo base_url('assets/sounds/all-eyes-on-me.mp3');?>" type="audio/mpeg"></audio>
	<div id="wrapper">
		<div id="page-wrapper" class="green-bg">
            <div class="row header-back-1">
                <div class="row header-back"></div>
            </div>
			<div class="row border-bottom white-bg">
				<nav class="navbar navbar-static-top" role="navigation">
					<div class="navbar-header">
						<button aria-controls="navbar" aria-expanded="false" data-target="#navbar" data-toggle="collapse" class="navbar-toggle collapsed" type="button">
							<i class="fa fa-reorder"></i>
						</button>
						<a href="<?php echo site_url("home")?>" class="navbar-brand"><?php echo $this->config->item('app_name')?></a>
					</div>
					<div class="navbar-collapse collapse " id="navbar">
						<ul class="nav navbar-nav">
							<?php echo make_menu()?>
						</ul>
						<ul class="nav navbar-top-links navbar-right">
							<li class="dropdown">
								<a class="dropdown-toggle count-info" data-toggle="dropdown" href="#" aria-expanded="false">
									<i class="fa fa-envelope"></i>
									<?php if ( jmlPesanBlmBaca()>0 ) {?>
									<span class="label label-danger bounceIn animated <?php echo jmlPesanBlmBaca()>0?"infinite":""; ?>"><?php echo jmlPesanBlmBaca() ?></span>
									<?php } ?>
								</a>
								<ul class="dropdown-menu dropdown-messages">
									<?php
									$rslt = PesanBlmBaca();
									foreach($rslt->result() as $row)
									{
									?>
									<li>
										<div class="dropdown-messages-box">
											<div class="media-body">
												<strong><?php echo $row->judul?></strong><br>
												<small class="text-muted"><?php echo format_date_ind(date("Y-m-d",strtotime($row->tgl_pesan)))." ".date("H:i:s",strtotime($row->tgl_pesan))?></small>
											</div>
										</div>
									</li>
									<?php
									}
									?>
									<li class="divider"></li>
									<li>
										<div class="text-center link-block">
											<a href="<?php echo base_url("pesan")?>">
												<i class="fa fa-envelope"></i> <strong>Semua Pesan</strong>
											</a>
										</div>
									</li>
								</ul>
							</li>
							<li class="dropdown">
								<a aria-expanded="false" role="button" href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $this->session->userdata('s_username')?> <b class="caret"></b></a>
								<ul role="menu" class="dropdown-menu">
									<li><a href="<?php echo site_url("profile")?>"><i class="fa fa-user"></i> Profile</a></li>
									<li><a href="<?php echo site_url("logout")?>"><i class="fa fa-sign-out"></i> Log out</a></li>
								</ul>
							</li>
						</ul>
					</div>
				</nav>
			</div>
			<div class="row wrapper border-bottom white-bg" style="padding: 0 5px 5px 5px;">
				<div class="col-md-9">
					<ol class="breadcrumb">
						<li>
							<a href="<?php echo site_url("home")?>">Home</a>
						</li>
						<?php echo makeBreadcrumb()?>
					</ol>
				</div>
				<div class="col-md-3 ">
                    <span class="pull-right"><?php echo indo_date(date("Y-m-d"))?> <span id="txtjam" class="txtjam"><?php echo date("H:i")?></span> </span>
				</div>
			<div class="wrapper wrapper-content" style="padding: 5px 5px 10px 5px;">
					<?php
					ini_set('max_execution_time', 3600000);
					ini_set('memory_limit', '-1');
					echo $contents
					?>
			</div>
			<div class="row wrapper footer">
				<div class="pull-right">
					<?php echo $this->config->item('app_full_name')?>
				</div>
				<div>
					<strong>Copyright</strong> &copy; <?php echo $this->config->item('app_years')?>
				</div>
			</div>
		</div>
	</div>
	<div id="loads" class="modal-backdrop" style="display:none;opacity:0.8 !important;background-color: rgba(47,37,255,0.16);z-index: 55001">
		<div style="padding-top:15%;"><center><img src="<?=base_url()?>assets/img/loading.gif" width="200px"></center></div>
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
