<link href="<?php echo base_url()?>assets/css/plugins/datepicker/datepicker3.css" rel="stylesheet">
<link href="<?php echo base_url()?>assets/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css" rel="stylesheet">
<script src="<?php echo base_url()?>assets/js/plugins/datapicker/bootstrap-datepicker.js"></script>
<script src="<?php echo base_url()?>assets/js/plugins/datapicker/bootstrap-datepicker.id.min.js"></script>
<style>
    .form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
        background-color: #821e16;
        color: #d8e0e0;
        opacity: 1;
    }
</style>
<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Author       : _abdi_iwan_
 * Project         :
 */
?>
<div class="row">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>Data Pegawai</h5>
            <div class="ibox-tools">
                <a class="collapse-link">
                    <i class="fa fa-chevron-up"></i>
                </a>
            </div>
        </div>
        <div class="ibox-content">
            <div class="ibox float-e-margins ">
                <div class="ibox-content gray-bg">
                    <form class="form-horizontal form-label-left" name="inputForm" id="inputForm" method="post">
                        <input type="hidden" name="iddata" id="iddata" value="<?php echo $id?>"/>

                        <div class="form-group">
                            <label class="control-label col-md-2 col-sm-2 col-xs-12" for="userid">ID</label>
                            <div class="col-md-3 col-sm-3 col-xs-12">
                                <input id="userid" name="userid" type="userid" placeholder="ID Pegawai" readonly value="<?php echo $field["userid"]?>" class="form-control col-md-12 col-xs-12">
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <label class="control-label col-md-1 col-sm-1 col-xs-12" for="badgenumber">NIP</label>
                                <div class="col-md-10 col-sm-10 col-xs-12">
                                    <input id="badgenumber" name="badgenumber"  readonly type="badgenumber" placeholder="NIP Pegawai" value="<?php echo $field["badgenumber"]?>" class="form-control col-md-12 col-xs-12">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Nama</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input id="name" name="name" type="text"  readonly placeholder="Nama Pegawai" value="<?php echo $field["name"]?>" class="form-control col-md-12 col-xs-12">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2 col-sm-2 col-xs-12" for="nickname">Nama Panggilan</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input id="nickname" name="nickname"  readonly type="text" placeholder="Nama Panggilan" value="<?php echo $field["nickname"]?>"  class="form-control col-md-12 col-xs-12">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2 col-sm-2 col-xs-12" for="cari_unker">Unit Kerja</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <div class="unker input-group">
                                    <input type="text" class="input-sm form-control" readonly name="cari_unker" id="cari_unker"  value="<?php echo $field["deptname"]?>"  placeholder="Unit Kerja ...">
                                    <input type="hidden" name="deptid" id="unit_search"  value="<?php echo $field["deptid"]?>">
                                    <div class="input-group-btn">
                                        <button class="btn btn-white btn-sm" type="button" disabled><span class="caret"></span></button>
                                    </div>
                                </div>
                                <div class="panel combo-p" style="position: absolute;  z-index:110003; display: none;">
                                    <div class="combo-panel panel-body panel-body-noheader" title="" style="max-height:250px; padding:5px;overflow-y:auto">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Jabatan</label>
                            <div class="col-md-6 col-sm-7 col-xs-12">
                                <input id="title" name="title" type="text"  readonly placeholder="Nama Jabatan" value="<?php echo $field["title"]?>" class="form-control col-md-12 col-xs-12">
                            </div>
                            <div class="col-md-2 col-sm-2 col-xs-12">
                                <div class="input-group">
                                    <input class="form-control"  readonly placeholder="TMT Jabatan" value="<?php echo ymdTodmy($field["tmtjabatan"])?>"  type="text">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2 col-sm-2 col-xs-12" for="gender">Jenis Kelamin</label>
                            <div class="col-md-3 col-sm-3 col-xs-12">
                                <?php echo form_dropdown("gender",$this->utils->getGender(),$field["gender"],"id='gender' class='form-control input-sm' disabled='disabled'");?>
                            </div>
                            <div class="col-md-offset-6">
                                <label class="control-label col-md-1 col-sm-1 col-xs-12" for="agama">Agama</label>
                                <div class="col-md-5 col-sm-5 col-xs-12">
                                    <?php echo form_dropdown("agama",$this->utils->getAgama(),$field["religion"],"id='agama' class='form-control input-sm'  disabled='disabled'");?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2 col-sm-2 col-xs-12" for="placebirthdate">TTL</label>
                            <div class="col-md-4 col-sm-4 col-xs-12">
                                <input id="placebirthdate" name="placebirthdate" type="text" placeholder="Tempat Lahir"  readonly value="<?php echo $field["placebirthdate"]?>" class="form-control col-md-12 col-xs-12">
                            </div>
                            <div class="col-md-2 col-sm-2 col-xs-12">
                                <div class="input-group">
                                    <input class="form-control" placeholder="Tanggal Lahir"  readonly value="<?php echo ymdTodmy($field["birthdate"])?>"  type="text">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2 col-sm-2 col-xs-12" for="jftstatus">Status Pegawai</label>
                            <div class="col-md-4 col-sm-4 col-xs-12">
                                <?php echo form_dropdown("stspeg",$this->utils->getStatusPegawai(),$field["jftstatus"],"id='stspeg' class='form-control input-sm'  disabled='disabled'");?>
                            </div>
                            <div class="col-md-2 col-sm-2 col-xs-12">
                                <div class="input-group">
                                    <div class="input-group date">
                                        <input class="form-control" placeholder="TMT" name="jftdate" value="<?php echo ymdTodmy($field["jftdate"])?>" type="text"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2 col-sm-2 col-xs-12" for="jenisjabatan">Jenis Jabatan</label>
                            <div class="col-md-4 col-sm-4 col-xs-12">
                                <?php echo form_dropdown("jenisjabatan",$this->utils->getJenisJabatan(),$field["jenisjabatan"],"id='jenisjabatan' class='form-control input-sm' disabled='disabled'");?>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-12">
                                <label class="control-label col-md-4 col-sm-4 col-xs-12" for="jenispegawai">Jenis Pegawai</label>
                                <div class="col-md-8 col-sm-8 col-xs-12">
                                    <?php echo form_dropdown("jenispegawai",$this->utils->getJenisPegawai(),$field["jenispegawai"],"id='jenispegawai' class='form-control input-sm' disabled='disabled'");?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-2 col-sm-2 col-xs-12" for="txt3">Kedudukan</label>
                            <div class="col-md-4 col-sm-4 col-xs-12">
                                <?php echo form_dropdown("kedudukan",$this->utils->getKedudukanPegawai(),$field["kedudukan"],"id='kedudukan' class='form-control input-sm' disabled='disabled'");?>
                            </div>
                            <div class="col-md-2 col-sm-2 col-xs-12">
                                <div class="input-group">
                                     <div class="input-group date">
                                        <input class="form-control" placeholder="TMT" name="tmtkedudukan" value="<?php echo ymdTodmy($field["tmtkedudukan"])?>" type="text"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2 col-sm-2 col-xs-12" for="golru">Gol/Ruang</label>
                            <div class="col-md-4 col-sm-4 col-xs-12">
                                <?php echo form_dropdown("golru",$lstGol,$field["golru"],"id='golru' class='form-control input-sm' disabled='disabled'");?>
                            </div>
                            <div class="col-md-2 col-sm-2 col-xs-12">
                                <div class="input-group ">
                                    <input class="form-control" placeholder="TMT"  readonly name="tmtpangkat" value="<?php echo ymdTodmy($field["tmtpangkat"])?>"  type="text">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2 col-sm-2 col-xs-12" for="eselon">Eselon</label>
                            <div class="col-md-4 col-sm-3 col-xs-12">
                                <?php echo form_dropdown("eselon",$lstEselon,$field["eselon"],"id='eselon' class='form-control input-sm' ");?>
                            </div>

                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2 col-sm-2 col-xs-12" for="kelasjabatan">Kelas Jabatan</label>
                            <div class="col-md-4 col-sm-3 col-xs-12">
                                <?php echo form_dropdown("kelasjabatan",$lstKelas,$field["kelasjabatan"],"id='kelasjabatan' class='form-control input-sm' ");?>
                            </div>
                            <div class="col-md-2 col-sm-2 col-xs-12">
                                <div class="input-group">
                                    <div class="input-group date">
                                        <input class="form-control" placeholder="TMT" name="tmtkelasjabatan" value="<?php echo ymdTodmy($field["tmtkelasjabatan"])?>" type="text"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2 col-sm-2 col-xs-12" for="tunjanganprofesi">Tunjangan Profesi</label>
                            <div class="col-md-4 col-sm-3 col-xs-12">
                                <div class="input-group">
                                        <input id="tunjanganprofesi" name="tunjanganprofesi" type="text"  value="<?php echo $field["tunjanganprofesi"]?>" placeholder="Tunjangan Profesi" class="form-control col-md-12 col-xs-12">
                                        <span class="help-inline text-danger" id="errmsg"></span>
                                    <div class="input-group-btn">
                                        <button class="btn btn-default btn-sm" type="button" id="cleartunjanganprofesi" title="Clear"><span class="fa fa-remove"></span></button>
                                    </div>
                                </div>
                                <span class="help-block">Nilai yang dimasukkan setelah dikurangi SIKERJA</span>
                            </div>
                            <div class="col-md-2 col-sm-2 col-xs-12">
                                <div class="input-group date">
                                    <input class="form-control" placeholder="TMT" name="tmtprofesi" value="<?php echo ymdTodmy($field["tmtprofesi"])?>" type="text"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2 col-sm-2 col-xs-12" >PLT/PLH</label>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2 col-sm-2 col-xs-12" for="cari_unker2">Unit Kerja</label>
                            <div class="col-md-4 col-sm-3 col-xs-12">
                                <div class="input-group">
                                    <div class="unker2 input-group">
                                        <input type="text" class="input-sm form-control" name="cari_unker2" id="cari_unker2" value="<?php echo $field["plt_deptname"]?>" placeholder="Unit Kerja ...">
                                        <input type="hidden" name="plt_deptid" id="unit_search2" value="<?php echo $field["plt_deptid"]?>">
                                        <div class="input-group-btn">
                                            <button class="btn btn-white btn-sm" type="button"><span class="caret"></span></button>
                                        </div>
                                    </div>
                                    <div class="input-group-btn">
                                        <button class="btn btn-default btn-sm" type="button" id="clear2" title="Clear"><span class="fa fa-remove"></span></button>
                                    </div>
                                </div>
                                <div class="panel combo-p panel2" style="position: absolute;  z-index:110003; display: none;" id="panel2">
                                    <div class="combo-panel panel-body panel-body-noheader" title="" style="max-height:250px; padding:5px;overflow-y:auto" id="combo-panel2">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-2 col-xs-12">
                                <div class="input-group date">
                                    <input class="form-control" placeholder="TMT" name="tmt_plt" value="<?php echo ymdTodmy($field["tmt_plt"])?>"  type="text"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2 col-sm-2 col-xs-12" for="plt_eselon">Eselon PLT/PLH</label>
                            <div class="col-md-4 col-sm-3 col-xs-12">
                                <?php echo form_dropdown("plt_eselon",$lstEselon,$field["plt_eselon"],"id='plt_eselon' class='form-control input-sm' ");?>
                            </div>
                            <div class="col-md-offset-6">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="plt_kelasjabatan">Kelas Jabatan</label>
                                <div class="col-md-4 col-sm-4 col-xs-12">
                                    <?php echo form_dropdown("plt_kelasjabatan",$lstKelas,$field["plt_kelasjabatan"],"id='plt_kelasjabatan' class='form-control input-sm' ");?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2 col-sm-2 col-xs-12" for="plt_eselon">SK PLT/PLH</label>
                            <div class="col-md-4 col-sm-3 col-xs-12">
                                <input id="plt_sk" name="plt_sk" type="text"  placeholder="SK PLT/PLH" value="<?php echo $field["plt_sk"]?>" class="form-control col-md-12 col-xs-12">
                            </div>
                            <div class="col-md-offset-6">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="plt_kelasjabatan">Jabatan</label>
                                <div class="col-md-7 col-sm-7 col-xs-12">
                                    <input id="plt_jbtn" name="plt_jbtn" type="text"  placeholder="Jabatan" value="<?php echo $field["plt_jbtn"]?>" class="form-control col-md-12 col-xs-12">
                                </div>
                            </div>

                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2 col-sm-2 col-xs-12" for="npwp">NPWP</label>
                            <div class="col-md-4 col-sm-3 col-xs-12">
                                <input class="form-control" placeholder="NPWP" name="npwp" value="<?php echo $field["npwp"]?>" type="text" disabled="disabled">
                            </div>
                            <div class="col-md-offset-6">
                                <?php
                                $cek1=""; $cek2="";
                                if ($field["payable"]==1)
                                {
                                    $cek1="checked";
                                } else
                                {
                                    $cek2="checked";
                                }
                                ?>
                            <label class="control-label col-md-2 col-sm-2 col-xs-12" for="payable">Dibayarkan</label>
                            <div class="col-md-4 col-sm-3 col-xs-12">
                                <div class="radio radio-info radio-inline">
                                    <input id="payable" value="1" name="payable" <?php echo $cek1;?> type="radio">
                                    <label for="payable"> Ya </label>
                                </div>
                                <div class="radio radio-inline">
                                    <input id="inlineRadio2" value="0" name="payable"  <?php echo $cek2;?>  type="radio">
                                    <label for="inlineRadio2"> Tidak </label>
                                </div>
                            </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2 col-sm-2 col-xs-12" for="no_rek">No.Rekening</label>
                            <div class="col-md-4 col-sm-3 col-xs-12">
                                <div class="input-group">
                                    <input class="form-control" placeholder="No. Rekening" name="no_rekening" id="no_rekening" value="<?php echo $field["no_rekening"]?>" type="text">
                                    <span class="help-inline text-danger" id="errmsg-no_rekening"></span>
                                <div class="input-group-btn">
                                    <button class="btn btn-default btn-sm" type="button" id="clearno_rekening" title="Clear"><span class="fa fa-remove"></span></button>
                                </div>
                            </div>
                            </div>
                            <div class="col-md-offset-6">

                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-2">
                                <input type="submit" id="" name="" class="btn btn-primary btn-sm" value="Simpan"/>
                                <a href="<?php echo site_url("pegawai")?>" class="btn btn-danger btn-sm">Batal</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function () {
        $("#inputForm").validate({
            rules: {
                txt1: {
                    required: true, digits: true, minlength: 1, maxlength: 15
                }, txt2: {
                    required: true, digits: true, minlength: 1, maxlength: 15
                }
            }, submitHandler: function () {
                saveForm();
            }, highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error');
            }, unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error');
            }
        });

        $('.input-group.date').datepicker({
            keyboardNavigation: false,
            forceParse: false,
            autoclose: true,
            format:"dd-mm-yyyy",
            language: 'id'
        });


        $('.unker2').click(function(){
            var inwidth = $(this).width();
            var dis = $('#panel2').css("display");
            if(dis=='none'){
                $('#panel2').css({
                    display : 'block',
                    width : inwidth
                });

                //$('#combo-panel2').empty();
                if ( $('#combo-panel2 > *').length == 0 ) {
                    $('#combo-panel2').html('Loading...........');
                    $.ajax({
                        url: '<?php echo site_url('ajax/getUnitKerjaN')?>', dataType: 'html', type: 'POST', success: function (data) {
                            $('#combo-panel2').html(data);
                        }
                    });
                }
            }else{
               // $('#combo-panel2').empty();
                $('#panel2').css({
                    display : 'none'
                });
                //$('.unker2').data('clicked',0);
            }
        });

        $('#clear2').click(function(){
            $("#unit_search2").val("");
            $("#cari_unker2").val("");
        });


        $('#cleartunjanganprofesi').click(function(){
            $("#tunjanganprofesi").val("0");
        });

        $('#clearno_rekening').click(function(){
            $("#no_rekening").val("");
        });
        $("#tunjanganprofesi").keypress(function (e) {
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                //display error message
                $("#errmsg").html("Hanya Angka saja..").show().fadeOut("slow");
                return false;
            }
        });

        $("#no_rekening").keypress(function (e) {
            var charCode = (e.which) ? e.which : e.keyCode;
            if ((charCode >= 48 && charCode <= 57)
                || charCode == 46
                || charCode == 44 || charCode==8 || charCode==0 ) {
                return true;
            } else {
                $("#errmsg-no_rekening").html("Hanya Angka dan Titik saja..").show().fadeOut("slow");
                return false;
            }

            /*return false;

            if (e.which != 8 && e.which != 0 && ((e.which >= 48 && e.which <= 57) || e.which == 46 || e.which == 44)) {


            }*/
        });
    });

    function saveForm() {
        if (($("#txt1").val() != "")) {
            $.ajax({
                url: '<?php echo site_url('pegawai/save');?>', dataType: 'json', type: 'POST', data: $("#inputForm").serialize(), success: function (data) {
                    bootbox.alert(data.msg);
                }
            });
        } else {
            bootbox.alert("Harap cek kembali inputannya ??");
        }
    }


</script>