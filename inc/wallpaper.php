<?php
  /*
    5 = default, server room
    6 = cloud daily image
    7 = color picker
    8 = user uploaded
  */

  $nemsver = shell_exec('/usr/local/bin/nems-info nemsver');

  // defaults
  $backgroundBlur=1; // 1 = disabled, 2 = slight, 3 = medium, 4 = heavy
  $background=5;
  // only allow >= NEMS 1.5 to change these defaults
  if ($nemsver >= 1.5) {
    $background=6;
    $conftmp = file('/usr/local/share/nems/nems.conf');
    if (is_array($conftmp) && count($conftmp) > 0) {
      foreach ($conftmp as $line) {
        $tmp = explode('=',$line);
        if (trim($tmp[0]) == 'background') {
          $background=trim($tmp[1]);
        } elseif (trim($tmp[0]) == 'backgroundBlur') {
          $backgroundBlur=trim($tmp[1]);
        } elseif (trim($tmp[0]) == 'backgroundColor') {
          $backgroundColor=trim($tmp[1]);
        } elseif (trim($tmp[0]) == 'backgroundImage') {
          $backgroundImage=trim($tmp[1]);
        }
      }
    }
  }

  // default image within nems-www
  $defaultimg = '/img/wallpaper/server_room_dark.jpg';

  // default background color
  $defaultbgcolor = '040111';

  // Set the default background element to replace
  if (!isset($backgroundElem)) $backgroundElem = 'body';

  switch ($background) {

    case 8:
      $bgimg = '/userfiles/' . $backgroundImage;
      $output = "<script>jQuery(document).ready(function() {
        $('" . $backgroundElem . "').backstretch([
            '" . $bgimg . "',
          ], {duration: 10000,transition: 'fade',speed: '1000'});
        });</script>";
      break;

    case 7:
      $tmp=explode(',',str_replace(array('hsv(',')'),array('',''),$backgroundColor));
      $h = trim($tmp[0]);
      $s = trim($tmp[1]);
      $v = trim($tmp[2]);
      $rgb=hsv2rgb($h,$s,$v);
      $vDark = ($v-40);
      if ($vDark < 1) $vDark = 1;
      $rgbDark=hsv2rgb($h,$s,$vDark);
      $output = "
        <style>$backgroundElem { background-image: radial-gradient(" . $rgb['html'] . "," . $rgbDark['html'] . "); }</style>
      ";
      break;

    case 9:
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

      list($rgb[0], $rgb[1], $rgb[2]) = sscanf($bgcolor, "%02x%02x%02x");
      $rgb['html'] = sprintf('#%02X%02X%02X', $rgb[0], $rgb[1], $rgb[2]);
      $vDark = ($v-40);
      if ($vDark < 1) $vDark = 1;
      $rgbDark=hsv2rgb($h,$s,$vDark);
      $output = "
        <style>$backgroundElem { background-image: radial-gradient(" . $rgb['html'] . "," . $rgbDark['html'] . "); }</style>
      ";
      break;

    case 6:
      $key = strtotime('today');
      // caches the response each day
      $cachefile = '/tmp/wallpaper-' . $key;
      if (file_exists($cachefile)) {
        $result = trim(file_get_contents($cachefile));
      } else {
        $json_url = 'https://cloud.nemslinux.com/wallpaper/' . $key . '.json';
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
        $img = $resultobj->$key;
      } else {
        $img = $defaultimg;
      }

      $output = "<script>jQuery(document).ready(function() {
        $('" . $backgroundElem . "').backstretch([
            '" . $img . "',
          ], {duration: 10000,transition: 'fade',speed: '1000'});
        });</script>";
      break;

    case 5:
    default:
      $output = "<script>jQuery(document).ready(function() {
        $('" . $backgroundElem . "').backstretch([
            '" . $defaultimg . "',
          ], {duration: 10000,transition: 'fade',speed: '1000'});
        });</script>";

  }
  echo $output;
  if ($backgroundBlur > 1) {
    switch ($backgroundBlur) {
      case 4:
        $bluramt = 80;
        break;

      case 3:
        $bluramt = 15;
        break;

      case 2:
      default:
        $bluramt = 5;
    }
    echo "<style>div.backstretch { -webkit-filter: blur(" . $bluramt . "px); -moz-filter: blur(" . $bluramt . "px); -o-filter: blur(" . $bluramt . "px); -ms-filter: blur(" . $bluramt . "px); filter: blur(" . $bluramt . "px); margin: -" . ($bluramt*3) . "px; }</style>";
  }
  if ($backgroundElem == 'body') {
    echo "<style>
      .backstretch:after {
        content: '\A';
        position: absolute;
        width: 100%; 
        height:100%;
        top:0;
        left:0;
        background:rgba(0,0,0,0.7);
        opacity: 1;
        transition: all 0.5s;
        -webkit-transition: all 0.5s;
        -moz-transition: all 0.5s;
        /*background: radial-gradient(circle, transparent 20%, black 170%);*/
        box-shadow: inset 0px 0px 600px black;
      }
    </style>";
  } else {
    // just a vignette, but don't darken the overall image (Unify will do that)
    echo "<style>
      .backstretch:after {
        content: '';
        position: absolute;
        width: 100%; 
        height:100%;
        top:0;
        left:0;
        transition: all 0.5s;
        -webkit-transition: all 0.5s;
        -moz-transition: all 0.5s;
        background: radial-gradient(circle, transparent 20%, black 100%);
        /*box-shadow: inset 0px 0px 600px black;*/
      }
    </style>";
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
    $rgb['html'] = sprintf('#%02X%02X%02X', $rgb[0], $rgb[1], $rgb[2]);
    return $rgb;
  }
}

?>
