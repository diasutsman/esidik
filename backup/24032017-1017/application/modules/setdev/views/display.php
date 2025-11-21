<?php
/**
 * File: list.php
 * Author: abdiIwan.
 * Date: 12/28/2016
 * Time: 9:38 PM
 * absensi.kemendagri.go.id
 */

$url_pag =  site_url("setdev/pagging/0");


?>

<div class="row">
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5>Daftar Mesin FP</h5>
				<div class="ibox-tools">
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
				</div>
			</div>
			<div class="ibox-content">
				<div class="row">
					<div class="col-sm-5 m-b-xs">
						<div class="btn-group">
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
								Opsi <span class="caret"></span>
							</button>
							<ul class="dropdown-menu" role="menu">
								<li><a href="#" id="btn1">Ganti SN</a>
								</li>
								<li><a href="#" id="btn2">Pindah Area</a>
								</li>
								<li><a href="#">Sinkronisasi</a>
								</li>
								<li><a href="#" id="btn1">Informasi Mesin</a>
								</li>
								<li><a href="#" id="btn2">Reboot</a>
								</li>
								<li><a href="#" onClick="doDelete();" >Hapus</a>
								</li>
							</ul>
						</div>
						<div class="btn-group">
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
								Download <span class="caret"></span>
							</button>
							<ul class="dropdown-menu" role="menu">
								<li><a href="#" id="btn1">Transaksi</a>
								</li>
								<li><a href="#" id="btn2">Pegawai</a>
								</li>
								<li><a href="#" id="btn3">Foto</a>
								</li>
							</ul>
						</div>
					</div>
					<div class="col-sm-4 m-b-xs ">

					</div>
					<div class="col-sm-3">
						<div class="input-group">
							<input type="text" id="caridata" name="caridata" placeholder="Pencarian" class="input-sm form-control" value="<?php echo isset($caridata)?$caridata:''?>">
							<span class="input-group-btn"><button type="button" id="btncari" class="btn btn-sm btn-primary"> <i class="fa fa-search"></i> </button> </span>
						</div>
					</div>

				</div>
				<div id="list-data">
					<?php include ("list.php") ?>
				</div>
				<div id="list-data-temp">
					<?php include ("list2.php") ?>
				</div>
			</div>
		</div>
</div>
