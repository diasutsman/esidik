<script src="<?php echo base_url()?>assets/js/plugins/table-edits.min.js"></script>

<?php
/**
 * File: list.php
 * Author: abdiIwan.
 * Date: 12/28/2016
 * Time: 9:38 PM
 * absensi.kemendagri.go.id
 */

$url_pag =  "'".site_url("shift/pagging/0")."'";
$domId ="'#list-data'";

?>

<style>

    td input[type=text], td select {
        width: 100%;
        height: 25px;
        margin: 0;
    }

    th:last-child {
        text-align: right;
    }

    /*td:last-child {
        text-align: right;
    }*/

    td:last-child .button {
        width: 30px;
        height: 30px;
        text-align: center;
        padding: 0px;
        margin-bottom: 0px;
        margin-right: 5px;
        background-color: #FFF;
    }

    td:last-child .button .fa {
        line-height: 30px;
        width: 30px;
    }
    .newdata{
        width: 100%;
        height: 25px;
        margin: 0;
    }

</style>

<div class="table-responsive">
    <div class="dataTables_wrapper dt-bootstrap">
    <table class="table small table-striped table-bordered tableku" id="tableku">
        <thead>
        <tr>
            <th rowspan="2"><input type="checkbox" name="cek_all" id="cek_all"></th>
            <th rowspan="2">Kode</th>
            <th rowspan="2">Nama</th>
            <th colspan="3" class="text-center">Masuk</th>
            <th colspan="4" class="text-center">Break</th>
            <th colspan="3" class="text-center">Keluar</th>
            <th rowspan="2" width="70px">Warna</th>
            <th rowspan="2">Status</th>
            <th rowspan="2"></th>
        </tr>
        <tr>
            <td width="70px">Awal</td>
            <td width="70px">Masuk</td>
            <td width="70px">Akhir</td>

            <td width="70px">Awal</td>
            <td width="70px">Keluar Istirahat</td>
            <td width="70px">Masuk Istirahat</td>
            <td width="70px">Akhir</td>

            <td width="70px">Awal</td>
            <td width="70px">Masuk</td>
            <td width="70px">Akhir</td>

        </tr>
        </thead>
        <tbody>
        <?php
        $co=1;
        $jm=0;
        foreach($result as $row)
        {
            $jm=$co;
            ?>
            <tr data-id="<?php echo $row->id_shift?>" id="rowdata-<?php echo $row->id_shift?>">
                <td width="20px"><input type="checkbox" name="cek_del" id="cek_del_<?php echo $row->id_shift?>" class="selected" value="<?php echo $row->id_shift?>"></td>
                <td data-field="code_shift"><?php echo $row->code_shift;?></td>
                <td data-field="name_shift"><?php echo $row->name_shift;?></td>
                <td data-field="start_in"><?php echo format_jammenit($row->start_in);?></td>
                <td data-field="check_in"><?php echo format_jammenit($row->check_in);?></td>
                <td data-field="end_check_in"><?php echo format_jammenit($row->end_check_in);?></td>

                <td data-field="start_break"><?php echo format_jammenit($row->start_break);?></td>
                <td data-field="break_out"><?php echo format_jammenit($row->break_out);?></td>
                <td data-field="break_in"><?php echo format_jammenit($row->break_in);?></td>
                <td data-field="end_break"><?php echo format_jammenit($row->end_break);?></td>

                <td data-field="start_out"><?php echo format_jammenit($row->start_out);?></td>
                <td data-field="check_out"><?php echo format_jammenit($row->check_out);?></td>
                <td data-field="end_check_out"><?php echo format_jammenit($row->end_check_out);?></td>

                <td data-field="colour_shift"><span style="color: <?php echo $row->colour_shift;?>"><?php echo $row->colour_shift;?></span></td>
                <td data-field="state"><?php echo $row->state?></span></td>
                <td width="20px"><a class="btn btn-xs btn-primary edit" data-id="<?php echo $row->id_shift?>" title="Edit"><i class="fa fa-pencil"></i></a></td>
            </tr>

            <?php

            $co++;
        }

        if (count($result)==0)
        {
            ?>
            <tr class="">
                <td colspan="15"><center>Tidak ada data..</center></td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
        <div class="row form-inline ">
            <div class="col-sm-6 m-b-xs">
                <div id="tabel_data_length" class="dataTables_length">
                    <div class="form-inline">
                        <?php
                        $options = array(
                            '10' => '10',
                            '25' => '25',
                            '50' => '50',
                            '100' => '100'
                        );
                        $selected = '10';
                        if(isset($limit_display) && trim($limit_display) != '')
                            $selected = $limit_display;
                        $js = 'id="limit_display" class="input-sm" onChange="load_url('.$url_pag.','.$domId.');" name="tabel_data_length" size="1" ';
                        echo form_dropdown('tabel_data_length',$options,$selected,$js);
                        ?>
                        Rec. <?php echo (($jum_data==0)?'0':($offset+1))?> s/d <?php echo(($offset+$jm))?> dari <?php echo$jum_data?> data
                    </div>

                </div>
            </div>
            <div class="col-sm-6 m-b-xs" id="pagering">
                <div class="dataTables_paginate paging_simple_numbers">
                    <?php echo $paging;?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){

        $('table tr').editable({
            dropdowns: {
                shift_in: [['0', 'T'],['1', 'Y']],
                shift_out: [['0', 'T'],['1', 'Y']],
                in_ot_tolerance: [['0', 'T'],['1', 'Y']],
                out_ot_tolerance: [['0', 'T'],['1', 'Y']],
                state: [['0', 'Tidak Aktif'],['1', 'Aktif']]
            },
            <?php if (($aksesrule["flagedit"]) || ($aksesrule["flagadd"])) { ?>
            dblclick: true,
            button: true,
            maintainWidth: true,
            edit: function(values) {
                $(".edit i", this)
                    .removeClass('fa-pencil')
                    .addClass('fa-save')
                    .attr('title', 'Save');
                $data = $("td[data-field=colour_shift] input", this);
                $data.addClass("colorpict");

                $('.colorpict').minicolors({
                    defaultValue: $data.val(),
                    format: $(this).attr('data-format') || 'hex',
                    inline: $(this).attr('data-inline') === 'true',
                    letterCase: $(this).attr('data-letterCase') || 'lowercase',
                    theme: 'default'
                });

                $data = $("td[data-field=start_in] input", this);
                $data.attr('id', 'start_in');

                $('#start_in').wickedpicker({
                    now: $data.val(),
                    twentyFour: true,  //Display 24 hour format, defaults to false
                    upArrow: 'wickedpicker__controls__control-up',  //The up arrow class selector to use, for custom CSS
                    downArrow: 'wickedpicker__controls__control-down', //The down arrow class selector to use, for custom CSS
                    close: 'wickedpicker__close', //The close class selector to use, for custom CSS
                    hoverState: 'hover-state', //The hover state class to use, for custom CSS
                    title: 'Jam', //The Wickedpicker's title,
                    showSeconds: false, //Whether or not to show seconds,
                    secondsInterval: 1, //Change interval for seconds, defaults to 1,
                    minutesInterval: 1, //Change interval for minutes, defaults to 1
                    beforeShow: null, //A function to be called before the Wickedpicker is shown
                    show: null, //A function to be called when the Wickedpicker is shown
                    clearable: true //Make the picker's input clearable (has clickable "x")
                });

                $data = $("td[data-field=check_in] input", this);
                $data.attr('id', 'check_in');
                $('#check_in').wickedpicker({
                    now: $data.val(),
                    twentyFour: true,  //Display 24 hour format, defaults to false
                    upArrow: 'wickedpicker__controls__control-up',  //The up arrow class selector to use, for custom CSS
                    downArrow: 'wickedpicker__controls__control-down', //The down arrow class selector to use, for custom CSS
                    close: 'wickedpicker__close', //The close class selector to use, for custom CSS
                    hoverState: 'hover-state', //The hover state class to use, for custom CSS
                    title: 'Jam', //The Wickedpicker's title,
                    showSeconds: false, //Whether or not to show seconds,
                    secondsInterval: 1, //Change interval for seconds, defaults to 1,
                    minutesInterval: 1, //Change interval for minutes, defaults to 1
                    beforeShow: null, //A function to be called before the Wickedpicker is shown
                    show: null, //A function to be called when the Wickedpicker is shown
                    clearable: true //Make the picker's input clearable (has clickable "x")
                });

                $data = $("td[data-field=end_check_in] input", this);
                $data.attr('id', 'end_check_in');
                $('#end_check_in').wickedpicker({
                    now: $data.val(),
                    twentyFour: true,  //Display 24 hour format, defaults to false
                    upArrow: 'wickedpicker__controls__control-up',  //The up arrow class selector to use, for custom CSS
                    downArrow: 'wickedpicker__controls__control-down', //The down arrow class selector to use, for custom CSS
                    close: 'wickedpicker__close', //The close class selector to use, for custom CSS
                    hoverState: 'hover-state', //The hover state class to use, for custom CSS
                    title: 'Jam', //The Wickedpicker's title,
                    showSeconds: false, //Whether or not to show seconds,
                    secondsInterval: 1, //Change interval for seconds, defaults to 1,
                    minutesInterval: 1, //Change interval for minutes, defaults to 1
                    beforeShow: null, //A function to be called before the Wickedpicker is shown
                    show: null, //A function to be called when the Wickedpicker is shown
                    clearable: true //Make the picker's input clearable (has clickable "x")
                });

                $data = $("td[data-field=start_break] input", this);
                $data.attr('id', 'start_break');
                $('#start_break').wickedpicker({
                    now: $data.val(),
                    twentyFour: true,  //Display 24 hour format, defaults to false
                    upArrow: 'wickedpicker__controls__control-up',  //The up arrow class selector to use, for custom CSS
                    downArrow: 'wickedpicker__controls__control-down', //The down arrow class selector to use, for custom CSS
                    close: 'wickedpicker__close', //The close class selector to use, for custom CSS
                    hoverState: 'hover-state', //The hover state class to use, for custom CSS
                    title: 'Jam', //The Wickedpicker's title,
                    showSeconds: false, //Whether or not to show seconds,
                    secondsInterval: 1, //Change interval for seconds, defaults to 1,
                    minutesInterval: 1, //Change interval for minutes, defaults to 1
                    beforeShow: null, //A function to be called before the Wickedpicker is shown
                    show: null, //A function to be called when the Wickedpicker is shown
                    clearable: true //Make the picker's input clearable (has clickable "x")
                });

                $data = $("td[data-field=break_out] input", this);
                $data.attr('id', 'break_out');
                $('#break_out').wickedpicker({
                    now: $data.val(),
                    twentyFour: true,  //Display 24 hour format, defaults to false
                    upArrow: 'wickedpicker__controls__control-up',  //The up arrow class selector to use, for custom CSS
                    downArrow: 'wickedpicker__controls__control-down', //The down arrow class selector to use, for custom CSS
                    close: 'wickedpicker__close', //The close class selector to use, for custom CSS
                    hoverState: 'hover-state', //The hover state class to use, for custom CSS
                    title: 'Jam', //The Wickedpicker's title,
                    showSeconds: false, //Whether or not to show seconds,
                    secondsInterval: 1, //Change interval for seconds, defaults to 1,
                    minutesInterval: 1, //Change interval for minutes, defaults to 1
                    beforeShow: null, //A function to be called before the Wickedpicker is shown
                    show: null, //A function to be called when the Wickedpicker is shown
                    clearable: true //Make the picker's input clearable (has clickable "x")
                });

                $data = $("td[data-field=break_in] input", this);
                $data.attr('id', 'break_in');
                $('#break_in').wickedpicker({
                    now: $data.val(),
                    twentyFour: true,  //Display 24 hour format, defaults to false
                    upArrow: 'wickedpicker__controls__control-up',  //The up arrow class selector to use, for custom CSS
                    downArrow: 'wickedpicker__controls__control-down', //The down arrow class selector to use, for custom CSS
                    close: 'wickedpicker__close', //The close class selector to use, for custom CSS
                    hoverState: 'hover-state', //The hover state class to use, for custom CSS
                    title: 'Jam', //The Wickedpicker's title,
                    showSeconds: false, //Whether or not to show seconds,
                    secondsInterval: 1, //Change interval for seconds, defaults to 1,
                    minutesInterval: 1, //Change interval for minutes, defaults to 1
                    beforeShow: null, //A function to be called before the Wickedpicker is shown
                    show: null, //A function to be called when the Wickedpicker is shown
                    clearable: true //Make the picker's input clearable (has clickable "x")
                });

                $data = $("td[data-field=end_break] input", this);
                $data.attr('id', 'end_break');
                $('#end_break').wickedpicker({
                    now: $data.val(),
                    twentyFour: true,  //Display 24 hour format, defaults to false
                    upArrow: 'wickedpicker__controls__control-up',  //The up arrow class selector to use, for custom CSS
                    downArrow: 'wickedpicker__controls__control-down', //The down arrow class selector to use, for custom CSS
                    close: 'wickedpicker__close', //The close class selector to use, for custom CSS
                    hoverState: 'hover-state', //The hover state class to use, for custom CSS
                    title: 'Jam', //The Wickedpicker's title,
                    showSeconds: false, //Whether or not to show seconds,
                    secondsInterval: 1, //Change interval for seconds, defaults to 1,
                    minutesInterval: 1, //Change interval for minutes, defaults to 1
                    beforeShow: null, //A function to be called before the Wickedpicker is shown
                    show: null, //A function to be called when the Wickedpicker is shown
                    clearable: true //Make the picker's input clearable (has clickable "x")
                });

                $data = $("td[data-field=start_out] input", this);
                $data.attr('id', 'start_out');
                $('#start_out').wickedpicker({
                    now: $data.val(),
                    twentyFour: true,  //Display 24 hour format, defaults to false
                    upArrow: 'wickedpicker__controls__control-up',  //The up arrow class selector to use, for custom CSS
                    downArrow: 'wickedpicker__controls__control-down', //The down arrow class selector to use, for custom CSS
                    close: 'wickedpicker__close', //The close class selector to use, for custom CSS
                    hoverState: 'hover-state', //The hover state class to use, for custom CSS
                    title: 'Jam', //The Wickedpicker's title,
                    showSeconds: false, //Whether or not to show seconds,
                    secondsInterval: 1, //Change interval for seconds, defaults to 1,
                    minutesInterval: 1, //Change interval for minutes, defaults to 1
                    beforeShow: null, //A function to be called before the Wickedpicker is shown
                    show: null, //A function to be called when the Wickedpicker is shown
                    clearable: true //Make the picker's input clearable (has clickable "x")
                });

                $data = $("td[data-field=check_out] input", this);
                $data.attr('id', 'check_out');
                $('#check_out').wickedpicker({
                    now: $data.val(),
                    twentyFour: true,  //Display 24 hour format, defaults to false
                    upArrow: 'wickedpicker__controls__control-up',  //The up arrow class selector to use, for custom CSS
                    downArrow: 'wickedpicker__controls__control-down', //The down arrow class selector to use, for custom CSS
                    close: 'wickedpicker__close', //The close class selector to use, for custom CSS
                    hoverState: 'hover-state', //The hover state class to use, for custom CSS
                    title: 'Jam', //The Wickedpicker's title,
                    showSeconds: false, //Whether or not to show seconds,
                    secondsInterval: 1, //Change interval for seconds, defaults to 1,
                    minutesInterval: 1, //Change interval for minutes, defaults to 1
                    beforeShow: null, //A function to be called before the Wickedpicker is shown
                    show: null, //A function to be called when the Wickedpicker is shown
                    clearable: true //Make the picker's input clearable (has clickable "x")
                });

                $data = $("td[data-field=end_check_out] input", this);
                $data.attr('id', 'end_check_out');
                $('#end_check_out').wickedpicker({
                    now: $data.val(),
                    twentyFour: true,  //Display 24 hour format, defaults to false
                    upArrow: 'wickedpicker__controls__control-up',  //The up arrow class selector to use, for custom CSS
                    downArrow: 'wickedpicker__controls__control-down', //The down arrow class selector to use, for custom CSS
                    close: 'wickedpicker__close', //The close class selector to use, for custom CSS
                    hoverState: 'hover-state', //The hover state class to use, for custom CSS
                    title: 'Jam', //The Wickedpicker's title,
                    showSeconds: false, //Whether or not to show seconds,
                    secondsInterval: 1, //Change interval for seconds, defaults to 1,
                    minutesInterval: 1, //Change interval for minutes, defaults to 1
                    beforeShow: null, //A function to be called before the Wickedpicker is shown
                    show: null, //A function to be called when the Wickedpicker is shown
                    clearable: true //Make the picker's input clearable (has clickable "x")
                });



            },
            save: function(values) {
                var id = $(this).data('id');

                $.ajax({
                    url     : '<?php echo site_url('shift/saveedit');?>/' + id,
                    dataType: 'json',
                    type    : 'POST',
                    data    : values,
                    success : function(data){
                            bootbox.alert(data.msg);
                    }
                });

                $(".edit i", this)
                    .removeClass('fa-save')
                    .addClass('fa-pencil')
                    .attr('title', 'Edit');
            },
            cancel: function(values) {
                $(".edit i", this)
                    .removeClass('fa-save')
                    .addClass('fa-pencil')
                    .attr('title', 'Edit');
            }
            <?php } ?>
        });
        createpaging("#list-data");
        $('#cek_all').click(function(){
            $(".selected").prop("checked", $("#cek_all").prop("checked"));
        });



        $('.selected').click(function(){
            if($(".selected").length == $(".selected:checked").length) {
                $("#cek_all").prop("checked", true);
            } else {
                $("#cek_all").prop("checked", false);
            }
        });


    });

    function createpaging(domId)
    {
        $("#pagering").find("a").each(function(i){
            var thisHref = $(this).attr("href");
            $(this).prop('href','javascript:void(0)');
            $(this).prop('rel',thisHref);
            if ( !$( this ).prop( "class" ) ) {
                $(this).prop('class', 'paginate_button');
            }

            $(this).bind('click', function(){
                load_url(thisHref,domId);
                return false;
            });
        });
    }

    function load_url(theurl,div)
    {
        var par4 = $('#limit_display').val();
        if ($('#caridata').val() != '')
            par5 = $('#caridata').val();
        else
            par5 = 'cri';

        if (theurl.substr(theurl.lastIndexOf('/') + 1) == "") {
            theurl=theurl+'0';
        }
        //loading();
        $.ajax({
            method:"post",
            url: theurl,
            data:{"lmt":par4,"cari":par5},
            success: function(response){
                $(div).html(response);
            },
            dataType:"html"
        });
        return false;
    }


</script>
