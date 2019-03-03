<?php

  $defaultbgcolor = '040111';
  include_once('/var/www/html/inc/functions.php');

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
    if ($background == 7 || $background == 8) {

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

  $bgcolorRGB = hex2rgb($bgcolor);

?>
