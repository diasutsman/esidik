<div class="row">
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5>Daftar Jenis Potongan</h5>
				<div class="ibox-tools">
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
				</div>
			</div>
			<div class="ibox-content">
				<div class="table-responsive">
					<table class="table table-striped">
						<thead>
						<tr>

							<th>Jenis Potongan</th>
							<th></th>
						</tr>
						</thead>
						<tbody>
						<?php
						$co=1;
						foreach($lstdata->result() as $row)
						{
							?>
						<tr>
							<td><?php echo $row->keterangan;?></td>
							<td></td>
						</tr>
							<?php

							$co++;
						}

						if (count($lstdata)==0)
						{
							?>
							<tr class="">
								<td colspan="2"><center>Tidak ada data..</center></td>
							</tr>
							<?php
						}
						?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
</div>
