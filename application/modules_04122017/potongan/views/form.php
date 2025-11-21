<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Author       : _abdi_iwan_
 * Project         :
 */
?>

<div id="view-form" style="display:none">
    <div class="ibox float-e-margins ">
        <div class="ibox-content gray-bg">
            <form class="form-horizontal form-label-left" name="inputForm" id="inputForm" method="post">
                <input type="hidden" name="iddata" id="iddata"/>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt1">Jenis Potongan</label>
                    <div class="col-md-3 col-sm-3 col-xs-12">
                        <?php
                        $sql1 = $this->db->query("select * from ref_jnspot order by id ASC");

                        $list_un['']='- Pilih Jenis Potongan -';
                        foreach($sql1->result() as $rls){
                            $list_un[$rls->id]= $rls->keterangan;
                        }
                        $js = 'id="txt1" class="form-control" ';
                        $selected="";
                        echo form_dropdown('txt1',$list_un,$selected,$js);
                        ?>
                    </div>
                </div>
                <div class="form-group" >
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt2">Durasi Waktu</label>
                    <div class="input-group col-md-3 col-sm-3 col-xs-12" style="padding-left:15px">
                        <input class="input-sm form-control" name="txt2" type="text"  id="txt2">
                        <span class="input-group-addon">s/d</span>
                        <input class="input-sm form-control" name="txt3" type="text"  id="txt3">
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt4">Satuan Waktu</label>
                    <div class="col-md-3 col-sm-3 col-xs-12">
                        <?php
                        $list_jab['']='- Pilih Satuan Waktu -';
                        $list_jab['Menit']='Menit';
                        $list_jab['Hari']='Hari';
                        $js = 'id="txt4" class="form-control" ';
                        echo form_dropdown('txt4',$list_jab,$selected,$js);
                        ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt5">Potongan</label>
                    <div class="col-md-3 col-sm-3 col-xs-12">
                        <input id="txt5" name="txt5" type="text" placeholder="Potongan" class="form-control col-md-5 col-xs-12">
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt4">Status</label>
                    <div class="col-md-3 col-sm-3 col-xs-12">
                        <label class="radio-inline"> <input checked value="1" id="txt4" name="status" type="radio" > Aktif </label>
                        <label class="radio-inline"> <input value="0" id="txt5" name="status" type="radio"> Tidak Aktif </label>
                    </div>
                </div>

                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <div class="col-md-6 col-md-offset-3">
                        <input type="submit" id="" name="" class="btn btn-primary btn-sm" value="Simpan"/>
                        <a href="#" onClick="Batal();" class="btn btn-danger btn-sm">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    $(document).ready(function(){
        $("#inputForm").validate({
            rules: {
                txt1: {
                    required: true
                }, txt2: {
                    required: true, digits:true
                }, txt3: {
                    required: true, digits:true
                }, txt4: {
                    required: true
                }, txt5: {
                    required: true,  number:true
                }
            }, submitHandler: function () {
                saveForm();
            }, highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error');
            }, unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error');
            }
        });
    });

    function Batal()
    {
        $("#iddata").val("");
        var validator = $( "#inputForm" ).validate();
        validator.resetForm();
        $("#btnAdd").show();$("#btnDeleteRow").show();
        $("#view-form").slideUp('fast');
    }

    function RefreshData()
    {
        load_url('<?php echo site_url('potongan/pagging/0') ?>','<?php echo (isset($order)?$order:'id')?>','<?php echo (isset($typeorder)?$typeorder:'sorting')?>');
    }

    function saveForm()
    {
        if(($("#txt1").val()!="") )
        {
            $.ajax({
                url     : '<?php echo site_url('potongan/save');?>',
                dataType: 'json',
                type    : 'POST',
                data    : $("#inputForm").serialize(),
                success : function(data){
                    if(data.status!="error"){
                        bootbox.alert(data.msg, function() {
                            $("#btnAdd").show();$("#btnDeleteRow").show();
                            $("#view-form").slideUp('fast');
                            $("#list-data").slideDown('fast');
                            RefreshData();
                        });
                    }else{
                        bootbox.alert(data.msg);
                    }
                }
            });
        }else{
            bootbox.alert("Harap cek kembali inputannya ??");
        }
    }


</script>