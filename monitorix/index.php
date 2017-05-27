<?php
  include('/var/www/html/inc/functions.php');
  if (!file_exists('/var/www/htpasswd')) {
    include('init.php');
    exit();
  }
  include('/var/www/html/inc/header.php');
?>

<div class="container" style="margin-top: 100px; padding-bottom: 100px;">
  <p><img src="./logo.png" /></p>
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
										<h4>NEMS Raspberry Pi &ndash; <b>One Day Overview</b></h4>
                                                                                <p>Updated <?= date ("F d Y H:i:s T", filemtime('./img/raspberrypi1.1day.png')) ?></p>

										<div class="row">
                                                                                  <div class="text-center col-md-12 col-xs-12">
                                                                                    <img src="./img/system1z.1day.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                  </div>
										</div>

										<div class="row">
                                                                                  <div class="text-center col-md-12 col-xs-12">
                                                                                    <img src="./img/system3z.1day.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                  </div>
										</div>

                                                                                <div class="row">

                                                                                  <div class="text-center col-md-6 col-xs-12">
                                                                                    <img src="./img/raspberrypi1.1day.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                  </div>

                                                                                  <div class="col-md-6">
										    <div class="row">
                                                                                      <div class="text-center col-md-6 col-xs-6">
                                                                                        <img src="./img/raspberrypi2.1day.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                      </div>
                                                                                      <div class="text-center col-md-6 col-xs-6">
                                                                                        <img src="./img/system2.1day.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                      </div>
										    </div>
										    <div class="row">
                                                                                      <div class="text-center col-md-6 col-xs-6">
                                                                                        <img src="./img/raspberrypi3.1day.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                      </div>
										    </div>
                                                                                  </div>

                                                                                </div>

                                                                                <div class="row">
                                                                                  <div class="text-center col-md-4 col-xs-12">
                                                                                    <img src="./img/fs01.1day.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                  </div>
                                                                                  <div class="text-center col-md-4 col-xs-12">
                                                                                    <img src="./img/fs02.1day.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                  </div>
                                                                                  <div class="text-center col-md-4 col-xs-12">
                                                                                    <img src="./img/net01.1day.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                  </div>
                                                                                </div>

                                                                                <div class="row">
                                                                                  <div class="text-center col-md-6 col-xs-12">
                                                                                    <img src="./img/apache01.1day.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                  </div>
                                                                                  <div class="text-center col-md-6 col-xs-12">
                                                                                    <img src="./img/apache04z.1day.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                  </div>
                                                                                </div>

									</div>
								</div>
							</div>

							<div class="tab-pane fade in" id="week">
								<div class="row">
									<div class="col-md-12">
										<h4>NEMS Raspberry Pi &ndash; <b>One Week Overview</b></h4>
                                                                                <p>Updated <?= date ("F d Y H:i:s T", filemtime('./img/raspberrypi1.1week.png')) ?></p>

										<div class="row">
                                                                                  <div class="text-center col-md-12 col-xs-12">
                                                                                    <img src="./img/system1z.1week.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                  </div>
										</div>

										<div class="row">
                                                                                  <div class="text-center col-md-12 col-xs-12">
                                                                                    <img src="./img/system3z.1week.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                  </div>
										</div>

                                                                                <div class="row">

                                                                                  <div class="text-center col-md-6 col-xs-12">
                                                                                    <img src="./img/raspberrypi1.1week.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                  </div>

                                                                                  <div class="col-md-6">
										    <div class="row">
                                                                                      <div class="text-center col-md-6 col-xs-6">
                                                                                        <img src="./img/raspberrypi2.1week.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                      </div>
                                                                                      <div class="text-center col-md-6 col-xs-6">
                                                                                        <img src="./img/system2.1week.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                      </div>
										    </div>
										    <div class="row">
                                                                                      <div class="text-center col-md-6 col-xs-6">
                                                                                        <img src="./img/raspberrypi3.1week.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                      </div>
										    </div>
                                                                                  </div>

                                                                                </div>

                                                                                <div class="row">
                                                                                  <div class="text-center col-md-4 col-xs-12">
                                                                                    <img src="./img/fs01.1week.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                  </div>
                                                                                  <div class="text-center col-md-4 col-xs-12">
                                                                                    <img src="./img/fs02.1week.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                  </div>
                                                                                  <div class="text-center col-md-4 col-xs-12">
                                                                                    <img src="./img/net01.1week.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                  </div>
                                                                                </div>

                                                                                <div class="row">
                                                                                  <div class="text-center col-md-6 col-xs-12">
                                                                                    <img src="./img/apache01.1week.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                  </div>
                                                                                  <div class="text-center col-md-6 col-xs-12">
                                                                                    <img src="./img/apache04z.1week.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                  </div>
                                                                                </div>

									</div>
								</div>
							</div>

							<div class="tab-pane fade in" id="month">
								<div class="row">
									<div class="col-md-12">
										<h4>NEMS Raspberry Pi &ndash; <b>One Month Overview</b></h4>
                                                                                <p>Updated <?= date ("F d Y H:i:s T", filemtime('./img/raspberrypi1.1month.png')) ?></p>

										<div class="row">
                                                                                  <div class="text-center col-md-12 col-xs-12">
                                                                                    <img src="./img/system1z.1month.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                  </div>
										</div>

										<div class="row">
                                                                                  <div class="text-center col-md-12 col-xs-12">
                                                                                    <img src="./img/system3z.1month.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                  </div>
										</div>

                                                                                <div class="row">

                                                                                  <div class="text-center col-md-6 col-xs-12">
                                                                                    <img src="./img/raspberrypi1.1month.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                  </div>

                                                                                  <div class="col-md-6">
										    <div class="row">
                                                                                      <div class="text-center col-md-6 col-xs-6">
                                                                                        <img src="./img/raspberrypi2.1month.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                      </div>
                                                                                      <div class="text-center col-md-6 col-xs-6">
                                                                                        <img src="./img/system2.1month.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                      </div>
										    </div>
										    <div class="row">
                                                                                      <div class="text-center col-md-6 col-xs-6">
                                                                                        <img src="./img/raspberrypi3.1month.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                      </div>
										    </div>
                                                                                  </div>

                                                                                </div>

                                                                                <div class="row">
                                                                                  <div class="text-center col-md-4 col-xs-12">
                                                                                    <img src="./img/fs01.1month.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                  </div>
                                                                                  <div class="text-center col-md-4 col-xs-12">
                                                                                    <img src="./img/fs02.1month.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                  </div>
                                                                                  <div class="text-center col-md-4 col-xs-12">
                                                                                    <img src="./img/net01.1month.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                  </div>
                                                                                </div>

                                                                                <div class="row">
                                                                                  <div class="text-center col-md-6 col-xs-12">
                                                                                    <img src="./img/apache01.1month.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                  </div>
                                                                                  <div class="text-center col-md-6 col-xs-12">
                                                                                    <img src="./img/apache04z.1month.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                  </div>
                                                                                </div>

									</div>
								</div>
							</div>
							<div class="tab-pane fade in" id="year">
								<div class="row">
									<div class="col-md-12">
										<h4>NEMS Raspberry Pi &ndash; <b>One Year Overview</b></h4>
                                                                                <p>Updated <?= date ("F d Y H:i:s T", filemtime('./img/raspberrypi1.1year.png')) ?></p>

										<div class="row">
                                                                                  <div class="text-center col-md-12 col-xs-12">
                                                                                    <img src="./img/system1z.1year.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                  </div>
										</div>

										<div class="row">
                                                                                  <div class="text-center col-md-12 col-xs-12">
                                                                                    <img src="./img/system3z.1year.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                  </div>
										</div>

                                                                                <div class="row">

                                                                                  <div class="text-center col-md-6 col-xs-12">
                                                                                    <img src="./img/raspberrypi1.1year.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                  </div>

                                                                                  <div class="col-md-6">
										    <div class="row">
                                                                                      <div class="text-center col-md-6 col-xs-6">
                                                                                        <img src="./img/raspberrypi2.1year.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                      </div>
                                                                                      <div class="text-center col-md-6 col-xs-6">
                                                                                        <img src="./img/system2.1year.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                      </div>
										    </div>
										    <div class="row">
                                                                                      <div class="text-center col-md-6 col-xs-6">
                                                                                        <img src="./img/raspberrypi3.1year.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                      </div>
										    </div>
                                                                                  </div>

                                                                                </div>

                                                                                <div class="row">
                                                                                  <div class="text-center col-md-4 col-xs-12">
                                                                                    <img src="./img/fs01.1year.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                  </div>
                                                                                  <div class="text-center col-md-4 col-xs-12">
                                                                                    <img src="./img/fs02.1year.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                  </div>
                                                                                  <div class="text-center col-md-4 col-xs-12">
                                                                                    <img src="./img/net01.1year.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                  </div>
                                                                                </div>

                                                                                <div class="row">
                                                                                  <div class="text-center col-md-6 col-xs-12">
                                                                                    <img src="./img/apache01.1year.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                  </div>
                                                                                  <div class="text-center col-md-6 col-xs-12">
                                                                                    <img src="./img/apache04z.1year.png" style="margin: 10px auto;" class="img-responsive" />
                                                                                  </div>
                                                                                </div>

									</div>
								</div>
							</div>
						</div>
					</div>

<p><span style="color:#444;">Powered By</span> <a href="http://www.monitorix.org/" target="_blank">Monitorix</a> by Jordi Sanfeliu</p>

</div>
<?php
  include('/var/www/html/inc/footer.php');
?>

