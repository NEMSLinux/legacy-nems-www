<?php

  $defaultbgcolor = '040111';

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

?>
