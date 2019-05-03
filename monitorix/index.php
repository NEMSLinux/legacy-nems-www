<?php
  include('/var/www/html/inc/functions.php');
  if (!initialized()) {
    include('../init.php');
    exit();
  }
  include('/var/www/html/inc/header.php');
  $platform = ver('platform');
?>

<div class="container" style="margin-top: 100px; padding-bottom: 100px;">
  <p><img src="./logo.png" /></p>

<?php
  if (file_exists('img/system1z.1year.png')) {
?>
					<div class="tab-v1">
						<ul class="nav nav-tabs">
							<li class="active"><a href="#day" data-toggle="tab">Day</a></li>
							<li><a href="#week" data-toggle="tab">Week</a></li>
							<li><a href="#month" data-toggle="tab">Month</a></li>
							<li><a href="#year" data-toggle="tab">Year</a></li>
						</ul>
						<div class="tab-content">

							<div class="tab-pane fade in active" id="day">
								<div class="row">
									<div class="col-md-12">
										<h4>NEMS Linux &ndash; <b>One Day Overview</b></h4>
                    <p>Updated <?= date ("F d Y H:i:s", filemtime('./img/system1z.1day.png')) ?></p>
                    <?php
                      $images = loadMonitorix('d');
                      if (is_array($images) && count($images) > 0) {
                        foreach ($images as $image) {
                          echo PHP_EOL . '                                                                                <div class="row"><div class="text-center col-md-12 col-xs-12"><img src="./img/' . $image . '" style="margin: 10px auto;" class="img-responsive" /></div></div>';
                        }
                      }
                    ?>
									</div>
								</div>
							</div>

              <div class="tab-pane fade in" id="week">
								<div class="row">
									<div class="col-md-12">
										<h4>NEMS Linux &ndash; <b>One Week Overview</b></h4>
                    <p>Updated <?= date ("F d Y H:i:s", filemtime('./img/system1z.1day.png')) ?></p>
                    <?php
                      $images = loadMonitorix('w');
                      if (is_array($images) && count($images) > 0) {
                        foreach ($images as $image) {
                          echo PHP_EOL . '                                                                                <div class="row"><div class="text-center col-md-12 col-xs-12"><img src="./img/' . $image . '" style="margin: 10px auto;" class="img-responsive" /></div></div>';
                        }
                      }
                    ?>
									</div>
								</div>
							</div>

              <div class="tab-pane fade in" id="month">
								<div class="row">
									<div class="col-md-12">
										<h4>NEMS Linux &ndash; <b>One Month Overview</b></h4>
                    <p>Updated <?= date ("F d Y H:i:s", filemtime('./img/system1z.1day.png')) ?></p>
                    <?php
                      $images = loadMonitorix('m');
                      if (is_array($images) && count($images) > 0) {
                        foreach ($images as $image) {
                          echo PHP_EOL . '                                                                                <div class="row"><div class="text-center col-md-12 col-xs-12"><img src="./img/' . $image . '" style="margin: 10px auto;" class="img-responsive" /></div></div>';
                        }
                      }
                    ?>
									</div>
								</div>
							</div>

              <div class="tab-pane fade in" id="year">
								<div class="row">
									<div class="col-md-12">
										<h4>NEMS Linux &ndash; <b>One Year Overview</b></h4>
                    <p>Updated <?= date ("F d Y H:i:s", filemtime('./img/system1z.1day.png')) ?></p>
                    <?php
                      $images = loadMonitorix('y');
                      if (is_array($images) && count($images) > 0) {
                        foreach ($images as $image) {
                          echo PHP_EOL . '                                                                                <div class="row"><div class="text-center col-md-12 col-xs-12"><img src="./img/' . $image . '" style="margin: 10px auto;" class="img-responsive" /></div></div>';
                        }
                      }
                    ?>
									</div>
								</div>
							</div>



						</div>
					</div>
<?php
  } else {
?>

    <div class="row">
      <div class="col-md-12">
        <p>When you first boot your NEMS server, all Monitorix data and graphs are reset. It takes some time for Monitorix to be ready.</p>
        <p>Please check back soon.</p>
      </div>
    </div>

<?php
  }
?>


<p><span style="color:#444;">Powered By</span> <a href="http://www.monitorix.org/" target="_blank">Monitorix</a> by Jordi Sanfeliu</p>

</div>
<?php
  include('/var/www/html/inc/footer.php');
?>
