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
<div class="form-group">
    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt1">Tanggal</label>
    <div class="col-md-5 col-sm-5 col-xs-12">
        <div class="form-group form-inline" id="data_5">
            <div class="input-daterange input-group" id="datepicker">
                <input type="text" class="input-sm form-control" name="start" value="<?php echo date("01-m-Y")?>"/>
                <span class="input-group-addon">s/d</span>
                <input type="text" class="input-sm form-control" name="end" value="<?php echo date("t-m-Y", strtotime(date("Y-m-d")));?>" />
            </div>
        </div>
    </div>
</div>
<div class="form-group">
    <label class="control-label col-md-3 col-sm-3 col-xs-12">Hari Kerja</label>
    <label class="control-label col-md-1 col-sm-3 col-xs-12"></label>
    <label class="control-label col-md-4 col-sm-4 col-xs-12" style="text-align: left">Shift</label>
</div>

<div class="form-group">
    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="cek1">Senin</label>
    <div class="col-md-1 col-sm-3 col-xs-12 checkbox">
        <input type="checkbox" name="cek1" id="cek1">
    </div>
    <div class="col-md-4 col-sm-4 col-xs-12">
        <select class="form-control input-sm" name="sel1" id="sel1">
            <option value="0">--Pilih--</option>
            <?php
            foreach($lstShift as $row)
            {
                echo '<option value="'.$row->code_shift.'">'.$row->name_shift.'</option>';
            }
            ?>
        </select>
    </div>
</div>
<div class="form-group">
    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="cek2">Selasa</label>
    <div class="col-md-1 col-sm-3 col-xs-12 checkbox">
        <input type="checkbox" name="cek2" id="cek2">
    </div>
    <div class="col-md-4 col-sm-4 col-xs-12">
        <select class="form-control input-sm" name="sel2" id="sel2">
            <option value="0">--Pilih--</option>
            <?php
            foreach($lstShift as $row)
            {
                echo '<option value="'.$row->code_shift.'">'.$row->name_shift.'</option>';
            }
            ?>
        </select>
    </div>
</div>
<div class="form-group">
    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="cek3">Rabu</label>
    <div class="col-md-1 col-sm-3 col-xs-12 checkbox">
        <input type="checkbox" name="cek3" id="cek3">
    </div>
    <div class="col-md-4 col-sm-4 col-xs-12">
        <select class="form-control input-sm" name="sel3" id="sel3">
            <option value="0">--Pilih--</option>
            <?php
            foreach($lstShift as $row)
            {
                echo '<option value="'.$row->code_shift.'">'.$row->name_shift.'</option>';
            }
            ?>
        </select>
    </div>
</div>
<div class="form-group">
    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="cek4">Kamis</label>
    <div class="col-md-1 col-sm-3 col-xs-12 checkbox">
        <input type="checkbox" name="cek4" id="cek4">
    </div>
    <div class="col-md-4 col-sm-4 col-xs-12">
        <select class="form-control input-sm" name="sel4" id="sel4">
            <option value="0">--Pilih--</option>
            <?php
            foreach($lstShift as $row)
            {
                echo '<option value="'.$row->code_shift.'">'.$row->name_shift.'</option>';
            }
            ?>
        </select>
    </div>
</div>
<div class="form-group">
    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="cek5">Jumat</label>
    <div class="col-md-1 col-sm-3 col-xs-12 checkbox">
        <input type="checkbox" name="cek5" id="cek5">
    </div>
    <div class="col-md-4 col-sm-4 col-xs-12">
        <select class="form-control input-sm" name="sel5" id="sel5">
            <option value="0">--Pilih--</option>
            <?php
            foreach($lstShift as $row)
            {
                echo '<option value="'.$row->code_shift.'">'.$row->name_shift.'</option>';
            }
            ?>
        </select>
    </div>
</div>
<div class="form-group">
    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="cek6">Sabtu</label>
    <div class="col-md-1 col-sm-3 col-xs-12 checkbox">
        <input type="checkbox" name="cek6" id="cek6">
    </div>
    <div class="col-md-4 col-sm-4 col-xs-12">
        <select class="form-control input-sm" name="sel6" id="sel6">
            <option value="0">--Pilih--</option>
            <?php
            foreach($lstShift as $row)
            {
                echo '<option value="'.$row->code_shift.'">'.$row->name_shift.'</option>';
            }
            ?>
        </select>
    </div>
</div>
<div class="form-group" style="">
    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt7">Minggu</label>
    <div class="col-md-1 col-sm-3 col-xs-12 checkbox">
        <input type="checkbox" name="cek7" id="cek7">
    </div>
    <div class="col-md-4 col-sm-4 col-xs-12">
        <select class="form-control input-sm" name="sel7"  id="sel7">
            <option value="0">--Pilih--</option>
            <?php
            foreach($lstShift as $row)
            {
                echo '<option value="'.$row->code_shift.'">'.$row->name_shift.'</option>';
            }
            ?>
        </select>
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