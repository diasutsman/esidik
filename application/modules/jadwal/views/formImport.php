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
    
        <?php
            $form = array(
                'name'        => 'userfile'
            );
            echo "<a class='btn btn-primary' href='javascript:;'>";
            echo "Browse ".form_upload($form);
            echo "</a>";
        ?>
</div>