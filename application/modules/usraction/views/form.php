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
                <input type="hidden" name="idlvl" id="idlvl" value="<?php echo $lvlID?>"/>
        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt1">Keterangan</label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <?php
                $this->db->select("menu_id,menu_desc");
                $this->db->from("menu_new");
                $this->db->join('menu_level', 'menu_new.menu_id=menu_level.menu_level_menu');
                $this->db->where("menu_parent <>",0);
                $this->db->where("menu_status",1);
                $this->db->where("menu_level.menu_level_user_level ",$lvlID);
                $this->db->order_by("menu_parent,menu_sort");
                $sql1=$this->db->get();

                //echo $this->db->last_query();

                $list_un['0']='- Menu -';
                foreach($sql1->result() as $rls){
                    $list_un[$rls->menu_id]= $rls->menu_desc;
                }
                $js = 'id="txt1" class="form-control" ';
                $selected="";
                echo form_dropdown('txt1',$list_un,$selected,$js);
                ?>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt4">Tambah Data</label>
            <div class="col-md-3 col-sm-3 col-xs-12">
                <label class="radio-inline"> <input checked value="1" id="txt4" name="flagadd" type="radio" > Ya </label>
                <label class="radio-inline"> <input value="0" id="txt5" name="flagadd" type="radio"> Tidak </label>
            </div>
        </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt4">Memperbaharui Data</label>
                    <div class="col-md-3 col-sm-3 col-xs-12">
                        <label class="radio-inline"> <input checked value="1" id="txt4" name="flagedit" type="radio" > Ya </label>
                        <label class="radio-inline"> <input value="0" id="txt5" name="flagedit" type="radio"> Tidak </label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt4">Menghapus</label>
                    <div class="col-md-3 col-sm-3 col-xs-12">
                        <label class="radio-inline"> <input checked value="1" id="txt4" name="flagdelete" type="radio" > Ya </label>
                        <label class="radio-inline"> <input value="0" id="txt5" name="flagdelete" type="radio"> Tidak </label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="txt4">Mencetak</label>
                    <div class="col-md-3 col-sm-3 col-xs-12">
                        <label class="radio-inline"> <input checked value="1" id="txt4" name="flagprint" type="radio" > Ya </label>
                        <label class="radio-inline"> <input value="0" id="txt5" name="flagprint" type="radio"> Tidak </label>
                    </div>
                </div>

        <div class="hr-line-dashed"></div>
        <div class="form-group">
            <div class="col-md-6 col-md-offset-3">
                <input type="submit" id="" name="" class="btn btn-primary btn-sm" value="Simpan"/>
                <a href="#" onClick="Batal();" class="btn btn-danger btn-sm" >Batal</a>
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
                    required: true,
                    minlength: 1,
                    maxlength: 50
                }
            },
            submitHandler: function() {
                saveForm();
            },
            highlight: function(element) {
                    $(element).closest('.form-group').addClass('has-error');
            },
            unhighlight: function(element) {
                    $(element).closest('.form-group').removeClass('has-error');
            }
        });

    });

    function Batal()
    {
        var validator = $( "#inputForm" ).validate();
        validator.resetForm();
        $("#iddata").val("0");
        $("#btnAdd").show();$("#btnDeleteRow").show();
        $("#view-form").slideUp('fast');
    }

    function RefreshData()
    {
        load_url('<?php echo site_url('usraction/pagging/0') ?>', '<?php echo(isset($order) ? $order : 'id_action')?>', '<?php echo(isset($typeorder) ? $typeorder : 'sorting')?>');
    }

    function saveForm()
    {
        if(($("#txt1").val()!="") )
        {
            $.ajax({
                url     : '<?php echo site_url('usraction/save');?>',
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