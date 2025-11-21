<head>

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>Aplikasi <?php echo $this->config->item('app_full_name')?></title>

	<link href="<?php echo base_url()?>assets/css/bootstrap.min.css" rel="stylesheet">
	<link href="<?php echo base_url()?>assets/font-awesome/css/font-awesome.css" rel="stylesheet">

	<link href="<?php echo base_url()?>assets/css/animate.css" rel="stylesheet">
	<link href="<?php echo base_url()?>assets/css/style.css" rel="stylesheet">

</head>

<body class="gray-bg">

<div class="middle-box text-center lockscreen animated fadeInDown">
	<div>
		<div class="m-b-md">
			<img alt="image" class="img-shadow" width="128px" src="<?php echo base_url("assets/addon/logo.png")?>">
		</div>
		<h3><?php echo $this->session->userdata('s_username')?></h3>
		<p>Aplikasi terkunci.Silakan masukkan kata kunci</p>
		<form class="m-t" role="form" action="<?php echo site_url("home/tryunlock")?>">
			<div class="form-group">
				<input type="password" class="form-control" placeholder="******" required="">
			</div>
			<button type="submit" class="btn btn-primary block full-width">Unlock</button>
		</form>
	</div>
</div>

<script src="<?php echo base_url()?>assets/js/jquery-2.1.1.js"></script>
<script src="<?php echo base_url()?>assets/js/bootstrap.min.js"></script>

</body>