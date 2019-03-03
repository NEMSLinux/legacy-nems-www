<?php
  $functions_loaded=1;

  $alias = trim(shell_exec('/usr/local/bin/nems-info alias'));

  function ver($product='nems') {
    $platform = new stdClass();
    $arrContextOptions=array(
        "ssl"=>array(
          "verify_peer"=>false,
          "verify_peer_name"=>false,
        ),
    );
    switch ($product) {
      case 'nems':
        $nemsver = shell_exec('/usr/local/share/nems/nems-scripts/info.sh nemsver');
        return trim($nemsver); // version of NEMS
        break;
      case 'nems-branch':
        $nemsbranch = shell_exec('/usr/local/share/nems/nems-scripts/info.sh nemsbranch');
        return trim($nemsbranch);
        break;
      case 'nems-available': // obtained from our site each day via root cron
        $ver = file_get_contents('/var/www/html/inc/ver-available.txt');
        return trim($ver); // version of NEMS currently available on our site
        break;
      case 'nems-branch-avail':
        $ver = file_get_contents('/var/www/html/inc/ver-available.txt');
        $tmp = explode('.',$ver);
        $nems_branch_avail = $tmp[0] . '.' . $tmp[1];
        return trim($nems_branch_avail);
        break;
//      case 'nagios': // /usr/sbin/nagios3 --version
//        return '3.5.1'; // is this used anywhere?! If yes, need to fix this as it's completely wrong.
//	break;
      case 'platform': // which platform is this for
        $platform->num = trim(shell_exec('/usr/local/share/nems/nems-scripts/info.sh platform'));
//        $platform = json_decode(file_get_contents('https://nemslinux.com/api/platform/' . $platform_num, false, stream_context_create($arrContextOptions)));
        $platform->name = trim(shell_exec('/usr/local/share/nems/nems-scripts/info.sh platform-name'));
        return $platform; // version of NEMS currently available on our site
	break;
    }
  }


  if (!function_exists('_getServerLoadLinuxData')) {
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
  }


  $self = new stdClass();
  if (isset($_SERVER['REQUEST_SCHEME'])) $self->protocol = $_SERVER['REQUEST_SCHEME']; else $self->protocol = 'http';
  if (isset($_SERVER['HTTP_HOST'])) $self->host = $_SERVER['HTTP_HOST']; else $self->host = 'nems.local';

  function checkConfEnabled($service) {
    $response = false;
    $conf = '/usr/local/share/nems/nems.conf';
    $tmp = file($conf);
    if (is_array($tmp)) {
      foreach ($tmp as $line) {
        $data = explode('=',$line);
        $confdata[$data[0]] = $data[1];
      }
      if (is_array($confdata) && isset($confdata['service.' . $service])) {
        if ($confdata['service.' . $service] == 0) {
          $response = false;
        } else {
          $response = true;
        }
      } else {
        $response = true; // it's true because it has not been set otherwise
      }
    }
    return($response);
  }

  function formatBytes($bytes, $precision = 2) {
      $units = array('B', 'KB', 'MB', 'GB', 'TB');
      $bytes = max($bytes, 0);
      $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
      $pow = min($pow, count($units) - 1);
      // Uncomment one of the following alternatives
      $bytes /= pow(1024, $pow);
      // $bytes /= (1 << (10 * $pow));
      return round($bytes, $precision) . $units[$pow];
  }

  function initialized() {
    $initialized = 0;
    $htpasswd = '/var/www/htpasswd';
    if (file_exists($htpasswd)) {
      $initialized = strlen(file_get_contents($htpasswd));
    }
    if ($initialized > 0) {
      return true;
    } else {
      return false;
    }
  }


  // Theme color helpers

  function hex2rgb($color){
    $color = str_replace('#', '', $color);
    if (strlen($color) != 6){ return array(0,0,0); }
    $rgb = array();
    for ($x=0;$x<3;$x++){
      $rgb[$x] = hexdec(substr($color,(2*$x),2));
    }
    return $rgb;
  }

  function rgb2hsv($rgb,$version='string'){
    $r = $rgb[0] / 255;
    $g = $rgb[1] / 255;
    $b = $rgb[2] / 255;

    $v = max($r, $g, $b);
    $diff = $v - min($r, $g, $b);

    $diffc = function($c) use ($v, $diff) {
      return ($v - $c) / 6 / $diff + 1 / 2;
    };

    if($diff == 0){
      $h = $s = 0;
    }else{
      $s = $diff / $v;
      $rr = $diffc($r);
      $gg = $diffc($g);
      $bb = $diffc($b);

      if($r === $v){
          $h = $bb - $gg;
      }else if($g === $v){
          $h = (1 / 3) + $rr - $bb;
      }else if($b === $v){
          $h = (2 / 3) + $gg - $rr;
      }

      if($h < 0){
          $h += 1;
      }else if($h > 1){
          $h -= 1;
      }
    }
    if ($version == 'array') {
      return [ round($h * 360), round($s * 100), round($v * 100) ];
    } else {
      $hsv = round($h * 360) . ',' . round($s * 100) . '%,' . round($v * 100) . '%';
      return $hsv;
    }
  }

  function hsv2rgb($hue,$sat,$val) {;
    $rgb = array(0,0,0);
    //calc rgb for 100% SV, go +1 for BR-range
    for($i=0;$i<4;$i++) {
      if (abs($hue - $i*120)<120) {
        $distance = max(60,abs($hue - $i*120));
        $rgb[$i % 3] = 1 - (($distance-60) / 60);
      }
    }
    //desaturate by increasing lower levels
    $max = max($rgb);
    $factor = 255 * (intval($val)/100);
    for($i=0;$i<3;$i++) {
      //use distance between 0 and max (1) and multiply with value
      $rgb[$i] = round(($rgb[$i] + ($max - $rgb[$i]) * (1 - intval($sat)/100)) * $factor);
    }
    $rgb['html'] = sprintf('%02X%02X%02X', $rgb[0], $rgb[1], $rgb[2]);
    return $rgb;
  }




?>
