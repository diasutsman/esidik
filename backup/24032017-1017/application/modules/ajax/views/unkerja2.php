<style>
.tree:before {
	display:inline-block;
	content:"";
	position:absolute;
	top:-20px;
	bottom:16px;
	left:0;
	border:1px dotted #67b2dd;
	z-index:1;
	border-width:0 0 0 1px;
}

.tree .tree-folder {
	width:auto;
	min-height:20px;
	cursor:pointer;
	font-size: 9px;
}

.tree .tree-folder .tree-folder-header {
	position:relative;
	/*height:20px;
	line-height:20px;*/
	border-radius:0;
}

.tree .tree-folder .tree-folder-header .tree-folder-name,.tree .tree-item .tree-item-name {
	display:inline;
	z-index:2;
}

.tree .tree-folder .tree-folder-header>[class*="icon-"]:first-child,.tree .tree-item>[class*="icon-"]:first-child {
	display:inline-block;
	position:relative;
	z-index:2;
	top:-1px;
}

.tree .tree-folder .tree-folder-header .tree-folder-name {
	margin-left:2px;
	list-style-type: none;
	overflow: hidden;
    text-decoration: none;
    white-space: nowrap;
}

.tree .tree-folder .tree-folder-header>[class*="icon-"]:first-child {
	margin:-2px 0 0 -2px;
}

.tree .tree-folder:last-child:after {
	display:inline-block;
	content:"";
	position:absolute;
	z-index:1;
	top:15px;
	bottom:0;
	left:-15px;
	border-left:1px solid #FFF;
}

.tree .tree-folder .tree-folder-content {
	margin-left:23px;
	position:relative;
}

.tree .tree-folder .tree-folder-content:before {
	display:inline-block;
	content:"";
	position:absolute;
	z-index:1;
	top:-14px;
	bottom:16px;
	left:-14px;
	border:1px dotted #67b2dd;
	border-width:0 0 0 1px;
}

.tree .tree-item {
	position:relative;
	/*height:20px;
	line-height:20px;*/
	cursor:pointer;
}

.tree .tree-item .tree-item-name {
	margin-left:3px;
}

.tree .tree-item .tree-item-name>[class*="icon-"]:first-child {
	margin-right:3px;
}

.tree .tree-item>[class*="icon-"]:first-child {
	margin-top:-1px;
	color:#f9e8ce;
	width:13px;
	height:13px;
	line-height:13px;
	font-size:9px;
	text-align:center;
	border-radius:3px;
	background-color:#fafafa;
	/*border:1px solid #CCC;*/
	box-shadow:0 1px 2px rgba(0,0,0,0.05);
}

.tree .tree-folder,.tree .tree-item {
	position:relative;
}

.tree .tree-folder:before,.tree .tree-item:before {
	display:inline-block;
	content:"";
	position:absolute;
	top:14px;
	left:-13px;
	width:18px;
	height:0;
	border-top:1px dotted #67b2dd;
	z-index:1;
}

.tree .tree-selected {
	background-color:rgba(98,168,209,0.1);
	color:#6398b0;
}

.tree .tree-selected:hover {
	background-color:rgba(98,168,209,0.1);
}

.tree .tree-item,.tree .tree-folder {
	/*border:1px solid #FFF;*/
}

.tree .tree-item,.tree .tree-folder .tree-folder-header {
	color:#4d6878;
	margin:0;
	padding:5px;
}

.tree .tree-selected>[class*="icon-"]:first-child {
	background-color:#f9a021;
	color:#FFF;
	border-color:#f9a021;
}

.tree .icon-plus[class*="icon-"]:first-child,.tree .icon-minus[class*="icon-"]:first-child {
	vertical-align:middle;
	height:11px;
	width:11px;
	text-align:center;
	border:1px solid #8baebf;
	line-height:10px;
	background-color:#FFF;
	position:relative;
	z-index:1;
}

.tree .icon-plus[class*="icon-"]:first-child:before {
	display:block;
	content:"+";
	font-family:"Open Sans";
	font-size:16px;
	position:relative;
	z-index:1;
}

