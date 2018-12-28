<?php
  /*
    5 = default, server room
    6 = cloud daily image
    7 = color picker
    8 = user uploaded
  */

  // defaults
  $background=5;
  $backgroundBlur=2; // 2 = disabled, 1 = enabled

  $conftmp = file('/usr/local/share/nems/nems.conf');
  if (is_array($conftmp) && count($conftmp) > 0) {
    foreach ($conftmp as $line) {
      $tmp = explode('=',$line);
      if (trim($tmp[0]) == 'background') {
        $background=trim($tmp[1]);
      } elseif (trim($tmp[0]) == 'backgroundBlur') {
        $backgroundBlur=trim($tmp[1]);
      }
    }
  }
  // default image within nems-www
  $defaultimg = '/img/wallpaper/server_room_dark.jpg';
  // Set the default background element to replace
  if (!isset($backgroundElem)) $backgroundElem = 'body';

  switch ($background) {

    case 6:
      $key = strtotime('today');
      // caches the response each day
      if (file_exists('/tmp/wallpaper-' . $key)) {
        $result = trim(file_get_contents('/tmp/wallpaper-' . $key));
      } else {
        $json_url = 'https://cloud.nemslinux.com/wallpaper/' . $key . '.json';
        $ch = curl_init( $json_url );
        $options = array(
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_HTTPHEADER => array('Content-type: application/json') ,
        );
        curl_setopt_array( $ch, $options );
        $result = curl_exec($ch);
        file_put_contents('/tmp/wallpaper-' . $key,$result);
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
  if ($backgroundBlur == 1) {
    $bluramt = 15;
    echo "<style>div.backstretch { -webkit-filter: blur(" . $bluramt . "px); -moz-filter: blur(" . $bluramt . "px); -o-filter: blur(" . $bluramt . "px); -ms-filter: blur(" . $bluramt . "px); filter: blur(" . $bluramt . "px); margin: -" . ($bluramt*2) . "px; }</style>";
  }
?>
