<link href="<?php echo base_url()?>assets/mapsvg/mapsvg.css" rel="stylesheet">
<script src="<?php echo base_url()?>assets/mapsvg/jquery.mousewheel.min.js"></script>
<script src="<?php echo base_url()?>assets/mapsvg/mapsvg.js"></script>

<style>
    .legendapeta{
        position: relative;
        left: 15px;
    }
    </style>
<div class="row">
	<div class="ibox float-e-margins">
		<div class="ibox-content gray-bg">
			<div class="row">
				<div class="col-lg-12">
                    <div id="mapsvg"></div>
                    <script type="text/javascript">
                        jQuery(document).ready(function() {
                            jQuery("#mapsvg").mapSvg({
                                markerLastID: 13,
                                width: 2156,
                                height: 800,
                                regions: {
                                    'IDN1136': {
                                        id: "IDN1136",
                                        fill: "#2eb886",
                                        tooltip: "DI ACEH"
                                    },
                                    'IDN381': {
                                        id: "IDN381",
                                        fill: "#1ca7b0",
                                        tooltip: "Sumatera Utara"
                                    },
                                    'IDN492': {
                                        id: "IDN492",
                                        fill: "#2cb594",
                                        tooltip: "Riau"
                                    },
                                    'IDN539': {
                                        id: "IDN539",
                                        fill: "#2e3894",
                                        tooltip: "Sumatera Barat"
                                    },
                                    'IDN1930': {
                                        id: "IDN1930",
                                        fill: "#8d359c",
                                        tooltip: "Jambi"
                                    },
                                    'IDN1230': {
                                        id: "IDN1230",
                                        fill: "#4db31d",
                                        tooltip: "Sumatera Selatan"
                                    },
                                    'IDN1229': {
                                        id: "IDN1229",
                                        fill: "#b169de",
                                        tooltip: "Lampung"
                                    },
                                    'IDN1225': {
                                        id: "IDN1225",
                                        fill: "#3e99db",
                                        tooltip: "Bengkulu"
                                    },
                                    'IDN1228': {
                                        id: "IDN1228",
                                        fill: "#5f4bdb",
                                        tooltip: "Kalimantan Barat"
                                    },
                                    'IDN1185': {
                                        id: "IDN1185",
                                        fill: "#133094",
                                        tooltip: "Kalimantan Timu"
                                    },
                                    'IDN1931': {
                                        id: "IDN1931",
                                        fill: "#297d32",
                                        tooltip: "Kalimantan Tengah"
                                    },
                                    'IDN1234': {
                                        id: "IDN1234",
                                        fill: "#21705d",
                                        tooltip: "Kalimantan Selatan"
                                    },
                                    'IDN557': {
                                        id: "IDN557",
                                        fill: "#1a8c18",
                                        tooltip: "Sulawesi Tengah"
                                    },
                                    'IDN513': {
                                        id: "IDN513",
                                        fill: "#365dc2",
                                        tooltip: "Sulawesi Utara"
                                    },
                                    'IDN1837': {
                                        id: "IDN1837",
                                        fill: "#d132b8",
                                        tooltip: "Gorontalo"
                                    },
                                    'IDN556': {
                                        id: "IDN556",
                                        fill: "#3f74c4",
                                        tooltip: "Sulawesi Tenggara"
                                    },
                                    'IDN1236': {
                                        id: "IDN1236",
                                        fill: "#d9a33b",
                                        tooltip: "Sulawesi Selatan"
                                    },
                                    'IDN1237': {
                                        id: "IDN1237",
                                        fill: "#bdd625",
                                        tooltip: "Sulawesi Barat"
                                    },
                                    'IDN1226': {
                                        id: "IDN1226",
                                        fill: "#3caeb5",
                                        tooltip: "Banten"
                                    },
                                    'IDN1223': {
                                        id: "IDN1223",
                                        fill: "#293178",
                                        tooltip: "Jawa Barat"
                                    },
                                    'IDN1224': {
                                        id: "IDN1224",
                                        fill: "#264ac9",
                                        tooltip: "Jawa Tengah"
                                    },
                                    'IDN1233': {
                                        id: "IDN1233",
                                        fill: "#601ad4",
                                        tooltip: "Jawa Timur"
                                    },
                                    'IDN1232': {
                                        id: "IDN1232",
                                        fill: "#26bf5a",
                                        tooltip: "Bali"
                                    },
                                    'IDN555': {
                                        id: "IDN555",
                                        fill: "#a83146",
                                        tooltip: "Nusa Tenggara Barat"
                                    },
                                    'IDN1235': {
                                        id: "IDN1235",
                                        fill: "#341c94",
                                        tooltip: "Nusa Tenggara Timur"
                                    },
                                    'IDN558': {
                                        id: "IDN558",
                                        fill: "#1d5da8",
                                        tooltip: "Papua"
                                    },
                                    'IDN1933': {
                                        id: "IDN1933",
                                        fill: "#106985",
                                        tooltip: "Irian Jaya Barat"
                                    },
                                    'IDN538': {
                                        id: "IDN538",
                                        fill: "#2745b0",
                                        tooltip: "Maluku Utara"
                                    },
                                    'IDN554': {
                                        id: "IDN554",
                                        fill: "#c93bbb",
                                        tooltip: "Maluku"
                                    },
                                    'IDN1231': {
                                        id: "IDN1231",
                                        fill: "#f2ed64",
                                        tooltip: "Bangka-Belitung"
                                    },
                                    'IDN99': {
                                        id: "IDN99",
                                        fill: "#2d6cb5"
                                    },
                                    'IDN1227': {
                                        id: "IDN1227",
                                        fill: "#ba0f19",
                                        tooltip: "DKI Jakarta"
                                    },
                                    'IDN540': {
                                        id: "IDN540",
                                        fill: "#16a624",
                                        tooltip: "Yogyakarta"
                                    },
                                    'IDN1796': {
                                        id: "IDN1796",
                                        tooltip: "Kepulauan Riau"
                                    }
                                },
                                viewBox: [0, -0.028756957328397448, 1000, 371.0575139146568],
                                cursor: "pointer",
                                zoom: {
                                    on: true,
                                    limit: [0, 10],
                                    delta: 1.2,
                                    buttons: {
                                        on: true,
                                        location: "right"
                                    }
                                },
                                scroll: {
                                    on: true,
                                    limit: false,
                                    background: false
                                },
                                tooltips: {
                                    mode: "title",
                                    on: true,
                                    priority: "local"
                                },
                                gauge: {
                                    on: false,
                                    labels: {
                                        low: "low",
                                        high: "high"
                                    },
                                    colors: {
                                        lowRGB: {
                                            r: 85,
                                            g: 0,
                                            b: 0,
                                            a: 1
                                        },
                                        highRGB: {
                                            r: 238,
                                            g: 0,
                                            b: 0,
                                            a: 1
                                        },
                                        low: "#550000",
                                        high: "#ee0000",
                                        diffRGB: {
                                            r: 153,
                                            g: 0,
                                            b: 0,
                                            a: 0
                                        }
                                    },
                                    min: 0,
                                    max: false
                                },
                                source: "<?php echo base_url()?>assets/mapsvg/id.svg",
                                title: "Peta Indonesia",
                                markers: [{
                                    id: "marker_0",
                                    tooltip: "Kantor Pusat",
                                    popover: "<?php echo $pusat?>",
                                    src: "<?php echo base_url()?>assets/mapsvg/pin1_blue.png",
                                    width: 15,
                                    height: 24,
                                    x: 249.7937837050337,
                                    y: 240.88957131717407
                                }, {
                                    id: "marker_1",
                                    tooltip: "Regional Bandung",
                                    popover: "<?php echo $bandung?>",
                                    src: "<?php echo base_url()?>assets/mapsvg/pin1_red.png",
                                    width: 15,
                                    height: 24,
                                    x: 264.45240412427955,
                                    y: 257.38598190185894
                                }, {
                                    id: "marker_2",
                                    tooltip: "Regional Jatinangor",
                                    popover: "<?php echo $jatinangor?>",
                                    target: "blank",
                                    src: "<?php echo base_url()?>assets/mapsvg/pin1_red.png",
                                    width: 15,
                                    height: 24,
                                    x: 273.60374590200297,
                                    y: 259.4755702494172
                                }, {
                                    id: "marker_3",
                                    tooltip: "Regional Yogyakarta",
                                    popover: "<?php echo $Yogyakarta?>",
                                    src: "<?php echo base_url()?>assets/mapsvg/pin1_red.png",
                                    width: 15,
                                    height: 24,
                                    x: 327.25801470318174,
                                    y: 277.7436718375297
                                }, {
                                    id: "marker_4",
                                    tooltip: "Regional Malang",
                                    popover: "<?php echo $Malang?>",
                                    src: "<?php echo base_url()?>assets/mapsvg/pin1_red.png",
                                    width: 15,
                                    height: 24,
                                    x: 377.92011839174876,
                                    y: 279.3255342862585
                                }, {
                                    id: "marker_5",
                                    tooltip: "Regional Sumatera Barat",
                                    popover: "<?php echo $sumbar?>",
                                    src: "<?php echo base_url()?>assets/mapsvg/pin1_red.png",
                                    width: 15,
                                    height: 24,
                                    x: 114.43430867927347,
                                    y: 117.7795388028701
                                }, {
                                    id: "marker_6",
                                    tooltip: "Regional Riau",
                                    src: "<?php echo base_url()?>assets/mapsvg/pin1_red.png",
                                    popover: "<?php echo $riau?>",
                                    width: 15,
                                    height: 24,
                                    x: 136.28867815448885,
                                    y: 96.65364831016188
                                }, {
                                    id: "marker_7",
                                    tooltip: "Regional Lampung",
                                    popover: "<?php echo $riau?>",
                                    src: "<?php echo base_url()?>assets/mapsvg/pin1_red.png",
                                    width: 15,
                                    height: 24,
                                    x: 214.93107306702473,
                                    y: 210.27331161883183
                                }, {
                                    id: "marker_8",
                                    tooltip: "Regional Kalimantan Barat",
                                    popover: "<?php echo $kalbar?>",
                                    src: "<?php echo base_url()?>assets/mapsvg/pin1_red.png",
                                    width: 15,
                                    height: 24,
                                    x: 333.81884301219645,
                                    y: 128.97505717103053
                                }, {
                                    id: "marker_9",
                                    tooltip: "Regional Sulawesi Selatan",
                                    popover: "<?php echo $sulsel?>",
                                    src: "<?php echo base_url()?>assets/mapsvg/pin1_red.png",
                                    width: 15,
                                    height: 24,
                                    x: 532.1699116034463,
                                    y: 219.53816559667672
                                }, {
                                    id: "marker_10",
                                    tooltip: "Regional Sulawesi Utara",
                                    popover: "<?php echo $sulut?>",
                                    src: "<?php echo base_url()?>assets/mapsvg/pin1_red.png",
                                    width: 15,
                                    height: 24,
                                    x: 638.8192346424975,
                                    y: 82.29272529232404
                                }, {
                                    id: "marker_11",
                                    tooltip: "Regional Nusa Tenggara Barat",
                                    popover: "<?php echo $ntb?>",
                                    src: "<?php echo base_url()?>assets/mapsvg/pin1_red.png",
                                    width: 15,
                                    height: 24,
                                    x: 457.23592928275707,
                                    y: 293.33272753021146
                                }, {
                                    id: "marker_12",
                                    tooltip: "Regional Papua",
                                    popover: "<?php echo $papua?>",
                                    src: "<?php echo base_url()?>assets/mapsvg/pin1_red.png",
                                    width: 15,
                                    height: 24,
                                    x: 948.5411771287904,
                                    y: 193.71680496111668
                                }],
                                responsive: true
                            });

                        });
                    </script>
				</div>
                <!--<div class="legendapeta">
                    <span class="alert alert-info">
                        Mesin On line: <?php /*echo $jmlAllon;*/?>
                    </span>
                    <span class="alert alert-danger">
                        Mesin Off line: <?php /*echo $jmlAlloff;*/?>
                    </span>
                </div>-->
			</div>
		</div>
	</div>
</div>

<form id="inputForm" name="inputForm" action="javascript:;" method="post" class="form-horizontal form-validate">
    <div class="popup-wrapper fade" id="popup">
        <div class="popup-container">
            <div class="modal-header">
                <span type="button" class="close" data-dismiss="modal" onClick="hidePopup('#popup');">&times;</span>
                <h3 id="title">Daftar Mesin</h3>
            </div>
            <div class="modal-body" id="input_data">

            </div>
            <div class="modal-footer">
                <a class="btn btn-danger btn-sm" data-dismiss="modal" onClick="hidePopup('#popup');">Tutup</a>
            </div>
        </div>
    </div>
</form>

<script>
function bukaDaftar(idData)
{
    var url ="<?php echo site_url('rptmaps/showmesin')?>/"+idData;
    $("#input_data").html('<div style="position:absolute;left:0;right:0;top:50%;"></div>').load(url);
    showPopup('#popup');
}

</script>



