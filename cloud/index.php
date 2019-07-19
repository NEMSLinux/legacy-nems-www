<?php
  $cloudauth = intval(shell_exec('/usr/local/bin/nems-info cloudauthcache'));
  if ($cloudauth != 1) {
    exit('This NEMS Server is not authorized to access NEMS Cloud Services.' . PHP_EOL . 'Please sign up and configure your connection in NEMS SST.');
  }

  // Get the HWID
  $nemsconf['hwid'] = shell_exec('/usr/local/bin/nems-info hwid');

  // Load the NEMS Cloud Services License Key
  $nemsconffile = '/usr/local/share/nems/nems.conf'; // www-admin must have access to read/write
  $conf = file($nemsconffile);
  if (is_array($conf)) { // Load the existing conf data
        foreach ($conf as $line) {
                $tmp = explode('=',$line);
                if (is_array($tmp) && count($tmp) == 2 && trim($tmp[0]) == 'osbkey') $nemsconf['osbkey'] = trim($tmp[1]);
        }
  }

  redirect_post('https://cloud.nemslinux.com/dashboard/',$nemsconf);

  /**
 * Redirect with POST data.
 *
 * @param string $url URL.
 * @param array $post_data POST data. Example: array('foo' => 'var', 'id' => 123)
 * @param array $headers Optional. Extra headers to send.
 */
function redirect_post($url, array $data, array $headers = null) {
    $params = array(
        'http' => array(
            'method' => 'POST',
            'content' => http_build_query($data)
        )
    );
    if (!is_null($headers)) {
        $params['http']['header'] = '';
        foreach ($headers as $k => $v) {
            $params['http']['header'] .= "$k: $v\n";
        }
    }

    $ctx = stream_context_create($params);
    $fp = @fopen($url, 'rb', false, $ctx);
    if ($fp) {
        echo @stream_get_contents($fp);
        die();
    } else {
        // Error
        throw new Exception("Error loading '$url', $php_errormsg");
    }
}
?>
