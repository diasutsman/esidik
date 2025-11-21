<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Author       : _abdi_iwan_
 * Project         :
 */
?>
<link href="<?php echo base_url()?>assets/css/plugins/datepicker/datepicker3.css" rel="stylesheet">
<link href="<?php echo base_url()?>assets/css/plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet">
<script src="<?php echo base_url()?>assets/js/plugins/datapicker/bootstrap-datepicker.js"></script>
<script src="<?php echo base_url()?>assets/js/plugins/datapicker/bootstrap-datepicker.id.min.js"></script>
<script src="<?php echo base_url()?>assets/js/plugins/daterangepicker/daterangepicker.js"></script>
<link href="<?php echo base_url()?>assets/css/plugins/clockpicker/clockpicker.css" rel="stylesheet">
<script src="<?php echo base_url()?>assets/js/plugins/clockpicker/clockpicker.js"></script>

<input type="hidden" id="userid" name="userid">
<input type="hidden" id="startattilog" name="startattilog">
<input type="hidden" id="endattilog" name="endattilog">
<input type="hidden" id="shiftid" name="shiftid" value="<?php echo $shifid?>">
<div class="form-group">
    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="sel1">Status</label>
    <div class="col-md-4 col-sm-4 col-xs-12">
        <select class="form-control input-sm" name="sel1" id="sel1">
            <option value=" ">--Pilih--</option>
            <?php
                foreach ($lst->result() as $row )
                {
                    echo "<option value='".$row->atid."'>".$row->atname."</option>";
                }
            ?>

        </select>
    </div>
</div>
<div class="form-group">
    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="start">Tanggal</label>
    <div class="col-md-5 col-sm-5 col-xs-12">
        <div class="form-group form-inline" id="data_5" style="padding-left: 15px">
            <div class="input-daterange input-group" id="datepicker">
                <input type="text" class="input-sm form-control" name="start" value="<?php echo $tgl1?>" id="t1"/>
                <span class="input-group-addon">s/d</span>
                <input type="text" class="input-sm form-control" name="end" value="<?php echo $tgl2?>" id="t2"/>
            </div>
        </div>
    </div>
</div>

<!--<div class="form-group">
    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="cek1">Jam Mulai</label>
    <div class="col-md-2 col-sm-2 col-xs-12">
        <div class="input-group clockpicker" data-autoclose="true">
            <input type="text" class="form-control input-sm" id="cek1" name="time1">
            <span class="input-group-addon">
                <span class="fa fa-clock-o"></span>
            </span>
        </div>
    </div>
</div>
<div class="form-group">
    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="cek2">Jam Akhir</label>
    <div class="col-md-2 col-sm-2 col-xs-12">
        <div class="input-group clockpicker" data-autoclose="true">
            <input type="text" class="form-control input-sm" id="cek2" name="time2" >
            <span class="input-group-addon">
                <span class="fa fa-clock-o"></span>
            </span>
        </div>
    </div>
</div>-->

<input name="time1" type="hidden"><input name="time2" type="hidden">

<div class="form-group">
    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="cek4">Catatan</label>
    <div class="col-md-6 col-sm-6 col-xs-12">
        <input class="form-control" name="catatan"  id="catatan" type="text" required>
    </div>
</div>
<div class="form-group">
    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="cek4">Nomor</label>
    <div class="col-md-6 col-sm-6 col-xs-12">
        <input class="form-control" name="nosk" id="nosk" type="text" required>
    </div>
</div>

<script>
    $(document).ready(function () {
        /*$('.clockpicker').clockpicker();*/

        $('#data_5 .input-daterange').datepicker({
            keyboardNavigation: false,
            forceParse: false,
            autoclose: true,
            format:"dd-mm-yyyy",
            language: 'id'
        });
    });

</script>