<link href="<?php echo base_url()?>assets/css/plugins/datepicker/datepicker3.css" rel="stylesheet">
<link href="<?php echo base_url()?>assets/css/plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet">
<script src="<?php echo base_url()?>assets/js/plugins/datapicker/bootstrap-datepicker.js"></script>
<script src="<?php echo base_url()?>assets/js/plugins/datapicker/bootstrap-datepicker.id.min.js"></script>
<script src="<?php echo base_url()?>assets/js/plugins/daterangepicker/daterangepicker.js"></script>
<div class="form-group">
    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="ket">Keterangan WFO Normal</label>
    <div class="col-md-5 col-sm-5 col-xs-12">
        <input type="text" class="input-sm form-control" name="ket" id="ket"/>
    </div>
</div>
<script type="text/javascript">
    function saveForm() {
        var ln   = $('#ket').val().length;
        var ln1  = $('#ket1').val().length;
        var ln2  = $('#ket2').val().length;
        var ln3  = $('#ket3').val().length;
        var ln4  = $('#ket4').val().length;
        var ln5  = $('#ket5').val().length;
        var ln6  = $('#ket6').val().length;
        var ln7  = $('#ket7').val().length;
        var ln8  = $('#ket8').val().length;
        var ln9  = $('#ket9').val().length;
        var ln10 = $('#ket10').val().length;
        if (ln == 0) {
            bootbox.alert("Keterangan WFO Normal Harus diisi..");
            return false;
        }else if(ln1 == 0 || ln4 == 0 || ln5 == 0){
            bootbox.alert("Pelaporan Jam Kerja ASN Harus diisi..");
            return false;
        }else{
            hidePopup('#popup');
            var par5 = $('#caridata').val();
            var par6 = $('#unit_search').val();
            var par7 = $('#stspeg').val();
            var par8 = $('#jnspeg').val();
            var par9 = $('#start').val();
            var par10 = $('#end').val();
            var par11 = $('#ket').val();
            var par12 = $('#idcetak').val();

            var ket1  = $('#ket1').val();
            var ket2  = $('#ket2').val();
            var ket3  = $('#ket3').val();
            var ket4  = $('#ket4').val();
            var ket5  = $('#ket5').val();
            var ket6  = $('#ket6').val();
            var ket7  = $('#ket7').val();
            var ket8  = $('#ket8').val();
            var ket9  = $('#ket9').val();
            var ket10 = $('#ket10').val();
            $('<form action="<?php echo site_url('rptjbd/view');?>" method="POST" target="_blank" style="display:none">' +
                '<input type="hidden" name="cari" value="'+par5+'" />'+
                '<input type="hidden" name="org" value="'+par6+'" />'+
                '<input type="hidden" name="stspeg" value="'+par7+'" />'+
                '<input type="hidden" name="jnspeg" value="'+par8+'" />'+
                '<input type="hidden" name="start" value="'+par9+'" />'+
                '<input type="hidden" name="ket" value="'+par11+'" />'+
                '<input type="hidden" name="idcetak" value="'+par12+'" />'+
                '<input type="hidden" name="end" value="'+par10+'" />' +

                '<input type="hidden" name="ket1" value="'+ket1+'" />' +
                '<input type="hidden" name="ket2" value="'+ket2+'" />' +
                '<input type="hidden" name="ket3" value="'+ket3+'" />' +
                '<input type="hidden" name="ket4" value="'+ket4+'" />' +
                '<input type="hidden" name="ket5" value="'+ket5+'" />' +
                '<input type="hidden" name="ket6" value="'+ket6+'" />' +
                '<input type="hidden" name="ket7" value="'+ket7+'" />' +
                '<input type="hidden" name="ket8" value="'+ket8+'" />' +
                '<input type="hidden" name="ket9" value="'+ket9+'" />' +
                '<input type="hidden" name="ket10" value="'+ket10+'" />' 
            ).appendTo("body").submit().remove();
        }
    }
</script>