<?php
$conftmp = file('/home/pi/nems.conf');
if (is_array($conftmp) && count($conftmp) > 0) {
  foreach ($conftmp as $line) {
    $tmp = explode('=',$line);
    $config[trim($tmp[0])] = trim($tmp[1]);
  }
}

  // Really, no validation security needed since this folder (/config) requires a password to execute
  if (isset($_POST) && isset($_POST['name']) && isset($_POST['value'])) {
    switch ($_POST['name']) {

      case 'nagios-api':
        if ($_POST['value'] == false) {
          $config['service.nagios-api'] = 0;
	} else {
          $config['service.nagios-api'] = 1;
        }
        break;

    }
  }

// writeout the config
if (is_array($config) && count($config) > 0) {
  $confstring = '';
  foreach ($config as $key=>$value) {
    $confstring .= $key . '=' . $value . PHP_EOL;
  }
  file_put_contents('/home/pi/nems.conf',$confstring);
}
?>
