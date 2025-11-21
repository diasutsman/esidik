<?php
/**
 * File: login.php
 * Author: abdiIwan.
 * Date: 12/23/2016
 * Time: 4:57 PM
 * absensi.kemendagri.go.id
 */
?>
<html>
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Aplikasi <?php echo $this->config->item('app_full_name')?></title>

    <?php include "header.php" ?>
    <script type="text/javascript">
        $(document).ready(function() {
            $("#refresh").click(function() {
                jQuery.ajax({
                    type: "POST",
                    url: "<?php echo site_url(); ?>" + "ajax/kalangkabut",
                    success: function(res) {
                        if (res)
                        {
                            $("#renew").html(res);
                        }
                    }
                });
            });
        });
    </script>
</head>

<body class="blue-bg">

<div class="passwordBox animated fadeInDown">
    <div class="row">

        <div class="col-md-12">
            <div class="ibox-content " style="color:#000">
                <h2 class="font-bold">Lupa kata kunci</h2>
                <p>
                    Masukan alamat surat elektronik anda dan kata kunci akan direset dan dikirim ke surat elektronik anda.
                </p>

                <div class="row">

                    <div class="col-lg-12">
                        <form class="m-t" role="form" action="">
                            <div class="input-group m-b">
                            <span class="input-group-btn">
                                 <button type="button" class="btn btn-danger" disabled id="renew"><?php echo $image ?></button>
                            </span> <input class="form-control" type="text"><span class="input-group-addon"><a href="javascript:void()" id="refresh"><i class="fa fa-repeat"></i></a></span>
                            </div>
                            <div class="form-group">
                                <input type="email" class="form-control" placeholder="Email address" required="">
                            </div>
                            <button type="submit" class="btn btn-success block full-width m-b">Kirim kata kunci baru</button>
                            <a href="login">
                                <small>Masuk ke system?</small>
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <p class="m-t text-center"> <small>Biro Kepegawaian Kemendagri &copy; 2016</small> </p>
</div>

</body>

</html>