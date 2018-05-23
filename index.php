<?php
  include('/var/www/html/inc/functions.php');
  if (!initialized()) {
    include('init.php');
    exit();
  }
  include('/var/www/html/inc/header.php');
?>
	<!-- Promo block BEGIN -->
	<section class="promo-section" id="intro">
		<!-- Fullscreen Static Image BEGIN -->
		<div class="fullscreen-static-image fullheight">
			<!-- Promo Content BEGIN -->
			<div class="container valign__middle">
				<div class="row">
				  <div class="col-sm-10 col-sm-offset-1 text-center-xs">
					<div class="promo-text">
					<div class="promo-next">



	<section id="graphs" class="about-section">



		<div class="parallax-counter-v4 parallaxBg1" id="facts">
						<span style="padding:10px 20px;"><span class="color-green">N</span>EMS Linux Version <?= ver('nems') ?></span><br />
						<span style="color: #aaa;font-size:0.6em;">Running on <?php $platform = ver('platform'); echo $platform->name; ?></span>
						<?php if (ver('nems-available') > ver('nems')) echo '<div class="alert alert-warning fade in"><strong>Note:</strong> NEMS ' . ver('nems-available') . ' is available. Please review the changelog on <a href="http://baldnerd.com/nems" target="_blank">our web site</a>.</div></div>'; ?>
						<br /><br />
<?php /*			<div class="container content-sm">
*/
?>
<div>
				<div class="row">

					<div class="col-md-3 col-xs-6 md-margin-bottom-50">

						<i class="fa fa-bar-chart"></i>

						<span class="counter"><?php $load = sys_getloadavg(); echo $load[1]; ?></span>

						<h4>5 Minute Load Average</h4>

					</div>

					<div class="col-md-3 col-xs-6 md-margin-bottom-50">

						<i class="fa fa-line-chart"></i>

						<span class="counter"><?php $mem=get_server_memory_usage(); echo round($mem,2); ?></span>

						<h4>% Memory Usage</h4>

					</div>

					<div class="col-md-3 col-xs-6">

						<i class="fa fa-hdd-o"></i>

						<span class="counter"><?php

                $bytes = disk_total_space("/"); 

                $si_prefix = array( 'B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB' );

                $base = 1024;

                $class = min((int)log($bytes , $base) , count($si_prefix) - 1);

                echo sprintf('%1.2f' , $bytes / pow($base,$class));

              ?></span>

						<h4><?= $si_prefix[$class] ?> Disk Space</h4>

					</div>

					<div class="col-md-3 col-xs-6">

						<i class="fa fa-pie-chart"></i>

						<span class="counter"><?php

                $bytes = disk_free_space("/"); 

                $si_prefix = array( 'B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB' );

                $base = 1024;

                $class = min((int)log($bytes , $base) , count($si_prefix) - 1);

                echo sprintf('%1.2f' , $bytes / pow($base,$class));

              ?></span>

						<h4><?= $si_prefix[$class] ?> Free Disk Space</h4>

					</div>

				</div><!--/end row-->

	<p align="center"><a href="https://www.patreon.com/bePatron?u=8543379" class="btn btn-u btn-u-red" target="_blank"><img src="/img/fa-patreon.png" align="bottom" style="max-height:14px; margin-right: 8px;" class="pull-left img-responsive" /> Become a Patron</a></p>

			</div><!--/end container-->

		</div>



	</section>
					</div>
				  </div>
				</div>
			</div>
		  <!-- Promo Content END -->
		</div>
		<!-- Fullscreen Static Image END -->
	</section>
	<!-- Promo block END -->

<?php
  include('/var/www/html/inc/footer.php');
?>


<?php



    function _getServerLoadLinuxData()

    {

        if (is_readable("/proc/stat"))

        {

            $stats = @file_get_contents("/proc/stat");



            if ($stats !== false)

            {

                // Remove double spaces to make it easier to extract values with explode()

                $stats = preg_replace("/[[:blank:]]+/", " ", $stats);



                // Separate lines

                $stats = str_replace(array("\r\n", "\n\r", "\r"), "\n", $stats);

                $stats = explode("\n", $stats);



                // Separate values and find line for main CPU load

                foreach ($stats as $statLine)

                {

                    $statLineData = explode(" ", trim($statLine));



                    // Found!

                    if

                    (

                        (count($statLineData) >= 5) &&

                        ($statLineData[0] == "cpu")

                    )

                    {

                        return array(

                            $statLineData[1],

                            $statLineData[2],

                            $statLineData[3],

                            $statLineData[4],

                        );

                    }

                }

            }

        }



        return null;

    }



    // Returns server load in percent (just number, without percent sign)

    function getServerLoad()

    {

        $load = null;



        if (stristr(PHP_OS, "win"))

        {

            $cmd = "wmic cpu get loadpercentage /all";

            @exec($cmd, $output);



            if ($output)

            {

                foreach ($output as $line)

                {

                    if ($line && preg_match("/^[0-9]+\$/", $line))

                    {

                        $load = $line;

                        break;

                    }

                }

            }

        }

        else

        {

            if (is_readable("/proc/stat"))

            {

                // Collect 2 samples - each with 1 second period

                // See: https://de.wikipedia.org/wiki/Load#Der_Load_Average_auf_Unix-Systemen

                $statData1 = _getServerLoadLinuxData();

                sleep(1);

                $statData2 = _getServerLoadLinuxData();



                if

                (

                    (!is_null($statData1)) &&

                    (!is_null($statData2))

                )

                {

                    // Get difference

                    $statData2[0] -= $statData1[0];

                    $statData2[1] -= $statData1[1];

                    $statData2[2] -= $statData1[2];

                    $statData2[3] -= $statData1[3];



                    // Sum up the 4 values for User, Nice, System and Idle and calculate

                    // the percentage of idle time (which is part of the 4 values!)

                    $cpuTime = $statData2[0] + $statData2[1] + $statData2[2] + $statData2[3];



                    // Invert percentage to get CPU time, not idle time

                    $load = 100 - ($statData2[3] * 100 / $cpuTime);

                }

            }

        }



        return $load;

    }



    //----------------------------





    function get_server_memory_usage(){



        $free = shell_exec('free');

        $free = (string)trim($free);

        $free_arr = explode("\n", $free);

        $mem = explode(" ", $free_arr[1]);

        $mem = array_filter($mem);

        $mem = array_merge($mem);

        $memory_usage = $mem[2]/$mem[1]*100;



        return $memory_usage;

    }



?>


