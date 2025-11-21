<div class="row">
    <div class="ibox float-e-margins">
        <div class="iibox-content  gray-bg">
<div class="row animated fadeInRight">
    <div class="col-md-4">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Profile Detail</h5>
            </div>
            <?php
            $fil = base_url("photo/" . $myProfile['userid'] . ".jpg");
            $urlAda = "";
            $type = getContentType("http://ropeg.setjen.kemendagri.go.id/foto/" . $myProfile['userid'] . "/" . $myProfile['userid'] . ".JPG");
            if (strpos($type, 'image') !== false) {
                $urlAda = "http://ropeg.setjen.kemendagri.go.id/foto/" . $myProfile['userid'] . "/" . $myProfile['userid'] . ".JPG";
            } else {
                $type = getContentType("http://ropeg.setjen.kemendagri.go.id/foto/" . $myProfile['userid'] . "/" . $myProfile['userid'] . ".jpg");
                if (strpos($type, 'image') !== false) {
                    $urlAda = "http://ropeg.setjen.kemendagri.go.id/foto/" . $myProfile['userid'] . "/" . $myProfile['userid'] . ".jpg";
                } else {
                    $type = getContentType("http://ropeg.setjen.kemendagri.go.id/foto/" . $myProfile['userid'] . "/" . $myProfile['userid'] . ".png");
                    if (strpos($type, 'image') !== false) {
                        $urlAda = "http://ropeg.setjen.kemendagri.go.id/foto/" . $myProfile['userid'] . "/" . $myProfile['userid'] . ".png";
                    }
                }
            }
            if ($urlAda != "") {
                $fil = $urlAda;
            } else {
                if (!file_exists($fil)) {

                    $gen = ($myProfile['gender'] == 1 ? "male" : "female") . ".png";
                    $fil = base_url("assets/img/" . $gen);
                }

            }
            ?>

            <div>
                <div class="ibox-content no-padding center-orientation">
                    <center>
                        <img alt="image" class="img-responsive" style="padding-left: 15px;width:100px" src="<?php echo $fil; ?>">
                    </center>
                </div>
                <div class="ibox-content profile-content  text-center">
                    <?php if ($isValid) { ?>
                        <h4><strong><?php echo $myProfile['name'] ?></strong></h4>
                        <p><?php echo $myProfile['title'] ?></p>
                        <p><?php echo $myProfile['deptname'] ?></p>
                        <p><?php $myProfile['badgenumber'] != null ? "NIP. " : "" ?><?php echo $myProfile['badgenumber'] ?></p>
                    <?php } else { ?>
                        <h4><strong><?php echo $myProfile['username'] ?></strong></h4>
                        <p><?php echo $myProfile['user_level_name'] ?></p>
                        <p><?php echo $myProfile['deptname'] ?></p>
                    <?php } ?>
                </div>
            </div>
        </div>

    </div>
    <div class="col-md-8">
        <div class="ibox float-e-margins">
            <div class="tabs-container">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#tab-1" aria-expanded="true">Aktivitas</a></li>
                    <li class=""><a data-toggle="tab" href="#tab-2" aria-expanded="false">Penggantian Kata Kunci</a></li>
                </ul>
                <div class="tab-content">
                    <div id="tab-1" class="tab-pane active">
                        <div class="panel-body">
                            <div class="feed-activity-list">
                                <div class="feed-element">

                                    <?php
                                    foreach ($myData->result() as $row) {
                                        $waktu = explode(" ", $row->logtime);
                                        $tgl = $waktu[0];
                                        $jam = $waktu[1];

                                        ?>
                                        <div class="media-body ">
                                            <small class="pull-right text-navy"><?php echo $jam; ?></small>
                                            <strong><?php echo $row->logdetail; ?></strong><br>
                                            <small class="text-muted"><?php echo indo_date($tgl); ?></small>
                                        </div>
                                    <?php } ?>

                                </div>

                            </div>
                        </div>
                    </div>
                    <div id="tab-2" class="tab-pane">
                        <div class="panel-body">
                            <form class="form-horizontal form-label-left" name="snForm" id="snForm" method="post">
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-3 col-xs-12" for="txt1">Katakunci Baru</label>
                                    <div class="col-md-5 col-sm-5 col-xs-12">
                                        <input name="txtpassnew" id="txtpassnew" type="text" placeholder="Password" class="form-control col-md-5 col-xs-12">
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
                                <div class="form-group">
                                    <div class="col-md-4 col-md-offset-4">
                                        <button type="button" class="btn btn-sm btn-success" data-dismiss="modal" onClick="saveSNForm();">Simpan</button>
                                    </div>
                                </div>
                            </form>
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
    function saveSNForm() {
        if (($("#txtpassnew").val() != "")) {

            $.ajax({
                url: '<?php echo site_url('profile/rubihpwd');?>', dataType: 'json', type: 'POST', data: $("#snForm").serialize(), success: function (data) {
                    bootbox.alert(data.msg);
                }
            });
        } else {
            bootbox.alert("Harap cek kembali inputannya ??");
        }
    }

</script>
