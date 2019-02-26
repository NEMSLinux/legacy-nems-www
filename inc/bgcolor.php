<?php

  $defaultbgcolor = '040111';

    $conftmp = file('/usr/local/share/nems/nems.conf');
    if (is_array($conftmp) && count($conftmp) > 0) {
      foreach ($conftmp as $line) {
        $tmp = explode('=',$line);
        if (trim($tmp[0]) == 'background') {
          $background=trim($tmp[1]);
        } elseif (trim($tmp[0]) == 'backgroundColor') {
          $backgroundColor=trim($tmp[1]);
        }
      }
    }

    // User has defined their own color
    if ($background == 7) {

      $tmp=explode(',',str_replace(array('hsv(',')'),array('',''),$backgroundColor));
      $h = trim($tmp[0]);
      $s = trim($tmp[1]);
      $v = trim($tmp[2]);
      $rgb=hsv2rgb($h,$s,$v);
      $bgcolor = $rgb['html'];

    } else {

      $key = strtotime('today');
      // caches the response each day
      $cachefile = '/tmp/bgcolor-' . $key;
      if (file_exists($cachefile)) {
        $result = trim(file_get_contents($cachefile));
      } else {
        $json_url = 'https://cloud.nemslinux.com/bgcolor/' . $key . '.json';
        $ch = curl_init( $json_url );
        $options = array(
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_HTTPHEADER => array('Content-type: application/json') ,
        );
        curl_setopt_array( $ch, $options );
        $result = curl_exec($ch);
        file_put_contents($cachefile,$result);
      }
      $resultobj = json_decode($result);
      if (isset($resultobj->$key)) {
        $bgcolor = $resultobj->$key;
      } else {
        $bgcolor = $defaultbgcolor;
      }
    }


if (!function_exists('hsv2rgb')) {
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
    $factor = 255 * ($val/100);
    for($i=0;$i<3;$i++) {
      //use distance between 0 and max (1) and multiply with value
      $rgb[$i] = round(($rgb[$i] + ($max - $rgb[$i]) * (1 - $sat/100)) * $factor);
    }
    $rgb['html'] = sprintf('%02X%02X%02X', $rgb[0], $rgb[1], $rgb[2]);
    return $rgb;
  }
}




?>
