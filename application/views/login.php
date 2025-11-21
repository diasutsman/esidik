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

<body class="blue-bg" style="background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAYAAACNMs+9AAAAW0lEQVQoU2NkYGAwZmBgOMuAACA+CKCIMSIpADGRNaEYgKwQ3WQUjTCFcEUC3c8bQCo+lEqCaZgtIIUYimDOQVZMkkIUt+CzGmYTUZ7Bpxgsh+JGkAAWq8FOAwCzzCTXoP82bgAAAABJRU5ErkJggg==')">

    <div class="passwordBox text-center animated fadeInDown">
                <img src="<?php echo base_url()?>assets/addon/bannerdpn.png" alt="" width="100%">
                <div class="ibox-content" style="color:#000">

                    <?php

                    if ($this->session->userdata('error_msg')) {
                        echo '<h4 class="alert alert-danger">' . $this->session->userdata('error_msg') . '</h4>';
                        $this->session->unset_userdata('error_msg');
                    }
                    ?>
                    <form class="m-t" role="form" action="login" method="post">
                        <div class="input-group m-b"><span class="input-group-addon"><i class="fa fa-user"></i></span> <input placeholder="Username" class="form-control" type="text" name="username" required=""></div>
                        <div class="input-group m-b"><span class="input-group-addon"><i class="fa fa-key"></i></span> <input placeholder="Password" class="form-control" type="password" name="password" required=""></div>
                        <div class="input-group m-b">
                            <span class="input-group-btn">
                                 <button type="button" class="btn btn-danger" disabled id="renew"><?php echo strtoupper($image) ?></button>
                            </span> <input class="form-control" type="text" required="" name="capture" autocomplete="off" value="<?php echo $acak?>"><span class="input-group-addon"><a href="javascript:void()" id="refresh"><i class="fa fa-repeat"></i></a></span>
                        </div>

                        <button type="submit" class="btn btn-success block full-width m-b">Login</button>
                        <!--<a href="forgot">
                            <small>Lupa kata sandi?</small>
                        </a>-->
                    </form>
                </div>
        <p class="m-t"> <small>Biro Kepegawaian Kemendagri &copy; 2016</small> </p>
    </div>
</body>

</html>