.tree .icon-minus[class*="icon-"]:first-child:before {
	content:"";
	display:block;
	width:7px;
	height:0;
	border-top:1px solid #4d6878;
	position:absolute;
	top:5px;
	left:2px;
}

.tree .tree-unselectable .tree-item>[class*="icon-"]:first-child {
	color:#5084a0;
	width:13px;
	height:13px;
	line-height:13px;
	font-size:10px;
	text-align:center;
	border-radius:0;
	background-color:transparent;
	border:0;
	box-shadow:none;
}

.tree [class*="icon-"][class*="-down"] {
	transform:rotate(-45deg);
}

.tree .icon-spin {
	height:auto;
}

.tree .tree-loading {
	margin-left:36px;
}

.tree img {
	display:inline;
	veritcal-align:middle;
}

.tree .tree-folder .tree-folder-header:hover,.tree .tree-item:hover {
	background-color:#f0f7fc;
}

.text-tree{
	list-style-type: none;
	overflow: hidden;
    text-decoration: none;
    text-overflow: ellipsis;
    white-space: nowrap;
}
</style>
<?php
	$kode_unker = (($this->session->userdata('s_dept')!='')?$this->session->userdata('s_dept'):'');
	$sql = $this->db->query("SELECT * FROM departments WHERE deptid=$kode_unker ORDER BY deptid ASC");

?>
<div class="tree tree-selectable" style="oveflow-y:hidden" id="treefirst">
<?php

	foreach($sql->result() as $row){
	?>
		<div class="tree-folder" style="display: block;">
			<div class="tree-folder-header hda">
				<i id="fda<?php echo $row->deptid?>" class="icon-plus" onClick="TreeMenu2('<?php echo $row->deptid?>',1)"></i>
				<div class="tree-folder-name fna" onClick="PilihData2('<?php echo $row->deptid?>','<?php echo $row->deptname?>')"><?php echo $row->deptname?></div>
				</div>
			<div id="tra<?php echo $row->deptid?>" class="tree-folder-content" style="display: none;">
		
			</div>
			<div id="loada<?php echo $row->deptid?>" class="tree-loader" style="display: none;">
				<div class="tree-loading">
					<i class="icon-refresh icon-spin blue"></i>
				</div>
			</div>
		</div>
	<?php
	}
?>
</div>
<script>
$(function(){
	var inwidth = $('.hda:eq(0)').width();
	var blt = (Math.floor(inwidth / 7) - 2);
	var count = $('.fna');
	for(i=0;i<(count.length);i++){
		var intext = $('.fna:eq('+i+')').text();
		$('.fna:eq('+i+')').empty();
		if(intext.length > blt){
			$('.fna:eq('+i+')').attr('rel','tooltip');
			$('.fna:eq('+i+')').attr('data-original-title',intext);
		}
		$('.fna:eq('+i+')').text(intext.substring(0,blt)+((intext.length > blt)?'..':''));
	}

	$('[rel="tooltip"]').tooltip();
});

function TreeMenu2(v,lvl)
{
	$('.tree').data('clicked',1);
	var namaClass= document.getElementById('fda'+v).className;
	if(namaClass=='icon-plus'){
		$("#loada"+v).css('display','block');
		$.ajax({
			url     : '<?php echo site_url('ajax/child_unkerjaN')?>/'+v+'/'+lvl,
			dataType: 'html',
			type    : 'POST',
			success : function(data){
				$("#loada"+v).css('display','none');
				$("#fda"+v).removeClass('icon-plus').addClass('icon-minus');
				$("#tra"+v).html(data);
				$("#tra"+v).css('display','block');
			}
		});
	}else{
		$("#fda"+v).removeClass('icon-minus').addClass('icon-plus');
		$("#tra"+v).empty();
		$("#tra"+v).css('display','none');
	}
}

function PilihData2(a,b)
{
	$('#cari_unker2').val(b);
	$('#unit_search2').val(a);
	//$('#combo-panel2').empty();
	$('#treefirst').data('clicked',0);
	$('#paneltree').css({
		display : 'none'
	});
}
</script>