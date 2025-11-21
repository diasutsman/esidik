<div class="row animated fadeInRight">
	<div class="col-md-4">
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5>Profile Detail</h5>
			</div>
			<?php
			$fil = base_url("photo/".$myProfile['userid'].".jpg");
			$urlAda="";
			$type = getContentType("http://ropeg.setjen.kemendagri.go.id/foto/".$myProfile['userid']."/".$myProfile['userid'].".JPG");
			if (strpos($type, 'image') !== false) {
				$urlAda="http://ropeg.setjen.kemendagri.go.id/foto/".$myProfile['userid']."/".$myProfile['userid'].".JPG";
			} else {
				$type = getContentType("http://ropeg.setjen.kemendagri.go.id/foto/".$myProfile['userid']."/".$myProfile['userid'].".jpg");
				if (strpos($type, 'image') !== false) {
					$urlAda="http://ropeg.setjen.kemendagri.go.id/foto/".$myProfile['userid']."/".$myProfile['userid'].".jpg";
				} else {
					$type = getContentType("http://ropeg.setjen.kemendagri.go.id/foto/".$myProfile['userid']."/".$myProfile['userid'].".png");
					if (strpos($type, 'image') !== false) {
						$urlAda="http://ropeg.setjen.kemendagri.go.id/foto/".$myProfile['userid']."/".$myProfile['userid'].".png";
					}
				}
			}
			if ($urlAda != "")
			{
				$fil=$urlAda;
			} else {
				if (!file_exists($fil)) {

					$gen = ($myProfile['gender'] == 1 ? "male" : "female") . ".png";
					$fil = base_url("assets/img/" . $gen);
				}

			}
			?>

			<div>
				<div class="ibox-content no-padding center-orientation">
					<center>
					<img alt="image" class="img-responsive" style="padding-left: 15px;width:100px" src="<?php echo $fil;?>">
					</center>
				</div>
				<div class="ibox-content profile-content  text-center">
					<?php if ($isValid ) { ?>
						<h4><strong><?php echo $myProfile['name']?></strong></h4>
						<p><?php echo $myProfile['title']?></p>
						<p><?php echo $myProfile['deptname']?></p>
						<p><?php $myProfile['badgenumber']!=null?"NIP. ":""?> <?php echo $myProfile['badgenumber']?></p>
					<?php }  else  { ?>
						<h4><strong><?php echo $myProfile['username']?></strong></h4>
						<p><?php echo $myProfile['user_level_name']?></p>
						<p><?php echo $myProfile['deptname']?></p>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-8">
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5>Aktivitas</h5>
				<div class="ibox-tools">
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
				</div>
			</div>
			<div class="ibox-content">
				<div>
					<div class="feed-activity-list">
						<div class="feed-element">

								<?php
								foreach($myData->result() as $row)
								{
									$waktu = explode(" ",$row->logtime);
									$tgl = $waktu[0];
									$jam = $waktu[1];

								?>
							<div class="media-body ">
								<small class="pull-right text-navy"><?php echo $jam;?></small>
								<strong><?php echo $row->logdetail;?></strong><br>
								<small class="text-muted"><?php echo indo_date($tgl);?></small>
							</div>
								<?php } ?>

						</div>

						</div>
					</div>

				</div>

			</div>
		</div>

	</div>
</div>