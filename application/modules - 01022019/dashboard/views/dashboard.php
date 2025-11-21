<script src="<?php echo base_url() ?>assets/js/plugins/newsTicker.js"></script>
<script src="<?php echo base_url() ?>assets/amcharts/amcharts.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/amcharts/serial.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/amcharts/pie.js" type="text/javascript"></script>
<!-- Styles -->
<style>
    #chartdiv {
        min-width: 310px;
        height: 402px;
        max-width: 600px;
        margin: 0 auto
    }
</style>

<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div style="10px 10px 10px 10px;" class="ibox-content">
                <div class="row form-horizontal">
                    <form action="<?php echo site_url('dashboard/index_list'); ?>" method="post" name="bb" id="bb"
                          class="form-horizontal form-label-left">
                        <div class="form-group">
                            <label class="control-label col-md-1 col-sm-3 col-xs-12" for="jnspal">Unit Kerja :</label>
                            <div class="col-sm-5" id="data_5">
                                <div class="unker input-group">
                                    <input type="text" class="input-sm form-control" readonly name="cari_unker"
                                           id="cari_unker" value="<?php echo $nmunitkerja?>" placeholder="Unit Kerja ...">
                                    <input type="hidden" name="unit_search" id="unit_search" value="<?php echo $idunitkerja?>">
                                    <div class="input-group-btn">
                                        <button class="btn btn-white btn-sm" type="button"><span class="caret"></span>
                                        </button>
                                    </div>
                                </div>
                                <div class="panel combo-p" style="position: absolute;  z-index:50001; display: none;">
                                    <div class="combo-panel panel-body panel-body-noheader" title=""
                                         style="max-height:250px; padding:5px;overflow-y:auto">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3" id="data_5">
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
                                                <span class="label pull-right"
                                                      style="background-color: #1AB394;color: #ffffff"><?php echo $lstprocess; ?></span>
                                                <h5>Tepat Waktu</h5>
                                            </div>
                                            <div class="ibox-content">
                                                <h1 class="no-margins"><?php echo $jmlTepatWaktu > 0 ? number_format($jmlTepatWaktu, 0, ",", ".") . " Pegawai" : "-"; ?> </h1>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="ibox float-e-margins">
                                            <div class="ibox-title">
                                                <span class="label pull-right"
                                                      style="background-color: #567fac;color: #ffffff"><?php echo $lstprocess; ?></span>
                                                <h5>Terlambat</h5>
                                            </div>
                                            <div class="ibox-content">
                                                <h1 class="no-margins"><?php echo $jmlTerlambat > 0 ? number_format($jmlTerlambat, 0, ",", ".") . " Pegawai" : "-"; ?></h1>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="ibox float-e-margins">
                                            <div class="ibox-title">
                                                <span class="label pull-right"
                                                      style="background-color: #8eac4c;color: #ffffff"><?php echo $lstprocess; ?></span>
                                                <h5>Cuti</h5>
                                            </div>
                                            <div class="ibox-content">
                                                <h1 class="no-margins"><?php echo ($jmlCuti + $jmlSakit) > 0 ? number_format(($jmlCuti + $jmlSakit), 0, ",", ".") . " Pegawai" : "-"; ?></h1>
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
                                                <span class="label pull-right"
                                                      style="background-color: #4c4cac;color: #e3ff3a"><?php echo $lstprocess; ?></span>
                                                <h5>Tugas Belajar</h5>
                                            </div>
                                            <div class="ibox-content">
                                                <h1 class="no-margins"><?php echo $jmlTb > 0 ? number_format($jmlTb, 0, ",", ".") . " Pegawai" : "-"; ?></h1>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="ibox float-e-margins">
                                            <div class="ibox-title">
                                                <span class="label pull-right"
                                                      style="background-color: #3eff72;color: #1c13b2"><?php echo $lstprocess; ?></span>
                                                <h5>Dinas Luar</h5>
                                            </div>
                                            <div class="ibox-content">
                                                <h1 class="no-margins"><?php echo $jmlDinas > 0 ? number_format($jmlDinas, 0, ",", ".") . " Pegawai" : "-"; ?></h1>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="ibox float-e-margins">
                                            <div class="ibox-title">
                                                <span class="label pull-right"
                                                      style="background-color: #484b90;color: #e0e3ee"><?php echo $lstprocess; ?></span>
                                                <h5>Shift Khusus</h5>
                                            </div>
                                            <div class="ibox-content">
                                                <h1 class="no-margins"><?php echo $jmlLain > 0 ? number_format($jmlLain, 0, ",", ".") . " Pegawai" : "-"; ?></h1>
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
                                                <span class="label pull-right"
                                                      style="background-color: #8eac4c;color: #ffffff"><?php echo $lstprocess; ?></span>
                                                <h5>Statistik Jumlah Kehadiran Pegawai </h5>
                                            </div>
                                            <div class="ibox-content">
                                                <script>
                                                    var chart = AmCharts.makeChart("chartdiv", {
                                                        "type": "pie",
                                                        "theme": "light",
                                                        "dataProvider": [{
                                                            "country": "Tepat Waktu",
                                                            "litres": <?php echo $jmlTepatWaktu;?>
                                                        }, {
                                                            "country": "Terlambat",
                                                            "litres": <?php echo $jmlTerlambat;?>
                                                        }, {
                                                            "country": "Cuti",
                                                            "litres": <?php echo $jmlCuti + $jmlSakit;?>
                                                        }, {
                                                            "country": "Tugas Belajar",
                                                            "litres": <?php echo $jmlTb;?>
                                                        }, {
                                                            "country": "Dinas Luar",
                                                            "litres": <?php echo $jmlDinas;?>
                                                        }, {
                                                            "country": "Shift Khusus",
                                                            "litres": <?php echo $jmlLain;?>
                                                        }],
                                                        "titleField": "country",
                                                        "valueField": "litres",

                                                        "balloon": {
                                                            "fixedPosition": true
                                                        },
                                                        "export": {
                                                            "enabled": true
                                                        }
                                                    });
                                                </script>

                                                <!-- HTML -->
                                                <div id="chartdiv"></div>

                                            </div>


                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="ibox float-e-margins">
                                            <div class="ibox-title">
                                                <span class="label label-info pull-right"><?php echo indo_date(date('y-m-d')); ?></span>
                                                <h5>Presensi</h5>
                                            </div>
                                            <div class="ibox-content">
                                                <div id="new-message-notif"></div>
                                                <div id="multilines">
                                                    <ul class="list-group" id="data-presensi">
                                                        <?php

                                                        foreach ($lsthariini as $row) {
                                                            ?>
                                                            <li class="list-group-item">
                                                                <p>
                                                                    <span class="text-info"><?php echo $row["name"] ?></span>
                                                                    |
                                                                    <small><span
                                                                                class="text-left"><?php echo $row["deptname"] ?></span>
                                                                    </small>
                                                                </p>
                                                                <small class="block text-muted"><i
                                                                            class="fa fa-clock-o"></i> <?php echo ymdToIna($row["checktime"]) ?>
                                                                </small>
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

<script>

    var arrPres = [];

    var multilines = $('#data-presensi').newsTicker({
        row_height: 127,
        speed: 800,
        duration: 30000,
        pauseOnHover: 0
    });

    $('#data-presensi li').each(function (i, li) {
        arrPres.push($(li).html());
    });

    setInterval(function () {
        $.ajax({
            method: "get",
            url: '<?php echo site_url('dashboard/listabsen')?>',
            success: function (response) {
                $('#multilines').html(response);
            },
            dataType: "html"
        });
    }, 30000);

    $('.unker').click(function(){
        var inwidth = $(this).width();
        var dis = $('.panel').css("display");
        if(dis=='none'){
            $('.panel').css({
                display : 'block',
                width : inwidth
            });

            if ( $('.combo-panel > *').length == 0 ) {
                $('.combo-panel').html('Loading...........');
                $.ajax({
                    url: '<?php echo site_url('dashboard/getUnitKerja')?>', dataType: 'html', type: 'POST', success: function (data) {
                        $('.combo-panel').html(data);
                    }
                });
            }
        }else{
            $('.panel').css({
                display : 'none'
            });
        }
    });
</script>


