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
<input type="hidden" id="userid" name="userid">
<input type="hidden" id="startattilog" name="startattilog">
<input type="hidden" id="endattilog" name="endattilog">
<div class="form-group">
    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="sel1">Status</label>
    <div class="col-md-4 col-sm-4 col-xs-12">
        <select class="form-control input-sm" name="sel1" id="sel1">
            <option value=" ">--Pilih--</option>
            <?php
                foreach ($lst->result() as $row )
                {
                    echo "<option value='".$row->abid."'>".$row->abname."</option>";
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
                <input type="text" class="input-sm form-control" name="end" value="<?php echo $tgl2?>" id="t2" />
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="cek4">Catatan</label>
    <div class="col-md-6 col-sm-6 col-xs-12">
        <input class="form-control" name="catatan" type="text">
    </div>
</div>
<div class="form-group">
    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="cek4">No.SK</label>
    <div class="col-md-6 col-sm-6 col-xs-12">
        <input class="form-control" name="nosk" type="text">
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#data_5 .input-daterange').datepicker({
            keyboardNavigation: false,
            forceParse: false,
            autoclose: true,
            format:"dd-mm-yyyy",
            language: 'id'
        });

    });

</script>